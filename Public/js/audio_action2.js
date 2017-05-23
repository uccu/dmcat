

        
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
    var fog = new THREE.Fog( 0x101010, 0, 300 )
    scene.fog = fog;
    window.line;

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
            line.rotation.y = (-e.clientX+j(window).width() / 2) / j(window).width() * Math.PI * 0.4
            line.rotation.x = (-e.clientY+j(window).height() / 2) / j(window).height() * Math.PI * 0.4
        },resize:function(){
            camera.aspect = j(window).width() / j(window).height();
            camera.updateProjectionMatrix();
            renderer.setSize( j(window).width(), j(window).height() );
        }
    })

	/* 创建渲染器 */
    var renderer = new THREE.WebGLRenderer();
    renderer.setClearColor( 0x101010 );
	renderer.setPixelRatio( window.devicePixelRatio );
    j(window).resize();

    j('body').append( renderer.domElement );


    /* 最大三角数量 */
    var triangles = 400;
	var geometry = new THREE.BufferGeometry();
	var vertices = new Float32Array( triangles * 3 * 3 );
	for ( var i = 0, l = triangles * 3 * 3; i < l; i += 9 ) {

        var rand1 = 600*(Math.random()-0.5);
        var rand2 = Math.random()-0.5;
        var rand3 = Math.random()-0.5;
        if(rand2<100 && rand3<100){

            if(Math.random()<0.5){
                rand2 = Math.random()<0.5?Math.random()/5+0.3:-Math.random()/5-0.3;
            }else{
                rand3 = Math.random()<0.5?Math.random()/5+0.3:-Math.random()/5-0.3;
            }

        }


		vertices[ i     ] = rand1;
		vertices[ i + 1 ] = 400*rand2;
		vertices[ i + 2 ] = 400*rand3;

        vertices[ i + 3 ] = vertices[ i     ] + 50*(Math.random() - 0.5);
		vertices[ i + 4 ] = vertices[ i + 1 ] + 50*(Math.random() - 0.5);
		vertices[ i + 5 ] = vertices[ i + 2 ] + 50*(Math.random() - 0.5);

        vertices[ i + 6 ] = vertices[ i     ] + 50*(Math.random() - 0.5);
		vertices[ i + 7 ] = vertices[ i + 1 ] + 50*(Math.random() - 0.5);
		vertices[ i + 8 ] = vertices[ i + 2 ] + 50*(Math.random() - 0.5);
    }
    geometry.addAttribute( 'position', new THREE.BufferAttribute( vertices, 3 ) );

    var colors = new Uint8Array( triangles * 3 * 4 );
    for ( var i = 0, l = triangles * 3 * 4; i < l; i += 4 ) {
		colors[ i     ] = Math.random() * 255;
		colors[ i + 1 ] = Math.random() * 255;
		colors[ i + 2 ] = Math.random() * 255;
		colors[ i + 3 ] = Math.random() * 255;
	}
    geometry.addAttribute( 'color', new THREE.BufferAttribute( colors, 4, true ) );
    
    var material = new THREE.MeshBasicMaterial( {
		
		side: THREE.DoubleSide,
		transparent: true,
        opacity:0.1,
        vertexColors:THREE.FaceColors
	} );


    back = new THREE.Mesh( geometry, material );
    back.position.z = 100
    scene.add( back );






















	/** 设置基础频谱条 */
    var geometry = new THREE.Geometry();

    for(var i = 0;i<512;i++){
        geometry.vertices.push(new THREE.Vector3())
    }

    var material = new THREE.LineBasicMaterial( {color: 0xffffff} );
    line = new THREE.LineSegments( geometry, material );
	

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

        var time = performance.now();
        back.rotateX(0.001);

        var time = Date.now()
        api.fps = parseInt(1000/(time-lastTime))
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