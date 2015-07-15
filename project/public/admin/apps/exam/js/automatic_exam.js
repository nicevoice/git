var automatic = {
    add_tr : function(h, i){

        var tr = $('#'+h +' tr:last');
        var s = tr.attr('val');
        ++s;
        if (!s)s = 1;
        var html = '<tr id="'+h+s+'" val="'+s+'"><td class="t_c" width="30" style="cursor:move">'
            +   '<input id="'+h+'_'+s+'_id" width="" class="selectree" name="'+h+'[]" value="" initurl="?app=system&controller=property&action=name&proid=%s" url="?app=system&controller=property&action=cate&issingle=1&isonly='+i+'&proid=%s" paramVal="proid" paramTxt="name" />'
            +   '<script>$(\'#'+h+'_'+s+'_id\').selectree();</script></td>'
            +   '<td width="29" style="padding:5px 0px;text-align:center"><img src="images/del.gif" height="16" width="16" alt="删除" title="删除" onclick="automatic.remove_tr(\''+h+s+'\')" class="hand"></td></tr>'

        $('#'+h).append(html)
    },
    add_qtype : function(h, i){

        var tr = $('#'+h +' .qtypelist:last');
        var s = tr.attr('val');
        ++s;
        if (!s)s = 1;
        var html = '<tr class="qtypelist" id="'+h+s+'" val="'+s+'"><td class="t_c" width="30" style="cursor:move">'
            +   '<input id="'+h+'_'+s+'_id" width="" class="selectree" name="'+h+'['+s+'][id]" value="" initurl="?app=system&controller=property&action=name&proid=%s" url="?app=system&controller=property&action=cate&issingle=1&isonly='+i+'&proid=%s" paramVal="proid" paramTxt="name" />'
            +   '<script>$(\'#'+h+'_'+s+'_id\').selectree();</script></td>'
            +   '<td><input type="text" value="" size="30" name="'+h+'['+s+'][alias]"></td>'
            +   '<td><input type="text" value="" size="5" name="'+h+'['+s+'][num]"></td>'
            +   '<td width="29" style="padding:5px 0px;text-align:center"><a href="javascript:void(0);" onclick="automatic.seeexam('+s+')" >查看</a> <a href="javascript:void(0);" onclick="automatic.addexam('+s+')" >添加</a> <img src="images/del.gif" height="16" width="16" alt="删除" title="删除" onclick="automatic.remove_tr(\''+h+s+'\')" class="hand"></td></tr>'

        $('#'+h).append(html)
    },
    add_qtype2 : function(h, i){

        var tr = $('#'+h +' tr:last');
        var s = tr.attr('val');
        ++s;
        if (!s)s = 1;
        var html = '<tr id="'+h+s+'" val="'+s+'"><td class="t_c" width="30" style="cursor:move">'
            +   '<input id="'+h+'_'+s+'_id" width="" class="selectree" name="'+h+'['+s+'][id]" value="" initurl="?app=system&controller=property&action=name&proid=%s" url="?app=system&controller=property&action=cate&issingle=1&isonly='+i+'&proid=%s" paramVal="proid" paramTxt="name" />'
            +   '<script>$(\'#'+h+'_'+s+'_id\').selectree();</script></td>'
            +   '<td><input type="text" value="" size="30" name="'+h+'['+s+'][alias]"></td>'
            +   '<td><input type="text" value="" size="5" name="'+h+'['+s+'][num]"></td>'
            +   '<td width="29" style="padding:5px 0px;text-align:center"><img src="images/del.gif" height="16" width="16" alt="删除" title="删除" onclick="automatic.remove_tr(\''+h+s+'\')" class="hand"></td></tr>'

        $('#'+h).append(html)
    },
    addexam : function(s) {
        var qtypeid = $('#qtype_'+s+'_id').val();
        var subjectid = $('#typeid_referto').val();
        if(!qtypeid){
            alert('请选择类型');
            return
        }
        if(!subjectid){
            alert('请选择栏目扩展->科目');
            return
        }

        ct.form('添加题目', '?app=exam&controller=question&action=serach&s='+s +'&qtypeid='+qtypeid+'&subjectid='+subjectid, 800, 600, function (response){
            if (response.state)
            {
                if (response.question) {
                    n = response.question;
                    var html = '';

                    if (!$('#eaxmlist_'+s).size()) {
                        $('#qtype'+s).after('<tr id="eaxmlist_'+s+'" style="display:none;"><td colspan="4"></td></tr>');
                    }
                    for (i in n ){
                        if ($('#q_dd_'+n[i].questionid+'_'+s).html() == null)html += '<dd id="q_dd_'+n[i].questionid+'_'+s+'">'+n[i].subject + '<input type="hidden" value="'+ n[i].questionid+'" name="qtype['+s+'][qids][]"><a style="margin-left: 10px;" href="javascript:void(0);" onclick="automatic.remove_tr(\'q_dd_'+ n[i].questionid+'_'+s+'\')">删除</a></dd>';
                    }
                    if (html)$('#eaxmlist_'+s +' td').append(html);
                }
                return true;
            }
            else
            {
                ct.error(response.error);
            }
        }, function (dialog){

        });
    },
    seeexam : function (i) {

        if ($('#eaxmlist_'+i).attr('rel')) {
            $('#eaxmlist_'+i).removeAttr('rel')
            $('#eaxmlist_'+i).hide();
        } else  {
            $('#eaxmlist_'+i).attr('rel', 1)
            $('#eaxmlist_'+i).show();
        }
    },
    selexam : function(id, i) {
        var subject = $('#row_'+id).attr('subject');
        if ($('#sel_dd_'+id).html())return
        var html = '<dd id="sel_dd_'+id+'">'+subject+' <a href="javascript:void(0);" style="margin-left: 10px;" onclick="automatic.remove_tr(\'sel_dd_'+id+'\')">删除</a> <input type="hidden" value="'+id+'" name="question[]"></dd>';
        $('#sel_id_'+i).append(html);
    },
    remove_tr : function(s, t){
        var t  = t ? t.split('_') : 0
        ct.confirm('您真的要删除?',function(){
        if (t) $.get('?app=exam&controller=exam&action=edel&id='+t[1]+'&t='+t[0], function (html){});
        $('#'+s).remove();
        })
    }
}



var runautomatic = {
    del : function(){
        var mid;
        mid = tableApp.checkedIds();
        if(mid.length<1) {
            ct.warn('请选择需要处理的信息！');
            return;
        } else {
            var msg = '确定通知选中的<b style="color:red">'+mid.length+'</b>条记录吗？';
            ct.confirm(msg,function(){
                $.get('?app=exam&controller=automatic_exam&action=del',{mid:mid.join(',')},function(json){
                    json.state
                        ? (ct.ok('操作完毕'), tableApp.load())
                        : ct.error(json.error);
                },'json');
            }).dialog('option','width',360);
        }
    },
    createExam : function(_c){
        if (_c == undefined) {
            _c = tableApp.checkedIds();
            if (_c.length === 0)
            {
                ct.warn('请选择要操作的记录');
                return false;
            }
            runautomatic._createExam(_c, 0);
            return false;
        }
        $.getJSON('?app=exam&controller=automatic_exam&action=cexam&id='+_c, function(response){
            if (response.state) {
                ct.ok('操作成功');
            } else {
                ct.error(response.error);
            }
        })

    },
    _createExam : function(_c, key){
        $.getJSON('?app=exam&controller=automatic_exam&action=cexam&id='+_c[key], function(response){
            if (response.state) {
                $('#row_'+_c[key]).removeClass('row_chked');
                $('#chk_row_'+_c[key]).attr('checked', false);
                key++;
                if (_c.length > key){
                    runautomatic._createExam(_c, key);
                }else{
                    $('#check_box').attr('checked', false);
                    ct.ok('操作成功');
                }
            } else {
                ct.error(response.error);
            }
        })

    }
}