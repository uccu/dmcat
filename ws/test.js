let SYNC = function(){
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



let a = new SYNC;

a.add = function(){

    console.log(1);
    a.run();
}

a.add = function(){

    console.log(a._s);
    a.run();
}

a.run();
