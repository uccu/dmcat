const post = require('./post')
let data = require('./data')

let UserInfo = function(){}

let z = function(obj,con){

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
                    driver.con.close();
                }
                con.driver_id = d.data.info.id
                driver = new UserInfo
                driver.con = con
                driver.id = d.data.info.id
                data.DriverMap.set(d.data.info.id,driver)
                console.log(`one driver ${d.data.info.id} linked`)

                

            })

            break;
        default:
            break;
    }



}

module.exports = z