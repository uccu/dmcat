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
        s = Math.round(s * 10000) / 10;
        return s
    },

    getDis = function (lat1, lng1, lat2, lng2,type = 1,fun){

        let data = {};

        data.key = '99e83d3bf5c6f8825f237440cc283b5e';
        data.origins = lng1 + ',' + lat1;
        data.destination = lng2 + ',' + lat2;
        data.type = type;

        post('http://restapi.amap.com/v3/distance',data,function(str){

            try{
                obj = JSON.parse(str)
                if(!$data.status)fun(false)
                else fun(obj.results[0])
            }catch(e){
                console.warn('message not obj(getDis)',str)
                fun(false)
            }
        })

    },loopD = function(a,o,w,f,i){

        i = i || 0;
        if(i<w.length){
            getDis(a,o,w[i].start_latitude,w[i].start_longitude,3,function(obj){
            

                if(!obj)w[i].toDistance = 0
                else w[i].toDistance = obj.distance

                if(w[i].toDistance < 1000)w[i].toDistance = parseInt(w[i].toDistance) + '米';
                else w[i].toDistance = parseInt(w[i].toDistance/100)/10 + '公里';

            
                i++;
                loopD(a,o,w,f,i)
            
            })
        }else{
            f && f(w)
        }
    }




module.exports = {
    
    driverGetOrders(a,o,f){
        // console.log(a,o)
        db.get('select * from c_trip where status=1 and type<3 and start_latitude between ? and ? and start_longitude between ? and ? and driver_id=0 order by create_time desc',[a-0.1,a+0.1,o-0.1,o+0.1],function(w){
            if(w.length){

                loopD(a,o,w,f)
            }else{
                f && f(w)
            }
            
        })

    },

    driverGetOrdersDriving(a,o,f){
        // console.log(a,o)
        db.get('select * from c_trip where status=1 and type=1 and start_latitude between ? and ? and start_longitude between ? and ? and driver_id=0 order by create_time desc',[a-0.1,a+0.1,o-0.1,o+0.1],function(w){
            if(w.length){
                loopD(a,o,w,f)
            }
            else{
                f && f(w)
            }
        })

    },

    driverGetOrdersTaxi(a,o,f){
        // console.log(a,o)
        db.get('select * from c_trip where status=1 and type=2 and start_latitude between ? and ? and start_longitude between ? and ? and driver_id=0 order by create_time desc',[a-0.1,a+0.1,o-0.1,o+0.1],function(w){
            if(w.length){

                loopD(a,o,w,f)
            }else{
                f && f(w)
            }
        })

    },
    getWayPrice($distance,$num){
        var $price = 0;
        if($distance<6)$price = 20;
        else if($distance<100)$price = 20 + 1.5 * ($distance - 6);
        else if($distance<300)$price = 20 + 1.5 * (100 - 6) + 1.4 * ($distance - 100);
        else $price = $price = 20 + 1.5 * 94 + 1.4 * 200 + 1.3 * ($distance - 300);

        if($num == 2){
            $price *= 1.7;
        }else if($num == 3){
            $price *= 2.8;
        }else if($num == 4){
            $price *= 4.1;
        }

        return $price;
    },
    getDrivingPrice(city_id,in_time,distance,g){

        distance = parseFloat(distance)
        in_time = parseFloat(in_time)
        let date = new Date;
        date.setTime(in_time * 1000)
        let hours = date.getHours()
        db.find('select * from c_area where id=?',[city_id],function(area){

            if(!area)return;
            let level = area.seq;
            if(!level)return;

            let start_price = 0;
            let distance_price = 0;
            let distance_r = 0;
            if(level == 1){
            
                if(hours < 7)start_price = 95;
                else if(hours < 22)start_price = 35;
                else if(hours < 23)start_price = 55;
                else start_price = 75;

                if(distance < 10){
                    distance_price = 0;
                }else{
                    distance_r = Math.ceil(distance / 10 ) - 1;
                    distance_price = distance_r * 25;
                }
            }else if(level == 2){

                if(hours < 7)start_price = 55;
                else if(hours < 22)start_price = 35;
                else start_price = 55;

                if(distance < 10){
                    distance_price = 0;
                }else{
                    distance_r = Math.ceil(distance / 5 ) - 2;
                    distance_price = distance_r * 20;
                }
            }else if(level == 3){
                if(hours < 7)start_price = 29;
                else if(hours < 21)start_price = 19;
                else start_price = 29;

                if(distance < 5){
                    distance_price = 0;
                }else{
                    distance_r = Math.ceil(distance / 5 ) - 1;
                    distance_price = distance_r * 20;
                }
            }

            let total = distance_price + start_price;

            g({
                total:total,
                distance:distance_price,
                start:start_price,
            });



        });
    },
    getTaxiPrice(city_id,in_time,distance,g){

        distance = parseFloat(distance)
        in_time = parseFloat(in_time)
        let date = new Date;
        date.setTime(in_time * 1000)
        let hours = date.getHours()
        db.find('select * from c_area where id=?',[city_id],function(area){

            if(!area)return;
            let level = area.seq;
            if(!level)return;

            let start_price = 0;
            let distance_price = 0;
            let distance_r = 0;
            let $price = 0;
            
            if(hours < 5)start_price = 14;
            else if(hours < 23)start_price = 18;
            else start_price = 14;

            // if(area.parent_id == 4522847){



            if(hours < 5 || hours > 23){
                if($distance<3)$price = start_price;
                else if($distance<10)$price = start_price + 3.1 * ($distance - 3);
                else $price = $price = start_price + 3.1 * 7 + 4.7 * ($distance - 10);
            }else{
                if($distance<3)$price = start_price;
                else if($distance<10)$price = start_price + 2.4 * ($distance - 3);
                else $price = $price = start_price + 2.4 * 7 + 3.6 * ($distance - 10);
            }

            g($price)

            


        });
    }



}