(function(w,d){





	
	var A = function(f){
		if(!w[f])['','webkit','moz','ms'].forEach(function(d){if(w[d+f])w[f]=w[d+f]})
		return w[f]
	}

	w.audioApi = function(e){
		this.ac = new (A('AudioContext'))
		this.audioUrls = []
		this.sourceNodes = []
		this.playedOne
		this.gain
		this.animationId
		this.source
		this.isdecoded = 0
		this.isplaying = 0
		this.ele = d.createElement('audio')
		this.loadAudio()
		this.onrender = null
		
	}
	w.audioApi.prototype = {
		get volume(){return this.gain.gain.value},
		set volume(x){return this.gain.gain.value = x},
		play:function(){
			if(!this.isplaying)this.loadAudio()
			this.ele.play()
			this.isplaying = 1
		},pause:function(){
			this.ele.pause()
		},stop:function(){
			this.loadAudio()
		},replay:function(){
			this.loadAudio()
			this.ele.play()
			this.isplaying = 1
		},loadAudio:function(){
			this.isplaying = 0
			if(this.playedOne === undefined && this.audioUrls.length)this.playedOne = 0
			if(this.playedOne !== undefined)this.ele.src = this.audioUrls[this.playedOne]
			if(!this.isdecoded)this.decode( this.ac.createMediaElementSource(this.ele) )
		},addFiles:function(e){
			var fl = d.querySelector(e).files
			if(fl.length===0)return false
			var v=[]
			for(var i=0;i<fl.length;i++)
				if(fl[i].type.match('audio'))
					this.audioUrls.push(URL.createObjectURL(fl[i]))
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