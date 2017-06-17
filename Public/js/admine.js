~function(j,w){
    w.gr = {}
    var curl = w.curl = function(url,data,func,raw){
        var a = {
            url:url,data:data,dataType:'json',
            type:raw || (data && JSON.stringify(data) !="{}")?'post':'get',
            success:function(d){
                if(d.code != 200)toastr['error'](d.message);else if(typeof func == 'function')func(d.data);
            }
        };
        raw && (a.contentType = a.processData = false);
        j.ajax(a);
    }
    w.curl_succ = function(message){
        toastr['success'](message);
    }
    w.curl_check = function(d){
        toastr['warning']('You cannot roll back this operation. Click to Continue?','',{"onclick": d,"closeButton": true});
    }
    w.gotoNewTag = function(addr,name){
        var d = w.top.$('.gotoTag a').text(name).attr('href',addr).click();
    }
    w.pagement = function(page,max,e){
        var p = j('<div>').addClass('btn-group page');
        var pa = [];
        if(max<5)for(var i=1;i<=max;i++)pa.push(i);else if(page<3)for(var i=1;i<=5;i++)pa.push(i);else if(page+2>max)for(var i=max-4;i<=max;i++)pa.push(i);else for(var i=page-2;i<=page+2;i++)pa.push(i);
        j('<div>').addClass('btn btn-white').attr('type','button').append('<i class="fa fa-chevron-left"></i>').on('click',function(){e(1)}).appendTo(p);
        for(r in pa){
            var q = pa[r],y = j('<div>').addClass('btn btn-white').attr('data-id',q);
            if(q == page)y.addClass('active');
            y.text(q).on('click',function(){e(j(this).attr('data-id'))}).appendTo(p);
        }
        j('<div>').addClass('btn btn-white').attr('type','button').append('<i class="fa fa-chevron-right"></i>').on('click',function(){e(max)}).appendTo(p);
        return p;
    }
    w.gotoTag = function(addr,close){
        var e = w.top.$('iframe[data-id="'+addr+'"]'),that = w.top.$('.J_menuTab.active i');
        if(e.length){
            if(e[0].contentWindow.gr && e[0].contentWindow.gr.flesh instanceof e[0].contentWindow.Function)e[0].contentWindow.gr.flesh()
            else e[0].contentWindow.location.reload(true)
        }
        w.top.$('.J_menuItem[href="'+addr+'"]').click();
        close && that.click();
    }
    w.packFormData = function(file,v,x){
        var form = new FormData();
        if(typeof v==="object")for(d in v)if(typeof v[d]==="object")form.append(v[d].name,v[d].value);else form.append(d,v[d]);
        if(file instanceof FileList);else file=j.prototype.isPrototypeOf(file)?file.get(0).files:j(file).get(0).files;
        if(file.length)form.append("file",file[0]);else{toastr['error']('error image');return}
        if(!file[0])return;
        return typeof x==='function'?x(form):form
    }

    w.cget = function(get,id,upd,er){
        curl(get,{id:id},function(g){
            j('#modal_new .sk-spinner').addClass('fadeOutUp');
            for(h in g.info){
                j('#modal_new form').find('[name="'+h+'"]').val(g.info[h]).change();
                if(g.info[h]){
                    j('#modal_new form').find('img[data-name="'+h+'"]').attr('src','/pic/'+g.info[h]);
                    j('#modal_new form').find('select[name="'+h+'"]').attr('data-value',g.info[h]);
                }
                j('#modal_new form').find('.summernote[data-name="'+h+'"]').each(function(){j(this).summernote('code',g.info[h])})
            }
            j('#modal_new .save').unbind('click').bind('click',function(){
                var data = j('#modal_new form').serializeArray(),k
                j('#modal_new form .summernote[data-name]').each(function(){data.push({name:j(this).attr('data-name'),value:j(this).summernote('code')})})
                curl(upd,data,function(b){
                    curl_succ('success!');
                    j('.modal .sr-only').click()
                    if(typeof gr.saveAfter === 'function')gr.saveAfter(b);else if(gr.flesh instanceof Function)gr.flesh();else setTimeout('location.reload()',1000)
                })
            });
            if(typeof gr.updFunction === 'function')gr.updFunction(g);
            er || j('#modal_new').modal();
        });
    }
    var upd = function(d,id){
        j('#modal_new .sk-spinner').removeClass('fadeOutUp');
        j('#modal_new [name]').val('');
        if(gr.updBefore instanceof Function)gr.updBefore(id);
        cget(d.get,id,d.upd);
    };
    w.getList = function(listUrl,gdata){
        if(gdata ==undefined)gdata = {}
        j('.ibox2').html('');
        j('.wrapper .sk-spinner').removeClass('fadeOutUp');
        curl(listUrl,gdata,function(d){
            j('.wrapper .sk-spinner').addClass('fadeOutUp');
            var body = j('<div>').addClass('ibox-content text-center animated fadeInUp'),
                head_tr = j('<tr>'),tbody = j('<tbody>'),
                table = j('<div>').addClass('table-responsive').append(
                    j('<table>').addClass('table table-striped').append(
                        j('<thead>').append(head_tr)
                    ).append(tbody)
                ).appendTo(body);
                
            for(e in d.thead){
                var r = j('<th>');
                d.thead[e].class && r.addClass(d.thead[e].class);
                if(e == '_opt')r.text(lang.adminIndex.option);
                else r.text(e.match(/\d*/)[0]?d.thead[e].name:e);
                r.appendTo(head_tr);
            }
            for(e in d.list){
                var tr = j('<tr>');
                for(a in d.tbody){
                    var td = j('<td>').attr('data-id',d.list[e].id);
                    if(a == '_opt'){
                        td.append('<a class="data-upd"><i class="fa fa-pencil text-navy"></i> '+lang.adminIndex.update+' </a><a class="data-del"><i class="fa fa-close text-danger"></i> '+lang.adminIndex.delete+' </a>');
                        if(d.tbody[a].updateLink)td.find('.data-upd').attr('data-href',d.upd+'?id='+d.list[e].id);
                        td.find('.data-upd').click(function(){
                            j('.updateOnly').removeClass('dn');
                            j('h4.modal-title').text(lang.adminIndex.update);
                            if(j(this).attr('data-href'))gotoNewTag(j(this).attr('data-href'),lang.adminIndex.update)
                            else upd(d,j(this).parent().attr('data-id'))
                        });
                        td.find('.data-del').click(function(){
                            var id = j(this).parent().attr('data-id');
                            curl_check(function(){
                                curl(d.del,{id:id},function(){
                                    curl_succ('success!');setTimeout(function(){
                                        if(gr.flesh instanceof Function)gr.flesh()
                                        else location.reload()
                                    },1000)
                                });
                            })
                        });
                    }else if(d.tbody[a].type == 'imga'){
                        td.append(j('<a>').attr({'href':d.list[e][a],'target':'_blank'}).append(j('<img>').addClass('img-m').attr('src',d.list[e][a])));

                    }else{
                        d.tbody[a].class && td.addClass(d.tbody[a].class);
                        if(d.tbody[a].type === 'checkbox'){
                            if(d.list[e][a] == '1')td.append(j('<input>').addClass('i-checks').attr('type','checkbox').attr('checked','checked'));
                            else td.append(j('<input>').addClass('i-checks').attr('type','checkbox'));
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
            j('.newOne').unbind('click').bind('click',function(){
                j('.updateOnly').addClass('dn');
                j('h4.modal-title').text(lang.adminIndex.create);
                if(j(this).attr('data-href'))gotoNewTag(j(this).attr('data-href'),lang.adminIndex.create)
                else upd(d,0);
            });
            j('tbody .i-checks').iCheck({checkboxClass:"icheckbox_square-green",radioClass:"iradio_square-green"});
            if(typeof gr.getListAfter === 'function')gr.getListAfter(d);
        })
    }
    w.upPic = function(url,f,i,p){
        j(f).bind('change',function(){
            var u = packFormData(f)
            if(u)curl(url,u,function(d){
                if(typeof i ==="function")i(d);
                else if(i)j(i).val(d.path);
                if(p)j(p).attr('src',d.fpath);
            },1);
        })
    }

    j('select.ajax').each(function(){
        var that=j(this),url = that.attr('data-list');
        if(url)curl(url,{},function(d){
            if(d.list.length){
                for(r in d.list)that.append('<option value="'+d.list[r].id+'">'+d.list[r].name+'</option>')
                that.val(d.list[0].id)
            }
        })
    })

}($,window)
