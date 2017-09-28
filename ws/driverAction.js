const post = require('./post')
const content = d => d instanceof Object ? JSON.stringify(d) : '{}'
let data = require('./data'),
db = require('./db'),
action = require('./action'),
UserInfo = function(){},
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
                    delete user.con.driver_id
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

                db.find('select * from c_trip where driver_id=? and status in(2,3,4)',[driver.id],function(re){
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
            }
            break;
        case 'orderDriving':
            if(con.driver_id){
                let id = obj.id
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
            }
            break;
        case 'orderTaxi':
            break;
        default:
            break;
    }



}

module.exports = z