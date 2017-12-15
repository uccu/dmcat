


try{

    try{

        JSON.parse('[p[')

    }catch(e){

        throw 'zz'
    }

}catch(e){

    console.log(2)
}
