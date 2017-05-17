(function(w,d){





	
	var A = function(f){
		if(!w[f])['','webkit','moz','ms'].forEach(function(d){if(w[d+f])w[f]=w[d+f]});return w[f]
	}

	var audioApi = function(e){
		this.ac = new (A('AudioContext'))
		this.audioUrls = []
		this.playedOne = undefined
		this.volume = 1
		this.fileInput = d.querySelector(e)
		this.animationId
		this.source = undefined
	}
	audioApi.prototype = {
		addFiles:function(){
			var fl = this.fileInput.files
			if(fl.length===0)return false
			var v=[]
			for(var i=0;i<fl.length;i++)
				if(fl[i].type.match('audio'))
					this.audioUrls.push(URL.createObjectURL(fl[i]))
		},readFile:function(){
			var fr = new FileReader();
			fr.onload = function(e) {
				this.ac.decodeAudioData(
					e.target.result,/* audioData */
					this.successDecode,/* successCallback */
					this.errorDecode,/* errorCallback */
				)
			};
			this.playedOne !== undefined && fr.readAsDataURL(
				this.audioUrls[this.playedOne]
			);
		},errorDecode:function(err){

		},successDecode:function(buffer){

			var abs = this.ac.createBufferSource(),
			var analyserLeft = this.ac.createAnalyser(),
			var analyserRight = this.ac.createAnalyser(),

			var aCurrent = current,
			var splitter = ac.createChannelSplitter(2);

			var gain = this.ac.createGain();
			gain.gain.value = this.volume;
			abs.connect(gain);
			
			gain.connect(splitter);

			splitter.connect(analyserLeft,0);
			splitter.connect(analyserRight,1);

			gain.connect(ac.destination);

			if(this.animationId)cancelAnimationFrame(this.animationId);

			if(this.source)this.source.stop(0);

			abs.onended=function(){

				
			};
			abs.buffer = buffer;
			currentAudioLength = abs.buffer.duration;
			this.source = abs;



			this.render(analyserLeft,analyserRight);
		}

	}

	



	



	



	



	



	



	





})(window,document)