(function(w,d){





	
	var A = function(f){
		if(!w[f])['','webkit','moz','ms'].forEach(function(d){if(w[d+f])w[f]=w[d+f]})
		return w[f]
	}

	w.audioApi = function(e){
		var that = this
		this.ac = new (A('AudioContext'))
		this.audioUrls = []
		this.sourceNodes = []
		this.playedOne
		this.gain
		this.animationId
		this.isdecoded = 0
		this.isplaying = 0
		this.ele = d.createElement('audio')
		this.loadAudio()
		this.onrender = null
		this.onplay = null
		this.onpause = null
		this.onVolumeChange = null
		this.onTimeUpdate = null
		this.onload = null
		this.ele.ontimeupdate = function(){
			that.onTimeUpdate && that.onTimeUpdate()
		}
		
	}
	w.audioApi.prototype = {
		get volume(){return this.gain.gain.value},
		set volume(x){this.onVolumeChange && this.onVolumeChange(x);return this.gain.gain.value = x},
		play:function(){
			if(!this.isplaying)this.loadAudio()
			if(!this.audioUrls.length)return false;
			this.ele.play()
			this.isplaying = 1
			if(this.onplay)this.onplay()
			return true;
		},pause:function(){
			this.ele.pause()
			if(this.onpause)this.onpause()
			return true;
		},stop:function(){
			this.loadAudio()
		},replay:function(){
			this.loadAudio()
			this.ele.play()
			this.isplaying = 1
		},prev:function(){
			if(!this.audioUrls.length)return false
			this.playedOne = this.playedOne === 0 ? this.audioUrls.length - 1 : this.playedOne - 1
			if(!this.ele.paused)this.replay()
			else{
				this.ele.currentTime = 0
				this.loadAudio()
			}
			return true;
		},next:function(){
			if(!this.audioUrls.length)return false
			this.playedOne = this.playedOne === this.audioUrls.length - 1 ? 0 : this.playedOne + 1
			if(!this.ele.paused)this.replay()
			else{
				this.ele.currentTime = 0
				this.loadAudio()
			}
			return true;
		},loadAudio:function(){
			this.isplaying = 0
			if(this.playedOne === undefined && this.audioUrls.length)this.playedOne = 0
			if(this.playedOne !== undefined){
				this.ele.src = this.audioUrls[this.playedOne].src
				this.onload && this.onload(this.audioUrls[this.playedOne])
			}
			if(!this.isdecoded)this.decode( this.ac.createMediaElementSource(this.ele) )
			
			return this
		},addFiles:function(e){
			var fl = d.querySelector(e).files
			if(fl.length===0)return false
			for(var i=0;i<fl.length;i++)
				if(fl[i].type.match('audio')){
					this.audioUrls.push({
						src:URL.createObjectURL(fl[i]),
						name:fl[i].name
					})
				}
			if(!this.audioUrls.length)this.playedOne = undefined
			return this
		},decode:function(source){
			var splitter = this.ac.createChannelSplitter(2)

			this.analyserLeft = this.ac.createAnalyser()
			this.analyserRight = this.ac.createAnalyser()
			this.gain = this.ac.createGain()

			this.volume = 1
			source.connect(this.gain)
			this.gain.connect(splitter)
			splitter.connect(this.analyserLeft,0)
			splitter.connect(this.analyserRight,1)
			this.gain.connect(this.ac.destination)
			this.isdecoded = 1
			this.render()
		},render:function(){
			
			var l = new Uint8Array(this.analyserLeft.frequencyBinCount)
			this.analyserLeft.getByteFrequencyData(l)
			var r = new Uint8Array(this.analyserRight.frequencyBinCount)
			this.analyserRight.getByteFrequencyData(r)
			if(this.onrender)this.onrender(l,r)
			var that = this;
			this.animationId = w.requestAnimationFrame(function(){
				that.render()
			})
		}

	}

	



	



	



	



	



	



	





})(window,document)