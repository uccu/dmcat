

        
j(function(){

    
    var step = (new URL(location)).searchParams.get('step')
    step = parseInt(step)
    step = !step || step<1 || step > 16 ? 24 : step



    if(document.all)document.body.onselectstart= function(){return false;}
    else{
        document.body.onmousedown= function(){return false;}
        document.body.onmouseup= function(){return true;}
    }
    document.onselectstart = new Function('event.returnValue=false;')


	/* 创建audioapi */
	api = new audioApi

    /* 播放暂停 */
    j('.dplay').bind('click',function(){
        var res = this.classList.contains('fa-play') ? api.play() : api.pause()
        if(res)j('.dplay').toggleClass('dn')
    })
    j('.fa-list').bind('click',function(){j('.mlist').toggleClass('dn')})
    j('.fa-plus').bind('click',function(){j('input').click()})
    /* 前进后退 */
    j('.fa-step-backward').bind('click',function(){api.prev()})
    j('.fa-step-forward').bind('click',function(){api.next()})
    /* 进度条 */
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
        j('.nlist li').removeClass('active')
        j('.nlist li').eq(api.playedOne).addClass('active')
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
        api.next()
        api.replay()
    }

	/* 绑定添加歌曲事件 */
    var fileEv = function(g){
        j('.mlist').removeClass('vn')
        g = g || 'input'
        api.addFiles(g)
        j('input').val('')
        var n = 0;
        j('.nlist li').remove();
        api.audioUrls.forEach(function(e){
            j('.nlist').append('<li class="cp row'+(api.playedOne==n?' active':'')+'"><i class="name col-xs-10 to ofh">'+e.name+'</i><i class="col-xs-2 del">x</i></li>')
            n++
        })
        j('.nlist li .col-xs-10').bind('click',function(){
            api.to(j(this).parent().index())
            if(api.ele.paused)j('.fa-play').click()
        })
        j('.nlist li .col-xs-2').bind('click',function(){
            var n = j(this).parent().index()
            var u = api.audioUrls[n]
            URL.revokeObjectURL(u.src)
            api.audioUrls.splice(n,1)
            j(this).parent().remove()
            if(n == api.playedOne){
                if(!api.audioUrls.length){
                    api.playedOne = undefined
                    if(!api.ele.paused)j('.fa-pause').click()
                    api.ele.currentTime = 0
                }else{
                    if(n == api.audioUrls.length)api.playedOne = api.audioUrls.length - 1
                    api.replay()
                }
            }else if(n < api.playedOne){
                api.playedOne--
            }
            
        })
        
    }
	j('input').bind('change',function(){fileEv()})
    j('.mlist').bind({
        'dragenter':function(e){
            e.preventDefault();
            j('.mlist').addClass('vn')
        },'dragleave':function(e){
            e.preventDefault();
            j('.mlist').removeClass('vn')
        },'dragover':function(e){
            e.preventDefault();
        }
    })
    j('.mlist')[0].addEventListener( "drop", function (e) {
        e.preventDefault();
        fileEv(e.dataTransfer.files)
    }, false );
    

    /* 创建场景 */
    var scene = new THREE.Scene();
    scene.fog = new THREE.Fog( 0x101010, 0, 300 );

	/* 创建相机 */
    camera = new THREE.PerspectiveCamera( 75, j(window).width() / j(window).height(), 0.1, 1000 );

	/**
	 * 给window绑定2个事件
	 * 1.鼠标移动变换角度
	 * 2.改变窗口大小
	 */ 
     var rotation = {x:0,y:0}
	j(window).bind({
        keydown:function(e){
            if(e.which == 32)j('.dplay:not(.dn)').click()
            else if(e.which == 77)j('.fa-list').click()
            else if(e.which == 38)api.volume = api.volume < 0.95 ? api.volume + 0.05 : 1;
            else if(e.which == 40)api.volume = api.volume > 0.05 ? api.volume - 0.05 : 0;
            else if(e.which == 39 && api.ele.readyState)api.ele.currentTime = api.ele.currentTime < api.ele.duration - api.ele.duration/100 ? api.ele.currentTime + api.ele.duration / 100 : api.ele.duration;
            else if(e.which == 37 && api.ele.readyState)api.ele.currentTime = api.ele.currentTime > api.ele.duration/100 ? api.ele.currentTime - api.ele.duration / 100 : 0;
        },
        mousemove:function(e){
            rotation.y = (-e.clientX+j(window).width() / 2) / j(window).width() * Math.PI * 0.4
            rotation.x = (-e.clientY+j(window).height() / 2) / j(window).height() * Math.PI * 0.4
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
        
        if(Math.random()<0.5)rand2 = Math.random()<0.5?Math.random()/5+0.3:-Math.random()/5-0.3;
        else rand3 = Math.random()<0.5?Math.random()/5+0.3:-Math.random()/5-0.3;
        


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

    var lineCount = 80,vertices = [];
    for(var i = 0;i<lineCount;i++)vertices.push(new THREE.Vector3())
    var line = new THREE.LineLoop( new THREE.Geometry(), new THREE.LineBasicMaterial( {
        color: 0xff0000,
        transparent: true,
        opacity:0.2,
    }));

    scene.add(line)

    /* 设置boxes */

    boxes = new THREE.Group();
    var center = new THREE.Vector3(0,0,0);
    var geometry = new THREE.BoxGeometry( 1, 1,  4);
    for(var i = 0;i<lineCount;i++){
        var material = new THREE.MeshPhysicalMaterial( {
            color: new THREE.Color( 1,0,0 ),
            transparent: true,
            opacity:0.6,
        });
        var box = new THREE.Mesh( geometry, material );

        boxes.add( box );
    }
    boxes.add(line)
    scene.add(boxes)
    
    


	

    camera.position.z = 100;

    var light = new THREE.AmbientLight( 0xffffff ); // soft white light
    scene.add( light );
    var directionalLight = new THREE.DirectionalLight( 0x00eeee, 0.3 );
    scene.add( directionalLight );
    // var light2 = new THREE.PointLight( 0xff2277, 1, 100 );
    // light2.position.set( 50, 20, 2 );
    // scene.add( light2 );

    var lastTime = 0,lasta1 = lasta2 = [],delay = 0,na = {l:[],r:[]};

    var max = function(a){
        var max = 0
        for(var e in a)max = a[e]>max?a[e]:max
        return max
    }

    api.onrender = function(a1,a2){

        /* 鼠标移动 */
        if(Math.abs(scene.rotation.y-rotation.y)<0.01)scene.rotation.y = rotation.y
        else scene.rotation.y += (rotation.y-scene.rotation.y)/50

        // if(Math.abs(back.rotation.y-rotation.y * 0.5)<0.01)back.rotation.y = rotation.y * 0.5
        // else back.rotation.y += (rotation.y * 0.5-back.rotation.y)/100

        if(Math.abs(scene.rotation.x-rotation.x)<0.01)scene.rotation.x = rotation.x
        else scene.rotation.x += (rotation.x-scene.rotation.x)/50

        /* 背景向上旋转 */
        back.rotateX(0.001);

        /* 计算帧数 */
        var time = Date.now()
        api.fps = parseInt(1000/(time-lastTime))
        j('.test p').text(api.fps)
        lastTime = time;

        /* 延迟 */
        if(delay>0){
            delay--   
            return
        }
        
        
        var changeUp = 0;
        var halfLineCount = lineCount / 2
        
        var baseLength = 30

        if(lasta1.length){
            for(var d in a1)if(d%step)continue;else if(a1[d]>10+lasta1[d])changeUp++;
            if(changeUp>halfLineCount/2)changeUp = 1,delay = 3,line.material.color = new THREE.Color( 0xffffff * Math.random() );
            else changeUp = 0
        }
        
        
        
        for(var i = 0;i<halfLineCount;i+=1){


            var v = Math.pow(1.022,max(a1.slice(i*step,i*step+step)))

            var length = 5 * changeUp + baseLength + v / 10
            if(na.l[i] > length){
                na.l[i] += (length - na.l[i])/10
                // na.l[i] = length;
            }else na.l[i] = length;

            var v = Math.pow(1.022,max(a2.slice(i*step,i*step+step)))
            var length = 5 * changeUp + baseLength + v / 10
            if(na.r[i] > length){
                na.r[i] += (length - na.r[i])/10
            }else na.r[i] = length;


            vertices[i].setFromSpherical(new THREE.Spherical( na.l[i] ,Math.PI * (i+.5) / halfLineCount ,Math.PI * 0.5))
            vertices[lineCount-1-i].setFromSpherical(new THREE.Spherical(na.r[i] ,-Math.PI * (i+.5) / halfLineCount ,Math.PI * 0.5))

            var ver = new THREE.Vector3
            ver.setFromSpherical(new THREE.Spherical( baseLength + 10 ,Math.PI * (i+.5) / halfLineCount ,Math.PI * 0.5))
            boxes.children[i].position.x = ver.x
            boxes.children[i].position.y = ver.y
            boxes.children[i].lookAt(center)
            boxes.children[i].scale.x = (na.l[i] - baseLength - 1) || 0.1
            var ef = na.l[i]/80,ef = ef<0?0:ef>1?1:ef;
            var ef2 = (na.l[i]-20)/100,ef2 = ef2<0?0:ef2>1?1:ef2;
            boxes.children[i].material.color = new THREE.Color( ef ,1-ef,1-ef);
            boxes.children[i].material.opacity = ef2;

            var ver = new THREE.Vector3
            ver.setFromSpherical(new THREE.Spherical(baseLength + 10 ,-Math.PI * (i+.5) / halfLineCount ,Math.PI * 0.5))
            boxes.children[lineCount-1-i].position.x = ver.x
            boxes.children[lineCount-1-i].position.y = ver.y
            boxes.children[lineCount-1-i].lookAt(center)
            boxes.children[lineCount-1-i].scale.x = (na.r[i] - baseLength - 1) || 0.1
            var ef = na.r[i]/80,ef = ef<0?0:ef>1?1:ef;
            var ef2 = (na.r[i]-20)/100,ef2 = ef2<0?0:ef2>1?1:ef2;
            boxes.children[lineCount-1-i].material.color = new THREE.Color(ef ,1-ef,1-ef );
            boxes.children[lineCount-1-i].material.opacity = ef2;
        }

        var curve = new THREE.SplineCurve(vertices);
        var path = new THREE.Path( curve.getPoints( 400 ) );
        line.geometry = path.createPointsGeometry( 400 );

        lasta1 = a1
        lasta2 = a2
            
        line.geometry.verticesNeedUpdate = true
        renderer.render( scene, camera );
    }

            
})