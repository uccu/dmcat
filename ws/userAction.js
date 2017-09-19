const post = require('./post')
let data = require('./data')
let db = require('./db')

let UserInfo = function(){}

let z = function(obj,con){

    switch(obj.type){

        case 'login':

            post('user/getMyinfo',{user_token:obj.user_token},function(d){

                if(d.code != 200){

                    console.error('one user error',d)
                    return
                }
                let user = data.UserMap.get(d.data.info.id)
                if(user){
                    if(user.con === con)return
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
                
                console.log(`one user ${d.data.info.id} linked`)

                

            })

            break;
        case 'updPostion':

            if(con.user_id){

                
                let latitude = obj.latitude
                let longitude = obj.longitude

                db.replace('update c_user_online set latitude=?,longitude=? where user_id=?',[latitude,longitude,con.user_id])
                
                console.log(`one user ${con.user_id} updated position`)

                

            }

            break;
        default:
            break;
    }



}

module.exports = z