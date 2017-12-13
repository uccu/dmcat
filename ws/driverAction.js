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
},SYNC = function(){
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
};

let act = {

    arriveStartPosition(obj,con){ /** 司机到达起点 */

        /** 司机是否登录 */
        if(!con.driver_id){
            con.sendText(content({status:400,type:'login',message:'未登录'}))
            return;
        }
        /** 获取司机 */
        let driver = data.DriverMap.get(con.driver_id)
        /** 获取订单ID */
        let id = obj.id
        if(!id){
            con.sendText(content({status:400,type:'arriveStartPosition',message:'订单不存在'}))
            return;
        }
        let sync = new SYNC;
        
        /** 是否有正在进行中的订单 */
        sync.add = function(){
            /** 查询订单 */
            db.find('select * from c_order_driving where id=?',[id],function(result){
            
                /** 订单是否存在 */
                if(!result){
                    con.sendText(content({status:400,type:'arriveStartPosition',message:'订单不存在'}))
                    return;
                }
                /** 订单是否属于登录司机 */
                if(result.driver_id != con.driver_id){
                    con.sendText(content({status:400,type:'arriveStartPosition',message:'无权限操作此订单'}))
                    return;
                }
                /** 订单是否是抢单状态 */
                if(result.statuss != 20){
                    con.sendText(content({status:400,type:'arriveStartPosition',message:'订单状态不正确'}))
                    return;
                }
                
                sync.run(result)
            })
        }
        sync.add = function(result){
            db.update('update c_trip set statuss=25,laying=1,start_lay_time=? where id=? AND type=1',[parseInt(Date.now() / 1000),id],function(){
                db.update('update c_order_driving set statuss=25 where id=?',[id],function(){
                
                    sync.run(result)
                })
            })
        }
        sync.add = function(result){
            
            /** 发送成功信息给司机 */
            con.sendText(content({status:200,type:'arriveStartPosition',id:id}))
            /** 发送成功信息给用户 */
            let user = data.UserMap.get(result.user_id+'')
            if(user)user.con.sendText(content({status:200,type:'arriveStartPosition',id:id}))
            sync.run(result)
        }
        sync.run()

    },
    startDrivingV2(obj,con){ /** 司机开始服务 */
        /** 司机是否登录 */
        if(!con.driver_id){
            con.sendText(content({status:400,type:'login',message:'未登录'}))
            return;
        }
        /** 获取司机 */
        let driver = data.DriverMap.get(con.driver_id)
        /** 获取订单ID */
        let id = obj.id
        if(!id){
            con.sendText(content({status:400,type:'startDriving',message:'订单id不存在'}))
            return;
        }
        let sync = new SYNC;
        
        
        sync.add = function(){
            db.find('select * from c_order_driving where id=?',[id],function(result){
                
                /** 订单是否存在 */
                if(!result){
                    con.sendText(content({status:400,type:'startDriving',message:'订单不存在'}))
                    return;
                }
                /** 订单是否属于登录司机 */
                if(result.driver_id != con.driver_id){
                    con.sendText(content({status:400,type:'startDriving',message:'无权限'}))
                    return;
                }
                /** 订单是否是带接客状态 */
                if(result.statuss != 25){
                    con.sendText(content({status:400,type:'startDriving',message:'未到达起点'}))
                    return;
                }
                sync.result = result;
                sync.run()
            })
            
        }
        
        sync.add = function(){
        
            db.find('select * from c_trip where id=? AND type=1',[id],function(w){
                if(!w){
                    con.sendText(content({status:400,type:'startDriving',message:'行程不存在'}))
                    return;
                }
                sync.run(w)
            })
        }
        sync.add = function(trip){
            let during = parseInt(Date.now() / 1000) - parseInt(trip.start_lay_time)
            let lay_fee = 0
            if(during > 600){
                lay_fee = Math.ceil((during-600)/60)
            }
        
            db.update('update c_order_driving set statuss=30,lay_fee=? where id=?',[lay_fee,id],function(){
                sync.run(trip,during)
            })
        
        
        }
        
        sync.add = function(trip,during){
            /** 更新行程 */
            db.update('update c_trip set driver_id=?,statuss=30,laying=0,in_time=?,last_latitude=?,last_longitude=?,during=? where id=? and type=1',[con.driver_id,parseInt(Date.now() / 1000),driver.latitude,driver.longitude,during,id],function(){
            
                sync.run(trip)
            })
        
        }
        sync.add = function(trip){
            /** 设置司机状态为服务中 */
            driver.serving = 1;
            con.sendText(content({status:200,type:'startDriving',id:id}))
            /** 获取用户 */
            let user = data.UserMap.get(sync.result.user_id+'')
            if(user)user.con.sendText(content({status:200,type:'startDriving',id:id}))
        
        }
        sync.run()
    },
    endDrivingV2(obj,con){ /** 结束服务 */

        /** 司机是否登录 */
        if(!con.driver_id){
            con.sendText(content({status:400,type:'login',message:'未登录'}))
            return;
        }
        /** 获取司机 */
        let driver = data.DriverMap.get(con.driver_id)
        /** 获取订单ID */
        let id = obj.id
        if(!id){
            con.sendText(content({status:400,type:'endDriving',message:'订单id不存在'}))
            return;
        }
        let sync = new SYNC;
        
        
        sync.add = function(){

            db.find('select * from c_order_driving where id=?',[id],function(result){
                /** 订单是否存在 */
                if(!result){
                    con.sendText(content({status:400,type:'endDriving',message:'订单不存在'}))
                    return;
                }
                /** 订单是否属于登录司机 */
                if(result.driver_id != con.driver_id){
                    con.sendText(content({status:400,type:'endDriving',message:'无权限'}))
                    return;
                }
                /** 订单是否是服务中状态 */
                if(result.statuss != 30){
                    con.sendText(content({status:400,type:'startDriving',message:'未开始服务'}))
                    return;
                }
                sync.run(result)
            })
        }
        sync.add = function(result){

            db.find('select * from c_trip where id=? and type=1',[id],function(w){
                if(!w){
                    con.sendText(content({status:400,type:'startDriving',message:'行程不存在'}))
                    return;
                }
                sync.run(w,result)
            })
        }
        sync.add = function(trip,result){

            db.update('update c_trip set driver_id=?,statuss=35,out_time=? where id=? and type=1',[con.driver_id,parseInt(Date.now() / 1000),id],function(){
                sync.run(trip,result)
            })
        }
        sync.add = function(trip,result){
            action.getDrivingPrice(result.city_id,trip.in_time,trip.real_distance,function(prices){
                sync.run(trip,prices,result)
            })
        }
        sync.add = function(trip,prices,result){

            let fee = prices.total;
            let total_fee = fee - result.coupon + result.lay_fee;
            db.update('update c_order_driving set statuss=35,distance=?,fee=?,total_fee=? where id=?',[trip.real_distance/1000,fee,total_fee,id],function(){
                sync.run(result)
            })
        }
        sync.add = function(result){

            /** 设置司机状态 */
            driver.serving = 0;
            con.sendText(content({status:200,type:'endDriving',id:id}))
            

            let g = function(r){
                driver.con.sendText(content({status:200,type:'fleshDrivingList','mode':'end',list:r}))
            };
            (driver.type_driving && driver.type_taxi) && action.driverGetOrders(driver.latitude,driver.longitude,g);
            (driver.type_driving && !driver.type_taxi) && action.driverGetOrdersDriving(driver.latitude,driver.longitude,g);
            (!driver.type_driving && driver.type_taxi) && action.driverGetOrdersTaxi(driver.latitude,driver.longitude,g);
            

            /** 获取用户 */
            let user = data.UserMap.get(result.user_id+'')
            if(user)user.con.sendText(content({status:200,type:'endDriving',id:id}))
        }
        sync.run()
    },
    logout(obj,con){
        if(!con.driver_id)return;

        db.delete('delete from c_driver_online where driver_id=?',[con.driver_id],function(){
            data.DriverMap.delete(con.driver_id)
            delete con.driver_id
            console.log(`driver ${con.driver_id} logout`)
            con.sendText(content({status:200,type:'logout'}))
        })

    },
    updPostion(obj,con){
        /** 司机是否登录 */
        if(!con.driver_id){
            con.sendText(content({status:400,type:'login',message:'未登录'}))
            console.error('one driver login error 4')
            return;
        }
        let id = obj.id || 0
        let driver = data.DriverMap.get(con.driver_id)
        let latitude = driver.latitude = parseFloat(obj.latitude || 0)
        let longitude = driver.longitude = parseFloat(obj.longitude || 0)

        let sync = new SYNC;

        sync.add = function(){

            db.replace('update c_driver_online set latitude=?,longitude=? where driver_id=?',[latitude,longitude,con.driver_id],sync.run)

        }

        sync.add = function(){

            console.log(`driver ${con.driver_id} updated position ${latitude},${longitude}`)
            if(driver.serving)db.find('select * from c_trip where driver_id=? AND type<3 AND statuss=30',[con.driver_id],sync.run)
        }
        
        sync.add = function(d){

            if(!d)return;
            
            let di;
            if(!di || !latitude || !latitude)di = 0;
            else di = dis(d.last_latitude,d.last_longitude,latitude,longitude);
            di += d.real_distance;
                        
            db.update('update c_trip set last_latitude=?,last_longitude=?,real_distance=? where driver_id=? AND type<3 AND status=30',[latitude,longitude,di,con.driver_id])
                    
        }

        sync.run()

        
    },
    waiting(obj,con){

        if(!con.driver_id){
            con.sendText(content({status:400,type:'login',message:'未登录'}))
            console.error('one user login error 7')
            return
        }

        /** 获取代驾订单id */
        let trip_id = obj.trip_id || 0
        let reason = obj.reason || ''
        let driver = data.DriverMap.get(con.driver_id)

        if(!trip_id){

            con.sendText(content({status:400,type:'waiting',id:id,message:'行程不存在'}))
            return;
        }

        let sync = new SYNC
        sync.add = function(){

            db.find('select * from c_trip where and trip_id=? and driver_id=? and type<3',[trip_id,con.driver_id],function(trip){

                if(!trip){
                    con.sendText(content({status:400,type:'waiting',id:id,message:'行程不存在'}))
                    return;
                }
                /** 订单状态 */
                if([30].indexOf(parseInt(result.statuss))==-1 || trip.laying == 1){
                    con.sendText(content({status:400,type:'waiting',id:id,message:'行程当前无法等候'}))
                    return;
                }
                sync.run()
            })
        }

        sync.add = function(){

            db.update('update c_trip set laying=1,start_lay_time=? where trip_id=?',[parseInt(Date.now() / 1000),trip_id],function(){
                con.sendText(content({status:200,type:'waiting',id:id,message:'等候中'}))

            })
        }

        sync.run()
    },
    endWaiting(obj,con){

        if(!con.driver_id){
            con.sendText(content({status:400,type:'login',message:'未登录'}))
            console.error('one user login error 7')
            return
        }

        /** 获取代驾订单id */
        let trip_id = obj.trip_id || 0
        let reason = obj.reason || ''
        let driver = data.DriverMap.get(con.driver_id)

        if(!trip_id){

            con.sendText(content({status:400,type:'endWaiting',id:id,message:'行程不存在'}))
            return;
        }

        let sync = new SYNC
        sync.add = function(){

            db.find('select * from c_trip where and trip_id=? and driver_id=? and type<3',[trip_id,con.driver_id],function(trip){

                if(!trip){
                    con.sendText(content({status:400,type:'endWaiting',id:id,message:'行程不存在'}))
                    return;
                }
                /** 订单状态 */
                if([30].indexOf(parseInt(result.statuss))==-1 || trip.laying == 0){
                    con.sendText(content({status:400,type:'endWaiting',id:id,message:'无法结束等候'}))
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
                con.sendText(content({status:200,type:'endWaiting',id:id,message:'等候中'}))

            })

            
        }

        sync.run()

    }
    


}
let z = function(obj,con){

    if(act[obj.type])act[obj.type](obj,con);

    else switch(obj.type){

        case 'login':

            post('driver/getMyinfo',{driver_token:obj.driver_token},function(d){
                if(!d)con.sendText(content({status:400,type:'login',message:'网络错误'}))
                if(d.code != 200){
                    
                    console.error('one driver error',d)
                    con.sendText(content({status:400,type:'login',message:d.message}))
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
                console.log(`driver ${d.data.info.id} linked`)
                console.log(`driver ${con.driver_id} updated position ${latitude},${longitude}`);

                db.find('select * from c_trip where driver_id=? AND type<3 and status in(2,3)',[driver.id],function(re){
                    if(re)driver.serving = 1;
                    if(driver.serving)return
                    let g = function(r){
                        driver.con.sendText(content({status:200,type:'fleshDrivingList','mode':'login',list:r}))
                    };
                    (driver.type_driving && driver.type_taxi) && action.driverGetOrders(driver.latitude,driver.longitude,g);
                    (driver.type_driving && !driver.type_taxi) && action.driverGetOrdersDriving(driver.latitude,driver.longitude,g);
                    (!driver.type_driving && driver.type_taxi) && action.driverGetOrdersTaxi(driver.latitude,driver.longitude,g);
                })

                
                con.sendText(content({status:200,type:'login'}))
                

            })

            break;
        case 'updPostion':
            
            break;
        case 'orderDriving':
            if(con.driver_id){
                let id = obj.id
                let driver = data.DriverMap.get(con.driver_id)

                db.find('select * from c_order_driving where driver_id=? and statuss in(20,25,30,35,40)',[con.driver_id],function(r){

                    if(r){
                        con.sendText(content({status:400,type:'orderDriving',id:id,'message':'不能重复接单'}))
                        return
                    }

                    db.find('select * from c_order_driving where id=?',[id],function(result){
                        if(result){
                            /** 更新订单 */

                            if(result.driver_id != '0'){
                                con.sendText(content({status:400,type:'orderDriving',id:id,'message':'该订单已接单'}))
                                return;
                            }

                            db.update('update c_order_driving set driver_id=?,statuss=20,order_time=? where id=?',[con.driver_id,parseInt(Date.now() / 1000),id],function(){
                                /** 更新行程 */

                                action.getDis(driver.latitude,driver.longitude,result.start_latitude,result.start_longitude,3,function(st){
                                    if(!st)st  = {}
                                    db.update('update c_trip set driver_id=?,statuss=20,duration=? where id=? and type=1',[con.driver_id,st.duration||0,id],function(){
                                        let driver_ids = result.driver_ids
                                        
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
                                        if(user){
                                            user.con.sendText(content({status:200,type:'orderDriving',id:id}))
                                            // post('user/push',{id:result.user_id,message:'有司机接了您的订单！',type:'order_order'});
                                        }
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

                                
                            })
                        }
                    })
                })
                
                
            }
            break;
        case 'orderTaxi':
            if(con.driver_id){
                let id = obj.id
                let driver = data.DriverMap.get(con.driver_id)
                db.find('select * from c_order_taxi where driver_id=? and status in(2,3)',[con.driver_id],function(r){

                    if(r){
                        con.sendText(content({status:400,type:'orderTaxi',id:id,'message':'不能重复接单'}))
                        return
                    }
                    db.find('select * from c_order_taxi where id=?',[id],function(result){
                        if(result){

                            if(result.driver_id != '0'){
                                con.sendText(content({status:400,type:'orderTaxi',id:id,'message':'该订单已接单'}))
                                return;
                            }
                            /** 更新订单 */
                            db.update('update c_order_taxi set driver_id=?,status=2,order_time=? where id=?',[con.driver_id,parseInt(Date.now() / 1000),id],function(){
                                /** 更新行程 */
                                action.getDis(driver.latitude,driver.longitude,result.start_latitude,result.start_longitude,1,function(st){
                                    if(!st)st  = {}
                                    db.update('update c_trip set driver_id=?,status=2,duration=? where id=? and type=2',[con.driver_id,st.duration||0,id],function(){
                                        let driver_ids = result.driver_ids
                                        
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
                                        if(user){
                                            user.con.sendText(content({status:200,type:'orderTaxi',id:id}))
                                            // post('user/push',{id:result.user_id,message:'有司机接了您的订单！',type:'order_order'});
                                        }
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

                    let gTime = result.create_time;
                    if(result.order_time){
                        gTime  =result.order_time;
                    }

                    let lay_fee = Math.floor((parseInt(Date.now() / 1000) - gTime) / 1800) * 20
                    if(lay_fee<0)lay_fee = 0;

                        /** 更新订单 */
                        db.update('update c_order_driving set status=3,lay_fee=? where id=?',[lay_fee,id],function(){

                            /** 更新行程 */
                            db.update('update c_trip set driver_id=?,status=3,in_time=? where id=? and type=1',[con.driver_id,parseInt(Date.now() / 1000),id],function(){

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
                            db.update('update c_trip set driver_id=?,status=3,in_time where id=? and type=2',[con.driver_id,parseInt(Date.now() / 1000),id],function(){

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
                        if(trip)db.update('update c_trip set driver_id=?,status=4,out_time=? where id=? and type=1',[con.driver_id,parseInt(Date.now() / 1000),id],function(){


                            action.getDrivingPrice(result.city_id,trip.in_time,trip.real_distance,function(prices){

                                let fee = prices.total;
                                let total_fee = fee - result.coupon + result.lay_fee;
                                db.update('update c_order_driving set status=4,distance=?,fee=?,total_fee=? where id=?',[trip.real_distance/1000,fee,total_fee,id],function(){

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
                            });

                            
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
                        
                        db.update('update c_trip set driver_id=?,status=4,out_time=? where id=? and type=2',[con.driver_id,parseInt(Date.now() / 1000),id],function(){
                            
                            action.getTaxiPrice(result.city_id,trip.in_time,trip.real_distance,function(price){

                                let fee = price;
                                let total_fee = fee - result.coupon + result.lay_fee;
                            
                                /** 更新订单 */
                                db.update('update c_order_taxi set status=4,distance=?,fee=?,total_fee=? where id=?',[trip.real_distance/1000,fee,total_fee,id],function(){

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
                })
            }
            break;

        default:
            break;
    }



}

module.exports = z