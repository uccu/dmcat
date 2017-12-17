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
                let admin = data.AdminMap.get(obj.id+'')
                /** 如果用户存在 */
                if(admin){
                    /** 用户重复登录无效 */
                    if(admin.con === con)return
                    /** 关闭前一次连接 */
                    // delete admin.con.admin_id
                    admin.con.close();
                }
                /** con.admin_id admin.id 字符串 */
                con.admin_id = obj.id+''
                admin = new UserInfo
                admin.con = con
                admin.id = obj.id+''
                data.AdminMap.set(con.admin_id,admin)
                console.log(`admin ${obj.id} linked`)
            }
        }
        
    },
    pushDrivingOrder(){
        
    }
    

}


let z = function(obj,con){

    if(act[obj.type])act[obj.type](obj,con);




}

module.exports = z