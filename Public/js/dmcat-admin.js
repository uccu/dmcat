"use strict";
~function (w, d, j) {

    var get = function (d) {
        var p = new URL(location);
        return p.searchParams.get(d);
    }

    var curl = window.curl = function (url, data, func, raw) {
        var a = {
            url: url, data: data, dataType: 'json',
            type: raw || (data && JSON.stringify(data) != "{}") ? 'post' : 'get',
            success: function (d) {
                if (d.code != 200) toastr['error'](d.message);
                else if (func instanceof Function) func(d.data);
            }
        };
        raw && (a.contentType = a.processData = false);
        j.ajax(a);
    }
    var curl_check = function (d) {
        toastr['warning']('操作后无法撤销. 是否继续？', '', { "onclick": d, "closeButton": true });
    }
    var pagement = function (page, max, e) {
        page = parseInt(page);
        max = parseInt(max);
        var g = j('<div class="btn-toolbar page form-inline">')
        var p = j('<div>').addClass('btn-group fn').appendTo(g);
        var pa = [];
        if (max < 5) for (var i = 1; i <= max; i++)pa.push(i);
        else if (page < 3) for (var i = 1; i <= 5; i++)pa.push(i);
        else if (page + 2 > max) for (var i = max - 4; i <= max; i++)pa.push(i);
        else for (var i = page - 2; i <= page + 2; i++)pa.push(i);
        j('<div>').addClass('btn btn-white').attr('type', 'button').append('<i class="fa fa-chevron-left"></i>').on('click', function () { e(1) }).appendTo(p);
        for (var r in pa) {
            var q = pa[r], y = j('<div>').addClass('btn btn-white').attr('data-id', q);
            if (q == page) y.addClass('active');
            y.text(q).on('click', function () { e(j(this).attr('data-id')) }).appendTo(p);
        }
        j('<div>').addClass('btn btn-white').attr('type', 'button').append('<i class="fa fa-chevron-right"></i>').on('click', function () { e(max) }).appendTo(p);

        var p2 = j('<div style="margin-left: 5px;">').addClass('input-group').appendTo(j('<div>').addClass('form-group').appendTo(g));
        j('<input style="width:50px">').addClass('form-control').appendTo(p2);
        j('<span>').addClass('input-group-btn').append(j('<button style="outline:0">Go!</button>').addClass('btn btn-white').attr('type', 'button').on('click', function () { var f = j(this).parent().parent().find('input').val(); if (f > max || f < 1) f = 1; e(f || 1) })).appendTo(p2);
        j('<span class="input-group" style="margin-left:10px;color:#ccc"></span>').text('共' + max + '页').appendTo(p2);
        return g;
    }
    var packFormData = window.packFormData = function (file, v, x) {

        var form = new FormData();
        if (typeof v === "object") for (d in v) if (typeof v[d] === "object") form.append(v[d].name, v[d].value); else form.append(d, v[d]);
        if (file instanceof FileList);
        else file = file[0].files;
        if (file.length) form.append("file", file[0]); else { toastr['error']('error image'); return false }
        if (!file[0]) return;
        return typeof x === 'function' ? x(form) : form
    }
    var upPic = function (f, n, i, b, r) {
        f.on('change', function () {
            if (b instanceof Function) b(f);
            var u = packFormData(f)
            if (u) curl(n, u, function (d) {
                f.parent().find('.picText').val(d.path)
                f.parent().find('img').attr('src', d.fpath)
                if (i instanceof Function) i(d, f)
            }, 1);
            else if (r instanceof Function) r(f)
        })
    }
    var admin = function (u, d, i) {
        this.opt = {}
        this.thead = []
        this.req = {}
        !i && this.getList(u, d)
        i && this.getInfo(u, d)
    }
    var gotoNewTag = window.gotoNewTag = function (addr, name) {
        w.top.$('.gotoTag a').text(name).attr('href', addr).click();
    }
    var gotoTag = function (addr, close) {
        var e = w.top.$('iframe[data-id="' + addr + '"]'), that = w.top.$('.J_menuTab.active i');
        if (e.length) {
            if (e[0].contentWindow.flesh && e[0].contentWindow.flesh instanceof e[0].contentWindow.Function) e[0].contentWindow.flesh()
            else e[0].contentWindow.location.reload(true)
        }
        w.top.$('.J_menuItem[href="' + addr + '"]').click();
        close && that.click();
    }

    var curl_succ = window.curl_succ = function (message) {
        toastr['success'](message);
    }

    admin.prototype.getInfo = function (u, d) {
        var that = this
        d = d || {};
        that.req = { url: u, param: d }

        curl(u, d, function (m) {

            j('.ibox-title h3').html(m.name)
            if (m.opt.back || get('back')) {
                var back = j('<button class="btn btn-default" type="button">关闭并返回</button>');
                back.prependTo('.optBox')
                back.on('click', function () {
                    gotoTag(get('back') || m.opt.back, 1)
                })
            }
            if (m.opt.upd) {
                var save = j('<button class="btn btn-primary" type="button">保存</button>');
                save.appendTo('.optBox')
                save.on('click', function () {
                    var data = j('#modal_new form').serializeArray(), k
                    j('#modal_new form .summernote[data-name]').each(function () { data.push({ name: j(this).attr('data-name'), value: j(this).summernote('code').replace(/<xml>[\s\S]*<\/xml>/ig, '') }) })
                    j('#modal_new form input[useid]').each(function () { data.push({ name: j(this).attr('data-name'), value: j(this).attr('data-id') }) })
                    curl(m.opt.upd, data, function (b) {
                        curl_succ('success!');
                        m.opt.back && setTimeout(function () { gotoTag(m.opt.back, 1) }, 1000)
                    })
                })
            }
            if (m.opt.link) {
                var link = j('<button class="btn btn-' + (m.opt.link.type || 'default') + '" style="margin-left:2px">' + (m.opt.link.name || '') + '</button>');
                link.appendTo('.optBox')
                link.on('click', function () {
                    gotoNewTag(m.opt.link.href || '#', m.opt.link.name || '')
                })

            }
            for (var i in m.tbody) {
                var para = m.tbody[i];
                switch (para.type) {

                    case 'pic':
                        var pa = j('<div class="form-group" work="' + para.name + '"><label class="col-sm-2 control-label">' + para.title + '</label><div class="col-sm-' + (para.size || 3) + '"><img class="cp picImg" src="/pic/' + (m.info[para.name] || para.default || 'nopic.jpg') + '" style="max-width:100%;max-height:100px"><input class="picFile" type="file" accept="image/*" style="display:none"><input class="form-control picText" type="hidden" name="' + para.name + '" value="' + (m.info[para.name] || para.default || 'nopic.jpg') + '"></div>' + (para.description ? '<label class="col-sm-4 control-label" style="text-align:left">' + para.description + '</label>' : '') + '</div>')
                        upPic(pa.find('.picFile'), '/home/uploadPic')
                        pa.find('.picImg').on('click', function () { j(this).parent().find('.picFile').click() })

                        break;
                    case 'pics':
                        var pa = j('<div class="form-group" work="' + para.name + '"><label class="col-sm-2 control-label">' + para.title + '</label><div class="col-sm-' + (para.size || 6) + '">' +
                            (function (p) {
                                var s = '';
                                p = p.split(',');
                                for (var i in p) {
                                    s += '<a href="/pic/' + p[i] + '" target="_blank"><img class="cp picImg" src="/pic/' + p[i] + '" style="max-width:100%;max-height:100px;margin-right: 10px;margin-bottom: 10px;"></a>'
                                }
                                return s

                            })(m.info[para.name] || para.default || 'nopic.jpg')


                            + (para.description ? '<label class="col-sm-4 control-label" style="text-align:left">' + para.description + '</label>' : '') + '</div>')

                        break;
                    case 'picss':
                        var pa = j('<div class="form-group" work="' + para.name + '"><label class="col-sm-2 control-label">' + para.title + '</label><div class="col-sm-' + (para.size || 10) + '">')
                        pa.find('div').append(
                            (function (p) {
                                var s = j('<div>');
                                p = p ? p.split(',') : [];
                                for (var i in p) {
                                    var r = j('<span class="dib pr"><i class="pa cp" style="right:5px;top:-3px;color:#fff;border-radius:50%;background:red;width:12px;height:12px"></i><a href="/pic/' + p[i] + '" target="_blank"><img class="cp picImg" src="/pic/' + p[i] + '" style="max-width:100%;max-height:100px;margin-right: 10px;margin-bottom: 10px;"></a><input type="hidden" name="' + para.name + '[]" value="' + p[i] + '"></span>')
                                    r.find('i').click(function () {
                                        var t = j(this)
                                        curl_check(function () {
                                            t.parent().remove();
                                        })
                                    })
                                    s.append(
                                        r
                                    )
                                }
                                var v = j('<span class="dib pr cp"><a class="btn btn-primary btn-outline">上传</a><input style="display:none" accept="image/*" type="file"></span>')
                                upPic(v.find('input'), '/home/uploadPic', function (d, f) {
                                    var r = j('<span class="dib pr"><i class="pa cp" style="right:5px;top:-3px;color:#fff;border-radius:50%;background:red;width:12px;height:12px"></i><a href="/pic/' + d.path + '" target="_blank"><img class="cp picImg" src="/pic/' + d.path + '" style="max-width:100%;max-height:100px;margin-right: 10px;margin-bottom: 10px;"></a><input type="hidden" name="' + f.parent().parent().parent().parent().attr('work') + '[]" value="' + d.path + '"></span>')
                                    r.find('i').click(function () {
                                        var t = j(this)
                                        curl_check(function () {
                                            t.parent().remove();
                                        })
                                    })
                                    f.parent().before(r)
                                })
                                v.find('a.btn').click(function () {
                                    j(this).parent().find('input').click()
                                })
                                s.append(
                                    v
                                )
                                return s

                            })(m.info[para.name] || para.default || '')


                        )

                        break;
                    case 'file':
                        var pa = j('<div class="form-group" work="' + para.name + '"><label class="col-sm-2 control-label">' + para.title + '</label><div class="col-sm-' + (para.size || 1) + '">' + '<i class="fa fa-plus-square-o cp picImg add" style="font-size:40px"></i>' + '<img class="cp picImg dn img" src="/pic/' + 'file.jpg' + '" style="max-width:100%;max-height:100px;"><input class="picFile" type="file" style="display:none"><input class="form-control picText" type="hidden" name="' + para.name + '" value="' + (m.info[para.name] || para.default || '') + '"></div><label class="col-sm-2 control-label dn del" style="text-align:left"><i class="fa fa-times cp"></i></label>' + (para.description ? '<label class="col-sm-4 control-label" style="text-align:left">' + para.description + '</label>' : '') + '</div>')
                        upPic(pa.find('.picFile'), '/home/uploadFile', function (d, f) {
                            f.parent().find('img').show();
                            f.parent().find('.loading').remove();
                            f.parent().find('.add').hide();
                            f.parent().parent().parent().parent().find('.del').show();
                        }, function (f) {
                            f.parent().find('img').after('<div class="loading sk-spinner sk-spinner-three-bounce"><div class="sk-bounce1"></div><div class="sk-bounce2"></div><div class="sk-bounce3"></div></div>');
                            f.parent().find('img').hide();

                        }, function (f) {
                            f.parent().find('img').show();
                            f.parent().find('.loading').remove();
                        })
                        pa.find('.picImg').on('click', function () { j(this).parent().find('.picFile').click() })
                        pa.find('.del').click(function () {
                            pa.find('img').hide();
                            pa.find('.add').show();
                            pa.find('.del').hide();
                            pa.find('.picText').val('');
                        })
                        if (m.info[para.name]) {
                            pa.find('img').show();
                            pa.find('.add').hide();
                            pa.find('.del').show();
                            pa.find('.picText').val(m.info[para.name]);
                        }
                        break;
                    case 'avatar':
                        var pa = j('<div class="form-group" work="' + para.name + '"><label class="col-sm-2 control-label">' + para.title + '</label><div class="col-sm-' + (para.size || 6) + '"><img class="cp picImg img-circle" src="/pic/' + (m.info[para.name] || para.default || 'noavatar.png') + '" style="max-width:100%;max-height:100px"><input class="picFile" type="file" accept="image/*" style="display:none"><input class="form-control picText" type="hidden" name="' + para.name + '" value="' + (m.info[para.name] || para.default || 'noavatar.png') + '"></div></div>')
                        upPic(pa.find('.picFile'), '/home/upAvatar')
                        pa.find('.picImg').on('click', function () { j(this).parent().find('.picFile').click() })

                        break;
                    case 'select':
                        var pa = j('<div class="form-group" work="' + para.name + '"><label class="col-sm-2 control-label">' + para.title + '</label><div class="col-sm-' + (para.size || 2) + '"><select class="form-control" ' + (para.disabled ? 'disabled' : '') + ' name="' + para.name + '">' + (function (o) {
                            var d = '';
                            for (var q in o) {
                                if (typeof o[q] == 'object') d += '<option value="' + o[q].id + '">' + o[q].value + '</option>'
                                else d += '<option value="' + q + '">' + o[q] + '</option>'
                            }
                            return d
                        })(para.option)
                            + '<select/></div>' + (para.description ? '<label class="col-sm-2 control-label" style="text-align:left">' + para.description + '</label>' : '') + '</div>')
                        pa.find('select').val(m.info[para.name] || para.default || '')
                        break;
                    case 'checkbox':
                        var pa = j('<div class="form-group" work="' + para.name + '"><label class="col-sm-2 control-label">' + para.title + '</label><div class="input-group col-sm-' + (para.size || 2) + '" style="margin-top: 8px;"><input type="checkbox" class="form-control" ' + (para.disabled ? 'disabled' : '') + ' name="' + para.name + '" style="display:none"></div>' + (para.description ? '<label class="col-sm-2 control-label" style="text-align:left">' + para.description + '</label>' : '') + '</div>')
                        if (m.info[para.name] != '0') pa.find('input').attr('checked', 'checked');
                        pa.find('input').iCheck({ checkboxClass: 'icheckbox_square-green', radioClass: 'iradio_square-green' })
                        break;
                    case 'radio':

                        var pa = j('<div class="form-group" work="' + para.name + '"><label class="col-sm-2 control-label">' + para.title + '</label><div class="form-inline col-sm-' + (para.size || 6) + '">' + (function (o) {
                            var d = '';
                            for (var q in o) d += '<label class="control-label cp" style="margin-right:10px"><input class="form-control" ' + (m.info[para.name] == q) + ' type="radio" name="' + para.name + '" value="' + q + '" style="display:none" ' + (para.disabled ? 'disabled' : '') + '>' + o[q] + '</label>'
                            return d
                        })(para.option)
                            + '</div>' + (para.description ? '<label class="col-sm-2 control-label" style="text-align:left">' + para.description + '</label>' : '') + '</div>')
                        var sd = pa.find('input:radio[value="' + m.info[para.name] + '"]');
                        if (sd.length) {
                            sd.attr('checked', 'checked')
                        } else {
                            pa.find('input:radio[value="' + para.default + '"]').attr('checked', 'checked');
                        }
                        pa.find('input').iCheck({ checkboxClass: 'icheckbox_square-green', radioClass: 'iradio_square-green' })
                        break;
                    case 'selects':
                        var url = para.url
                        var pa = $('<div>')
                        for (var i in para.detail) {
                            i = parseInt(i)

                            var pa0 = j('<div class="form-group" url="' + url + '" all="' + (para.detail[i].all ? '1' : '0') + '" work="' + para.detail[i].name + '" work-type="' + para.detail[i].type + '" default="' + (m.info[para.detail[i].name] || para.detail[i].default || 0) + '" next-work="' + (para.detail[i + 1] ? para.detail[i + 1].name : '') + '"><label class="col-sm-2 control-label">' + para.detail[i].title + '</label><div class="col-sm-' + (para.detail[i].size || 2) + '"><select class="form-control" ' + (para.disabled ? 'disabled' : '') + ' name="' + para.detail[i].name + '"><select/></div></div>')
                            if (para.detail[i].type == 'checkboxs') {
                                var sp = pa0.find('select').parent()
                                pa0.find('select').remove()
                                sp.append(j('<input type="hidden" name="' + para.detail[i].name + '">'))
                            }
                            if (pa0.attr('next-work')) {
                                pa0.find('select').change(function () {
                                    var next = j(this).parent().parent().attr('next-work')
                                    var ele = j('[name="' + next + '"]')
                                    var url = ele.parent().parent().attr('url')
                                    var all = ele.parent().parent().attr('all')
                                    var that = this
                                    var defaults = ele.parent().parent().attr('default')
                                    var work_type = ele.parent().parent().attr('work-type')
                                    if (j(that).val() != '0') curl(url, { id: j(that).val() }, function (w) {
                                        if (work_type == 'checkboxs') {
                                            ele.parent().find('label').remove()
                                            ele.parent().append((function (o) {
                                                var d = '';
                                                for (var q in o) d += '<label><input type="checkbox" data-id="' + q + '">' + o[q] + '</label>'
                                                return d
                                            })(w.list))
                                            ele.parent().find('input').click(function () {
                                                var val = [];
                                                ele.parent().find('input:checked').each(function () {
                                                    val.push(j(this).attr('data-id'))
                                                })
                                                ele.val(val)
                                            })
                                            if (defaults) defaults = defaults.split(',')
                                            for (var i in defaults) {
                                                ele.parent().find('input[data-id="' + defaults[i] + '"]').click()

                                            }
                                        } else {
                                            j(ele).html((all == '1' ? '<option value="0">全部</option>' : '<option value="0">请选择</option>') + (function (o) {
                                                var d = '';
                                                for (var q in o) d += '<option value="' + q + '">' + o[q] + '</option>'
                                                return d
                                            })(w.list))
                                            if (j(ele).find('[value="' + defaults + '"]').length) j(ele).val(defaults).change()
                                        }


                                    })

                                })
                            }

                            if (i == 0) {
                                var selects_dd = pa0.find('select')

                                curl(url, {}, function (w) {
                                    var defaults = selects_dd.parent().parent().attr('default')
                                    var all = selects_dd.parent().parent().attr('all')
                                    selects_dd.html((all == '1' ? '<option value="0">全部</option>' : '<option value="0">请选择</option>') + (function (o) {
                                        var d = '';
                                        for (var q in o) d += '<option value="' + q + '">' + o[q] + '</option>'
                                        return d
                                    })(w.list))
                                    if (j(selects_dd).find('[value="' + defaults + '"]').length) j(selects_dd).val(defaults).change()
                                })

                            }
                            pa.append(pa0)
                        }






                        break;
                    case 'option':
                        var pa = j('<div>')
                        for (var i in m.info[para.name]) {
                            var pa2 = j('<div class="form-group" work="' + para.name + '"><label class="col-sm-2 control-label">' + para.title + '</label><div class="col-sm-' + (para.size || 6) + '"><input class="form-control" name="' + para.name + '[]" value="' + m.info[para.name][i].name + '"></div>' + '<label class="col-sm-1 control-label" style="text-align:left"><i class="fa fa-times cp"></i></label>' + '</div>')
                            pa2.find('i').click(function () { j(this).parent().parent().remove() })
                            pa.append(pa2)
                        }
                        var pa2 = j('<div class="form-group" work="' + para.name + '" size="' + (para.size || 6) + '"><label class="col-sm-2 control-label">' + para.title + '</label><label class="col-sm-1 control-label" style="text-align:left"><i class="fa fa-plus cp" style="font-size:20px"></i></label>' + '</div>')
                        pa2.find('i').click(function () {
                            var h = j(this).parent().parent();
                            var pa3 = j('<div class="form-group" work="' + h.attr('work') + '"><label class="col-sm-2 control-label">' + para.title + '</label><div class="col-sm-' + (h.attr('size')) + '"><input class="form-control" name="' + h.attr('work') + '[]"></div>' + '<label class="col-sm-2 control-label" style="text-align:left"><i class="fa fa-times cp"></i></label>' + '</div>')
                            pa3.find('i').click(function () { j(this).parent().parent().remove() })
                            j(this).parent().parent().before(pa3)
                        })
                        pa.append(pa2)

                        break;
                    case 'h5':

                        var pa = j('<div class="form-group" work="' + para.name + '"><label class="col-sm-2 control-label">' + para.title + '</label><div class="col-sm-' + (para.size || 9) + '"><div class="summernote" data-name="' + para.name + '">' + (m.info[para.name] || para.default || '') + '</div></div>');
                        var su = pa.find(".summernote").summernote({
                            toolbar: [
                                ['style', ['style']],
                                ['style', ['bold', 'italic', 'underline', 'clear']],
                                ['fontname', ['fontname']],
                                ['fontsize', ['fontsize']],
                                ['color', ['color']],
                                ['height', ['height']],
                                ['para', ['ul', 'ol', 'paragraph']],
                                ['e', ['picture', 'link']],
                                ['o', ['codeview', 'fullscreen']],
                            ],
                            height: 300,
                            minHeight: 300,
                            maxHeight: null,
                            focus: true,
                            lang: "zh-CN",
                            callbacks: {
                                onImageUpload: function (files) {
                                    curl('/home/uploadPic', packFormData(files), function (d) {
                                        var imgNode = new Image
                                        imgNode.src = d.apath
                                        su.summernote('insertNode', imgNode);
                                    }, 1);
                                }
                            }
                        })
                        break;
                    case 'hidden':
                        var pa = j('<input type="hidden" name="' + para.name + '" value="' + (m.info[para.name] || para.default || '') + '">')
                        break;
                    case 'textarea':
                        var pa = j('<div class="form-group" work="' + para.name + '"><label class="col-sm-2 control-label">' + para.title + '</label><div class="col-sm-' + (para.size || 6) + '"><textarea class="form-control" name="' + para.name + '" style="height:200px" ' + (para.disabled ? 'disabled' : '') + '></textarea></div></div>')
                        pa.find('textarea').val(m.info[para.name] || para.default || '')
                        break;
                    case 'ajax':
                        var pa = j('<div class="form-group" work="' + para.name + '"><label class="col-sm-2 control-label">' + para.title + '</label><div class="col-sm-2"><input class="form-control" refresh="' + (para.refresh ? 1 : 0) + '" data-url="' + para.url + '" value="' + (para.default || '') + '"></div><div class="col-sm-2"><a class="btn btn-primary" >' + para.button + '</a></div></div>')
                        pa.find('a').click(function (e) {
                            e.preventDefault();
                            var url = j(this).parent().parent().find('input').attr('data-url')
                            var r = j(this).parent().parent().find('input').attr('refresh')
                            curl(url, { input: j(this).parent().parent().find('input').val(), id: that.req.param.id }, function () {
                                curl_succ('增加成功！');
                                if (r == '1') setTimeout('location.reload()', 1000)
                            })
                        })
                        break;
                    default:
                        var pa = j('<div class="form-group" work="' + para.name + '"><label class="col-sm-2 control-label">' + para.title + '</label><div class="col-sm-' + (para.size || 6) + '"><input ' + (para.fnPreprocessKeyword ? 'fnpreprocesskeyword="' + para.fnPreprocessKeyword + '"' : '') + ' class="form-control" name="' + para.name + '" ' + (para.disabled ? 'disabled' : '') + ' value="' + (m.info[para.name] || para.default || '') + '"></div>' + (para.description ? '<label class="col-sm-2 control-label" style="text-align:left">' + para.description + '</label>' : '') + '</div>')

                        if (para.suggest) {
                            pa.find('input').after('<div class="input-group-btn"><button type="button" class="btn btn-white dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></button>' + (para.button ? '<button type="button" class="btn btn-white subm">' + para.button + '</button>' : '') + '<ul class="dropdown-menu dropdown-menu-right" role="menu"></ul></div><!-- /btn-group -->');
                            if (para.button && w.Dmcat && w.Dmcat.submFunc) {
                                pa.find('.subm').click(function () {
                                    w.Dmcat.submFunc()
                                })
                            }
                            pa.find('input').attr('data-fields', JSON.stringify(para.fields))
                            if (para.useId) {
                                pa.find('input').attr('data-name', pa.find('input').attr('name'));
                                pa.find('input').attr('useid', 1);
                                pa.find('input').removeAttr('name')
                            }
                            var para33 = para;
                            pa.find('input').bsSuggest({
                                name: para.name,
                                indexId: para.index ? para.index : 0,
                                indexKey: para.index ? para.index : 0,
                                idField: para.idName ? para.idName : 'id',
                                allowNoKeyword: true,
                                multiWord: false,
                                separator: ",",
                                getDataMethod: "url",
                                effectiveFieldsAlias: para.fields,
                                showHeader: false,
                                url: para.suggest,
                                fnPreprocessKeyword: function (a, b, c) {
                                    var f = j('[name="' + b.name + '"]').attr('fnpreprocesskeyword')
                                    if (f) return w[f](a)
                                    else return a
                                },
                                processData: function (json) {
                                    json = json.data;
                                    var i, len, data = { value: [] };
                                    if (!json || !json.list || json.list.length == 0) { return false }

                                    len = json.list.length;
                                    for (i = 0; i < len; i++) {

                                        var obj = {};
                                        for (var s in para33.fields) obj[s] = json.list[i][s]
                                        data.value.push(obj)
                                    }
                                    return data
                                }
                            })
                        }
                        break;
                }
                pa.appendTo('form')

            }

        })
    }



    admin.prototype.flesh = function () {
        this.getList(this.req.url, this.req.param);
    }
    admin.prototype.getList = function (u, d) {
        var that = this
        d = d || {};
        if (!that.req.param) {
            that.req.param = d;
        }
        that.req.url = u
        for (var i in d) {
            that.req.param[i] = d[i]
        }
        j('.load1').removeClass('fadeOutUp')
        j('.mainList').html('')
        curl(u, d, function (m) {

            that.opt = m.opt
            that.name = m.name
            var the = j('<thead>'), tbo = j('<tbody>'), b = j('<div class="ibox-content tc animated fadeInUp"><table class="table table-striped table-responsive">')
            that.thead = m.thead
            that.tbody = m.tbody
            that.list = m.list
            the.append(that.parseThead())
            tbo.append(that.parseTbody())
            j('.mainList').html('').append(b).find('table').append(the).append(tbo)
            var maxPage = parseInt((m.max - 1) / m.limit) + 1;
            if (maxPage > 1) pagement(m.page, maxPage, function (p) {
                that.req.param.page = p;
                that.getList(that.req.url, that.req.param);
            }).appendTo(b);
            j('.topw .row').html('')
            if (that.opt.back || get('back')) {
                j('<div class="col-sm-1 animated fadeInRight"><a class="btn btn-default btn-outline">返回</a></div>').appendTo('.topw .row').on('click', function () {
                    gotoTag(get('back') || m.opt.back, 1)
                })
            }
            if (that.opt.add) {
                j('<div class="col-sm-1 animated fadeInRight"><a class="btn btn-primary btn-outline">' + (that.opt.tname || '新增') + '</a></div>').appendTo('.topw .row').on('click', function () {
                    gotoNewTag(that.opt.add + '?get=' + that.opt.get, (that.opt.tname || '新增') + that.name)
                })
            }
            if (that.opt.button) {
                j('<div class="col-sm-1 animated fadeInRight"><a class="btn btn-primary btn-outline">' + that.opt.button.name + '</a></div>').appendTo('.topw .row').on('click', function () {
                    gotoNewTag(that.opt.button.href, that.opt.button.name)
                })
            }
            if (that.opt.req) {
                for (var i in that.opt.req) {
                    if (!(that.opt.req[i] instanceof Object)) {
                        that.opt.req[i] = { name: that.opt.req[i] }
                    }
                    switch (that.opt.req[i].type) {
                        case 'select':
                            var pa = j('<div class="col-sm-' + (that.opt.req[i].size || 3) + ' animated fadeInRight"><div class="input-group"><span class="input-group-addon">' + that.opt.req[i].title + '</span><select name="' + that.opt.req[i].name + '" class="form-control">' + (function (o) {
                                var d = '';
                                for (var q in o) {
                                    if (typeof o[q] == 'object') d += '<option value="' + o[q].id + '">' + o[q].value + '</option>'
                                    else d += '<option value="' + q + '">' + o[q] + '</option>'
                                }
                                return d
                            })(that.opt.req[i].option) + '</select></div></div>')
                            pa.appendTo('.topw .row')
                            pa.find('select').val(that.req.param[that.opt.req[i].name] || that.opt.req[i].default || '')
                            if (pa.find('select').val() === null) pa.find('select').val(that.opt.req[i].default || '')
                            that.req.param[that.opt.req[i].name] = pa.find('select').val()
                            pa.find('select').change(function () {
                                that.req.param[j(this).attr('name')] = $(this).val()
                                that.req.param.page = 1
                                that.flesh()
                            })
                            break;
                        case 'checkbox':
                            var pa = j('<div class="col-sm-' + (that.opt.req[i].size || 2) + ' animated fadeInRight"><div class="input-group"><span class="input-group-addon">' + that.opt.req[i].title + '</span><div style="padding: 7px 0 3px 8px;border: 1px solid #E5E6E7;width: 37px;"><input type="checkbox"  name="' + that.opt.req[i].name + '" /></div>')
                            pa.appendTo('.topw .row')
                            that.req.param[that.opt.req[i].name] == '1' && pa.find('input').attr('checked', 'checked');
                            pa.find('input').iCheck({ checkboxClass: 'icheckbox_square-green', radioClass: 'iradio_square-green' }).on('ifChanged', function () {
                                that.req.param[j(this).attr('name')] = this.checked ? '1' : '0'
                                that.req.param.page = 1
                                that.flesh()
                            });
                            that.req.param[that.opt.req[i].name] = pa.find('input')[0].checked ? '1' : '0'

                            break;
                        case 'laydate':
                            var pa = j('<div class="col-sm-' + (that.opt.req[i].size || 2) + ' animated fadeInRight"><div class="input-group"><span class="input-group-addon">' + that.opt.req[i].title + '</span><input id="laydate' + that.opt.req[i].name + '" name="' + that.opt.req[i].name + '" type="text" class="form-control"></div></div>')
                            pa.appendTo('.topw .row')
                            pa.find('input').val(that.req.param[that.opt.req[i].name] || that.opt.req[i].default || '')
                            that.req.param[that.opt.req[i].name] = pa.find('input').val()
                            pa.find('input').keyup(function (e) {
                                that.req.param[j(this).attr('name')] = $(this).val()
                                that.req.param.page = 1
                                that.flesh()


                            })
                            laydate({
                                elem: "#laydate" + that.opt.req[i].name, event: "focus", choose: function (e) {
                                    j(this.elem).keyup()
                                }
                            });
                            break;
                        default:
                            var pa = j('<div class="col-sm-' + (that.opt.req[i].size || 2) + ' animated fadeInRight"><div class="input-group"><span class="input-group-addon">' + that.opt.req[i].title + '</span><input name="' + that.opt.req[i].name + '" type="text" class="form-control"></div></div>')
                            pa.appendTo('.topw .row')
                            pa.find('input').val(that.req.param[that.opt.req[i].name] || that.opt.req[i].default || '')
                            that.req.param[that.opt.req[i].name] = pa.find('input').val()
                            pa.find('input').keyup(function (e) {
                                if (e.which == 13) {
                                    that.req.param[j(this).attr('name')] = $(this).val()
                                    that.req.param.page = 1
                                    that.flesh()
                                }

                            })
                            break;
                    }
                }

            }
            j('.load1').addClass('fadeOutUp')
            w.loadListFunc && w.loadListFunc(m)
        })


    }
    admin.prototype.parseThead = function () {
        var tr = j('<tr>')
        if (this.opt.view || this.opt.del) this.thead.push('操作')

        for (var e in this.thead) {
            var th = j('<th>'), t = this.thead[e];
            if (t instanceof Object) {
                if (t.type == 'checkboxs') {
                    var checkbox = j('<input type="checkbox">');
                    checkbox.attr('master-data-name', t.name)
                    checkbox.appendTo(th).iCheck({ checkboxClass: 'icheckbox_square-green', radioClass: 'iradio_square-green' }).on('ifChanged', function () {
                        if (this.checked) {
                            j('[data-name="' + j(this).attr('master-data-name') + '"]').iCheck('check')
                        } else {
                            j('[data-name="' + j(this).attr('master-data-name') + '"]').iCheck('uncheck')
                        }
                    })
                    th.find('.icheckbox_square-green').addClass('pr')
                    th.addClass('tc')
                }
            }
            else {

                th.addClass('tc').html(t);
            } tr.append(th)
        }
        return tr
    }
    admin.prototype.parseTbody = function () {

        var trs = [], that = this;
        if (this.opt.view || this.opt.del) this.tbody.push({ type: "_opt" })


        for (var g in this.list) {
            var tr = j('<tr>')
            for (var e in this.tbody) {
                var td = j('<td>'), t = this.tbody[e];
                if (t instanceof Object) {
                    t.class !== undefined ? td.addClass(t.class) : td.addClass('tc')
                    switch (t.type) {
                        case '_opt':
                            if (this.opt.view) {
                                var view = j('<a><i class="fa fa-pencil text-navy"></i> 查看 </a>').attr('data-id', this.list[g].id);
                                view.on('click', function () {
                                    gotoNewTag(that.opt.view + '?id=' + j(this).attr('data-id') + '&get=' + that.opt.get, '查看' + that.name)
                                })
                                td.append(view)
                            }
                            if (this.opt.del) {
                                var del = j('<a><i class="fa fa-close text-danger"></i> 删除 </a>').attr('data-id', this.list[g].id)
                                del.on('click', function () {
                                    var data = { id: $(this).attr('data-id') }
                                    curl_check(function () {
                                        curl(that.opt.del, data, function () {
                                            curl_succ('success!');
                                            that.flesh()
                                        });
                                    })
                                })
                                td.append(del)
                            }
                            break;
                        case 'pic':
                            var ti = this.list[g][t.name];
                            var img = j('<img>').attr('src', ti).css('max-height', (t.size || 50) + 'px');
                            if (t.href) td.html(j('<a href="' + ti + '" target="_blank">').html(img));
                            else td.html(img);
                            break;
                        case 'checkbox':
                            var ti = this.list[g][t.name];
                            if (this.opt.upd) {
                                var checkbox = j('<input type="checkbox">');
                                checkbox.attr('data-id', this.list[g].id)
                                checkbox.attr('name', t.name)
                                ti == '1' && checkbox.attr('checked', 'checked');
                                checkbox.appendTo(td).iCheck({ checkboxClass: 'icheckbox_square-green', radioClass: 'iradio_square-green' }).on('ifChanged', function () {
                                    var data = {};
                                    data.id = $(this).attr('data-id')
                                    data[$(this).attr('name')] = this.checked ? 1 : 0
                                    curl(that.opt.upd, data, function () {
                                        curl_succ('success!');
                                    });
                                });
                                td.find('.icheckbox_square-green').addClass('pr')
                            } else {
                                var checkbox = j('<div class="state icheckbox_square-green">');
                                ti == '1' && checkbox.addClass('checked');
                                checkbox.appendTo(td)
                            }
                            break;
                        case 'checkboxs':
                            var checkbox = j('<input type="checkbox">');
                            checkbox.attr('data-id', this.list[g].id)
                            checkbox.attr('data-name', t.name)
                            checkbox.appendTo(td).iCheck({ checkboxClass: 'icheckbox_square-green', radioClass: 'iradio_square-green' })
                            td.find('.icheckbox_square-green').addClass('pr')

                            break;
                        default:
                            var ti = this.list[g][t.name];
                            if (!ti && t.default) ti = t.default
                            var ti2 = this.list[g][t.name + '_href'];
                            if (t.href && ti2) {
                                td.html(j('<a data-e="' + ti2 + '">').html(ti));
                                (function (tag) {
                                    td.find('a').click(function () {
                                        gotoNewTag(j(this).attr('data-e'), tag)
                                    })
                                })(t.tagName || '查看');

                            } else if (t.html) td.html(ti);
                            else td.text(ti);
                            break;
                    }
                } else {
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


}(window, document, $)