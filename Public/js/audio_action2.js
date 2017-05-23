

        
j(function(){
    if(document.all)document.body.onselectstart= function(){return false;}
    else{
        document.body.onmousedown= function(){return false;}
        document.body.onmouseup= function(){return true;}
    }
    document.onselectstart = new Function('event.returnValue=false;')


	/* 创建audioapi */
	api = new audioApi

    j('.dplay').bind('click',function(){
        var res = this.classList.contains('fa-play') ? api.play() : api.pause()
        if(res)j('.dplay').toggleClass('dn')
    })
    j('.fa-list').bind('click',function(){j('input').click()})
    j('.fa-step-backward').bind('click',function(){api.prev()})
    j('.fa-step-forward').bind('click',function(){api.next()})
    

    j('.bar').bind('mousedown',function(e){
        if(!api.ele.readyState)return
        var vo = (e.offsetX/200) * api.ele.duration;
        var u = api.onTimeUpdate
        j('.barr').width(e.offsetX);
        api.onTimeUpdate = null;
        var mm1 = function(z){
            var of  = z.clientX - j('.bar').offset().left
            if(of<0)of = 0;else if(of>200)of=200
            vo = (of/200) * api.ele.duration
            j('.barr').width(of);
        }
        var mu1 = function(z){
            if(api.ele.readyState)api.ele.currentTime = vo
            api.onTimeUpdate = u
            j('body').unbind('mousemove',mm1).unbind('mouseup',mu1)
        }
        j('body').bind({
            mousemove:mm1,mouseup:mu1
        })

    })
    api.onTimeUpdate = function(){
        j('.barr').width(200 * api.ele.currentTime / (api.ele.duration?api.ele.duration:9999));
    }
    api.onload = function(z){
        j('.title p').text(z.name)
    }

    j('.volume').bind('mousedown',function(e){
        var vo = (38-e.offsetY)/38;
        j('.vol').height(e.offsetY);
        api.volume = vo
        var mm = function(z){
            var of  = z.clientY - j('.volume').offset().top
            if(of<0)of = 0;else if(of>38)of=38
            vo = (38-of)/38;
            j('.vol').height(of);
            api.volume = vo
        }
        var mu = function(z){
            j('body').unbind('mousemove',mm).unbind('mouseup',mu)
        }
        j('body').bind({
            mousemove:mm,mouseup:mu
        })
    })
    api.onVolumeChange = function(x){
        j('.vol').height(38-38*x);
    }
    api.ele.onended = function(){
        // j('.fa-pause').addClass('dn');
        // j('.fa-play').removeClass('dn');
        api.next()
        api.replay()
    }

	/* 绑定添加歌曲事件 */
	j('input').bind('change',function(){
        api.addFiles('input')
        // j('.fa-play:not(.dn)').click()
    })

    /* 创建场景 */
    var scene = new THREE.Scene();


	/* 创建相机 */
    camera = new THREE.PerspectiveCamera( 75, j(window).width() / j(window).height(), 0.1, 1000 );

	/**
	 * 给window绑定2个事件
	 * 1.鼠标移动变换角度
	 * 2.改变窗口大小
	 * 
	 */ 
	j(window).bind({
        mousemove:function(e){
            camera.position.x = (e.clientX-j(window).width()/2) / j(window).width() * 10
            camera.position.y = (-e.clientY+j(window).height()/2) / j(window).height() * 10
        },resize:function(){
            camera.aspect = j(window).width() / j(window).height();
            camera.updateProjectionMatrix();
            renderer.setSize( j(window).width(), j(window).height() );
        }
    })

	/* 创建渲染器 */
    var renderer = new THREE.WebGLRenderer();
    j(window).resize();

    j('body').append( renderer.domElement );

	/** 设置基础频谱条 */
    geometry = new THREE.Geometry();

    for(var i = 0;i<512;i++){
        geometry.vertices.push(new THREE.Vector3())
    }

    var material = new THREE.LineBasicMaterial( {color: 0xffffff} );
    var line = new THREE.LineSegments( geometry, material );
	

	scene.add(line)

    camera.position.z = 100;

    // var light = new THREE.AmbientLight( 0xffffff ); // soft white light
    // scene.add( light );
    // var directionalLight = new THREE.DirectionalLight( 0xffffff, 0.2 );
    // scene.add( directionalLight );
    // var light2 = new THREE.PointLight( 0xffffff, 1, 100 );
    // light2.position.set( 50, 20, 2 );
    // scene.add( light2 );

    var lastTime = 0;
    api.onrender = function(a1,a2){

        

        var time = Date.now()
        j('.fps').text(parseInt(1000/(time-lastTime)))
        lastTime = time;
        for(var i = 1;i<256;i+=2){
            geometry.vertices[i].setFromSpherical(new THREE.Spherical( 30+(a1[i*2]) / 10 ,Math.PI * i / 256 ,Math.PI * 0.5))
            geometry.vertices[i-1].setFromSpherical(new THREE.Spherical( 30 ,Math.PI * i / 256 ,Math.PI * 0.5))
            geometry.vertices[511-i].setFromSpherical(new THREE.Spherical( 30+(a2[i*2]) / 10 ,-Math.PI * i / 256 ,Math.PI * 0.5))
            geometry.vertices[511-i+1].setFromSpherical(new THREE.Spherical( 30 ,-Math.PI * i / 256 ,Math.PI * 0.5))
        }
            
        geometry.verticesNeedUpdate = true
        renderer.render( scene, camera );
    }

            
})