const post = require('./post')
const content = d => d instanceof Object ? JSON.stringify(d) : '{}'
let data = require('./data'),
db = require('./db'),
action = require('./action'),
UserInfo = function(){},
dis = function (lat1, lng1, lat2, lng2) {
    var radLat1 = lat1 * Math.PI / 180.0;
    var radLat2 = lat2 * Math.PI / 180.0;
    var a = radLat1 - radLat2;
    var b = lng1 * Math.PI / 180.0 - lng2 * Math.PI / 180.0;
    var s = 2 * Math.asin(Math.sqrt(Math.pow(Math.sin(a / 2), 2) + Math.cos(radLat1) * Math.cos(radLat2) * Math.pow(Math.sin(b / 2), 2)));
    s = s * 6378.137;
    s = Math.round(s * 10000) / 10;
    return s
};
z = function(obj,con){

    switch(obj.type){

        case 'login':

            post('driver/getMyinfo',{driver_token:obj.driver_token},function(d){

                if(d.code != 200){

                    console.error('one driver error',d)
                    return
                }
                let driver = data.DriverMap.get(d.data.info.id)
                if(driver){
                    if(driver.con === con)return
                    delete driver.con.driver_id
                    driver.con.close();
                }
                con.driver_id = d.data.info.id
                driver = new UserInfo
                driver.con = con
                driver.id = d.data.info.id
                driver.type_driving = d.data.info.type_driving == 1?1:0
                driver.type_taxi = d.data.info.type_taxi == 1?1:0
                data.DriverMap.set(d.data.info.id,driver)

                let latitude = driver.latitude = parseFloat(obj.latitude || 0)
                let longitude = driver.longitude = parseFloat(obj.longitude || 0)
                db.replace('replace into c_driver_online (driver_id,latitude,longitude) VALUES(?,?,?)',[d.data.info.id,latitude,longitude])

                db.find('select * from c_trip where driver_id=? and status in(2,3)',[driver.id],function(re){
                    if(re)driver.serving = 1;
                    if(driver.serving)return
                    let g = function(r){
                        driver.con.sendText(content({status:200,type:'fleshDrivingList','mode':'login',list:r}))
                    };
                    (driver.type_driving && driver.type_taxi) && action.driverGetOrders(driver.latitude,driver.longitude,g);
                    (driver.type_driving && !driver.type_taxi) && action.driverGetOrdersDriving(driver.latitude,driver.longitude,g);
                    (!driver.type_driving && driver.type_taxi) && action.driverGetOrdersTaxi(driver.latitude,driver.longitude,g);
                })

                console.log(`driver ${d.data.info.id} linked`)
                con.sendText(content({status:200,type:'login'}))
                

            })

            break;
        case 'updPostion':
            if(con.driver_id){
                let id = obj.id
                let driver = data.DriverMap.get(con.driver_id)
                let latitude = driver.latitude = parseFloat(obj.latitude || 0)
                let longitude = driver.longitude = parseFloat(obj.longitude || 0)
                db.replace('update c_driver_online set latitude=?,longitude=? where driver_id=?',[latitude,longitude,con.driver_id])
                console.log(`driver ${con.driver_id} updated position`)

                if(driver.serving)db.find('select * from c_trip where driver_id=? AND type<3 AND status=3',[con.driver_id],
                function(d){
                    if(d.last_longitude == '0'){

                        db.update('update c_trip set last_latitude=?,last_longitude=? where driver_id=? AND type<3 AND status=3',[latitude,longitude,con.driver_id])

                    }else{
                        let di = dis(d.last_latitude,d.last_longitude,latitude,longitude)
                        di += d.real_distance
                        db.update('update c_trip set last_latitude=?,last_longitude=?,real_distance=? where driver_id=? AND type<3 AND status=3',[latitude,longitude,di,con.driver_id])
                        console.log('Move distance: '+ di)
                    }
                })


            }
            break;
        case 'orderDriving':
            if(con.driver_id){
                let id = obj.id

                db.find('select * from c_order_driving where driver_id=? and status in(2,3)',[con.driver_id],function(r){

                    if(r){
                        con.sendText(content({status:400,type:'orderDriving',id:id,'message':'不能重复接单'}))
                        return
                    }

                    db.find('select * from c_order_driving where id=?',[id],function(result){
                        if(result){
                            /** 更新订单 */
                            db.update('update c_order_driving set driver_id=?,status=2 where id=?',[con.driver_id,id],function(){
                                /** 更新行程 */
                                db.update('update c_trip set driver_id=?,status=2 where id=? and type=1',[con.driver_id,id],function(){
                                    let driver_ids = result.driver_ids
                                    let driver = data.DriverMap.get(con.driver_id)
                                    /** 设置司机状态为服务中 */
                                    driver.serving = 1;
                                    con.sendText(content({status:200,type:'orderDriving',id:id}))
                                    if(driver){

                                        let g = function(r){
                                            driver.con.sendText(content({status:200,type:'fleshDrivingList','mode':'order',list:r}))
                                        };
                                        (driver.type_driving && driver.type_taxi) && action.driverGetOrders(driver.latitude,driver.longitude,g);
                                        (driver.type_driving && !driver.type_taxi) && action.driverGetOrdersDriving(driver.latitude,driver.longitude,g);
                                        (!driver.type_driving && driver.type_taxi) && action.driverGetOrdersTaxi(driver.latitude,driver.longitude,g);
                                    }
                                    let user = data.UserMap.get(result.user_id+'')
                                    if(user)user.con.sendText(content({status:200,type:'orderDriving',id:id}))
                                    if(driver_ids){
                                        driver_ids = driver_ids.split(',')
                                        for(let k in driver_ids){
                                            let driver = data.DriverMap.get(driver_ids[k]+'')
                                            
                                            if(driver){
                                                if(driver.serving)continue
                                                let g = function(r){
                                                    driver.con.sendText(content({status:200,type:'fleshDrivingList','mode':'order',list:r}))
                                                };
                                                (driver.type_driving && driver.type_taxi) && action.driverGetOrders(driver.latitude,driver.longitude,g);
                                                (driver.type_driving && !driver.type_taxi) && action.driverGetOrdersDriving(driver.latitude,driver.longitude,g);
                                                (!driver.type_driving && driver.type_taxi) && action.driverGetOrdersTaxi(driver.latitude,driver.longitude,g);
                                            }
                                        }
                                    }
                                })
                            })
                        }
                    })
                })
                
                
            }
            break;
        case 'orderTaxi':
            if(con.driver_id){
                let id = obj.id

                db.find('select * from c_order_taxi where driver_id=? and status in(2,3)',[con.driver_id],function(r){

                    if(r){
                        con.sendText(content({status:400,type:'orderTaxi',id:id,'message':'不能重复接单'}))
                        return
                    }
                    db.find('select * from c_order_taxi where id=?',[id],function(result){
                        if(result){
                            /** 更新订单 */
                            db.update('update c_order_taxi set driver_id=?,status=2 where id=?',[con.driver_id,id],function(){
                                /** 更新行程 */
                                db.update('update c_trip set driver_id=?,status=2 where id=? and type=2',[con.driver_id,id],function(){
                                    let driver_ids = result.driver_ids
                                    let driver = data.DriverMap.get(con.driver_id)
                                    /** 设置司机状态为服务中 */
                                    driver.serving = 1;
                                    con.sendText(content({status:200,type:'orderTaxi',id:id}))
                                    if(driver){

                                        let g = function(r){
                                            driver.con.sendText(content({status:200,type:'fleshDrivingList','mode':'order',list:r}))
                                        };
                                        (driver.type_driving && driver.type_taxi) && action.driverGetOrders(driver.latitude,driver.longitude,g);
                                        (driver.type_driving && !driver.type_taxi) && action.driverGetOrdersDriving(driver.latitude,driver.longitude,g);
                                        (!driver.type_driving && driver.type_taxi) && action.driverGetOrdersTaxi(driver.latitude,driver.longitude,g);
                                    }
                                    let user = data.UserMap.get(result.user_id+'')
                                    if(user)user.con.sendText(content({status:200,type:'orderTaxi',id:id}))
                                    if(driver_ids){
                                        driver_ids = driver_ids.split(',')
                                        for(let k in driver_ids){
                                            let driver = data.DriverMap.get(driver_ids[k]+'')
                                            
                                            if(driver){
                                                if(driver.serving)continue
                                                let g = function(r){
                                                    driver.con.sendText(content({status:200,type:'fleshDrivingList','mode':'order',list:r}))
                                                };
                                                (driver.type_driving && driver.type_taxi) && action.driverGetOrders(driver.latitude,driver.longitude,g);
                                                (driver.type_driving && !driver.type_taxi) && action.driverGetOrdersDriving(driver.latitude,driver.longitude,g);
                                                (!driver.type_driving && driver.type_taxi) && action.driverGetOrdersTaxi(driver.latitude,driver.longitude,g);
                                            }
                                        }
                                    }
                                })
                            })
                        }
                    })
                })
            }
            break;
        case 'addDistanceDriving':
            break;
        case 'addDistanceTaxi':
            break;
        case 'startDriving':
            /** 司机是否登录 */
            if(con.driver_id){

                /** 获取订单ID */
                let id = obj.id

                /** 查询订单 */
                db.find('select * from c_order_driving where id=?',[id],function(result){

                    /** 订单是否存在 */
                    if(!result)return;

                    /** 订单是否属于登录司机 */
                    if(result.driver_id != con.driver_id)return;

                    /** 订单是否是带接客状态 */
                    if(result.status != 2)return;

                        /** 更新订单 */
                        db.update('update c_order_driving set status=3 where id=?',[id],function(){

                            /** 更新行程 */
                            db.update('update c_trip set driver_id=?,status=3 where id=? and type=1',[con.driver_id,id],function(){

                                /** 获取司机 */
                                let driver = data.DriverMap.get(con.driver_id)
                                /** 设置司机状态为服务中 */
                                driver.serving = 1;
                                con.sendText(content({status:200,type:'startDriving',id:id}))

                                /** 获取用户 */
                                let user = data.UserMap.get(result.user_id+'')
                                if(user)user.con.sendText(content({status:200,type:'startDriving',id:id}))
                                
                            })
                        })
                    
                })
            }
            break;
        case 'startTaxi':
            /** 司机是否登录 */
            if(con.driver_id){

                /** 获取订单ID */
                let id = obj.id

                /** 查询订单 */
                db.find('select * from c_order_taxi where id=?',[id],function(result){

                    /** 订单是否存在 */
                    if(!result)return;

                    /** 订单是否属于登录司机 */
                    if(result.driver_id != con.driver_id)return;

                    /** 订单是否是带接客状态 */
                    if(result.status != 2)return;

                        /** 更新订单 */
                        db.update('update c_order_taxi set status=3 where id=?',[id],function(){

                            /** 更新行程 */
                            db.update('update c_trip set driver_id=?,status=3 where id=? and type=2',[con.driver_id,id],function(){

                                /** 获取司机 */
                                let driver = data.DriverMap.get(con.driver_id)
                                /** 设置司机状态为服务中 */
                                driver.serving = 1;
                                con.sendText(content({status:200,type:'startTaxi',id:id}))

                                /** 获取用户 */
                                let user = data.UserMap.get(result.user_id+'')
                                if(user)user.con.sendText(content({status:200,type:'startTaxi',id:id}))
                                
                            })
                        })
                    
                })
            }
            break;
        case 'endDriving':
        
            /** 司机是否登录 */
            if(con.driver_id){

                /** 获取订单ID */
                let id = obj.id

                /** 查询订单 */
                db.find('select * from c_order_driving where id=?',[id],function(result){

                    /** 订单是否存在 */
                    if(!result)return;

                    /** 订单是否属于登录司机 */
                    if(result.driver_id != con.driver_id)return;

                    /** 订单是否是带接客状态 */
                    if(result.status != 3)return;

                    db.find('select * from c_trip where id=? and type=1',[id],function(trip){
                        /** 更新订单 */
                        if(trip)db.update('update c_trip set driver_id=?,status=4 where id=? and type=1',[con.driver_id,id],function(){

                            db.update('update c_order_driving set status=4,distance=? where id=?',[trip.real_distance,id],function(){

                            /** 更新行程 */
                            

                                /** 获取司机 */
                                let driver = data.DriverMap.get(con.driver_id)
                                /** 设置司机状态 */
                                driver.serving = 0;
                                con.sendText(content({status:200,type:'endDriving',id:id}))
                                if(driver){

                                        let g = function(r){
                                            driver.con.sendText(content({status:200,type:'fleshDrivingList','mode':'end',list:r}))
                                        };
                                    (driver.type_driving && driver.type_taxi) && action.driverGetOrders(driver.latitude,driver.longitude,g);
                                    (driver.type_driving && !driver.type_taxi) && action.driverGetOrdersDriving(driver.latitude,driver.longitude,g);
                                    (!driver.type_driving && driver.type_taxi) && action.driverGetOrdersTaxi(driver.latitude,driver.longitude,g);
                                }

                                /** 获取用户 */
                                let user = data.UserMap.get(result.user_id+'')
                                if(user)user.con.sendText(content({status:200,type:'endDriving',id:id}))
                                
                            })
                        })
                    })
                    
                })
            }
            break;
        case 'endTaxi':
            /** 司机是否登录 */
            if(con.driver_id){

                /** 获取订单ID */
                let id = obj.id

                /** 查询订单 */
                db.find('select * from c_order_taxi where id=?',[id],function(result){

                    /** 订单是否存在 */
                    if(!result)return;

                    /** 订单是否属于登录司机 */
                    if(result.driver_id != con.driver_id)return;

                    /** 订单是否是带接客状态 */
                    if(result.status != 3)return;

                    db.find('select * from c_trip where id=? and type=2',[id],function(trip){
                        
                        db.update('update c_trip set driver_id=?,status=4 where id=? and type=2',[con.driver_id,id],function(){
                        /** 更新订单 */
                            db.update('update c_order_taxi set status=4,distance=? where id=?',[trip.real_distance,id],function(){

                            /** 更新行程 */
                            

                                /** 获取司机 */
                                let driver = data.DriverMap.get(con.driver_id)
                                /** 设置司机状态 */
                                driver.serving = 0;
                                con.sendText(content({status:200,type:'endTaxi',id:id}))
                                if(driver){

                                    let g = function(r){
                                        driver.con.sendText(content({status:200,type:'fleshDrivingList','mode':'end',list:r}))
                                    };
                                    (driver.type_driving && driver.type_taxi) && action.driverGetOrders(driver.latitude,driver.longitude,g);
                                    (driver.type_driving && !driver.type_taxi) && action.driverGetOrdersDriving(driver.latitude,driver.longitude,g);
                                    (!driver.type_driving && driver.type_taxi) && action.driverGetOrdersTaxi(driver.latitude,driver.longitude,g);
                                }
                                /** 获取用户 */
                                let user = data.UserMap.get(result.user_id+'')
                                if(user)user.con.sendText(content({status:200,type:'endTaxi',id:id}))
                                
                            })
                        })
                    })
                })
            }
            break;
        default:
            break;
    }



}

module.exports = z