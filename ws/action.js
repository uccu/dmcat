const post = require('./post')
const content = d => d instanceof Object ? JSON.stringify(d) : '{}'


let 
    data = require('./data'),

    db = require('./db'),

    dis = function (lat1, lng1, lat2, lng2) {
        var radLat1 = lat1 * Math.PI / 180.0;
        var radLat2 = lat2 * Math.PI / 180.0;
        var a = radLat1 - radLat2;
        var b = lng1 * Math.PI / 180.0 - lng2 * Math.PI / 180.0;
        var s = 2 * Math.asin(Math.sqrt(Math.pow(Math.sin(a / 2), 2) + Math.cos(radLat1) * Math.cos(radLat2) * Math.pow(Math.sin(b / 2), 2)));
        s = s * 6378.137;
        s = Math.round(s * 10000) / 10000;
        return s
    };



module.exports = {

    driverGetOrders(a,o,f){
        // console.log(a,o)
        db.get('select * from c_trip where status=1 and type<3 and start_latitude between ? and ? and start_longitude between ? and ? and driver_id=0 order by create_time desc',[a-0.1,a+0.1,o-0.1,o+0.1],function(w){
            if(w.length){
                w.toDistance = dis(a,o,w.start_latitude,w.start_longitude)
                if(w.toDistance < 1000)w.toDistance = w.toDistance + '米';
                else w.toDistance = parseInt(w.toDistance/100)/10 + '公里';
            }
            f && f(w)
        })

    },

    driverGetOrdersDriving(a,o,f){
        // console.log(a,o)
        db.get('select * from c_trip where status=1 and type=1 and start_latitude between ? and ? and start_longitude between ? and ? and driver_id=0 order by create_time desc',[a-0.1,a+0.1,o-0.1,o+0.1],function(w){
            if(w.length){
                w.toDistance = dis(a,o,w.start_latitude,w.start_longitude)
                if(w.toDistance < 1000)w.toDistance = w.toDistance + '米';
                else w.toDistance = parseInt(w.toDistance/100)/10 + '公里';
            }
            f && f(w)
        })

    },

    driverGetOrdersTaxi(a,o,f){
        // console.log(a,o)
        db.get('select * from c_trip where status=1 and type=2 and start_latitude between ? and ? and start_longitude between ? and ? and driver_id=0 order by create_time desc',[a-0.1,a+0.1,o-0.1,o+0.1],function(w){
            if(w.length){
                w.toDistance = dis(a,o,w.start_latitude,w.start_longitude)
                if(w.toDistance < 1000)w.toDistance = w.toDistance + '米';
                else w.toDistance = parseInt(w.toDistance/100)/10 + '公里';
            }
            f && f(w)
        })

    }






}