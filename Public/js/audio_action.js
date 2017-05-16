jq(function(){
	// alert('on test!');
	j=jQuery;
		j('<canvas>').css({position:'absolute',top:0,left:0,'z-index':-1}).attr({width:j(window).width(),height:j(window).height()}).appendTo(window);	
		var test = 0,rf=[],laz=[],lza=[],lastTime=Date.now(),callback=function(cp,array,array2,ks,a,currentAudioTime,currentAudioLength){
			cp.cA();
			cp.bP().arc(0,0,240,0.3,1.2).sG([-240,-240,240,240],'#030','#fff','#90f','#f090f0','#ddd','#d54','#ffc').shCB('#ccc',5).s();
			var l = array.length,le = parseInt(l/10),ta;
			var lw = 0.45/parseInt(l/6);
			for(var i=0;i<parseInt(l/6);i++){
				if(array[le+i*3]-a.minDecibels<0)
					cp.bP().arc(0,0,240+array[le+i*3]-a.minDecibels,0.3+i*lw,0.3+i*lw+0.6*lw).arc(0,0,240+array[le+i*3]-a.minDecibels-3,0.3+i*lw+0.6*lw,0.3+i*lw,1).fC('#CBC',0.8).f()
				else
					cp.bP().arc(0,0,240,0.3+i*lw,0.3+i*lw+0.6*lw).arc(0,0,240+array[le+i*3]-a.minDecibels,0.3+i*lw+0.6*lw,0.3+i*lw,1).fC('#CBC',0.8).f();
			}
			
			var l = array2.length,le = parseInt(l/10),ta;
			var lw = 0.45/parseInt(l/6);
			for(var i=0;i<parseInt(l/6);i++){
				if(array2[le+i*3]-a.minDecibels<0)
					cp.bP().arc(0,0,240+array2[le+i*3]-a.minDecibels,0.2-i*lw-0.6*lw,0.2-i*lw).arc(0,0,240+array2[le+i*3]-a.minDecibels-3,0.2-i*lw,0.2-i*lw-0.6*lw,1).fC('#CBC',0.8).f()
				else
					cp.bP().arc(0,0,240,0.2-i*lw-0.6*lw,0.2-i*lw).arc(0,0,240+array2[le+i*3]-a.minDecibels,0.2-i*lw,0.2-i*lw-0.6*lw,1).fC('#CBC',0.8).f();
			}
			
			// var n = parseInt(l/8),lw = 400/n;
			// for(var i=0;i<n;i++){
				// if(array[i*8]-a.minDecibels<0)
					// cp.bP().mT(-200+i*lw,array[i*8]-a.minDecibels).lT(-200+i*lw,array[i*8]-a.minDecibels-2).lW(2).s();
				// else 
					// cp.bP().mT(-200+i*lw,0).lT(-200+i*lw,array[i*8]-a.minDecibels).lW(2).s();
				
			// }
			cp.bP().mT(-200,0).lT(200,0).sG([-200,0,200,0],'#030','#fff','#90f','#f090f0','#ddd','#d54','#ffc').shCB('#ccc',1).s();
			cp.bP().arc(-200+currentAudioTime/currentAudioLength*400,0,2,0,1).fC('black',1).f();
			cp.bP().arc(-200+currentAudioTime/currentAudioLength*400,0,10,0+ks/120,0.9+ks/120).lT(-200+currentAudioTime/currentAudioLength*400,0).sC('black',1).s();
			
			//var gg=parseInt(array[le]/laz[1][0]*100),gg2=parseInt(laz[1][0]/array[le]*100),gg3=parseInt(laz[1][0]),gg4=parseInt(array[le]);
			var zhen = parseInt(1000/(Date.now()-lastTime));
			lastTime = Date.now();
			j('body p.time').html(parseInt(currentAudioTime/60).t2() + ':' + parseInt(currentAudioTime%60).t2() + ' / '+ parseInt(currentAudioLength/60).t2() + ':' + parseInt(currentAudioLength%60).t2()+' ['+zhen+']');
			//console.log(rf);
		};
		for(var i=0;i<20;i++){rf[i]=0.25+i/20}
		var audioApi = j('.testin').audioApi({canvas:'body canvas',callback:callback,info:'body p.info',endedEvent:function(){
			j('.space').click()
		}});
		audioApi.pause();
		// j( "#slider" ).width(200).slider({
		// 	min: 0,
		// 	max: 100,
		// 	value: 100, 
		// 	slide: function( event, ui ) {
		// 		audioApi.volume(ui.value/100)
		// 	}
		// });
		j('.space,.space2').bind('click',function(){
			j('.space').toggleClass('dn',0);j('.space2').toggleClass('dn',0);
			if(j('.space').hasClass('dn'))audioApi.resume();
			else audioApi.pause();
		});
		j('.last').bind('click',audioApi.last);
		j('.next').bind('click',audioApi.next);
		j(window).bind('click',function(e){
			if(Math.abs(e.clientY-j(window).height()/2)<4 && Math.abs(e.clientX - j(window).width()/2)<200 ){
				var percent = (e.clientX - j(window).width()/2 + 200)/400;
				audioApi.toByPercent(percent)
			}
		})
		window.audioApi=audioApi;
})