const mysql = require('mysql')
const db_config = require('./db_config')
const db = require('./db')


let connection;
let cb;
function handleDisconnect() {
    connection = mysql.createConnection(db_config);                                               
    connection.connect(function(err) {              
        if(err) {                                     
        console.log("进行断线重连：" + new Date());
        setTimeout(handleDisconnect, 2000);   //2秒重连一次
        return;
        }         
        console.log("连接成功");
        cb instanceof Function && cb()

    });                                                                           
    connection.on('error', function(err) {
        console.log('db error', err);
        if(err.code === 'PROTOCOL_CONNECTION_LOST') { 
            handleDisconnect();                         
        } else {
            throw err;
        }
    });
}
handleDisconnect();

exports.$ = function(c){
    cb = c
}
exports.find = function(sql,param,cb){

    if(param instanceof Function){
        cb = param
        param = []
    }
    if(!param)param = []
    if(connection && connection.state === 'authenticated')
        connection.query(sql+' limit 1',param,function (err, result) {

            if(err){
                console.log('[SELECT ERROR] - ',err.message);
                cb instanceof Function && cb({})
                return;
            }        
            cb instanceof Function && cb(result.length?result[0]:{})
        });

}

exports.get = function(sql,param,cb){

    if(param instanceof Function){
        cb = param
        param = []
    }
    if(!param)param = []
    if(connection && connection.state === 'authenticated')
        connection.query(sql,param,function (err, result) {

            if(err){
                console.log('[SELECT ERROR] - ',err.message);
                cb instanceof Function && cb([])
                return;
            }        
            cb instanceof Function && cb(result?result:[])
        });
    
}


exports.insert = function(sql,param,cb){

    if(param instanceof Function){
        cb = param
        param = []
    }
    if(!param)param = []
    if(connection && connection.state === 'authenticated')
        connection.query(sql,param,function (err, result) {

            if(err){
                console.log('[INSERT ERROR] - ',err.message);
                cb instanceof Function && cb(0)
                return;
            }        
            cb instanceof Function && cb(result?result.insertId:0)
        });
    
}
    
exports.replace = function(sql,param,cb){

    if(param instanceof Function){
        cb = param
        param = []
    }
    if(!param)param = []

    if(connection && connection.state === 'authenticated')
        connection.query(sql,param,function (err, result) {

            if(err){
                console.log('[REPLACE ERROR] - ',err.message);
                cb instanceof Function && cb(0)
                return;
            }        
            cb instanceof Function && cb(result?result.insertId:0)
        });
    
}


exports.update = function(sql,param,cb){

    if(param instanceof Function){
        cb = param
        param = []
    }
    if(!param)param = []
    if(connection && connection.state === 'authenticated')
        connection.query(sql,param,function (err, result) {

            if(err){
                console.log('[UPDATE ERROR] - ',err.message);
                cb instanceof Function && cb(0)
                return;
            }        
            cb instanceof Function && cb(result?result.affectedRows:0)
        });
    
}


exports.delete = function(sql,param,cb){

    if(param instanceof Function){
        cb = param
        param = []
    }
    if(!param)param = []
    if(connection && connection.state === 'authenticated')
        connection.query(sql,param,function (err, result) {

            if(err){
                console.log('[DELETE ERROR] - ',err.message);
                cb instanceof Function && cb(0)
                return;
            }        
            cb instanceof Function && cb(result?result.affectedRows:0)
        });
    
    
}



// 插入
// var  userAddSql = 'INSERT INTO userinfo(Id,UserName,UserPass) VALUES(0,?,?)';
// var  userAddSql_Params = ['Wilson', 'abcd'];
// connection.query(userAddSql,userAddSql_Params,function (err, result) {
//         if(err){
//          console.log('[INSERT ERROR] - ',err.message);
//          return;
//         }        

//        console.log('--------------------------INSERT----------------------------');
//        //console.log('INSERT ID:',result.insertId);        
//        console.log('INSERT ID:',result);        
//        console.log('-----------------------------------------------------------------\n\n');  
// });

// 更新
// var userModSql = 'UPDATE userinfo SET UserName = ?,UserPass = ? WHERE Id = ?';
// var userModSql_Params = ['钟慰', '5678',1];
// //改
// connection.query(userModSql,userModSql_Params,function (err, result) {
//    if(err){
//          console.log('[UPDATE ERROR] - ',err.message);
//          return;
//    }        
//   console.log('--------------------------UPDATE----------------------------');
//   console.log('UPDATE affectedRows',result.affectedRows);
//   console.log('-----------------------------------------------------------------\n\n');
// });


// 删除
// var  userDelSql = 'DELETE FROM userinfo';
// connection.query(userDelSql,function (err, result) {
//         if(err){
//           console.log('[DELETE ERROR] - ',err.message);
//           return;
//         }        

//        console.log('--------------------------DELETE----------------------------');
//        console.log('DELETE affectedRows',result.affectedRows);
//        console.log('-----------------------------------------------------------------\n\n');  
// });

