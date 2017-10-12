const post = require('./post')
const content = d => d instanceof Object ? JSON.stringify(d) : '{}'
let data = require('./data'),
db = require('./db'),
action = require('./action'),
UserInfo = function(){},getDrivers = function(){

},
z = function(obj,con){

    switch(obj.type){

        case 'login':

            post('user/getMyinfo',{user_token:obj.user_token},function(d){

                if(!d)con.sendText(content({status:400,type:'login',message:'网络错误'}))
                if(d.code != 200){
                    console.error('one user error',d)
                    con.sendText(content({status:400,type:'login',message:d.message}))
                    return
                }
                let user = data.UserMap.get(d.data.info.id)
                if(user){
                    if(user.con === con)return
                    delete user.con.user_id
                    user.con.close();
                }
                con.user_id = d.data.info.id
                user = new UserInfo
                user.con = con
                user.id = d.data.info.id
                data.UserMap.set(d.data.info.id,user)

                let latitude = obj.latitude
                let longitude = obj.longitude
                if(latitude && longitude)db.replace('replace into c_user_online (user_id,latitude,longitude) VALUES(?,?,?)',[d.data.info.id,latitude,longitude])

                else db.replace('replace into c_user_online (user_id) VALUES(?)',[d.data.info.id])
                
                console.log(`user ${d.data.info.id} linked`)
                con.sendText(content({status:200,type:'login'}))
                

            })

            break;
        case 'updPostion':
            if(con.user_id){
                let latitude = obj.latitude
                let longitude = obj.longitude
                db.replace('update c_user_online set latitude=?,longitude=? where user_id=?',[latitude,longitude,con.user_id])
                console.log(`user ${con.user_id} updated position`)
            }
            break;
        case 'askForDriving':
            if(con.user_id){
                db.find('select * from c_trip where user_id=? and status in (1,2,3,4)',[con.user_id],function(result){
                    /** 判断是否有订单正在执行中 */
                    if(result){
                        con.sendText(content({status:400,type:'askForDriving',message:'不能重复下单'}))
                        return
                    }

                    let start_latitude = parseFloat(obj.start_latitude || 0)
                    let start_longitude = parseFloat(obj.start_longitude || 0)
                    let end_latitude = parseFloat(obj.end_latitude || 0)
                    let end_longitude = parseFloat(obj.end_longitude || 0)
                    
                    let start_name = obj.start_name || ''
                    let end_name = obj.end_name || ''
                    let create_time = parseInt(Date.now() / 1000)
                    let distance = parseInt(obj.distance || 0)
                    let start_time = parseInt(obj.start_time || 0)
                    let estimated_price = parseFloat(obj.estimated_price || 0)
                    let phone = obj.phone || ''
                    let name = obj.name || ''
                    let city_id = parseInt(obj.city_id || 0)

                    let latitudeRange = [start_latitude - 0.1,start_latitude + 0.1]
                    let longitudeRange = [start_longitude - 0.1,start_longitude + 0.1]

                    let id = 0;

                    db.get('select driver_id from c_driver_online where latitude between ? and ? and longitude between ? and ?',[latitudeRange[0],latitudeRange[1],longitudeRange[0],longitudeRange[1]],function(ids){

                        for(let i in ids){
                            ids[i] = ids[i].driver_id
                        }
                        db.insert('insert into c_order_driving set start_latitude=?,start_longitude=?,end_latitude=?,end_longitude=?,start_name=?,end_name=?,create_time=?,status=1,user_id=?,distance=?,estimated_price=?,start_time=?,phone=?,name=?,city_id=?',[start_latitude,start_longitude,end_latitude,end_longitude,start_name,end_name,create_time,con.user_id,distance,estimated_price,start_time,phone,name,city_id],function(e){
                            /** 创建订单 */
                            id = e
                            obj.id = id

                            /** 创建行程 */
                            db.insert('insert into c_trip set status=1,start_latitude=?,start_longitude=?,end_latitude=?,end_longitude=?,start_name=?,end_name=?,type=1,id=?,user_id=?,create_time=?,distance=?,estimated_price=?',[start_latitude,start_longitude,end_latitude,end_longitude,start_name,end_name,id,con.user_id,create_time,distance,estimated_price])

                            /** 发送成功信息 */
                            con.sendText(content({status:200,type:'askForDriving',info:obj}))
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
                            
                            db.update('update c_order_driving set driver_ids=? where id=?',[drivers.join(','),id])
                        })
                    })
                    console.log(`user ${con.user_id} create an order`)
                })


                
            }
            break;
        case 'cancelAskForDriving':
            if(con.user_id){

                let id = obj.id || 0
                db.find('select * from c_order_driving where id=? and user_id=?',[id,con.user_id],function(result){
                    if(result){
                        if([1,2].indexOf(parseInt(result.status))!==-1){
                            db.update('update c_order_driving set status=0 where id=?',[id],function(){
                                con.sendText(content({status:200,type:'cancelAskForDriving',id:id}))

                                db.update('update c_trip set status=0 where id=? and type=1',[id],function(){
                                    if(result.status == 2){
                                        let driver = data.DriverMap.get(result.driver_id+'')
                                        if(driver){
                                            let g = function(r){
                                                driver.con.sendText(content({status:200,type:'fleshDrivingList','mode':'cancel',list:r}));
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
                                })
                            })
                            
                        }
                    }
                })
            }
            break;
        case 'callTaxi':
            /** 用户是否登录 */
            if(!con.user_id)break;

            /** 查询进行中的订单 */
            db.find('select * from c_trip where user_id=? and status in (1,2,3,4)',[con.user_id],function(result){

                /** 判断是否有订单正在执行中 */
                if(result){
                    con.sendText(content({status:400,type:'callTaxi',message:'不能重复下单'}))
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
                let meter           = obj.meter == 1 ? 1:0

                let latitudeRange = [start_latitude - 0.1,start_latitude + 0.1]
                let longitudeRange = [start_longitude - 0.1,start_longitude + 0.1]

                let id = 0;

                /** 获取订单起点附近的司机 */
                db.get('select driver_id from c_driver_online where latitude between ? and ? and longitude between ? and ?',[latitudeRange[0],latitudeRange[1],longitudeRange[0],longitudeRange[1]],function(ids){

                    /** ids赋值为司机ID的数组 */
                    for(let i in ids){
                        ids[i] = ids[i].driver_id
                    }

                    /** 创建订单 */
                    db.insert('insert into c_order_taxi set start_latitude=?,start_longitude=?,end_latitude=?,end_longitude=?,start_name=?,end_name=?,create_time=?,status=1,user_id=?,distance=?,estimated_price=?,start_time=?,phone=?,name=?,city_id=?,meter=?',[start_latitude,start_longitude,end_latitude,end_longitude,start_name,end_name,create_time,con.user_id,distance,estimated_price,start_time,phone,name,city_id,meter],function(id){

                        obj.id = id

                        /** 创建行程 */
                        db.insert('insert into c_trip set status=1,start_latitude=?,start_longitude=?,end_latitude=?,end_longitude=?,start_name=?,end_name=?,type=2,id=?,user_id=?,create_time=?,distance=?,estimated_price=?,meter=?',[start_latitude,start_longitude,end_latitude,end_longitude,start_name,end_name,id,con.user_id,create_time,distance,estimated_price,meter])

                        /** 发送成功信息 */
                        con.sendText(content({status:200,type:'callTaxi',info:obj}))
                        console.log(`user ${con.user_id} create an order`)

                        let drivers = []
                        for(let k in ids){
                            drivers.push(ids[k])
                            let driver = data.DriverMap.get(ids[k]+'')

                            if(driver){
                                if(driver.serving)continue
                                let g = function(r){
                                    driver.con.sendText(content({status:200,type:'fleshTaxiList','mode':'create',list:r}))
                                };
                                (driver.type_driving && driver.type_taxi) && action.driverGetOrders(driver.latitude,driver.longitude,g);
                                (!driver.type_driving && driver.type_taxi) && action.driverGetOrdersTaxi(driver.latitude,driver.longitude,g);
                            }
                        }
                        /** 存储附近的司机的ID */
                        db.update('update c_order_taxi set driver_ids=? where id=?',[drivers.join(','),id])
                    })
                })
            })
            break;
        case 'cancelCallTaxi':
            if(con.user_id){

                let id = obj.id || 0
                /** 查询订单 */
                db.find('select * from c_order_taxi where id=? and user_id=?',[id,con.user_id],function(result){

                    /** 订单不存在返回 */
                    if(!result)return;
                    
                    /** 订单状态只有在12时候可以取消 */
                    if([1,2].indexOf(parseInt(result.status))==-1)return;
                    
                    /** 更新订单状态为取消 */
                    db.update('update c_order_taxi set status=0 where id=?',[id],function(){
                        
                        /** 发送成功信息 */
                        con.sendText(content({status:200,type:'cancelCallTaxi',id:id}))

                        /** 更新行程表 */
                        db.update('update c_trip set status=0 where id=? and type=2',[id],function(){

                            /** 状态为待接客时候动作 */
                            if(result.status == 2)
                            {
                                let driver = data.DriverMap.get(result.driver_id+'')
                                if(!driver)return;

                                let g = function(r){
                                    driver.con.sendText(content({status:200,type:'fleshDrivingList','mode':'cancel',list:r}));
                                    driver.serving = 0;
                                };
                                (driver.type_driving && driver.type_taxi) && action.driverGetOrders(driver.latitude,driver.longitude,g);
                                (!driver.type_driving && driver.type_taxi) && action.driverGetOrdersTaxi(driver.latitude,driver.longitude,g);

                            }else /** 状态为抢单时候动作 */
                            {
                                
                                let driver_ids = result.driver_ids
                                if(!driver_ids)return;

                                driver_ids = driver_ids.split(',')
                                for(let k in driver_ids){
                                    let driver = data.DriverMap.get(driver_ids[k]+'')
                                    if(!driver)continue;
                                    if(driver.serving)continue
                                    let g = function(r){
                                        driver.con.sendText(content({status:200,type:'fleshDrivingList','mode':'cancel',list:r}))
                                    };
                                    (driver.type_driving && driver.type_taxi) && action.driverGetOrders(driver.latitude,driver.longitude,g);
                                    (!driver.type_driving && driver.type_taxi) && action.driverGetOrdersTaxi(driver.latitude,driver.longitude,g);

                                }
                            }
                            
                        })
                    })
                })
            }
            break;
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

                let latitudeRange = [start_latitude - 0.1,start_latitude + 0.1]
                let longitudeRange = [start_longitude - 0.1,start_longitude + 0.1]



                /** 创建订单 */
                db.insert('insert into c_order_way set start_latitude=?,start_longitude=?,end_latitude=?,end_longitude=?,start_name=?,end_name=?,create_time=?,status=1,user_id=?,distance=?,estimated_price=?,start_time=?,phone=?,name=?,city_id=?',[start_latitude,start_longitude,end_latitude,end_longitude,start_name,end_name,create_time,con.user_id,distance,estimated_price,start_time,phone,name,city_id],function(id){

                    obj.id = id

                    /** 创建行程 */
                    db.insert('insert into c_trip set status=1,start_latitude=?,start_longitude=?,end_latitude=?,end_longitude=?,start_name=?,end_name=?,type=3,id=?,user_id=?,create_time=?,distance=?,estimated_price=?',[start_latitude,start_longitude,end_latitude,end_longitude,start_name,end_name,id,con.user_id,create_time,distance,estimated_price])

                    /** 发送成功信息 */
                    con.sendText(content({status:200,type:'callTaxi',info:obj}))
                    console.log(`user ${con.user_id} create an order`)

                })
            })
            break;
        case 'cancelCallWay':
            if(con.user_id){

                let id = obj.id || 0
                /** 查询订单 */
                db.find('select * from c_order_way where id=? and user_id=?',[id,con.user_id],function(result){

                    /** 订单不存在返回 */
                    if(!result)return;
                    
                    /** 订单状态只有在12时候可以取消 */
                    if([1,2].indexOf(parseInt(result.status))==-1)return;
                    
                    /** 更新订单状态为取消 */
                    db.update('update c_order_way set status=0 where id=?',[id],function(){
                        
                        /** 发送成功信息 */
                        con.sendText(content({status:200,type:'cancelCallWay',id:id}))

                        /** 更新行程表 */
                        db.update('update c_trip set status=0 where id=? and type=2',[id])
                    })
                })
            }
            break;
        default:
            break;
    }



}

module.exports = z