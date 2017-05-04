!function(){
    var link = 'ws:45.35.251.11:7777/',uid,cid,ws;
    var mesCore = {
        
        user_login:function(obj){
            if(obj.status==200)uid = obj.uid;
            console.log('登陆成功~');
            j('.login').addClass('channel').css({height:0,transform:'perspective(1000px) rotateY(90deg)','transform-origin': 'left'});
            j('.overlay>.back').removeClass('active');
            
            setTimeout(function(){
                j('.overlay>.back').addClass('active');                
                j('.login>div').addClass('dn');
                j('.login>div.channel').removeClass('dn');
                j('.login').addClass('channel').css({height:200,transform:'scale(2) rotate(-5deg)','transform-origin': 'center'});
            },650)


        },
        channel_login:function(obj){
            if(obj.status==200)cid = obj.cid;
            console.log('进入成功~');
            j('.overlay>.back').addClass('dark');
            j('.login').css({height:0,transform:'perspective(1000px) rotateY(90deg)','transform-origin': 'left'});
            setTimeout(function(){
                j('.login').addClass('dn');
                j('input[type=file],video').removeClass('dn');
            },1000)
        },
        channel_message:function(obj){
            console.log(obj.nickname+':'+obj.message);
        },
        video_sync:function(obj){
            if(obj.uid==uid)return;
            var time = obj.time+obj.offset;
            var v = j('video')[0];
            v.currentTime = time;
            if(obj.paused && !v.paused){
                // j('video').unbind('pause');
                v.pause();
            }else if(!obj.paused && v.paused){
                //j('video').unbind('play pause');
                v.play();
                // j('video').one('playing',function(){
                //     j('video').bind('play pause',function(){
                //         sync()
                //     });
                // });
            }
        },

    };

    var connectF = function(c){
        if(ws)return;
        
        ws = new WebSocket(link);
        window.ws=ws;
        ws.onopen = d=>{console.log('连接成功~');if(c && typeof c === 'function')c(ws)}
        ws.onmessage = d=>{
            try{
                var v = JSON.parse(d.data);
                if(typeof v !=='object')return;
                if(v.type && typeof mesCore[v.type] ==='function')mesCore[v.type](v);
            }catch(e){
                console.log(e.message);
            }
        };
        ws.onerror = d=>{ws = undefined;console.log(d)};
        ws.onclose = d=>{ws = undefined;console.log(d)};
        ws.sendMessageToChannel = function(x){
            var obj = {type:'channel_message',message:x,cid:cid};
            ws.send(JSON.stringify(obj));
        }
    };
    j(function(){
        connectF(function(){
            j('.login').addClass('active');
            j('.overlay>.back').addClass('active');
        });
    })



    j('.login_a').bind('click',function(){
        if(!ws){alert('没有连接');return}
        if(!ws.readyState){alert('未准备就绪');return}
        j(this).unbind('click').addClass('disabled');
        j('#password,#uid,#nickname,#avatar').attr('disabled','disabled');
        var g = {type:'user_login',nickname:j('#nickname').val(),password:j('#password').val(),'uid':j('#uid').val(),avatar:j('#avatar').val()};
        ws.send(JSON.stringify(g));


    });

    j('.channel_a').bind('click',function(){
        if(!uid){alert('未登录');return}
        j(this).unbind('click').addClass('disabled');
        j('#channel').attr('disabled','disabled');
        var g = {type:'channel_login',cid:j('#cid').val()};
        ws.send(JSON.stringify(g));


    });

    j('.info').bind('click',function(){
        var g = {type:'user_info',uid:uid};
        ws.send(JSON.stringify(g));
    });
    

    
    var sync = function(){
        var v = j('video')[0];
        var g = {type:'video_sync',cid:j('#cid').val(),time:v.currentTime,paused:v.paused?1:0};
        ws.send(JSON.stringify(g));
    };


    j('video').bind('play pause',function(e){
        //console.log(e.target.seeking)
        if(e.type==='pause' && e.target.seeking)return;
        sync()
    });

    //j('video')[0].controls = true;
    var play = function(f){
        //var f = j('input[type=file]')[0].files[0];
        if(!f)return;
        var u = URL.createObjectURL(f);
        j('video')[0].src = u;
        j('video').one('canplay',function(){j('video')[0].play();});
        
    };

    j('input[type=file]').bind('change',function(){
        play(j(this)[0].files[0])
    });

    j(document).on({
		dragleave:function(e){e.preventDefault()}, 
		drop:function(e){e.preventDefault()}, 
		dragenter:function(e){e.preventDefault()}, 
		dragover:function(e){e.preventDefault()} 
	});
    j(".overlay").bind({
        drop:function(e){
            e.preventDefault();
            console.log(e);
            play(e.originalEvent.dataTransfer.files[0]);
            j('.overlay>.back').removeClass('drag');
        },
        dragover:function(e){
				j('.overlay>.back').addClass('drag');
        },
		dragleave:function(e){
            j('.overlay>.back').removeClass('drag');
        },

    });







}()