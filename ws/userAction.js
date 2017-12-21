const post = require('./post')
const content = d => d instanceof Object ? JSON.stringify(d) : '{}'
let data = require('./data'),
db = require('./db'),
action = require('./action'),
UserInfo = function(){},getDrivers = function(){

},
dis = function (lat1, lng1, lat2, lng2) {
    var radLat1 = lat1 * Math.PI / 180.0;
    var radLat2 = lat2 * Math.PI / 180.0;
    var a = radLat1 - radLat2;
    var b = lng1 * Math.PI / 180.0 - lng2 * Math.PI / 180.0;
    var s = 2 * Math.asin(Math.sqrt(Math.pow(Math.sin(a / 2), 2) + Math.cos(radLat1) * Math.cos(radLat2) * Math.pow(Math.sin(b / 2), 2)));
    s = s * 6378.137;
    s = Math.round(s * 10000) / 10;
    return s
},
SYNC = function(){
    this._s = [];
    this._n = -1;
};

SYNC.prototype = {
    set add(w){
        this._s.push(w);
    },run:function(a,b,c,d){
        this._n++;
        this._s && this._s[this._n] && this._s[this._n](a,b,c,d);
        
    }
}


let act = {

    login(obj,con){ /** 登录 */
        if(!obj.user_token){
            con.sendText(content({status:400,type:'login',message:'token不合法'}))
            console.error('one user login error 1')
            return
        }
        let sync = new SYNC;
        
        sync.add = function(){ /** 获取我的信息（判断登录） */
            post('user/getMyinfo',{user_token:obj.user_token},function(d){
                sync.run(d)
            })
        }
        sync.add = function(d){
            if(!d){
                con.sendText(content({status:400,type:'login',message:'网络错误'}))
                console.error('one user login error 2')
                return
            }
            if(d.code != 200){
                con.sendText(content({status:400,type:'login',message:d.message}))
                console.error('one user login error 3')
                return
            }
            let user = data.UserMap.get(d.data.info.id+'')
            /** 如果用户存在 */
            if(user){
                /** 用户重复登录无效 */
                if(user.con === con)return
                /** 关闭前一次连接 */
                // delete user.con.user_id
                user.con.close();
            }
            /** con.user_id user.id 字符串 */
            con.user_id = d.data.info.id+''
            user = new UserInfo
            user.con = con
            user.id = d.data.info.id+''
            data.UserMap.set(con.user_id,user)
            let latitude = user.latitude = parseFloat(obj.latitude || 0);
            let longitude = user.longitude = parseFloat(obj.longitude || 0);
            
            db.replace('replace into c_user_online (user_id,latitude,longitude) VALUES(?,?,?)',[user.id,latitude,longitude],function(w){
                sync.run(user)
            })
            
            
        }
        sync.add = function(user){
            console.log(`user ${user.id} linked`)
            console.log(`user ${user.id} updated position ${user.latitude},${user.longitude}`)
            con.sendText(content({status:200,type:'login'}))
        }
        sync.run()
    },
    updPostion(obj,con){ /** 更新位置 */

        if(!con.user_id){
            con.sendText(content({status:400,type:'login',message:'未登录'}))
            console.error('one user login error 4')
            return
        }
        let sync = new SYNC;
        let user = data.UserMap.get(con.user_id)
        let latitude = user.latitude = parseFloat(obj.latitude || 0)
        let longitude = user.longitude = parseFloat(obj.longitude || 0)

        if(!latitude || !longitude){
            con.sendText(content({status:400,type:'updPostion',message:'更新位置失败'}))
            return
        }
        sync.add = function(){
            db.replace('update c_user_online set latitude=?,longitude=? where user_id=?',[latitude,longitude,user.id],function(){
                console.log(`user ${con.user_id} updated position ${latitude},${longitude}`)
                sync.run()
            })
        }
            
        sync.add = function(){
            db.find('select * from c_trip where driver_id=? AND type=3 AND status=3',[con.user_id],function(d){
                if(!d)return;
                if(d.last_longitude == '0'){
                    db.update('update c_trip set last_latitude=?,last_longitude=? where driver_id=? AND type=3 AND status=3',[latitude,longitude,con.user_id])
                }else{
                    let di = dis(d.last_latitude,d.last_longitude,latitude,longitude);
                    if(!di || !d.last_latitude || !latitude)di = 0;
                    di += d.real_distance;
                    // if(!di)di = 0;
                    db.update('update c_trip set last_latitude=?,last_longitude=?,real_distance=? where driver_id=? AND type=3 AND status=3',[latitude,longitude,di,con.user_id])
                    // console.log('Move distance: '+ di)
                }
            })
        }
            
        sync.run();
    },
    askForDrivingV2(obj,con){ /** 发起代驾订单 */
        if(!con.user_id){
            con.sendText(content({status:400,type:'login',message:'未登录'}))
            console.error('one user login error 5')
            return
        }

        let user = data.UserMap.get(con.user_id + '')
        let 
            start_latitude = parseFloat(obj.start_latitude || 0),
            start_longitude = parseFloat(obj.start_longitude || 0),
            end_latitude = parseFloat(obj.end_latitude || 0),
            end_longitude = parseFloat(obj.end_longitude || 0),
            start_name = obj.start_name || '',
            end_name = obj.end_name || '',
            start_fee = obj.start_fee || '0.00',
            create_time = parseInt(Date.now() / 1000),
            distance = parseFloat(obj.distance || 0),
            start_time = parseInt(obj.start_time || 0),
            estimated_price = parseFloat(obj.estimated_price || 0),
            phone = obj.phone || '',
            name = obj.name || '',
            city_id = parseInt(obj.city_id || 0),
            latitudeRange = [start_latitude - 0.02,start_latitude + 0.02],
            longitudeRange = [start_longitude - 0.02,start_longitude + 0.02];
        let sync = new SYNC;
        
        /** 是否有正在进行中的订单 */
        sync.add = function(){
            db.find('select * from c_trip where user_id=? and statuss in (5,10,15,20,25,30,35,40,45)',[con.user_id],function(result){
                /** 判断是否有订单正在执行中 */
                if(result){
                    con.sendText(content({status:400,type:'askForDriving',message:'不能重复下单.'+result.trip_id}))
                    return
                }
                sync.run();
            })
        }
        /** 插入订单和行程 */
        sync.add = function(){
            /** 创建订单 */
            db.insert('insert into c_order_driving set start_latitude=?,start_longitude=?,end_latitude=?,end_longitude=?,start_name=?,end_name=?,create_time=?,statuss=5,user_id=?,distance=?,estimated_price=?,start_time=?,phone=?,name=?,city_id=?',[start_latitude,start_longitude,end_latitude,end_longitude,start_name,end_name,create_time,con.user_id,distance,estimated_price,start_time,phone,name,city_id],function(id){
                if(!id)return;
                obj.id = id;
                /** 创建行程 */
                db.insert('insert into c_trip set start_fee=?,statuss=5,start_latitude=?,start_longitude=?,end_latitude=?,end_longitude=?,start_name=?,end_name=?,type=1,id=?,user_id=?,create_time=?,distance=?,estimated_price=?',[start_fee,start_latitude,start_longitude,end_latitude,end_longitude,start_name,end_name,id,con.user_id,create_time,distance,estimated_price],function(trip_id){
                    obj.trip_id = trip_id
                    sync.run(id,trip_id)
                })
            })
        }
        /** 查找3公里内的司机 */
        sync.add = function(id,trip_id){
            db.get('select o.driver_id from c_driver_online o inner join c_driver d on o.driver_id=d.id where o.latitude between ? and ? and o.longitude between ? and ? and d.type_driving=1',[latitudeRange[0],latitudeRange[1],longitudeRange[0],longitudeRange[1]],function(ids){
                for(let i in ids){
                    ids[i] = ids[i].driver_id
                }
                /** 发送成功信息 */
                con.sendText(content({status:200,type:'askForDriving',info:obj}))
                let run = function(n){
                    if(ids.length <= n){
                        latitudeRange = [start_latitude - 0.05,start_latitude + 0.05]
                        longitudeRange = [start_longitude - 0.05,start_longitude + 0.05]
                        sync.run(id,trip_id);
                        return;
                    }
                    let driver = data.DriverMap.get(ids[n]+'')
                    if(driver && !driver.serving){
                        driver.con.sendText(content({status:200,type:'distribute',order_id:id,trip_id:trip_id}))
                        if(user)user.clock = setTimeout(q=>run(n+1),30000)
                    }else{
                        run(n+1)
                    }
                }
                run(0)
            })
        }
        sync.add = function(id,trip_id){

            db.update('update c_order_driving set statuss=10 where id=?',[id],function(){
                db.update('update c_trip set statuss=10 where trip_id=?',[trip_id],function(){
                    sync.run(id);
                });
            });
        }
        sync.add = function(id){
            db.get('select driver_id from c_driver_online where latitude between ? and ? and longitude between ? and ?',[latitudeRange[0],latitudeRange[1],longitudeRange[0],longitudeRange[1]],function(ids){
                for(let i in ids){
                    ids[i] = ids[i].driver_id
                }
                
                let drivers = []
                for(let k in ids){
                    drivers.push(ids[k])
                    let driver = data.DriverMap.get(ids[k]+'')
                    if(driver){
                        if(driver.serving)continue
                        let g = function(r){
                            driver.con.sendText(content({status:200,type:'fleshDrivingList','mode':'create',list:r}))
                        };
                        (driver.type_driving && driver.type_taxi) && action.driverGetOrders(driver.latitude,driver.longitude,g);
                        (driver.type_driving && !driver.type_taxi) && action.driverGetOrdersDriving(driver.latitude,driver.longitude,g);
                        (!driver.type_driving && driver.type_taxi) && action.driverGetOrdersTaxi(driver.latitude,driver.longitude,g);
                    }
                }
                if(drivers.length)db.update('update c_order_driving set driver_ids=? where id=?',[drivers.join(','),id])
            })
        }
        sync.run();
    },
    askForTaxiV2(obj,con){ /** 发起代驾订单 */
        if(!con.user_id){
            con.sendText(content({status:400,type:'login',message:'未登录'}))
            console.error('one user login error 5')
            return
        }

        let user = data.UserMap.get(con.user_id + '')
        let 
            start_latitude = parseFloat(obj.start_latitude || 0),
            start_longitude = parseFloat(obj.start_longitude || 0),
            end_latitude = parseFloat(obj.end_latitude || 0),
            end_longitude = parseFloat(obj.end_longitude || 0),
            start_name = obj.start_name || '',
            end_name = obj.end_name || '',
            start_fee = obj.start_fee || '0.00',
            create_time = parseInt(Date.now() / 1000),
            distance = parseFloat(obj.distance || 0),
            start_time = parseInt(obj.start_time || 0),
            estimated_price = parseFloat(obj.estimated_price || 0),
            phone = obj.phone || '',
            name = obj.name || '',
            meter = obj.meter || 0,
            city_id = parseInt(obj.city_id || 0),
            latitudeRange = [start_latitude - 0.02,start_latitude + 0.02],
            longitudeRange = [start_longitude - 0.02,start_longitude + 0.02];
            
        let sync = new SYNC;
        
        /** 是否有正在进行中的订单 */
        sync.add = function(){
            db.find('select * from c_trip where user_id=? and statuss in (5,10,15,20,25,30,35,40,45)',[con.user_id],function(result){
                /** 判断是否有订单正在执行中 */
                if(result){
                    con.sendText(content({status:400,type:'callTaxi',message:'不能重复下单.'+result.trip_id}))
                    return
                }
                sync.run();
            })
        }
        /** 插入订单和行程 */
        sync.add = function(){
            /** 创建订单 */
            db.insert('insert into c_order_taxi set start_latitude=?,start_longitude=?,end_latitude=?,end_longitude=?,start_name=?,end_name=?,create_time=?,statuss=5,user_id=?,distance=?,estimated_price=?,start_time=?,phone=?,name=?,city_id=?,meter=?',[start_latitude,start_longitude,end_latitude,end_longitude,start_name,end_name,create_time,con.user_id,distance,estimated_price,start_time,phone,name,city_id,meter],function(id){
                if(!id)return;
                obj.id = id;
                /** 创建行程 */
                db.insert('insert into c_trip set start_fee=?,statuss=5,start_latitude=?,start_longitude=?,end_latitude=?,end_longitude=?,start_name=?,end_name=?,type=2,id=?,user_id=?,create_time=?,distance=?,estimated_price=?,meter=?',[start_fee,start_latitude,start_longitude,end_latitude,end_longitude,start_name,end_name,id,con.user_id,create_time,distance,estimated_price,meter],function(trip_id){
                    obj.trip_id = trip_id
                    sync.run(id,trip_id)
                })
            })
        }
        /** 查找3公里内的司机 */
        sync.add = function(id,trip_id){
            db.get('select o.driver_id from c_driver_online o inner join c_driver d on o.driver_id=d.id where o.latitude between ? and ? and o.longitude between ? and ? and d.type_taxi=1',[latitudeRange[0],latitudeRange[1],longitudeRange[0],longitudeRange[1]],function(ids){
                for(let i in ids){
                    ids[i] = ids[i].driver_id
                }
                /** 发送成功信息 */
                con.sendText(content({status:200,type:'callTaxi',info:obj}))
                let run = function(n){
                    if(ids.length <= n){
                        latitudeRange = [start_latitude - 0.05,start_latitude + 0.05]
                        longitudeRange = [start_longitude - 0.05,start_longitude + 0.05]
                        sync.run(id,trip_id);
                        return;
                    }
                    let driver = data.DriverMap.get(ids[n]+'')
                    if(driver && !driver.serving){
                        driver.con.sendText(content({status:200,type:'distribute',order_id:id,trip_id:trip_id}))
                        if(user)user.clock = setTimeout(q=>run(n+1),30000)
                    }else{
                        run(n+1)
                    }
                }
                run(0)
            })
        }
        sync.add = function(id,trip_id){

            db.update('update c_order_taxi set statuss=10 where id=?',[id],function(){
                db.update('update c_trip set statuss=10 where trip_id=?',[trip_id],function(){
                    sync.run(id);
                });
            });
        }
        sync.add = function(id){
            db.get('select driver_id from c_driver_online where latitude between ? and ? and longitude between ? and ?',[latitudeRange[0],latitudeRange[1],longitudeRange[0],longitudeRange[1]],function(ids){
                for(let i in ids){
                    ids[i] = ids[i].driver_id
                }
                
                let drivers = []
                for(let k in ids){
                    drivers.push(ids[k])
                    let driver = data.DriverMap.get(ids[k]+'')
                    if(driver){
                        if(driver.serving)continue
                        let g = function(r){
                            driver.con.sendText(content({status:200,type:'fleshDrivingList','mode':'create',list:r}))
                        };
                        (driver.type_driving && driver.type_taxi) && action.driverGetOrders(driver.latitude,driver.longitude,g);
                        (driver.type_driving && !driver.type_taxi) && action.driverGetOrdersDriving(driver.latitude,driver.longitude,g);
                        (!driver.type_driving && driver.type_taxi) && action.driverGetOrdersTaxi(driver.latitude,driver.longitude,g);
                    }
                }
                if(drivers.length)db.update('update c_order_taxi set driver_ids=? where id=?',[drivers.join(','),id])
            })
        }
        sync.run();
    },
    cancelAskForDriving(obj,con){/** 取消代驾订单 */

        if(!con.user_id){
            con.sendText(content({status:400,type:'login',message:'未登录'}))
            console.error('one user login error 6')
            return
        }


        /** 获取代驾订单id */
        let id = obj.id || 0
        let trip_id = obj.trip_id || 0
        let types = obj.types || 1
        let reason = obj.reason || ''
        let user = data.UserMap.get(con.user_id)

        let className;

        if(!id && !trip_id){

            con.sendText(content({status:400,type:'cancelAskForDriving',id:id,trip_id:trip_id,message:'订单不存在'}))
            return;
        }

        let sync = new SYNC
        if(id){
            sync.add = function(){
                if(types == 1)db.find('select * from c_trip where id=? and type=? and user_id=?',[id,types,con.user_id],function(w){sync.run(w)})
            }
        }
        else if(trip_id){
            sync.add = function(){
                db.find('select * from c_trip where trip_id=? and user_id=?',[trip_id,con.user_id],function(w){sync.run(w)})
            }
        }
        else{

            con.sendText(content({status:400,type:'cancelAskForDriving',id:id,trip_id:trip_id,message:'不知道什么原因'}))
            return;
        }

        sync.add = function(trip){
            

            if(!trip){
                con.sendText(content({status:400,type:'cancelAskForDriving',id:id,trip_id:trip_id,message:'行程不存在'}))
                return;
            }

            trip_id = trip.trip_id;
            id = trip.id;

            className = trip.type == 1?'driving':trip.type == 2?'taxi':'way'

            db.find('select * from c_order_'+className+' where id=? and user_id=?',[id,con.user_id],function(result){

                if(!result){
                    con.sendText(content({status:400,type:'cancelAskForDriving',id:id,trip_id:trip_id,message:'订单不存在'}))
                    return;
                }

                /** 接单后多少时间内不能取消 */
                if(result.order_time + 120 < parseInt(Date.now() / 1000) && result.order_time + trip.duration + 120 > parseInt(Date.now() / 1000)){
                    // con.sendText(content({status:400,type:'cancelAskForDriving',id:id,trip_id:trip_id,message:'行程无法取消'}))

                }
                /** 订单状态 */
                if([5,10,20,25].indexOf(parseInt(result.statuss))==-1){
                    con.sendText(content({status:400,type:'cancelAskForDriving',id:id,trip_id:trip_id,message:'行程无法取消'}))
                    return;
                }

                let cancel_type;

                if(result.statuss == 5){
                    cancel_type = 1;
                }else if(result.statuss == 10){
                    cancel_type = 1;
                }else if(result.statuss == 20){
                    cancel_type = 2;
                }else if(result.statuss == 25){
                    cancel_type = 3;
                }
                
                if(user && user.clock)clearTimeout(user.clock);
                
                db.update('update c_order_'+className+' set statuss=0 where id=?',[id],function(){
                    db.update('update c_trip set cancel_type=?,statuss=0,cancel_reason=? where trip_id=?',[cancel_type,reason,trip_id],function(){
                        sync.run(result)
                    })
                })
                
            })
        }

        sync.add = function(result){
            con.sendText(content({status:200,type:'cancelAskForDriving',id:id,trip_id:trip_id}))
            con.sendText(content({status:200,type:'statusChange'}))
            if(result.statuss >=20){
                let driver = data.DriverMap.get(result.driver_id+'')
                if(driver){
                    driver.con.sendText(content({status:200,type:'cancelAskForDriving',id:id,trip_id:trip_id}))
                    let g = function(r){
                        driver.con.sendText(content({status:200,type:'fleshDrivingList','mode':'order_cancel',list:r}));
                        driver.serving = 0;
                    };
                    (driver.type_driving && driver.type_taxi) && action.driverGetOrders(driver.latitude,driver.longitude,g);
                    (driver.type_driving && !driver.type_taxi) && action.driverGetOrdersDriving(driver.latitude,driver.longitude,g);
                    (!driver.type_driving && driver.type_taxi) && action.driverGetOrdersTaxi(driver.latitude,driver.longitude,g);
                }
            }else{
                let driver_ids = result.driver_ids
                if(driver_ids){
                    driver_ids = driver_ids.split(',')
                    for(let k in driver_ids){
                        let driver = data.DriverMap.get(driver_ids[k]+'')
                        if(driver){
                            if(driver.serving)continue
                            let g = function(r){
                                driver.con.sendText(content({status:200,type:'fleshDrivingList','mode':'cancel',list:r}))
                            };
                            (driver.type_driving && driver.type_taxi) && action.driverGetOrders(driver.latitude,driver.longitude,g);
                            (driver.type_driving && !driver.type_taxi) && action.driverGetOrdersDriving(driver.latitude,driver.longitude,g);
                            (!driver.type_driving && driver.type_taxi) && action.driverGetOrdersTaxi(driver.latitude,driver.longitude,g);
                        }
                    }
                }
            }
        }

        sync.run()

        

    }
    

}


let z = function(obj,con){

    if(act[obj.type])act[obj.type](obj,con);

    else switch(obj.type){

        case 'callWay':
            /** 用户是否登录 */
            if(!con.user_id)break;

            /** 查询进行中的订单 */
            db.find('select * from c_trip where user_id=? and status in (1,2,3,4)',[con.user_id],function(result){

                /** 判断是否有订单正在执行中 */
                if(result){
                    con.sendText(content({status:400,type:'callWay',message:'不能重复下单'}))
                    return
                }

                /** 获取参数 */
                let start_latitude  = parseFloat(obj.start_latitude || 0)
                let start_longitude = parseFloat(obj.start_longitude || 0)
                let end_latitude    = parseFloat(obj.end_latitude || 0)
                let end_longitude   = parseFloat(obj.end_longitude || 0)
                    
                let start_name      = obj.start_name || ''
                let end_name        = obj.end_name || ''
                let create_time     = parseInt(Date.now() / 1000)
                let distance        = parseInt(obj.distance || 0)
                let start_time      = parseInt(obj.start_time || 0)
                let estimated_price = parseFloat(obj.estimated_price || 0)
                let phone           = obj.phone || ''
                let name            = obj.name || ''
                let city_id         = parseInt(obj.city_id || 0)
                let num         = parseInt(obj.num || 1)

                let latitudeRange = [start_latitude - 0.05,start_latitude + 0.05]
                let longitudeRange = [start_longitude - 0.05,start_longitude + 0.05]



                /** 创建订单 */
                db.insert('insert into c_order_way set start_latitude=?,start_longitude=?,end_latitude=?,end_longitude=?,start_name=?,end_name=?,create_time=?,status=1,user_id=?,distance=?,estimated_price=?,start_time=?,phone=?,name=?,city_id=?,num=?',[start_latitude,start_longitude,end_latitude,end_longitude,start_name,end_name,create_time,con.user_id,distance,estimated_price,start_time,phone,name,city_id,num],function(id){

                    obj.id = id

                    /** 创建行程 */
                    db.insert('insert into c_trip set status=1,start_latitude=?,start_longitude=?,end_latitude=?,end_longitude=?,start_name=?,end_name=?,type=3,id=?,user_id=?,create_time=?,distance=?,estimated_price=?',[start_latitude,start_longitude,end_latitude,end_longitude,start_name,end_name,id,con.user_id,create_time,distance,estimated_price])

                    /** 发送成功信息 */
                    con.sendText(content({status:200,type:'callWay',info:obj}))
                    console.log(`user ${con.user_id} create an order`)

                })
            })
            break;
        case 'cancelCallWay':
            if(con.user_id){

                let id = obj.id || 0
                /** 查询订单 */

                db.find('select * from c_trip where id=? and type = 3 and user_id=?',[id,con.user_id],function(trip){

                    if(!trip){
                        con.sendText(content({status:400,type:'cancelCallWay',id:id,message:'行程不存在'}))
                        return;
                    }
                    db.find('select * from c_order_way where id=? and user_id=?',[id,con.user_id],function(result){

                        /** 订单不存在返回 */
                        if(!result)return;

                        if(result.order_time + 120 < parseInt(Date.now() / 1000) && result.order_time + trip.duration + 120 > parseInt(Date.now() / 1000)){
                            con.sendText(content({status:400,type:'cancelCallWay',id:id,message:'行程无法取消'}))
                            return;
                        }
                        
                        /** 订单状态只有在12时候可以取消 */
                        if([1,2].indexOf(parseInt(result.status))==-1)return;
                        
                        /** 更新订单状态为取消 */
                        db.update('update c_order_way set status=0 where id=?',[id],function(){
                            
                            /** 发送成功信息 */
                            con.sendText(content({status:200,type:'cancelCallWay',id:id}))

                            let driver_id = result.driver_id
                            let driver = data.UserMap.get(driver_id+'')
                            if(driver){

                                // post('user/push',{id:result.driver_id,message:'用户取消了订单！',type:'cancel_order'});

                                console.log('one driver get cancelCallWayDriver request');
                                driver.con.sendText(content({status:200,type:'cancelCallWayDriver',id:id}))
                            }
                            /** 更新行程表 */
                            db.update('update c_trip set status=0 where id=? and type=3',[id])
                        })
                    })
                })
                
            }
            break;
        case 'startWay':
            /** 司机是否登录 */
            if(con.user_id){

                /** 获取订单ID */
                let id = obj.id

                /** 查询订单 */
                db.find('select * from c_order_way where id=?',[id],function(result){

                    /** 订单是否存在 */
                    if(!result)return;

                    /** 订单是否属于登录司机 */
                    if(result.driver_id != con.user_id)return;

                    /** 订单是否是带接客状态 */
                    if(result.status != 2)return;

                        /** 更新订单 */
                        db.update('update c_order_way set status=3 where id=?',[id],function(){
                            /** 获取司机 */
                            let driver = data.UserMap.get(con.user_id+'')
                            /** 更新行程 */
                            db.update('update c_trip set driver_id=?,status=3,in_time=?,last_latitude=?,last_longitude=? where id=? and type=3',[con.user_id,parseInt(Date.now() / 1000),driver.latitude,driver.longitude,id],function(){

                                db.update('update c_driver_way set status=0 where user_id=? and status=1',[con.user_id,con.user_id],function(){

                                    
                                    /** 设置司机状态为服务中 */
                                    driver.serving = 1;
                                    con.sendText(content({status:200,type:'startWay',id:id}))

                                    /** 获取用户 */
                                    let user = data.UserMap.get(result.user_id+'')
                                    if(user)user.con.sendText(content({status:200,type:'startWay',id:id}))

                                })

                                
                            })
                        })
                    
                })
            }
            break;
        case 'endWay':
        
            /** 司机是否登录 */
            if(con.user_id){

                /** 获取订单ID */
                let id = obj.id

                /** 查询订单 */
                db.find('select * from c_order_way where id=?',[id],function(result){

                    /** 订单是否存在 */
                    if(!result)return;

                    /** 订单是否属于登录司机 */
                    if(result.driver_id != con.user_id)return;

                    /** 订单是否是带接客状态 */
                    if(result.status != 3)return;
                    db.find('select * from c_trip where id=? and type=3',[id],function(trip){
                        
                        if(!trip)return;

                        let fee = action.getWayPrice(result.distance,result.num);
                        let total_fee = fee - result.coupon;

                        /** 更新订单 */
                        db.update('update c_order_way set status=4,distance=?,fee=?,total_fee=? where id=?',[trip.real_distance/1000,fee,total_fee,id],function(){

                            /** 更新行程 */
                            db.update('update c_trip set driver_id=?,status=4,out_time=? where id=? and type=3',[con.user_id,parseInt(Date.now() / 1000),id],function(){

                                /** 获取司机 */
                                let driver = data.UserMap.get(con.user_id+'')
                                /** 设置司机状态 */
                                driver.serving = 0;
                                con.sendText(content({status:200,type:'endWay',id:id}))

                                /** 获取用户 */
                                let user = data.UserMap.get(result.user_id+'')
                                if(user)user.con.sendText(content({status:200,type:'endWay',id:id}))
                                
                            })
                        })
                    })
                })
            }
            break;

        case 'orderWay':
            /** 司机是否登录 */
            if(con.user_id){
                let id = obj.id
                let user = data.UserMap.get(id+'')
                if(user)user.con.sendText(content({status:200,type:'orderWay',driver_id:con.user_id}))
                con.sendText(content({status:200,type:'orderWay',user_id:con.id}))
                console.log('one driver get the order')
            }
            break;
        
        default:
            break;
    }



}

module.exports = z