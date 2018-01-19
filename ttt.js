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




// /** 全排列
//  * pi
//  * @return mixed 
//  */
// function pi(a = []){

//     if(!a.length)return [[]]

//     let t = []

//     for(let i = 0;i<a.length;i++){

//         let p = pi(a.slice(0,i).concat(a.slice(i+1)))
        
//         for(let j in p){
//             p[j].unshift(a[i])
//         }
//         t = t.concat(p)
//     }

//     return t

// }


// console.log(pi([1,2,3,4,5]))



/** 最长公共子序列 */
function z(a = '',b = ''){

    if(a == '' || b == '')return '';
    
    var g = '';
    for(var i = 0;i<a.length;i++){

        var f = a.substr(i,1),e = b.search(f);
        if(e === -1)continue;

        var u = f + z( a.substr( i+1 ),b.substr( e+1 ) );
        if( u.length > g.length )g = u;
       
    }
    return g

}

var w = z('wwwqqabwcd','dawbfcqdc');

console.log(w)



