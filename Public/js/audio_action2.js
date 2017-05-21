

        
j(function(){

	/* 创建audioapi */
	var api = new audioApi

	/* 绑定添加歌曲事件 */
	j('input').bind('change',function(){
        api.addFiles('input').play()
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
    var geometry = new THREE.CylinderGeometry( 1, 1, 1, 3 );
    var material = new THREE.MeshPhysicalMaterial( {color: 0xffffff,opacity:1} );
    spectrumBar = new THREE.Mesh( geometry, material );
	


	scene.add(spectrumBar)

            camera.position.z = 100;

            var light = new THREE.AmbientLight( 0xffffff ); // soft white light
            scene.add( light );

            var directionalLight = new THREE.DirectionalLight( 0xffffff, 0.2 );
            scene.add( directionalLight );

            var light2 = new THREE.PointLight( 0xffffff, 1, 100 );
            light2.position.set( 50, 20, 2 );
            scene.add( light2 );
            var e1 = e2 = 0;
            api.onrender = function(a1,a2){


                j('p').text(a1[0])
                if(a1[0])spectrumBar.scale.y = a1[0]/2

                

                renderer.render( scene, camera );
            }

            
        })