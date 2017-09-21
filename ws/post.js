
const querystring = require('querystring')
const http = require('http')
const config = require('./config')

let z = function(url,data,cb){
    if(data instanceof Function){
        cb = data;data = {}
    }
    data = data || {}
    let postData = querystring.stringify(data),
    gData = '',
    options = {
        hostname: config.host,
        port: config.port||80,
        path: config.path+url,
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'Content-Length': Buffer.byteLength(postData)
        }
    },req = http.request(options, (res) => {

        res.setEncoding('utf8')
        res.on('data', (chunk) => {
            gData += chunk;
        })
        res.on('end', () => {
            let obj,f = cb instanceof Function ? 1 : 0
            try{
                obj = JSON.parse(gData)
                // if(obj.code != 200){
                //     console.warn(obj.message,gData)
                //     f && cb(false)
                //     return
                // }
                f && cb(obj)
            }catch(e){
                f && cb(false)
                console.warn(e.message,gData)
                return
            }
        })
    })

    req.on('error', (e) => {
        cb instanceof Function && cb(false)
        console.warn(`请求遇到问题: ${e.message}`);
    });
    req.write(postData);
    req.end();


}

z.prototype;


module.exports = z;