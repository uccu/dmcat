const ws = require("nodejs-websocket")
const crypto = require('crypto')
const util = require('util')
const fs = require('fs')
const md5 = d => crypto.createHash('md5').update(d).digest('hex')
const content = d => d instanceof Object ? JSON.stringify(d) : '{}'
const userAction = require('./userAction')
const driverAction = require('./driverAction')
const adminAction = require('./adminAction')
const db = require('./db')
let data = require('./data'),
post = require('./post'),
sendAdmin = function(f){
    for(let i of data.AdminMap){
        i[1].con.sendText(content({status:200,type:'log',data:f}))
    }

}


db.$(function(){
    db.delete('delete from c_user_online')
    db.delete('delete from c_driver_online')
})


let serverCallback = function(con){

    sendAdmin("one connection linked")
    let path = con.path.slice(1)


    if(['user','driver','admin'].indexOf(path) === -1){
        sendAdmin(['error path',path,path.indexOf(['user','driver','admin']) === -1])
        con.sendText(content({status:400,type:'connect'}))
        con.close()
        return;
    }

    

    con.sendText(content({status:200,type:'connect'}))

    con.on("close", function (code, reason) {
        
        if(con.user_id){

            let user = data.UserMap.get(con.user_id + '')
            data.UserMap.delete(con.user_id)
            db.delete('delete from c_user_online where user_id=?',[con.user_id])
        }else if(con.driver_id){
            data.DriverMap.delete(con.driver_id)
            db.delete('delete from c_driver_online where driver_id=?',[con.driver_id])
            post('driver/ws_logout',{driver_id:con.driver_id})
        }else if(con.admin_id){
            data.AdminMap.delete(con.admin_id)
        }

		sendAdmin("one connection closed")
	});
	con.on("error", function (code, reason) {

        if(con.user_id){
            let user = data.UserMap.get(con.user_id + '')
            data.UserMap.delete(con.user_id)
            db.delete('delete from c_user_online where user_id=?',[con.user_id])
        }else if(con.driver_id){
            data.DriverMap.delete(con.driver_id)
            db.delete('delete from c_driver_online where driver_id=?',[con.driver_id])
            post('driver/ws_logout',{driver_id:con.driver_id})
        }else if(con.admin_id){
            data.AdminMap.delete(con.admin_id)
        }
        
		sendAdmin("one connection occurred error")
    });
    con.on("text", function (str){
        let obj
        try{
            obj = JSON.parse(str)
        }catch(e){
            sendAdmin(['message not obj',str])
            return
        }

        if(!obj || !(obj instanceof Object))return
        try{
            if(path == 'user'){
                userAction(obj,con)
            }else if(path == 'driver'){
                driverAction(obj,con)
            }else if(path == 'admin'){
                adminAction(obj,con)
            }
        }catch(e){
            sendAdmin(['obj has problem',str])
            console.log(e)
            return
        }
        

	});

}

let server = ws.createServer(serverCallback).listen(7777)
sendAdmin("server started")

setInterval(function(){
    sendAdmin(['Driver:'+data.DriverMap.size,'User:'+data.UserMap.size,'Admin:'+data.AdminMap.size])
},10000)