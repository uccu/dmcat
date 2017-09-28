const post = require('./post')
const content = d => d instanceof Object ? JSON.stringify(d) : '{}'


let 
    data = require('./data'),

    db = require('./db')


module.exports = {

    driverGetOrders(a,o,f){
        console.log(a,o)
        db.get('select * from c_trip where status=1 and type<3 and start_latitude between ? and ? and start_longitude between ? and ? and driver_id=0 order by create_time desc',[a-0.1,a+0.1,o-0.1,o+0.1],f)

    },

    driverGetOrdersDriving(a,o,f){
        console.log(a,o)
        db.get('select * from c_trip where status=1 and type=1 and start_latitude between ? and ? and start_longitude between ? and ? and driver_id=0 order by create_time desc',[a-0.1,a+0.1,o-0.1,o+0.1],f)

    },

    driverGetOrdersTaxi(a,o,f){
        console.log(a,o)
        db.get('select * from c_trip where status=1 and type=2 and start_latitude between ? and ? and start_longitude between ? and ? and driver_id=0 order by create_time desc',[a-0.1,a+0.1,o-0.1,o+0.1],f)

    }






}