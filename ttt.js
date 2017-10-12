"use strict";




// /** 汉诺塔 
//  * mu
//  * @return mixed 
//  */
// function mu(n,a,b,c){

//     if(n == 1){

//         console.log('from ' + a + ' to ' + b)
//         return
//     }

//     mu(n-1,a,c,b)

//     console.log('from ' + a + ' to ' + b)

//     mu(n-1,c,b,a)

// }


// mu(3,'A','B','C')




/** 全排列
 * pi
 * @return mixed 
 */
function pi(a = []){

    if(!a.length)return [[]]

    let t = []

    for(let i = 0;i<a.length;i++){

        let p = pi(a.slice(0,i).concat(a.slice(i+1)))
        
        for(let j in p){
            p[j].unshift(a[i])
        }
        t = t.concat(p)
    }

    return t

}


console.log(pi([1,2,3,4,5]))