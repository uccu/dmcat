"use strict";
~function(w,d,j){

    var get = function(d){
        var p =new URL(location);
        return p.searchParams.get(d);
    }
    
    var curl = function(url,data,func,raw){
        var a = {
            url:url,data:data,dataType:'json',
            type:raw || (data && JSON.stringify(data) !="{}")?'post':'get',
            success:function(d){
                if(d.code != 200)toastr['error'](d.message);
                else if(func instanceof Function)func(d.data);
            }
        };
        raw && (a.contentType = a.processData = false);
        j.ajax(a);
    }
    var curl_check = function(d){
        toastr['warning']('操作后无法撤销. 是否继续？','',{"onclick": d,"closeButton": true});
    }
    var pagement = function(page,max,e){
        page = parseInt(page);
        max = parseInt(max);
        var p = j('<div>').addClass('btn-group page');
        var pa = [];
        if(max<5)for(var i=1;i<=max;i++)pa.push(i);
        else if(page<3)for(var i=1;i<=5;i++)pa.push(i);
        else if(page+2>max)for(var i=max-4;i<=max;i++)pa.push(i);
        else for(var i=page-2;i<=page+2;i++)pa.push(i);
        j('<div>').addClass('btn btn-white').attr('type','button').append('<i class="fa fa-chevron-left"></i>').on('click',function(){e(1)}).appendTo(p);
        for(var r in pa){
            var q = pa[r],y = j('<div>').addClass('btn btn-white').attr('data-id',q);
            if(q == page)y.addClass('active');
            y.text(q).on('click',function(){e(j(this).attr('data-id'))}).appendTo(p);
        }
        j('<div>').addClass('btn btn-white').attr('type','button').append('<i class="fa fa-chevron-right"></i>').on('click',function(){e(max)}).appendTo(p);
        return p;
    }
    var packFormData = function(file,v,x){
        var form = new FormData();
        if(typeof v==="object")for(d in v)if(typeof v[d]==="object")form.append(v[d].name,v[d].value);else form.append(d,v[d]);
        file = file[0].files;
        if(file.length)form.append("file",file[0]);else{toastr['error']('error image');return}
        if(!file[0])return;
        return typeof x==='function'?x(form):form
    }
    var upPic = function(f,i){
        f.on('change',function(){
            var u = packFormData(f)
            if(u)curl('/home/uploadPic',u,function(d){
                f.parent().find('.picText').val(d.path)
                f.parent().find('img').attr('src',d.fpath)
                if(i instanceof Function)i(d)
            },1);
        })
    }
    var admin = function(u,d,i){
        this.opt = {}
        this.thead = []
        this.req = {}
        !i && this.getList(u,d)
        i && this.getInfo(u,d)
    }

    admin.prototype.getInfo = function(u,d){
        var that = this
        d = d || {};
        that.req = {url:u,param:d}
        curl(u,d,function(m){

            j('.ibox-title h3').html(m.name)
            if(m.opt.back){
                var back = j('<button class="btn btn-default" type="button">关闭并返回</button>');
                back.prependTo('.optBox')
                back.on('click',function(){
                    gotoTag(m.opt.back,1)
                })
            }
            if(m.opt.upd){
                var save = j('<button class="btn btn-primary" type="button">保存</button>');
                save.appendTo('.optBox')
                save.on('click',function(){
                    var data = j('#modal_new form').serializeArray(),k
                    j('#modal_new form .summernote[data-name]').each(function(){data.push({name:j(this).attr('data-name'),value:j(this).summernote('code')})})
                    curl(m.opt.upd,data,function(b){
                        curl_succ('success!');
                        setTimeout(function(){gotoTag(m.opt.back,1)},1000)
                    })
                })
            }
            for(var i in m.tbody){
                var para = m.tbody[i];
                switch(para.type){
                    
                    case 'pic':
                        var pa = j('<div class="form-group" work="'+para.name+'"><label class="col-sm-2 control-label">'+para.title+'</label><div class="col-sm-'+(para.size || 6)+'"><img class="cp picImg" src="/pic/'+(m.info[para.name]||para.default||'nopic.jpg')+'" style="max-width:100%;max-height:100px"><input class="picFile" type="file" accept="image/*" style="display:none"><input class="form-control picText" type="hidden" name="'+para.name+'" value="'+(para.default||'nopic.jpg')+'"></div></div>')
                        upPic(pa.find('.picFile'))
                        pa.find('.picImg').on('click',function(){j(this).parent().find('.picFile').click()})

                        break;
                    case 'select':
                        var pa = j('<div class="form-group" work="'+para.name+'"><label class="col-sm-2 control-label">'+para.title+'</label><div class="col-sm-'+(para.size || 2)+'"><select class="form-control" name="'+para.name+'">'+(function(o){
                            var d = '';
                            for(var q in o)d += '<option value="'+q+'">'+o[q]+'</option>'
                            return d
                        })(para.option)
                        +'<select/></div></div>')
                        pa.find('select').val(m.info[para.name]||para.default||'')
                        break;
                    case 'h5':
                        var pa = j('<div class="form-group" work="'+para.name+'"><label class="col-sm-2 control-label">'+para.title+'</label><div class="col-sm-'+(para.size || 9)+'"><div class="summernote" data-name="'+para.name+'">'+(m.info[para.name]||para.default||'')+'</div></div>');
                        var su = pa.find(".summernote").summernote({
                            toolbar: [
                                ['style', ['style']],
                                ['style', ['bold', 'italic', 'underline', 'clear']],
                                ['fontname',['fontname']],
                                ['fontsize', ['fontsize']],
                                ['color', ['color']],
                                ['height', ['height']],
                                ['para', ['ul', 'ol', 'paragraph']],
                                ['e',['picture','link']],
                                ['o',['codeview']],
                            ],
                            height: 300,
                            minHeight: 300,
                            maxHeight: null,
                            focus: true,
                            lang:"zh-CN",
                            callbacks: {
                                onImageUpload: function(files) {
                                    curl('/home/uploadPic',packFormData(files),function(d){
                                        var imgNode = new Image
                                        imgNode.src = d.fpath
                                        su.summernote('insertNode', imgNode);
                                    },1);
                                }
                            }
                        })
                        break;
                    case 'hidden':
                        var pa = j('<input type="hidden" name="'+para.name+'" value="'+(m.info[para.name]||para.default||'')+'">')
                        break;
                    default:
                        var pa = j('<div class="form-group" work="'+para.name+'"><label class="col-sm-2 control-label">'+para.title+'</label><div class="col-sm-'+(para.size || 6)+'"><input class="form-control" name="'+para.name+'" value="'+(m.info[para.name]||para.default||'')+'"></div></div>')
                        break;
                }
                pa.appendTo('form')
                
            }

        })
    }

    var pagement = function(page,max,e){
        page = parseInt(page);
        max = parseInt(max);
        var p = j('<div>').addClass('btn-group page');
        var pa = [];
        if(max<5)for(var i=1;i<=max;i++)pa.push(i);
        else if(page<3)for(var i=1;i<=5;i++)pa.push(i);
        else if(page+2>max)for(var i=max-4;i<=max;i++)pa.push(i);
        else for(var i=page-2;i<=page+2;i++)pa.push(i);

        j('<div>').addClass('btn btn-white').attr('type','button').append('<i class="fa fa-chevron-left"></i>').on('click',function(){e(1)}).appendTo(p);
        for(var r in pa){
            var q = pa[r],y = j('<div>').addClass('btn btn-white').attr('data-id',q);
            if(q == page)y.addClass('active');
            y.text(q).on('click',function(){e(j(this).attr('data-id'))}).appendTo(p);
        }
        j('<div>').addClass('btn btn-white').attr('type','button').append('<i class="fa fa-chevron-right"></i>').on('click',function(){e(max)}).appendTo(p);
        return p;
    }

    var gotoNewTag = function(addr,name){
        w.top.$('.gotoTag a').text(name).attr('href',addr).click();
    }
    var gotoTag = function(addr,close){
        var e = w.top.$('iframe[data-id="'+addr+'"]'),that = w.top.$('.J_menuTab.active i');
        if(e.length){
            if(e[0].contentWindow.flesh && e[0].contentWindow.flesh instanceof e[0].contentWindow.Function)e[0].contentWindow.flesh()
            else e[0].contentWindow.location.reload(true)
        }
        w.top.$('.J_menuItem[href="'+addr+'"]').click();
        close && that.click();
    }
    
    var curl_succ = function(message){
        toastr['success'](message);
    }
    admin.prototype.flesh = function(){
        this.getList(this.req.url,this.req.param);
    }
    admin.prototype.getList = function(u,d){
        var that = this
        d = d || {};
        that.req = {url:u,param:d}
        curl(u,d,function(m){
            that.opt    =   m.opt
            that.name   =   m.name
            var the=j('<thead>'),tbo=j('<tbody>'),b=j('<div class="ibox-content tc animated fadeInUp"><table class="table table-striped table-responsive">')
            that.thead = m.thead
            that.tbody = m.tbody
            that.list = m.list
            the.append(that.parseThead())
            tbo.append(that.parseTbody())
            j('.mainList').html('').append(b).find('table').append(the).append(tbo)
            var maxPage = parseInt( ( m.max - 1 )/m.limit ) + 1;
            if(maxPage > 1)pagement(m.page,maxPage,function(p){
                that.req.param.page = p;
                that.getList(that.req.url,that.req.param);
            }).appendTo(b);
            if(that.opt.add){
                j('.topw .row').html('')
                j('<div class="col-sm-1 animated fadeInRight"><a class="btn btn-primary btn-outline">新增</a></div>').appendTo('.topw .row').on('click',function(){
                    gotoNewTag(that.opt.add+'?get='+that.opt.get,'新增'+that.name)
                })
            }
            j('.load1').addClass('fadeOutUp')
        })


    }
    admin.prototype.parseThead = function(){
        var tr = j('<tr>')
        if(this.opt.view || this.opt.del)this.thead.push('操作')
        
        for(var e in this.thead){
            var th = j('<th>'),t = this.thead[e];
            if(t instanceof Object);
            else th.addClass('tc').html(t);
            tr.append(th)
        }
        return tr
    }
    admin.prototype.parseTbody = function(){
        
        var trs = [],that = this;
        if(this.opt.view || this.opt.del)this.tbody.push({type:"_opt"})
        
        
        for(var g in this.list){
            var tr = j('<tr>')
            for(var e in this.tbody){
                var td = j('<td>'),t = this.tbody[e];
                if(t instanceof Object){
                    t.class !== undefined ? td.addClass(t.class) : td.addClass('tc')
                    switch(t.type){
                        case '_opt':
                            if(this.opt.view){
                                var view = j('<a><i class="fa fa-pencil text-navy"></i> 查看 </a>').attr('data-id',this.list[g].id);
                                view.on('click',function(){
                                    gotoNewTag(that.opt.view+'?id='+j(this).attr('data-id')+'&get='+that.opt.get,'查看'+that.name)
                                })
                                td.append(view)
                            }
                            if(this.opt.del){
                                var del = j('<a><i class="fa fa-close text-danger"></i> 删除 </a>').attr('data-id',this.list[g].id)
                                del.on('click',function(){
                                    var data = {id:$(this).attr('data-id')}
                                    curl_check(function(){
                                        curl(that.opt.del,data,function(){
                                            curl_succ('success!');
                                        });
                                    })
                                })
                                td.append(del)
                            }
                            break;
                        case 'pic':
                            var ti = this.list[g][t.name];
                            var img = j('<img>').attr('src',ti).css('max-height','50px');
                            if(t.href)td.html(j('<a href="'+ti+'" target="_blank">').html(img));
                            else td.html(img);
                            break;
                        case 'checkbox':
                            var ti = this.list[g][t.name];
                            if(this.opt.upd){
                                var checkbox = j('<input type="checkbox">');
                                checkbox.attr('data-id',this.list[g].id)
                                checkbox.attr('name',t.name)
                                ti == '1' && checkbox.attr('checked','checked');
                                checkbox.appendTo(td).iCheck({checkboxClass: 'icheckbox_square-green',radioClass: 'iradio_square-green'}).on('ifChanged',function(){
                                    var data = {};
                                    data.id = $(this).attr('data-id')
                                    data[$(this).attr('name')] = this.checked?1:0
                                    curl(that.opt.upd,data,function(){
                                        curl_succ('success!');
                                    });
                                });
                                td.find('.icheckbox_square-green').addClass('pr')
                            }else{
                                var checkbox = j('<div class="state icheckbox_square-green">');
                                ti == '1' && checkbox.addClass('checked');
                                
                            }
                            break;
                        default:
                            var ti = this.list[g][t.name];
                            td.html(ti);
                            break;
                    }
                }else{
                    var ti = this.list[g][t];
                    td.addClass('tc').html(ti);
                }
                tr.append(td)
            }
            trs.push(tr)
        }
            
        return trs
    }



    w.Dmcat = w.Dmcat || {};
    w.Dmcat.admin = admin;


}(window,document,$)