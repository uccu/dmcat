const post = require('./post')
const content = d => d instanceof Object ? JSON.stringify(d) : '{}'
let data = require('./data'),
db = require('./db'),
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
                data.DriverMap.set(d.data.info.id,driver)

                let latitude = obj.latitude
                let longitude = obj.longitude
                if(latitude && longitude)db.replace('replace into c_driver_online (driver_id,latitude,longitude) VALUES(?,?,?)',[d.data.info.id,latitude,longitude])

                else db.replace('replace into c_driver_online (driver_id) VALUES(?)',[d.data.info.id])


                console.log(`driver ${d.data.info.id} linked`)

                

            })

            break;
        case 'updPostion':
            if(con.driver_id){
                let latitude = obj.latitude
                let longitude = obj.longitude
                db.replace('update c_driver_online set latitude=?,longitude=? where driver_id=?',[latitude,longitude,con.driver_id])
                console.log(`driver ${con.driver_id} updated position`)
            }
            break;
        case 'order':
            
            break;
        default:
            break;
    }



}

module.exports = z