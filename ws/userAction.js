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

                db.find('select * from c_order_driving where user_id=? and status in (1,2,3,4)',[con.user_id],function(result){
                    /** 判断是否有订单正在执行中 */
                    if(result){
                        con.sendText(content({status:400,type:'askForDriving',message:'不能重复下单'}))
                        return
                    }

                    let start_latitude = obj.start_latitude || 0
                    let start_longitude = obj.start_longitude || 0
                    let end_latitude = obj.end_latitude || 0
                    let end_longitude = obj.end_longitude || 0
                    
                    let start_name = obj.start_name || ''
                    let end_name = obj.end_name || ''
                    let create_time = parseInt(Date.now() / 1000)
                    let distance = obj.distance || 0
                    let start_time = obj.start_time || 0
                    let estimated_price = obj.estimated_price || 0
                    let phone = obj.phone || ''
                    let name = obj.name || ''
                    let city_id = obj.city_id || 0

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
                            db.insert('insert into c_trip set start_latitude=?,start_longitude=?,end_latitude=?,end_longitude=?,start_name=?,end_name=?,type=1,id=?,user_id=?,create_time=?,distance=?,estimated_price=?',[start_latitude,start_longitude,end_latitude,end_longitude,start_name,end_name,id,con.user_id,create_time,distance,estimated_price])

                            /** 发送成功信息 */
                            con.sendText(content({status:200,type:'askForDriving',info:obj}))
                            let drivers = []
                            for(let k in ids){
                                drivers.push(ids[k])
                                let driver = data.DriverMap.get(ids[k]+'')

                                driver && action.driverGetOrders(driver.latitude,driver.longitude,function(r){

                                    driver.con.sendText(content({type:'fleshDrivingList','mode':'create',list:r}))
                                })
                                
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
                            })
                            if(result.status == 2){
                                let driver = data.DriverMap.get(result.driver_id+'')
                                driver && action.driverGetOrders(driver.latitude,driver.longitude,function(r){

                                    driver.con.sendText(content({type:'fleshDrivingList','mode':'cancel',list:r}))
                                })
                            }else{
                                let driver_ids = result.driver_ids
                                if(driver_ids){
                                    driver_ids = driver_ids.split(',')
                                    for(let k in driver_ids){
                                        let driver = data.DriverMap.get(driver_ids[k]+'')
                                        driver && action.driverGetOrders(driver.latitude,driver.longitude,function(r){

                                            driver.con.sendText(content({type:'fleshDrivingList','mode':'cancel',list:r}))
                                        })
                                    }
                                }

                            }
                        }
                    }
                })
            }
            break;
        case 'callTaxi':
            break;
        case 'cancelCallTaxi':
            break;
        default:
            break;
    }



}

module.exports = z