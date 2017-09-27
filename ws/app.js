const ws = require("nodejs-websocket")
const crypto = require('crypto')
const util = require('util')
const fs = require('fs')
const md5 = d => crypto.createHash('md5').update(d).digest('hex')
const content = d => d instanceof Object ? JSON.stringify(d) : '{}'
const userAction = require('./userAction')
const driverAction = require('./driverAction')
const db = require('./db')
let data = require('./data')

db.$(function(){
    db.delete('delete from c_user_online')
    db.delete('delete from c_driver_online')
})


let serverCallback = function(con){

    console.log("one connection linked")
    let path = con.path.slice(1)


    if(['user','driver'].indexOf(path) === -1){
        console.warn('error path',path,path.indexOf(['user','driver']) === -1)
        con.sendText(content({status:400,type:'connect'}))
        con.close()
        return;
    }

    

    con.sendText(content({status:200,type:'connect'}))

    con.on("close", function (code, reason) {
        
        if(con.user_id){
            data.UserMap.delete(con.user_id)
            db.delete('delete from c_user_online where user_id=?',[con.user_id])
        }else if(con.driver_id){
            data.DriverMap.delete(con.driver_id)
            db.delete('delete from c_driver_online where driver_id=?',[con.driver_id])
        }

		console.log("one connection closed")
	});
	con.on("error", function (code, reason) {

        if(con.user_id){
            data.UserMap.delete(con.user_id)
            db.delete('delete from c_user_online where user_id=?',[con.user_id])
        }else if(con.driver_id){
            data.DriverMap.delete(con.driver_id)
            db.delete('delete from c_driver_online where driver_id=?',[con.driver_id])
        }
        
		console.log("one connection occurred error")
    });
    con.on("text", function (str){
        let obj
        try{
            obj = JSON.parse(str)
        }catch(e){
            console.warn('message not obj',str)
            return
        }

        if(path == 'user'){
            userAction(obj,con)
        }else if(path == 'driver'){
            driverAction(obj,con)
        }
        

	});

}

let server = ws.createServer(serverCallback).listen(7777)
console.log("server started")

setInterval(function(){
    console.log('Driver:'+data.DriverMap.size,'User:'+data.UserMap.size)
},10000)