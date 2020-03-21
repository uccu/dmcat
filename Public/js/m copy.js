j=jQuery.noConflict();
~function(w,j){

    w.folder =location.pathname.split("/").slice(1);
    w.tool = {
        isFunc:function(f){
            return typeof f === 'function'
        },
        post:function(z){
            var a = {
                url:z.url,data:z.data,type:'post',dataType:'json',
                beforeSend:function(xhr){
                    if(w.tool.isFunc(z.bfunc))z.bfunc(xhr);
                },success:function(d){
                    if(d.code != 200)alert(d.message);
                    else if(w.tool.isFunc(z.func))z.func(d.data);
                }
            };
            if(z.raw){
                a.contentType = false;
                a.processData = false;
            }
            return j.ajax(a);
        }
    }
    

}(window,j)