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
        if(obj.token){
            
            if(obj.token == 'mm'){
                // let admin = data.AdminMap.get(obj.id+'')
                /** 如果用户存在 */
                // if(admin){
                    /** 用户重复登录无效 */
                    // if(admin.con === con)return
                    /** 关闭前一次连接 */
                    // delete admin.con.admin_id
                    // admin.con.close();
                // }
                /** con.admin_id admin.id 字符串 */
                con.admin_id = obj.id+''
                admin = new UserInfo
                admin.con = con
                admin.id = obj.id+''
                data.AdminMap.set(con.admin_id,admin)
                console.log(`admin ${con.admin_id} linked`)
                con.sendText(content({status:200,type:'login'}))
            }
        }
        
    },
    pushDrivingOrder(obj,con){
        if(con.admin_id){
            
            if(!obj.driver_id){
                con.sendText(content({status:400,type:'pushDrivingOrder',message:'请选择司机'}))
                return
            }

            if(!obj.trip_id || !obj.id || !obj.driver_id){
                con.sendText(content({status:400,type:'pushDrivingOrder',message:'参数错误'}))
                return
            }
            ;

            let driver = data.DriverMap.get(obj.driver_id + '')
            if(driver && !driver.serving){
                driver.con.sendText(content({status:200,type:'distribute',order_id:obj.id,trip_id:obj.trip_id}))
            }else if(!driver){
                con.sendText(content({status:400,type:'pushDrivingOrder',message:'司机不存在'}))
                return
            }else{
                con.sendText(content({status:400,type:'pushDrivingOrder',message:'司机在服务中'}))
                return
            }

            con.sendText(content({status:200,type:'pushDrivingOrder'}))

            console.log(`admin ${con.admin_id} pushDrivingOrder ${obj.id}(${obj.trip_id}) to driver ${obj.driver_id}`)
        }
    },
    askForDrivingV2(obj,con){ /** 发起代驾订单 */
        if(!obj.user_id){
            con.sendText(content({status:400,type:'login',message:'未登录'}))
            console.error('one user login error 5')
            return
        }

        let user = data.UserMap.get(obj.user_id + '')
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
            db.find('select * from c_trip where user_id=? and statuss in (5,10,15,20,25,30,35,40,45)',[obj.user_id],function(result){
                /** 判断是否有订单正在执行中 */
                if(result){
                    con.sendText(content({status:400,type:'askForDriving',message:'该用户不能重复下单.'+result.trip_id}))
                    return
                }
                sync.run();
            })
        }
        /** 插入订单和行程 */
        sync.add = function(){
            /** 创建订单 */
            db.insert('insert into c_order_driving set start_latitude=?,start_longitude=?,end_latitude=?,end_longitude=?,start_name=?,end_name=?,create_time=?,statuss=5,user_id=?,distance=?,estimated_price=?,start_time=?,phone=?,name=?,city_id=?',[start_latitude,start_longitude,end_latitude,end_longitude,start_name,end_name,create_time,obj.user_id,distance,estimated_price,start_time,phone,name,city_id],function(id){
                if(!id)return;
                obj.id = id;
                /** 创建行程 */
                db.insert('insert into c_trip set start_fee=?,statuss=5,start_latitude=?,start_longitude=?,end_latitude=?,end_longitude=?,start_name=?,end_name=?,type=1,id=?,user_id=?,create_time=?,distance=?,estimated_price=?',[start_fee,start_latitude,start_longitude,end_latitude,end_longitude,start_name,end_name,id,obj.user_id,create_time,distance,estimated_price],function(trip_id){
                    obj.trip_id = trip_id
                    sync.run(id,trip_id)
                })
            })
        }
        /** 查找3公里内的司机 */
        sync.add = function(id,trip_id){
            db.get('select d.driver_id,round( 6378.138 * 2 * asin( sqrt( pow( sin( (d.latitude* PI()/180- ? * PI() /180)/2 ),2 )+ cos(d.latitude* PI()/180)*cos(? * PI() /180)* pow( sin( (d.longitude* PI()/180 - ? * PI()/180)/2 ),2 ) ) )*1000 ) AS `distance` from c_driver_online d inner join c_driver r on d.driver_id=r.id where round( 6378.138 * 2 * asin( sqrt( pow( sin( (d.latitude* PI()/180- ? * PI() /180)/2 ),2 )+ cos(d.latitude* PI()/180)*cos(? * PI() /180)* pow( sin( (d.longitude* PI()/180 - ? * PI()/180)/2 ),2 ) ) )*1000 ) between ? and ? and r.type_driving=1 order by distance',[start_latitude,start_latitude,start_longitude,start_latitude,start_latitude,start_longitude,0,3000],function(ids){
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
                        (driver.type_driving && driver.type_taxi) && action.driverGetOrders(driver.latitude,driver.longitude,g,driver.city_id);
                        (driver.type_driving && !driver.type_taxi) && action.driverGetOrdersDriving(driver.latitude,driver.longitude,g,driver.city_id);
                        (!driver.type_driving && driver.type_taxi) && action.driverGetOrdersTaxi(driver.latitude,driver.longitude,g,driver.city_id);
                    }
                }
                if(drivers.length)db.update('update c_order_driving set driver_ids=? where id=?',[drivers.join(','),id])
            })
        }
        sync.run();
    },
    

}


let z = function(obj,con){

    if(act[obj.type])act[obj.type](obj,con);




}

module.exports = z