exports.find = function(connection,sql,cb){

    if(connection && connection.state === 'authenticated')
        connection.query(sql+' limit 1',function (err, result) {

            if(err){
                console.log('[SELECT ERROR] - ',err.message);
                cb instanceof Function && cb({})
                return;
            }        
            cb instanceof Function && cb(result.length?result[0]:{})
        });

}

exports.get = function(connection,sql,cb){

    if(connection && connection.state === 'authenticated')
        connection.query(sql,function (err, result) {

            if(err){
                console.log('[SELECT ERROR] - ',err.message);
                cb instanceof Function && cb([])
                return;
            }        
            cb instanceof Function && cb(result?result:[])
        });
    
}


exports.insert = function(connection,sql,cb){

    
    
}


exports.update = function(connection,sql,cb){

    
    
}


exports.delete = function(connection,sql,cb){

    
    
}
