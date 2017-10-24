~function(w,d,j){

    var A = function(f){if(!window[f])['','webkit','moz','ms'].forEach(function(d){if(window[d+f])window[f]=window[d+f]});return window[f]}

    w.audioApi = function(url){

        this.ac = new (A('AudioContext'))
        this.source = null
        this.audioBuffer = null
        this.loadAudioFile(url)
    }

    w.audioApi.prototype.play = function(){
        if(this.audioBuffer)this.playSound()
    }

    w.audioApi.prototype.playSound = function () {
        delete this.source;
        this.source = this.ac.createBufferSource();
        this.source.buffer = this.audioBuffer;
        this.source.connect(this.ac.destination);
        this.source.start(0,0);
    }
    w.audioApi.prototype.initSound = function (arrayBuffer) {
        var that = this
        that.ac.decodeAudioData(arrayBuffer, function(buffer) {
            that.audioBuffer = buffer;
        }, function(e) {
            console.log('Error decoding file', e);
        });
    }

    w.audioApi.prototype.loadAudioFile = function (url) {
        var xhr = new XMLHttpRequest(); //通过XHR下载音频文件
        var that = this;
        xhr.open('GET', url, true);
        xhr.responseType = 'arraybuffer';
        xhr.onload = function(e) { //下载完成
            that.initSound(this.response);
        };
        xhr.send();
    }




}(window,document,jQuery)