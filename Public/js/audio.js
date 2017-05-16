jq=j;
Number.prototype.t2=function(){if(this.toString().length===1)return '0'+this;else return this.toString()};

(function(j){
	var A = function(f){if(!window[f])['','webkit','moz','ms'].forEach(function(d){if(window[d+f])window[f]=window[d+f]});return window[f]},j=jQuery,file,fname,ac=new (A('AudioContext')),gain=ac.createGain(),source,files=[],ca,cp={
		w:0,
		h:0,
		center:[0,0],
		bP:function(){ca.beginPath();return cp},
		mT:function(){var a=arguments;ca.moveTo(cp.center[0]+a[0],cp.center[1]+a[1]);return cp},
		lT:function(){var a=arguments;ca.lineTo(cp.center[0]+a[0],cp.center[1]+a[1]);return cp},
		lW:function(a){ca.lineWidth=a;return cp},
		shCB:function(a,b,c,d){ca.shadowBlur=b?b:0;ca.shadowColor=a;if(c)ca.shadowOffsetX=c;if(d)ca.shadowOffsetY=d;return cp},
		s:function(){ca.stroke();return cp},
		fS:function(a){ca.fillStyle=a;return cp},
		fC:function(a,b,c,d,e){
			if(!e)e='fillStyle';
			if(typeof a==='string'){
				ca[e] = a;
			}else{
				if(typeof b==='undefined'){b=c=a}
				var g = function(a){if(a>255)return'ff';else if(a<0)return '00';a = parseInt(a).toString(16);return a.length===1 ? '0'+a : a};
				ca[e] = '#'+g(a)+g(b)+g(c);
			}
			if(typeof c==='undefined' && typeof d==='undefined')d=b;
			if(typeof d!=='undefined')ca.globalAlpha=d;return cp
		},
		sS:function(a){ca.strokeStyle=a;return cp},
		sG:function(a,b){
			var g = ca.createLinearGradient(cp.center[0]+a[0],cp.center[1]+a[1],cp.center[0]+a[2],cp.center[1]+a[3]);
			if(typeof b ==='object'){
				for(var d in b)g.addColorStop(b[d][0],b[d][1]);
			}else{
				delete(arguments[0]);
				var l=arguments.length-1;
				for(var d in arguments)g.addColorStop(d/l,arguments[d]);
			}
			ca.strokeStyle = g;
			return cp
		},sC:function(a,b,c,d){return this.fC(a,b,c,d,'strokeStyle')},
		f:function(){ca.fill();return cp},
		cR:function(){var a=arguments;ca.clearRect(a[0],a[1],a[2],a[3],a[4]);return cp},
		arc:function(){var a=arguments;ca.arc(cp.center[0]+a[0],cp.center[1]+a[1],a[2],a[3]*Math.PI*2,a[4]*Math.PI*2,a[5]);return cp},
		cA:function(){cp.cR(0,0,cp.w,cp.h);return cp}
		
	},test,fi,buff=[],startTime=ks=end=nn=currentAudioLength=currentAudioTime=0,setting={volume:1},info={
		runError:'filesInput and canvas is necessary!',
		PageReadyInfo:'Page is ready!',
		changeInputInfo:'songs changed!',
		fileReaderBeforeLoadInfo:'loading...',
		fileReaderOnLoadInfo:'onload...',
		errorInputInfo:'not audio',
		decodeAudioDataError:'decodeAudioData failed!',
		endedInfo:'ended',
		renderingInfo:function(){
			return 'playing '+fname;
		},
		hasNotNext:'has not next song',
		hasNotLast:'has not last song',
		addAudioInfo:'add songs finish'
	},current=0,animationId,td,DD = {
		run:function(s){
			if(!s.filesInput || !s.canvas){console.error(info.runError);return false}
			for(var d in s)setting[d] = s[d];
			DD.BF(s.filesInput,s.canvas,s.callback)
			if(setting.success)setting.success();
		},showInfo:function(){
			if(setting.info){
				var a = arguments,kk;
				if(typeof setting.info ==='function')kk = setting.info(a[1],a[2],a[3]);
				else kk = a[0];
				if(kk)j(setting.info).html(kk)
			}
		},BF:function(f,c,t){ 
			DD.showInfo(info.PageReadyInfo,f,c,t);
			ca = j(c)[0].getContext("2d");
			fi=f;td=t;
			cp.w = j(c).width();
			cp.h = j(c).height();
			cp.center = [cp.w/2,cp.h/2];
			j(fi).bind('change',DD.ADD)
		},ADD:function(){
			var thisfiles = j(fi)[0].files;
			if(thisfiles.length===0)return;
			var v=[];
			for(var i=0;i<thisfiles.length;i++)if(thisfiles[i].type.match('audio'))v[v.length]=files[files.length]=thisfiles[i];
			DD.showInfo(info.addAudioInfo,v);
			if(!v.length){
				DD.showInfo(info.errorInputInfo);
				return;
			}
			if(current===0)DD.BFF(nn);
			else if(end && v.length)DD.BFF(nn+1);
		},BFF:function(n){
			DD.showInfo(info.changeInputInfo,files,n);
			current++;nn=n;
			file=files[nn];
			fname=file.name;
			if(buff[nn])DD.V(buff[nn]);
			else DD.FR(DD.V);
		},FR:function(b){
			DD.showInfo(info.fileReaderBeforeLoadInfo,fname);
			var fr = new FileReader();
			fr.onprogress = function(e){
				console.log(e)
			};
			fr.onloadstart = function(e){
				console.log(e)
			};
			fr.onload = function(e) {
				console.log(e);
				DD.showInfo(info.fileReaderOnLoadInfo);
				ac.decodeAudioData(e.target.result,b,function(e){DD.showInfo(info.decodeAudioDataError);
				var t = [];delete(files[nn]);
				for(var d in files)t[t.length] = files[d];
				files = t;
				console.error(info.decodeAudioDataError)});
			};
			fr.readAsArrayBuffer(file);
		},V:function(b,n){
			var abs = ac.createBufferSource(),a=ac.createAnalyser(),a2=ac.createAnalyser(),cc=current,splitter = ac.createChannelSplitter(2);
			//,merger = ac.createChannelMerger(2);
			//abs.connect(splitter);
			//splitter.connect(merger, 1, 0);
			//splitter.connect(a,1);
			end=0;
			gain=ac.createGain();
			gain.gain.value = setting.volume;
			abs.connect(gain);
			
			gain.connect(splitter);
			splitter.connect(a,0);
			splitter.connect(a2,1);
			gain.connect(ac.destination);
			if(animationId)cancelAnimationFrame(animationId);
			if(source)source.stop(0);
			abs.onended=function(){
				if(ca)cp.cA();
				if(cc===current){
					end=1;
					DD.showInfo(info.endedInfo);
					cancelAnimationFrame(animationId);
					//cancelAnimationFrame(animationId);
					if(nn+1<files.length)DD.BFF(nn+1);
					else if(setting.endedEvent){
						if(setting.endedEvent===1)DD.BFF(0);
						else if(setting.endedEvent===2)ac.suspend();
						else if(typeof setting.endedEvent==='function'){
							setting.endedEvent(ac,nn,files.length);
						}
					}
				}
			};
			buff[nn] = abs.buffer = b;
			currentAudioLength = abs.buffer.duration;
			source = abs;
			abs.start(0,n?n:0);
			startTime=ac.currentTime-(n?n:0);
			window.a = a;window.ac = ac;window.abs=abs;window.gain=gain;
			//,a2=ac.createAnalyser();
			DD.D(a,a2);
		},D:function(a,a2){
			//
			
			//splitter.connect(a2,1);
			DD.showInfo(info.renderingInfo);
			ks=0;
			(function(){
				var array = new Float32Array(a.frequencyBinCount);
				a.getFloatFrequencyData(array);
				var array2 = new Float32Array(a2.frequencyBinCount);
				a2.getFloatFrequencyData(array2);
				currentAudioTime = ac.currentTime-startTime;
				// var array2 = new Uint8Array(a2.frequencyBinCount);
				// a2.getByteFrequencyData(array2);
				ks++;
				if(td)td(cp,array,array2,ks,a,currentAudioTime,currentAudioLength);
				animationId = requestAnimationFrame(arguments.callee)
			})()
		}
	};
	var audioApi = function(t,v,c){
		
		if(typeof v =='object' && v.canvas && !c){
			v.filesInput=t[0];DD.run(v);v = v.canvas;
		}else DD.run({filesInput:t[0],canvas:v,callback:c})
		j(window).resize(function(){
			cp.w = j(v).attr({width:j(window).width(),height:j(window).height()}).width();
			cp.h = j(v).height();
			cp.center = [cp.w/2,cp.h/2];
		});
		return this
	};
	audioApi.prototype={
		setting:function(v){
			for(var d in v)setting[d] = v[d];return this
		},infoSetting:function(v){
			for(var d in v)info[d] = v[d];return this
		},volume:function(v){
			if(!v)return gain.gain.value;
			gain.gain.value = setting.volume = v;return this
		},pause:function(v){
			ac.suspend();return this
		},resume:function(v){
			DD.showInfo(info.renderingInfo);
			if(end && files)DD.BFF(nn);
			ac.resume();return this
		},state:function(){
			return ac.state
		},next:function(){
			if(nn+1<files.length)DD.BFF(nn+1);
			else DD.showInfo(info.hasNotNext);
			return this;
		},last:function(){
			if(nn>0)DD.BFF(nn-1);
			else DD.showInfo(info.hasNotLast);
			return this;
		},repeat:function(){
			current++;DD.V(buff[nn]);return this
		},to:function(n){
			current++;DD.V(buff[nn],n);return this
		},toByPercent:function(p){
			return this.to(currentAudioLength*p)
		},nowWhich:function(){
			return nn;
		},lengthWhich:function(){
			return files.length;
		},toWhich:function(w){
			DD.BFF(w);return this
		}
	};
	jQuery.fn.extend({
		audioApi:function(v,c){return new audioApi(this,v,c)}
	});
	
	

})()