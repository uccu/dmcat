const post = require('./post')
const content = d => d instanceof Object ? JSON.stringify(d) : '{}'


let 
    data = require('./data'),

    db = require('./db')


module.exports = {

    driverGetOrders(){

        db.find('select * from c_order_driving where user_id=? and status in (1,2,3,4)',[con.user_id],function(result){})


    }








}