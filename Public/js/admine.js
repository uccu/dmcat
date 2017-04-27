var gr = {};

function curl(url,data,func,raw){

    var a = {
        url:url,
        data:data,
        type:raw || (data && JSON.stringify(data) !="{}")?'post':'get',
        dataType:'json',
        beforeSend:function(xhr){
            
        },success:function(d){
            if(d.code != 200)toastr['error'](d.message);
            else if(typeof func == 'function')func(d.data);
        }
    };
    if(raw){
        a.contentType = false;
        a.processData = false;
    }
    $.ajax(a);
}
function curl_succ(message){
    toastr['success'](message);
}
function curl_check(d){
    toastr['warning']('You cannot roll back this operation. Click to Continue?','',{"onclick": d,"closeButton": true});
}
function gotoNewTag(addr,name){
    var d = window.top.$('.gotoTag a').text(name).attr('href',addr).click();
}
function pagement(page,max,e){
    var p = $('<div>').addClass('btn-group page');
    var pa = [];
    if(max<5)for(var i=1;i<=max;i++)pa.push(i);
    else if(page<3)for(var i=1;i<=5;i++)pa.push(i);
    else if(page+2>max)for(var i=max-4;i<=max;i++)pa.push(i);
    else for(var i=page-2;i<=page+2;i++)pa.push(i);
    $('<div>').addClass('btn btn-white').attr('type','button').append('<i class="fa fa-chevron-left"></i>').on('click',function(){e(1)}).appendTo(p);
    for(r in pa){
        var q = pa[r];
        var j = $('<div>').addClass('btn btn-white').attr('data-id',q);
        if(q == page)j.addClass('active');
        j.text(q).on('click',function(){e($(this).attr('data-id'))}).appendTo(p);
    }
    $('<div>').addClass('btn btn-white').attr('type','button').append('<i class="fa fa-chevron-right"></i>').on('click',function(){e(max)}).appendTo(p);
    return p;
}

function gotoTag(addr,close){
    var e = window.top.$('iframe[data-id="'+addr+'"]');
    var that = window.top.$('.J_menuTab.active i');
    if(e.length)e[0].contentWindow.location.reload(true);
    window.top.$('.J_menuItem[href="'+addr+'"]').click();
    if(close){
        that.click();
    }
}
function packFormData(file,v,x){
    var form=new FormData();
    if(typeof v==="object")
        for(d in v){
            if(typeof v[d]==="object")form.append(v[d].name,v[d].value);
            else form.append(d,v[d]);
        }
    file=$.prototype.isPrototypeOf(file)?file.get(0).files:$(file).get(0).files;
    if(file.length)form.append("file",file[0]);
    else{
        toastr['error']('error image');return
    }
    if(!file[0])return;
    return typeof x==='function'?x(form):form
}
$(function(){
    var upd = function(d,id){
        $('#modal_new .sk-spinner').removeClass('fadeOutUp');
        $('#modal_new [name]').val('');
        curl(d.get,{id:id},function(g){
            $('#modal_new .sk-spinner').addClass('fadeOutUp');
            for(h in g.info){
                $('#modal_new form').find('[name="'+h+'"]').val(g.info[h]).change();
                if(g.info[h]){
                    $('#modal_new form').find('img[data-name="'+h+'"]').attr('src','/pic/'+g.info[h]);
                    $('#modal_new form').find('select[name="'+h+'"]').attr('data-value',g.info[h]);
                }
            }
            $('#modal_new .save').unbind('click').bind('click',function(){
                curl(d.upd,$('#modal_new form').serialize(),function(b){
                    curl_succ('success!');
                    if(typeof gr.saveAfter === 'function')gr.saveAfter(b);
                    else setTimeout('location.reload()',1000)
                })
            });
            if(typeof gr.updFunction === 'function')gr.updFunction(g);
            $('#modal_new').modal();
        });
    };
    window.getList = function(listUrl,gdata){
        $('.ibox2').html('');
        $('.wrapper .sk-spinner').removeClass('fadeOutUp');
        curl(listUrl,gdata,function(d){
            $('.wrapper .sk-spinner').addClass('fadeOutUp');
            var body = $('<div>').addClass('ibox-content text-center animated fadeInUp'),
                head_tr = $('<tr>'),tbody = $('<tbody>'),
                table = $('<div>').addClass('table-responsive').append(
                    $('<table>').addClass('table table-striped').append(
                        $('<thead>').append(head_tr)
                    ).append(tbody)
                ).appendTo(body);
                
            for(e in d.thead){
                var r = $('<th>');
                d.thead[e].class && r.addClass(d.thead[e].class);
                if(e == '_opt')r.text(lang.adminIndex.option);
                else r.text(e.match(/\d*/)[0]?d.thead[e].name:e);
                r.appendTo(head_tr);
            }
            for(e in d.list){
                var tr = $('<tr>');
                for(a in d.tbody){
                    var td = $('<td>').attr('data-id',d.list[e].id);
                    if(a == '_opt'){
                        td.append('<a class="data-upd"><i class="fa fa-pencil text-navy"></i> '+lang.adminIndex.update+' </a><a class="data-del"><i class="fa fa-close text-danger"></i> '+lang.adminIndex.delete+' </a>');
                        td.find('.data-upd').click(function(){
                            $('.updateOnly').removeClass('dn');
                            $('h4.modal-title').text(lang.adminIndex.update);
                            upd(d,$(this).parent().attr('data-id'))
                        });
                        td.find('.data-del').click(function(){
                            var id = $(this).parent().attr('data-id');
                            curl_check(function(){
                                curl(d.del,{id:id},function(){
                                    curl_succ('success!');setTimeout('location.reload()',1000)
                                });
                            })
                        });
                    }else{
                        d.tbody[a].class && td.addClass(d.tbody[a].class);
                        if(d.tbody[a].type === 'checkbox'){
                            if(d.list[e][a] == '1')td.append($('<input>').addClass('i-checks').attr('type','checkbox').attr('checked','checked'));
                            else td.append($('<input>').addClass('i-checks').attr('type','checkbox'));
                        }else td.text(d.list[e][a]);
                    }
                    td.appendTo(tr);
                }
                tr.appendTo(tbody);
            }
            var maxPage = parseInt( ( d.max - 1 )/d.limit ) + 1;
            if(maxPage>1)pagement(d.page,maxPage,function(p){
                gdata.page = p;
                getList(listUrl,gdata);
            }).appendTo(body);
            body.appendTo('.ibox2');
            $('.newOne').unbind('click').bind('click',function(){
                $('.updateOnly').addClass('dn');
                $('h4.modal-title').text(lang.adminIndex.create);
                upd(d,0);
            });
            $('tbody .i-checks').iCheck({checkboxClass:"icheckbox_square-green",radioClass:"iradio_square-green"});
            if(typeof gr.getListAfter === 'function')gr.getListAfter(d);
        })
    };
    window.upPic = function(url,f,i,p){
        $(f).bind('change',function(){
            curl(url,packFormData(f),function(d){
                if(i)$(i).val(d.path);
                if(p)$(p).attr('src',d.fpath);
            },1);
        })
    }
                
})