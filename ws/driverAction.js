const post = require('./post')
const content = d => d instanceof Object ? JSON.stringify(d) : '{}'
let data = require('./data'),
db = require('./db'),
action = require('./action'),
UserInfo = function(){},
dis = function (lat1, lng1, lat2, lng2) {

    lat1 = parseFloat(lat1)
    lat2 = parseFloat(lat2)
    lng1 = parseFloat(lng1)
    lng2 = parseFloat(lng2)

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
                if(result.driver_id != con.driver_id){
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
                if(className == 'way'){
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
            con.sendText(content({status:200,type:'arriveStartPosition',id:id,trip_id:trip_id}))
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
    startDrivingV2(obj,con){ /** 司机开始服务 */
        /** 司机是否登录 */
        if(!con.driver_id){
            con.sendText(content({status:400,type:'login',message:'未登录'}))
            return;
        }
        /** 获取司机 */
        let driver = data.DriverMap.get(con.driver_id)
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
                if(className == 'way'){
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
            db.update('update c_trip set driver_id=?,statuss=30,laying=0,in_time=?,last_latitude=?,last_longitude=?,during=? where trip_id=?',[con.driver_id,parseInt(Date.now() / 1000),driver.latitude,driver.longitude,during,trip_id],function(){
            
                sync.run(trip)
            })
        
        }
        sync.add = function(trip){
            /** 设置司机状态为服务中 */
            driver.serving = 1;
            con.sendText(content({status:200,type:'startDriving',id:id}))
            /** 获取用户 */
            let user = data.UserMap.get(sync.result.user_id+'')
            if(user){
                user.con.sendText(content({status:200,type:'startDriving',id:id}))
                user.con.sendText(content({status:200,type:'statusChange'}))
            }
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
                if(className == 'way'){
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
                if(result.driver_id != con.driver_id){
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
        }
        sync.add = function(trip,prices,result){
            db.update('update c_trip set driver_id=?,statuss=35,laying=0,out_time=?,start_fee=? where trip_id=?',[con.driver_id,parseInt(Date.now() / 1000),prices.start,trip_id],function(){
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
            driver.serving = 0;
            con.sendText(content({status:200,type:'endDriving',id:id}))
            

            let g = function(r){
                driver.con.sendText(content({status:200,type:'fleshDrivingList','mode':'end',list:r}))
            };
            (driver.type_driving && driver.type_taxi) && action.driverGetOrders(driver.latitude,driver.longitude,g,driver.city_id);
            (driver.type_driving && !driver.type_taxi) && action.driverGetOrdersDriving(driver.latitude,driver.longitude,g,driver.city_id);
            (!driver.type_driving && driver.type_taxi) && action.driverGetOrdersTaxi(driver.latitude,driver.longitude,g,driver.city_id);
            

            /** 获取用户 */
            let user = data.UserMap.get(result.user_id+'')
            if(user){
                user.con.sendText(content({status:200,type:'endDriving',id:id}))
                user.con.sendText(content({status:200,type:'statusChange'}))
            }
        }
        sync.run()
    },
    logout(obj,con){
        if(!con.driver_id)return;

        db.delete('delete from c_driver_online where driver_id=?',[con.driver_id],function(){

            post('driver/ws_logout',{driver_id:con.driver_id},function(d){

                data.DriverMap.delete(con.driver_id)
                delete con.driver_id
                sendAdmin(`driver ${con.driver_id} logout`);
                con.sendText(content({status:200,type:'logout'}))
                
            })
        })

    },
    updPostion(obj,con){
        /** 司机是否登录 */
        if(!con.driver_id){
            con.sendText(content({status:400,type:'login',message:'未登录'}))
            sendAdmin('one driver login error 4')
            return;
        }
        let id = obj.id || 0
        let driver = data.DriverMap.get(con.driver_id)
        let latitude = driver.latitude = parseFloat(obj.latitude || 0)
        let longitude = driver.longitude = parseFloat(obj.longitude || 0)

        let sync = new SYNC;

        sync.add = function(){

            db.replace('update c_driver_online set latitude=?,longitude=? where driver_id=?',[latitude,longitude,con.driver_id],function(w){sync.run(w)})

        }

        sync.add = function(){

            sendAdmin(`driver ${con.driver_id} updated position ${latitude},${longitude}`)
            db.find('select * from c_trip where driver_id=? AND type IN (1,2) AND statuss IN (20,30)',[con.driver_id],function(re){
                
                if(re){
                    db.insert('insert into c_driver_serving_position set driver_id=?,trip_id=?,latitude=?,longitude=?,status=?',[driver.id,re.trip_id,latitude,longitude,re.statuss])

                    sendAdmin(`driver ${con.driver_id} updated serving_position`)

                    if((driver.serving && re.statuss == 30) || (re.statuss == 20 && re.meter))sync.run(re)
                }
                
            })
            
        }
        
        sync.add = function(d){

            if(!d)return;
            
            let di;
            if(!latitude || !latitude)di = 0;
            else di = dis(d.last_latitude,d.last_longitude,latitude,longitude);
            di += d.real_distance;
                        
            db.update('update c_trip set last_latitude=?,last_longitude=?,real_distance=? where driver_id=? AND type IN (1,2) AND statuss=30',[latitude,longitude,di,con.driver_id])

            sendAdmin([`driver ${con.driver_id} add distance ${di}`,d.last_latitude,d.last_longitude,latitude,longitude])
                    
        }

        sync.run()

        
    },
    waiting(obj,con){

        if(!con.driver_id){
            con.sendText(content({status:400,type:'login',message:'未登录'}))
            console.error('one driver login error 7')
            return
        }

        /** 获取代驾订单id */
        let trip_id = obj.trip_id || 0
        let driver = data.DriverMap.get(con.driver_id)

        if(!trip_id){

            con.sendText(content({status:400,type:'waiting',trip_id:trip_id,message:'行程不存在'}))
            return;
        }

        let sync = new SYNC
        sync.add = function(){

            db.find('select * from c_trip where trip_id=? and driver_id=? and type IN (1,2)',[trip_id,con.driver_id],function(trip){

                if(!trip){
                    con.sendText(content({status:400,type:'waiting',trip_id:trip_id,message:'行程不存在'}))
                    return;
                }
                sync.trip = trip
                /** 订单状态 */
                if([30].indexOf(parseInt(trip.statuss))==-1 || trip.laying == 1){
                    con.sendText(content({status:400,type:'waiting',trip_id:trip_id,message:'行程当前无法等候'}))
                    return;
                }
                sync.run()
            })
        }

        sync.add = function(){

            db.update('update c_trip set laying=1,start_lay_time=? where trip_id=?',[parseInt(Date.now() / 1000),trip_id],function(){
                con.sendText(content({status:200,type:'waiting',trip_id:trip_id,message:'等候中'}))
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

        if(!con.driver_id){
            con.sendText(content({status:400,type:'login',message:'未登录'}))
            console.error('one driver login error 8')
            return
        }

        /** 获取代驾订单id */
        let trip_id = obj.trip_id || 0
        let driver = data.DriverMap.get(con.driver_id)

        if(!trip_id){

            con.sendText(content({status:400,type:'endWaiting',trip_id:trip_id,message:'行程不存在'}))
            return;
        }

        let sync = new SYNC
        sync.add = function(){

            db.find('select * from c_trip where trip_id=? and driver_id=? and type IN (1,2)',[trip_id,con.driver_id],function(trip){

                if(!trip){
                    con.sendText(content({status:400,type:'endWaiting',trip_id:trip_id,message:'行程不存在'}))
                    return;
                }
                sync.trip = trip
                /** 订单状态 */
                if([30].indexOf(parseInt(trip.statuss))==-1 || trip.laying == 0){
                    con.sendText(content({status:400,type:'endWaiting',trip_id:trip_id,message:'无法结束等候'}))
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
                con.sendText(content({status:200,type:'endWaiting',trip_id:trip_id,message:'结束等候'}))
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

        if(!con.driver_id){
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

        let driver = data.DriverMap.get(con.driver_id)

        if(!trip_id){

            con.sendText(content({status:400,type:'confirmPrice',trip_id:trip_id,message:'行程不存在'}))
            return;
        }

        let sync = new SYNC
        sync.add = function(){

            db.find('select * from c_trip where trip_id=? and driver_id=? and type IN (1,2)',[trip_id,con.driver_id],function(trip){

                if(!trip){
                    con.sendText(content({status:400,type:'confirmPrice',trip_id:trip_id,message:'行程不存在'}))
                    return;
                }
                sync.trip = trip
                /** 订单状态 */
                if([35].indexOf(parseInt(trip.statuss))==-1){
                    con.sendText(content({status:400,type:'confirmPrice',trip_id:trip_id,message:'行程未结束'}))
                    return;
                }
                sync.run(trip)
            })
        }
        sync.add = function(trip){

            if(trip.type==1){
                db.find('select * from c_order_driving where id=?',[trip.id],function(w){sync.run(w)})
            }else if(trip.type==2){
                db.find('select * from c_order_taxi where id=?',[trip.id],function(w){sync.run(w)})
            }
        }

        sync.add = function(re){
            if(!re){
                con.sendText(content({status:400,type:'confirmPrice',trip_id:trip_id,message:'订单不存在'}))
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

            if(sync.trip.type==1){
                db.update('update c_order_driving set statuss=45,total_fee=? where id=?',[sync.total,sync.trip.id],function(w){sync.run(w)})
            }else if(sync.trip.type==2){
                db.update('update c_order_taxi set statuss=45,total_fee=? where id=?',[sync.total,sync.trip.id],function(w){sync.run(w)})
            }
        }
        sync.add = function(){
            con.sendText(content({status:200,type:'confirmPrice',trip_id:trip_id,message:'已确认计费'}))
            let user = data.UserMap.get(sync.trip.user_id+'')
            if(user){
                user.con.sendText(content({status:200,type:'confirmPrice',trip_id:trip_id,message:'已确认计费'}))
                user.con.sendText(content({status:200,type:'statusChange'}))
            }
        }
        sync.run()
    },
    login(obj,con){ /** 登录 */

        if(!obj.driver_token){
            con.sendText(content({status:400,type:'login',message:'token不合法'}))
            console.error('one driver login error 1')
            return
        }
        let sync = new SYNC;

        sync.add = function(){
            post('driver/getMyinfo2',{driver_token:obj.driver_token},function(d){
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
                driver.city_id = d.data.info.city_id
                data.DriverMap.set(d.data.info.id,driver)

                let latitude = driver.latitude = parseFloat(obj.latitude || 0)
                let longitude = driver.longitude = parseFloat(obj.longitude || 0)
                db.replace('replace into c_driver_online (driver_id,latitude,longitude) VALUES(?,?,?)',[d.data.info.id,latitude,longitude])
                sendAdmin(`driver ${d.data.info.id} linked`)
                sendAdmin(`driver ${con.driver_id} updated position ${latitude},${longitude}`);

                con.sendText(content({status:200,type:'login'}))

                db.find('select * from c_trip where driver_id=? AND type IN (1,2) and statuss in(20,25,30,35)',[driver.id],function(re){
                    if(re)driver.serving = 1;
                    if(driver.serving)return
                    let g = function(r){
                        driver.con.sendText(content({status:200,type:'fleshDrivingList','mode':'login',list:r}))
                    };

                    

                    (driver.type_driving && driver.type_taxi) && action.driverGetOrders(driver.latitude,driver.longitude,g,driver.city_id);
                    (driver.type_driving && !driver.type_taxi) && action.driverGetOrdersDriving(driver.latitude,driver.longitude,g,driver.city_id);
                    (!driver.type_driving && driver.type_taxi) && action.driverGetOrdersTaxi(driver.latitude,driver.longitude,g,driver.city_id);
                    
                })
            })

        }

        sync.run()
    
           
    },

    cancelAskForDriving(obj,con){/** 取消代驾订单 */
        /** 判断登录 */
        if(!con.driver_id){
            con.sendText(content({status:400,type:'login',message:'未登录'}))
            console.error('one driver login error 6')
            return
        }


        /** 获取代驾订单id */
        let id = obj.id || 0
        let trip_id = obj.trip_id || 0
        let types = obj.types || 1
        let reason = obj.reason || ''

        let className;

        if(!id && !trip_id){

            con.sendText(content({status:400,type:'cancelAskForDriving',id:id,trip_id:trip_id,message:'订单不存在'}))
            return;
        }

        let sync = new SYNC
        if(id){
            sync.add = function(){
                if(types == 3){
                    con.sendText(content({status:400,type:'cancelAskForDriving',id:id,trip_id:trip_id,message:'订单不存在'}))
                    return;
                }
                if(types == 1)db.find('select * from c_trip where id=? and driver_id=?',[id,types,con.driver_id],function(w){sync.run(w)})
            }
        }
        else if(trip_id){
            sync.add = function(){
                db.find('select * from c_trip where trip_id=? and driver_id=? and type IN (1,2)',[trip_id,con.driver_id],function(w){sync.run(w)})
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

            db.find('select * from c_order_'+className+' where id=? and driver_id=?',[id,con.driver_id],function(result){

                if(!result){
                    con.sendText(content({status:400,type:'cancelAskForDriving',id:id,trip_id:trip_id,message:'订单不存在'}))
                    return;
                }

                /** 订单状态 */
                if([20,25].indexOf(parseInt(result.statuss))==-1){
                    con.sendText(content({status:400,type:'cancelAskForDriving',id:id,trip_id:trip_id,message:'行程无法取消'}))
                    return;
                }

                let cancel_type;

                if(result.statuss == 20){
                    cancel_type = 4;
                }else if(result.statuss == 25){
                    cancel_type = 5;
                }
                
                db.update('update c_order_'+className+' set statuss=0 where id=?',[id],function(){
                    db.update('update c_trip set cancel_type=?,statuss=0,cancel_reason=? where trip_id=?',[cancel_type,reason,trip_id],function(){
                        sync.run(result)
                    })
                })
                
            })
        }

        sync.add = function(result){
            con.sendText(content({status:200,type:'cancelAskForDrivingDriver',id:id,trip_id:trip_id}))
            let user = data.UserMap.get(result.user_id+'')
            if(data.clock.get(result.user_id+''))clearTimeout(data.clock.get(result.user_id+''));
            if(user){
                user.con.sendText(content({status:200,type:'statusChange'}))
                user.con.sendText(content({status:200,type:'cancelAskForDrivingDriver',id:id,trip_id:trip_id}))
            }
            if(result.statuss >=20){
                let driver = data.DriverMap.get(result.driver_id+'')
                if(driver){
                    let g = function(r){
                        driver.con.sendText(content({status:200,type:'fleshDrivingList','mode':'order_cancel',list:r}));
                        driver.serving = 0;
                    };
                    (driver.type_driving && driver.type_taxi) && action.driverGetOrders(driver.latitude,driver.longitude,g,driver.city_id);
                    (driver.type_driving && !driver.type_taxi) && action.driverGetOrdersDriving(driver.latitude,driver.longitude,g,driver.city_id);
                    (!driver.type_driving && driver.type_taxi) && action.driverGetOrdersTaxi(driver.latitude,driver.longitude,g,driver.city_id);
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
                            (driver.type_driving && driver.type_taxi) && action.driverGetOrders(driver.latitude,driver.longitude,g,driver.city_id);
                            (driver.type_driving && !driver.type_taxi) && action.driverGetOrdersDriving(driver.latitude,driver.longitude,g,driver.city_id);
                            (!driver.type_driving && driver.type_taxi) && action.driverGetOrdersTaxi(driver.latitude,driver.longitude,g,driver.city_id);
                        }
                    }
                }
            }
        }

        sync.run()

        

    },
    orderDriving(obj,con){/** 接单 */

        /** 司机是否登录 */
        if(!con.driver_id){
            con.sendText(content({status:400,type:'login',message:'未登录'}))
            console.error('one driver login error 9')
            return
        }

        /** 获取代驾订单id */
        let id
        let type
        let className
        let result
        let trip_id = obj.trip_id || 0
        let driver = data.DriverMap.get(con.driver_id)

        /** 参数是否传了 */
        if(!trip_id){
            con.sendText(content({status:400,type:'orderDriving',trip_id:trip_id,message:'行程不存在'}))
            return;
        }

        let sync = new SYNC

        sync.add = function(){
            db.find('select * from c_driver where id=?',[con.driver_id],function(driver){

                if(!driver){
                    con.sendText(content({status:400,type:'orderDriving',trip_id:trip_id,message:'司机不存在'}))
                    return;
                }
                
                if(driver.money < 50 && driver.type_driving){
                    con.sendText(content({status:400,type:'orderDriving',trip_id:trip_id,message:'余额不足'}))
                    return;
                }
                sync.run()
            })
        }


        sync.add = function(){
            db.find('select * from c_trip where trip_id=? and type IN (1,2)',[trip_id,con.driver_id],function(trip){

                if(!trip){
                    con.sendText(content({status:400,type:'orderDriving',trip_id:trip_id,message:'行程不存在'}))
                    return;
                }
                sync.trip = trip
                id = trip.id
                type = trip.type
                /** 订单状态 */
                if([5,10].indexOf(parseInt(trip.statuss))==-1){
                    con.sendText(content({status:400,type:'orderDriving',trip_id:trip_id,message:'无法接单'}))
                    return;
                }
                className = trip.type == 1?'driving':trip.type == 2?'taxi':'way'
                if(className == 'way'){
                    con.sendText(content({status:400,type:'orderDriving',message:'行程不存在N'}))
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
                    con.sendText(content({status:400,type:'orderDriving',message:'订单不存在'}))
                    return;
                }
                /** 订单是否属于登录司机 */
                if(r.driver_id != 0){
                    con.sendText(content({status:400,type:'orderDriving',message:'无权限'}))
                    return;
                }
                result = r;
                sync.run()
            })
            
        }

        sync.add = function(){
            // sendAdmin(3)

            db.update('update c_order_'+className+' set driver_id=?,statuss=20,order_time=? where id=?',[con.driver_id,parseInt(Date.now() / 1000),id],function(){

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
            db.update('update c_trip set driver_id=?,statuss=20,duration=? where trip_id=?',[con.driver_id,st.duration||0,trip_id],function(){
                let driver_ids = result.driver_ids
                                        
                /** 设置司机状态为服务中 */
                driver.serving = 1;
                con.sendText(content({status:200,type:'orderDriving',id:id}))
                if(driver){

                    let g = function(r){
                        driver.con.sendText(content({status:200,type:'fleshDrivingList','mode':'order',list:r}))
                    };
                    (driver.type_driving && driver.type_taxi) && action.driverGetOrders(driver.latitude,driver.longitude,g,driver.city_id);
                    (driver.type_driving && !driver.type_taxi) && action.driverGetOrdersDriving(driver.latitude,driver.longitude,g,driver.city_id);
                    (!driver.type_driving && driver.type_taxi) && action.driverGetOrdersTaxi(driver.latitude,driver.longitude,g,driver.city_id);
                }
                let user = data.UserMap.get(result.user_id+'')
                if(data.clock.get(result.user_id+''))clearTimeout(data.clock.get(result.user_id+''));
                if(user){
                    user.con.sendText(content({status:200,type:'orderDriving',trip_id:trip_id,id:id}))
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
                            (driver.type_driving && driver.type_taxi) && action.driverGetOrders(driver.latitude,driver.longitude,g,driver.city_id);
                            (driver.type_driving && !driver.type_taxi) && action.driverGetOrdersDriving(driver.latitude,driver.longitude,g,driver.city_id);
                            (!driver.type_driving && driver.type_taxi) && action.driverGetOrdersTaxi(driver.latitude,driver.longitude,g,driver.city_id);
                        }
                    }
                }
            })
        }

        sync.run();

        
    },
    offline(obj,con){

        let trip_id = obj.trip_id || 0
        db.find('select * from c_trip where trip_id=?',[obj.trip_id],function(result){
            con.sendText(content({status:200,type:'offlineDriver'}))
            let user = data.UserMap.get(result.user_id+'')
            if(user){
                user.con.sendText(content({status:200,type:'offline'}))
                user.con.sendText(content({status:200,type:'statusChange'}))
            }
        })
    }
    


}
let z = function(obj,con){

    if(act[obj.type])act[obj.type](obj,con);

    



}

module.exports = z