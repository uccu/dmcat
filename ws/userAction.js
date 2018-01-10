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
},sendAdmin = function(f){
    for(let i of data.AdminMap){
        i[1].con.sendText(content({status:200,type:'log',data:f}))
    }

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
            sendAdmin(`user ${user.id} linked`)
            sendAdmin(`user ${user.id} updated position ${user.latitude},${user.longitude}`)
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
        if(!user){
            return
        }
        let latitude = user.latitude = parseFloat(obj.latitude || 0)
        let longitude = user.longitude = parseFloat(obj.longitude || 0)

        if(!latitude || !longitude){
            con.sendText(content({status:400,type:'updPostion',message:'更新位置失败'}))
            return
        }
        sync.add = function(){
            db.replace('update c_user_online set latitude=?,longitude=? where user_id=?',[latitude,longitude,user.id],function(){
                sendAdmin(`user ${con.user_id} updated position ${latitude},${longitude}`)
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
                    // sendAdmin('Move distance: '+ di)
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
            city_id = parseInt(obj.city_id || 0);
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
            db.get('select d.driver_id,round( 6378.138 * 2 * asin( sqrt( pow( sin( (d.latitude* PI()/180- ? * PI() /180)/2 ),2 )+ cos(d.latitude* PI()/180)*cos(? * PI() /180)* pow( sin( (d.longitude* PI()/180 - ? * PI()/180)/2 ),2 ) ) )*1000 ) AS `distance` from c_driver_online d inner join c_driver r on d.driver_id=r.id where round( 6378.138 * 2 * asin( sqrt( pow( sin( (d.latitude* PI()/180- ? * PI() /180)/2 ),2 )+ cos(d.latitude* PI()/180)*cos(? * PI() /180)* pow( sin( (d.longitude* PI()/180 - ? * PI()/180)/2 ),2 ) ) )*1000 ) between ? and ? and r.type_driving=1 order by distance',[start_latitude,start_latitude,start_longitude,start_latitude,start_latitude,start_longitude,0,3000],function(ids){
                sendAdmin(ids)
                for(let i in ids){
                    ids[i] = ids[i].driver_id
                }
                /** 发送成功信息 */
                con.sendText(content({status:200,type:'askForDriving',info:obj}))
                let run = function(n){
                    if(ids.length <= n){
                        sync.run(id,trip_id);
                        return;
                    }
                    let driver = data.DriverMap.get(ids[n]+'')
                    if(driver && !driver.serving){
                        driver.con.sendText(content({status:200,type:'distribute',order_id:id,trip_id:trip_id}))
                        data.clock.set(obj.user_id + '',setTimeout(q=>run(n+1),30000))
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
            db.get('select d.driver_id,round( 6378.138 * 2 * asin( sqrt( pow( sin( (d.latitude* PI()/180- ? * PI() /180)/2 ),2 )+ cos(d.latitude* PI()/180)*cos(? * PI() /180)* pow( sin( (d.longitude* PI()/180 - ? * PI()/180)/2 ),2 ) ) )*1000 ) AS `distance` from c_driver_online d where round( 6378.138 * 2 * asin( sqrt( pow( sin( (d.latitude* PI()/180- ? * PI() /180)/2 ),2 )+ cos(d.latitude* PI()/180)*cos(? * PI() /180)* pow( sin( (d.longitude* PI()/180 - ? * PI()/180)/2 ),2 ) ) )*1000 ) between ? and ?',[start_latitude,start_latitude,start_longitude,start_latitude,start_latitude,start_longitude,3000,5000],function(ids){

                con.sendText(content({status:200,type:'gotoOrder',info:obj}))


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
            city_id = parseInt(obj.city_id || 0);
            
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
            db.insert('insert into c_order_taxi set start_latitude=?,start_longitude=?,end_latitude=?,end_longitude=?,start_name=?,end_name=?,create_time=?,statuss=10,user_id=?,distance=?,estimated_price=?,start_time=?,phone=?,name=?,city_id=?,meter=?',[start_latitude,start_longitude,end_latitude,end_longitude,start_name,end_name,create_time,con.user_id,distance,estimated_price,start_time,phone,name,city_id,meter],function(id){
                if(!id)return;
                obj.id = id;
                /** 创建行程 */
                db.insert('insert into c_trip set start_fee=?,statuss=10,start_latitude=?,start_longitude=?,end_latitude=?,end_longitude=?,start_name=?,end_name=?,type=2,id=?,user_id=?,create_time=?,distance=?,estimated_price=?,meter=?',[start_fee,start_latitude,start_longitude,end_latitude,end_longitude,start_name,end_name,id,con.user_id,create_time,distance,estimated_price,meter],function(trip_id){
                    obj.trip_id = trip_id
                    sync.run(id,trip_id)
                })
            })
        }
        /** 查找3公里内的司机 */
        sync.add = function(id){
            db.get('select d.driver_id,round( 6378.138 * 2 * asin( sqrt( pow( sin( (d.latitude* PI()/180- ? * PI() /180)/2 ),2 )+ cos(d.latitude* PI()/180)*cos(? * PI() /180)* pow( sin( (d.longitude* PI()/180 - ? * PI()/180)/2 ),2 ) ) )*1000 ) AS `distance` from c_driver_online d where round( 6378.138 * 2 * asin( sqrt( pow( sin( (d.latitude* PI()/180- ? * PI() /180)/2 ),2 )+ cos(d.latitude* PI()/180)*cos(? * PI() /180)* pow( sin( (d.longitude* PI()/180 - ? * PI()/180)/2 ),2 ) ) )*1000 ) between ? and ?',[start_latitude,start_latitude,start_longitude,start_latitude,start_latitude,start_longitude,0,3000],function(ids){
                // sendAdmin(ids)
                /** 发送成功信息 */
                con.sendText(content({status:200,type:'callTaxi',info:obj}))

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
        let user_type = 1;

        let className;

        if(!id && !trip_id){

            con.sendText(content({status:400,type:'cancelAskForDriving',id:id,trip_id:trip_id,message:'订单不存在'}))
            return;
        }

        let sync = new SYNC
        if(id){
            sync.add = function(){
                if(types == 1)db.find('select * from c_trip where id=? and type=?',[id,types],function(w){sync.run(w)})
            }
        }
        else if(trip_id){
            sync.add = function(){
                db.find('select * from c_trip where trip_id=?',[trip_id],function(w){sync.run(w)})
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

            if(trip.user_id == con.user_id){
                user_type = 1;
            }else if(trip.driver_id == con.user_id){
                user_type = 2;
            }else{
                con.sendText(content({status:400,type:'cancelAskForDriving',id:id,trip_id:trip_id,message:'订单不存在N'}))
                return;
            }

            className = trip.type == 1?'driving':(trip.type == 2?'taxi':'way')

            db.find('select * from c_order_'+className+' where id=?',[id],function(result){

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
                    if(user_type == 1){
                        cancel_type = 2;
                    }else cancel_type = 4;
                }else if(result.statuss == 25){
                    if(user_type == 1){
                    cancel_type = 3;
                    }else cancel_type = 5;
                }
                if(data.clock.get(result.user_id+''))clearTimeout(data.clock.get(result.user_id+''));
                
                db.update('update c_order_'+className+' set statuss=0 where id=?',[id],function(){
                    db.update('update c_trip set cancel_type=?,statuss=0,cancel_reason=? where trip_id=?',[cancel_type,reason,trip_id],function(){
                        sync.run(result)
                    })
                })
                
            })
        }

        sync.add = function(result){
            if(user_type == 1){
                con.sendText(content({status:200,type:'cancelAskForDriving',id:id,trip_id:trip_id}))
                con.sendText(content({status:200,type:'statusChange'}))
            }else{
                con.sendText(content({status:200,type:'cancelCallWayDriver',id:id,trip_id:trip_id}))
                
            }
            if(className != 'way'){

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
            }else if(className == 'way'){
                if(user_type == 1){
                    let driver = data.UserMap.get(result.driver_id+'')
                    driver && driver.con.sendText(content({status:200,type:'cancelCallWayUser',id:id,trip_id:trip_id}))
                }else{
                    let user = data.UserMap.get(result.user_id+'')
                    user && user.con.sendText(content({status:200,type:'cancelAskForDrivingDriver',id:id,trip_id:trip_id}))
                    user && user.con.sendText(content({status:200,type:'statusChange'}))
                }
                
            }
            
        }

        sync.run()

        

    },
    callWay(obj,con){

        /** 用户是否登录 */
        if(!con.user_id)return;

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
        let num             = parseInt(obj.num || 1)
        let start_fee       = obj.start_fee || '0.00'
        let sync = new SYNC

        sync.add = function(){
            /** 查询进行中的订单 */
            db.find('select * from c_trip where user_id=? and statuss in (5,10,20,25,30,35,45)',[con.user_id],function(result){
                /** 判断是否有订单正在执行中 */
                if(result){
                    con.sendText(content({status:400,type:'callWay',message:'不能重复下单'}))
                    return
                }
                sync.run()
            })
        }

        sync.add = function(){
            /** 创建订单 */
                db.insert('insert into c_order_way set start_latitude=?,start_longitude=?,end_latitude=?,end_longitude=?,start_name=?,end_name=?,create_time=?,statuss=10,user_id=?,distance=?,estimated_price=?,start_time=?,phone=?,name=?,city_id=?,num=?',[start_latitude,start_longitude,end_latitude,end_longitude,start_name,end_name,create_time,con.user_id,distance,estimated_price,start_time,phone,name,city_id,num],function(id){
                    obj.id = id
                    sync.run()
                })
        }
        sync.add = function(){
            /** 创建行程 */
            db.insert('insert into c_trip set statuss=10,start_latitude=?,start_longitude=?,end_latitude=?,end_longitude=?,start_name=?,end_name=?,type=3,id=?,user_id=?,create_time=?,distance=?,estimated_price=?,start_fee=?',[start_latitude,start_longitude,end_latitude,end_longitude,start_name,end_name,obj.id,con.user_id,create_time,distance,estimated_price,start_fee],function(trip_id){
                obj.trip_id = trip_id
                /** 发送成功信息 */
                con.sendText(content({status:200,type:'callWay',info:obj}))
                sendAdmin(`user ${con.user_id} create an order`)
            })
        }
        sync.run()
    },
    orderWay(obj,con){

        /** 司机是否登录 */
        if(!con.user_id){
            con.sendText(content({status:400,type:'login',message:'未登录'}))
            console.error('one user login error 9')
            return
        }

        /** 获取代驾订单id */
        let id
        let type
        let className
        let result
        let trip_id = obj.trip_id || 0
        let driver = data.UserMap.get(con.user_id)

        /** 参数是否传了 */
        if(!trip_id){
            con.sendText(content({status:400,type:'orderWay',trip_id:trip_id,message:'行程不存在'}))
            return;
        }

        let sync = new SYNC

        sync.add = function(){
            db.find('select * from c_user where id=?',[con.user_id],function(driver){

                if(!driver){
                    con.sendText(content({status:400,type:'orderWay',trip_id:trip_id,message:'司机不存在'}))
                    return;
                }

                sync.run()
            })
        }


        sync.add = function(){
            db.find('select * from c_trip where trip_id=? and type IN (3)',[trip_id,con.user_id],function(trip){

                if(!trip){
                    con.sendText(content({status:400,type:'orderWay',trip_id:trip_id,message:'行程不存在'}))
                    return;
                }
                sync.trip = trip
                id = trip.id
                type = trip.type
                /** 订单状态 */
                if([5,10].indexOf(parseInt(trip.statuss))==-1){
                    con.sendText(content({status:400,type:'orderWay',trip_id:trip_id,message:'无法接单'}))
                    return;
                }
                className = trip.type == 1?'driving':trip.type == 2?'taxi':'way'
                if(className != 'way'){
                    con.sendText(content({status:400,type:'orderWay',message:'行程不存在N'}))
                    return;
                }
                sync.run(trip)
            })
        }

        sync.add = function(){
            // sendAdmin(2)
            db.find('select * from c_order_'+className+' where id=?',[id],function(r){
                
                /** 订单是否存在 */
                if(!r){
                    con.sendText(content({status:400,type:'orderWay',message:'订单不存在'}))
                    return;
                }
                /** 订单是否属于登录司机 */
                if(r.driver_id != 0){
                    con.sendText(content({status:400,type:'orderWay',message:'无权限'}))
                    return;
                }
                result = r;
                sync.run()
            })
            
        }

        sync.add = function(){
            // sendAdmin(3)

            db.update('update c_order_'+className+' set driver_id=?,statuss=20,order_time=? where id=?',[con.user_id,parseInt(Date.now() / 1000),id],function(){

                sync.run();
            })
        }

        sync.add = function(){

            action.getDis(driver.latitude,driver.longitude,result.start_latitude,result.start_longitude,3,function(st){
                sync.run(st);
            })
        }

        sync.add = function(st){
            // sendAdmin(5)
            /** 更新行程 */
            if(!st)st  = {}
            db.update('update c_trip set driver_id=?,statuss=20,duration=? where trip_id=?',[con.user_id,st.duration||0,trip_id],function(){

                con.sendText(content({status:200,type:'orderWayDriver',id:id,trip_id:trip_id}))


                let user = data.UserMap.get(result.user_id+'')
                if(data.clock.get(result.user_id+''))clearTimeout(data.clock.get(result.user_id+''));
                if(user){
                    
                    user.con.sendText(content({status:200,type:'orderWay',trip_id:trip_id,id:id}))
                }
                
            })
        }

        sync.run();
    },
    arriveStartPosition(obj,con){ /** 司机到达起点 */

        /** 司机是否登录 */
        if(!con.user_id){
            con.sendText(content({status:400,type:'login',message:'未登录'}))
            return;
        }
        /** 获取司机 */
        let driver = data.UserMap.get(con.user_id)
        /** 获取订单ID */
        let id
        let type
        let className
        let trip_id = obj.trip_id
        if(!trip_id){
            con.sendText(content({status:400,type:'arriveStartPosition',message:'行程不存在'}))
            return;
        }
        let sync = new SYNC;
        
        /** 是否有正在进行中的订单 */
        sync.add = function(){
            /** 查询订单 */
            db.find('select * from c_trip where trip_id=?',[trip_id],function(result){
            
                /** 订单是否存在 */
                if(!result){
                    con.sendText(content({status:400,type:'arriveStartPosition',message:'订单不存在'}))
                    return;
                }
                /** 订单是否属于登录司机 */
                if(result.driver_id != con.user_id){
                    con.sendText(content({status:400,type:'arriveStartPosition',message:'无权限操作此订单'}))
                    return;
                }
                /** 订单是否是抢单状态 */
                if(result.statuss != 20){
                    con.sendText(content({status:400,type:'arriveStartPosition',message:'订单状态不正确'}))
                    return;
                }
                id = result.id
                type = result.type
                className = result.type == 1?'driving':result.type == 2?'taxi':'way'
                if(className != 'way'){
                    con.sendText(content({status:400,type:'arriveStartPosition',message:'行程不存在N'}))
                    return;
                }
                
                sync.run(result)
            })
        }
        sync.add = function(result){
            db.update('update c_trip set statuss=25,laying=1,start_lay_time=? where trip_id=?',[parseInt(Date.now() / 1000),trip_id],function(){
                db.update('update c_order_'+className+' set statuss=25 where id=?',[id],function(){
                
                    sync.run(result)
                })
            })
        }
        sync.add = function(result){
            
            /** 发送成功信息给司机 */
            con.sendText(content({status:200,type:'arriveStartPositionDriver',id:id,trip_id:trip_id}))
            /** 发送成功信息给用户 */
            let user = data.UserMap.get(result.user_id+'')
            if(user){
                user.con.sendText(content({status:200,type:'arriveStartPosition',id:id,trip_id:trip_id}))
                user.con.sendText(content({status:200,type:'statusChange'}))
            }
            sync.run(result)
        }
        sync.run()

    },
    startDriving(obj,con){ /** 司机开始服务 */
        /** 司机是否登录 */
        if(!con.user_id){
            con.sendText(content({status:400,type:'login',message:'未登录'}))
            return;
        }
        /** 获取司机 */
        let driver = data.UserMap.get(con.user_id)
        /** 获取订单ID */
        let id
        let type
        let className
        let trip_id = obj.trip_id
        if(!trip_id){
            con.sendText(content({status:400,type:'startDriving',message:'行程不存在'}))
            return;
        }
        let sync = new SYNC;
        
        sync.add = function(){
        
            db.find('select * from c_trip where trip_id=?',[trip_id],function(trip){
                if(!trip){
                    con.sendText(content({status:400,type:'startDriving',message:'行程不存在'}))
                    return;
                }
                id = trip.id
                type = trip.type
                className = trip.type == 1?'driving':trip.type == 2?'taxi':'way'
                if(className != 'way'){
                    con.sendText(content({status:400,type:'startDriving',message:'行程不存在N'}))
                    return;
                }
                sync.run(trip)
            })
        }
        sync.add = function(w){
            db.find('select * from c_order_'+className+' where id=?',[id],function(result){
                
                /** 订单是否存在 */
                if(!result){
                    con.sendText(content({status:400,type:'startDriving',message:'订单不存在'}))
                    return;
                }
                /** 订单是否属于登录司机 */
                if(result.driver_id != con.user_id){
                    con.sendText(content({status:400,type:'startDriving',message:'无权限'}))
                    return;
                }
                /** 订单是否是带接客状态 */
                if(result.statuss != 25){
                    con.sendText(content({status:400,type:'startDriving',message:'未到达起点'}))
                    return;
                }
                sync.result = result;
                sync.run(w)
            })
            
        }
        
        
        sync.add = function(trip){
            let during = parseInt(Date.now() / 1000) - parseInt(trip.start_lay_time)
            let lay_fee = 0
            if(during > 600){
                lay_fee = Math.ceil((during-600)/60)
            }
        
            db.update('update c_order_'+className+' set statuss=30,lay_fee=? where id=?',[lay_fee,id],function(){
                sync.run(trip,during)
            })
        
        
        }
        
        sync.add = function(trip,during){
            /** 更新行程 */
            db.update('update c_trip set driver_id=?,statuss=30,laying=0,in_time=?,last_latitude=?,last_longitude=?,during=? where trip_id=?',[con.user_id,parseInt(Date.now() / 1000),driver.latitude,driver.longitude,during,trip_id],function(){
            
                sync.run(trip)
            })
        
        }
        sync.add = function(trip){
            /** 设置司机状态为服务中 */
            con.sendText(content({status:200,type:'startDrivingDriver',id:id}))
            /** 获取用户 */
            let user = data.UserMap.get(sync.result.user_id+'')
            if(user){
                user.con.sendText(content({status:200,type:'startDriving',id:id}))
                user.con.sendText(content({status:200,type:'statusChange'}))
            }
        }
        sync.run()
    },
    endDriving(obj,con){ /** 结束服务 */

        /** 司机是否登录 */
        if(!con.user_id){
            con.sendText(content({status:400,type:'login',message:'未登录'}))
            return;
        }
        /** 获取司机 */
        let driver = data.UserMap.get(con.user_id)
        /** 获取订单ID */
        let id
        let type
        let trip_id = obj.trip_id
        let className
        if(!trip_id){
            con.sendText(content({status:400,type:'endDriving',message:'订单id不存在'}))
            return;
        }
        let sync = new SYNC;
        
        sync.add = function(){
            db.find('select * from c_trip where trip_id=?',[trip_id],function(trip){
                if(!trip){
                    con.sendText(content({status:400,type:'endDriving',message:'行程不存在'}))
                    return;
                }
                id = trip.id
                type = trip.type
                className = trip.type == 1?'driving':trip.type == 2?'taxi':'way'
                if(className != 'way'){
                    con.sendText(content({status:400,type:'endDriving',message:'行程不存在N'}))
                    return;
                }
                sync.run(trip)
            })
        }
        
        sync.add = function(trip){

            db.find('select * from c_order_'+className+' where id=?',[id],function(result){
                /** 订单是否存在 */
                if(!result){
                    con.sendText(content({status:400,type:'endDriving',message:'订单不存在'}))
                    return;
                }
                /** 订单是否属于登录司机 */
                if(result.driver_id != con.user_id){
                    con.sendText(content({status:400,type:'endDriving',message:'无权限'}))
                    return;
                }
                /** 订单是否是服务中状态 */
                if(result.statuss != 30){
                    con.sendText(content({status:400,type:'startDriving',message:'未开始服务'}))
                    return;
                }
                sync.run(trip,result)
            })
        }
        
        
        sync.add = function(trip,result){
            if(type == 1)action.getDrivingPrice(result.city_id,trip.in_time,trip.real_distance/1000,function(prices){
                sync.run(trip,prices,result)
            })
            else if(type == 2)action.getTaxiPrice(result.city_id,trip.in_time,trip.real_distance/1000,function(prices){
                sync.run(trip,prices,result)
            })
            else if(type == 3)action.getWayPrice(trip.real_distance/1000,result.num,function(prices){
                sync.run(trip,prices,result)
            })
        }
        sync.add = function(trip,prices,result){
            db.update('update c_trip set driver_id=?,statuss=35,laying=0,out_time=?,start_fee=? where trip_id=?',[con.user_id,parseInt(Date.now() / 1000),prices.start,trip_id],function(){
                sync.run(trip,prices,result)
            })
        }
        sync.add = function(trip,prices,result){
            let fee = prices.total;
            let total_fee = fee - result.coupon + result.lay_fee;
            db.update('update c_order_'+className+' set statuss=35,distance=?,fee=?,total_fee=? where id=?',[trip.real_distance/1000,fee,total_fee,id],function(){
                sync.run(result)
            })

        }
        sync.add = function(result){
            /** 设置司机状态 */
            con.sendText(content({status:200,type:'endDrivingDriver',id:id}))
            

            /** 获取用户 */
            let user = data.UserMap.get(result.user_id+'')
            if(user){
                user.con.sendText(content({status:200,type:'endDriving',id:id}))
                user.con.sendText(content({status:200,type:'statusChange'}))
            }
        }
        sync.run()
    },
    waiting(obj,con){

        if(!con.user_id){
            con.sendText(content({status:400,type:'login',message:'未登录'}))
            console.error('one user login error 7')
            return
        }

        /** 获取代驾订单id */
        let trip_id = obj.trip_id || 0
        let driver = data.DriverMap.get(con.user_id)

        if(!trip_id){

            con.sendText(content({status:400,type:'waitingDriver',trip_id:trip_id,message:'行程不存在'}))
            return;
        }

        let sync = new SYNC
        sync.add = function(){

            db.find('select * from c_trip where trip_id=? and driver_id=? and type IN (3)',[trip_id,con.user_id],function(trip){

                if(!trip){
                    con.sendText(content({status:400,type:'waitingDriver',trip_id:trip_id,message:'行程不存在'}))
                    return;
                }
                sync.trip = trip
                /** 订单状态 */
                if([30].indexOf(parseInt(trip.statuss))==-1 || trip.laying == 1){
                    con.sendText(content({status:400,type:'waitingDriver',trip_id:trip_id,message:'行程当前无法等候'}))
                    return;
                }
                sync.run()
            })
        }

        sync.add = function(){

            db.update('update c_trip set laying=1,start_lay_time=? where trip_id=?',[parseInt(Date.now() / 1000),trip_id],function(){
                con.sendText(content({status:200,type:'waitingDriver',trip_id:trip_id,message:'等候中'}))
                let user = data.UserMap.get(sync.trip.user_id+'')
                if(user){
                    user.con.sendText(content({status:200,type:'waiting',trip_id:trip_id,message:'等候中'}))
                    user.con.sendText(content({status:200,type:'statusChange'}))
                }
            })
        }

        sync.run()
    },
    endWaiting(obj,con){ /** 结束等待 */

        if(!con.user_id){
            con.sendText(content({status:400,type:'login',message:'未登录'}))
            console.error('one user login error 8')
            return
        }

        /** 获取代驾订单id */
        let trip_id = obj.trip_id || 0
        let driver = data.DriverMap.get(con.user_id)

        if(!trip_id){

            con.sendText(content({status:400,type:'endWaitingDriver',trip_id:trip_id,message:'行程不存在'}))
            return;
        }

        let sync = new SYNC
        sync.add = function(){

            db.find('select * from c_trip where trip_id=? and driver_id=? and type IN (3)',[trip_id,con.user_id],function(trip){

                if(!trip){
                    con.sendText(content({status:400,type:'endWaitingDriver',trip_id:trip_id,message:'行程不存在'}))
                    return;
                }
                sync.trip = trip
                /** 订单状态 */
                if([30].indexOf(parseInt(trip.statuss))==-1 || trip.laying == 0){
                    con.sendText(content({status:400,type:'endWaitingDriver',trip_id:trip_id,message:'无法结束等候'}))
                    return;
                }
                sync.run(trip)
            })
        }

        sync.add = function(trip){

            let during = parseInt(Date.now() / 1000) - parseInt(trip.start_lay_time) + trip.during
            let lay_fee = 0
            if(during > 600){
                lay_fee = Math.ceil((during-600)/60)
            }

            if(trip.type==1)db.update('update c_order_driving set lay_fee=? where id=?',[lay_fee,trip.id],function(){
                sync.run(trip,during)
            });
            else if(trip.type==2)db.update('update c_order_taxi set lay_fee=? where id=?',[lay_fee,trip.id],function(){
                sync.run(trip,during)
            });
            else if(trip.type==3)db.update('update c_order_way set lay_fee=? where id=?',[lay_fee,trip.id],function(){
                sync.run(trip,during)
            })

            
        }
        sync.add = function(trip,during){

            db.update('update c_trip set laying=0,during=? where trip_id=?',[during,trip_id],function(){
                con.sendText(content({status:200,type:'endWaitingDriver',trip_id:trip_id,message:'结束等候'}))
                let user = data.UserMap.get(sync.trip.user_id+'')
                if(user){
                    user.con.sendText(content({status:200,type:'endWaiting',trip_id:trip_id,message:'结束等候'}))
                    user.con.sendText(content({status:200,type:'statusChange'}))
                }

            })


        }

        sync.run()

    },
    confirmPrice(obj,con){ /** 确认费用 */

        if(!con.user_id){
            con.sendText(content({status:400,type:'login',message:'未登录'}))
            console.error('one user login error 7')
            return
        }

        /** 获取代驾订单id */
        let trip_id = obj.trip_id || 0
        let other = obj.other || '[]'

        let otherJSON
        try{
            otherJSON = JSON.parse(other)
        }catch(e){
            otherJSON = []
        }
        other = JSON.stringify(otherJSON)

        let driver = data.DriverMap.get(con.user_id)

        if(!trip_id){

            con.sendText(content({status:400,type:'confirmPriceDriver',trip_id:trip_id,message:'行程不存在'}))
            return;
        }

        let sync = new SYNC
        sync.add = function(){

            db.find('select * from c_trip where trip_id=? and driver_id=? and type IN (3)',[trip_id,con.user_id],function(trip){

                if(!trip){
                    con.sendText(content({status:400,type:'confirmPriceDriver',trip_id:trip_id,message:'行程不存在'}))
                    return;
                }
                sync.trip = trip
                /** 订单状态 */
                if([35].indexOf(parseInt(trip.statuss))==-1){
                    con.sendText(content({status:400,type:'confirmPriceDriver',trip_id:trip_id,message:'行程未结束'}))
                    return;
                }
                sync.run(trip)
            })
        }
        sync.add = function(trip){

            if(trip.type==3){
                db.find('select * from c_order_way where id=?',[trip.id],function(w){sync.run(w)})
            }
        }

        sync.add = function(re){
            if(!re){
                con.sendText(content({status:400,type:'confirmPriceDriver',trip_id:trip_id,message:'订单不存在'}))
                return;
            }

            /** 行程总价 */
            re.fee
            /** 等待费用 */
            re.lay_fee
            /** 优惠券 */
            re.coupon

            let total = parseFloat(re.fee) +parseFloat( re.lay_fee) - parseFloat(re.coupon)

            for(let i in otherJSON){
                if(otherJSON[i].price){
                    total += parseFloat( otherJSON[i].price )
                }
            }

            sync.total = total;

            sync.run()
        }

        sync.add = function(){

            db.update('update c_trip set statuss=45,other_fee=? where trip_id=?',[other,trip_id],function(w){sync.run()})

            
        }

        sync.add = function(){

            if(sync.trip.type==3){
                db.update('update c_order_way set statuss=45,total_fee=? where id=?',[sync.total,sync.trip.id],function(w){sync.run(w)})
            }
        }
        sync.add = function(){
            con.sendText(content({status:200,type:'confirmPriceDriver',trip_id:trip_id,message:'已确认计费'}))
            let user = data.UserMap.get(sync.trip.user_id+'')
            if(user){
                user.con.sendText(content({status:200,type:'confirmPrice',trip_id:trip_id,message:'已确认计费'}))
                user.con.sendText(content({status:200,type:'statusChange'}))
            }
        }
        sync.run()
    },
}


let z = function(obj,con){

    if(act[obj.type]){
        act[obj.type](obj,con);
    }


}

module.exports = z