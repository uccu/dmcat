

        
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
    geometry = new THREE.Geometry();
    var vector1 = new THREE.Vector3();
    var vector2 = new THREE.Vector3();
    var vector3 = new THREE.Vector3();
    vector1.setFromSpherical(new THREE.Spherical( 20, 0, Math.PI * 0.5 ));
    vector2.setFromSpherical(new THREE.Spherical( 30, 0, Math.PI * 0.5 ));
    vector3.setFromSpherical(new THREE.Spherical( 30, Math.PI * 0.2, Math.PI * 0.5 ));
    geometry.vertices.push(
        vector1,vector2,vector3
    )
    var material = new THREE.LineBasicMaterial( {color: 0xffffff} );
    var line = new THREE.LineLoop( geometry, material );
	

	scene.add(line)

    camera.position.z = 100;

    var light = new THREE.AmbientLight( 0xffffff ); // soft white light
    scene.add( light );
    var directionalLight = new THREE.DirectionalLight( 0xffffff, 0.2 );
    scene.add( directionalLight );
    var light2 = new THREE.PointLight( 0xffffff, 1, 100 );
    light2.position.set( 50, 20, 2 );
    scene.add( light2 );

    var n = 0;
    api.onrender = function(a1,a2){


        j('p').text(a1[0])
        geometry.vertices[0].setFromSpherical(new THREE.Spherical( 10,- Math.PI * 0.01 * n++))
        geometry.verticesNeedUpdate = true
        renderer.render( scene, camera );
    }

            
})