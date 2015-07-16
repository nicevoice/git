// JavaScript Document
var domain = '.kuaiji.com/';
var kj_url = 'http://www'+ domain
var app_url = 'http://app'+ domain
var js_url = 'http://static'+domain + 'js/';
var auth = getCookie('auth');

function login(){

    var r = '?redirect=' + encodeURIComponent(location.href);
    var url = 'http://passport' + domain + 'login' + r;
    var html = '<div class="fn-tac pt20">'
        +'<div class="fts14 mt20">您目前状态是游客状态，需要登录才能进行下一步操作哦。</div>'
        +'<div class="mt20 pt20">'
        +'<a href="'+url+'" class="alinkbg"><span>马上注册 / 登录</span></a>'
        +'<a href="javascript:void(0);" style="margin-left: 20px;" class="j-remove alinkbg"><span>谢谢，我先浏览一下</span></a>'
        +'</div>'
        +'</div>'
    exam.dialog({width:400,height:200,titleclass:'fn-dn'}, html);
    return false;
}
function getCookie(n, p) {
    p = p || 'KJ_';
    n = p + '' + n;
    var d = document.cookie.match(new RegExp("(^| )" + n + "=([^;]*)(;|$)"));
    return d != null ? d[2] : '';
}

var exam = {
    dialog : function(param,html) {
        var param = param ? param : {width:617, height:313, titleclass : 'fn-dn'};
        $.dialog(
            {
                width:param.width,
                height:param.height,
                run:true,
                titleclass:param.titleclass,
                html:html
            }
        )
    },
    notes : function(questionid, notesid) {
        var title = $('.question_note_'+questionid).attr('title');
        var notesid = notesid ? notesid : 0;
        var content = notesid ? $('.note_content_'+notesid).text() : '';
        var html = '<div class="editarea"><div class="edittile">';
        html += title + '   笔记：';
        html += '</div><div class="editmain"><textarea data-notesid="'+notesid+'" data-id="'+questionid+'" id="notes_content">'+content+'</textarea></div>';
        html += '<div class="pt10 pb10 fts14" id="maxNumber">还可以输入<b>500</b>个字</div></div>'
        html += '<div class="fn-tar edittool"><input type="button" id="ajaxSubmit" disabled="disabled" value="确定" class="tk-confirm mr15"><input type="button" value="取消" class="tk-cancel"></div>';
        this.dialog({width:720,height:310}, html)
    }

}

$(function(){
    var maxNumber = 500;
    $("#notes_content").live({
        keydown: function() {
            var _this = $(this);
            var extraNumber = maxNumber - _this.val().length;

            if(extraNumber>=0){
                $('#maxNumber').html('还可以输入<b>'+extraNumber+'</b>个字');
            } else {
                $('#maxNumber').html('还可以输入<b style="color: red;">'+extraNumber+'</b>个字');
            }
        },
        mousedown: function() {
            var _this = $(this);
            var extraNumber = maxNumber - _this.val().length;

            if(extraNumber>=0){
                $('#maxNumber').html('还可以输入<b>'+extraNumber+'</b>个字');
            } else {
                $('#maxNumber').html('还可以输入<b style="color: red;">'+extraNumber+'</b>个字');
            }
        },
        focus: function() {
            var _this = $(this);
            var extraNumber = maxNumber - _this.val().length;

            if(extraNumber>=0){
                $('#maxNumber').html('还可以输入<b>'+extraNumber+'</b>个字');
            } else {
                $('#maxNumber').html('还可以输入<b style="color: red;">'+extraNumber+'</b>个字');
            }
        },
        keyup: function() {
            var _this = $(this);
            var extraNumber = maxNumber - _this.val().length;
            if(extraNumber>=0){

                $('#ajaxSubmit').removeAttr('disabled');
            } else {
                $('#ajaxSubmit').attr('disabled', 'disabled');
            }
        }
    });
    $('#ajaxSubmit').live('click', function(){
        var notesid = $('#notes_content').attr('data-notesid');
        var id = $('#notes_content').attr('data-id');
        var content = $('#notes_content').val();
        $.ajax({
            type: "POST",
            url: kj_url+"exam/my/notes_add.html",
            data: "id="+id+"&content="+content+"&notesid="+notesid,
            success: function(info){

                if(notesid > 0){
                    alert('编辑笔记成功！')
                } else {
                    alert('成功添加笔记！')
                }
                $('.question_note_'+id + ' .fc-blue').html(content);
                $('.question_note_'+id + ' .fc-blue').attr('onclick', 'exam.notes('+id+', '+ info +')');
                $('.question_note_'+id + ' .fc-blue').addClass('note_content_'+info);
                $('#close').click();

            }
        });
    })
    $('.tk-cancel').live('click', function(){
        $('#close').click();
    })
    $('.favorite').click(function(){
        if (!auth)return login();
        var qid = $(this).attr('data-id');
        if (qid) {
            $.getJSON(app_url + "exam/my/go_favorite?jsoncallback=?",{qid:qid}, function(json){
                if(json.state) {
                    alert(json.info)
                } else {
                    alert(json.error)
                }
            });
        }
    })
    /**
     * 去除CDN缓存的问题 很难
     *//*
    $('.tk-tmmore').live('click', function(){
        if (!auth)login();
        var subjectid = $('#subjectid').val();
        var knowledgeid = $(this).attr('data-knowledgeid');
        var _rand = Math.random()*10000;
        var _url = app_url + "exam/my/automatic?tt=1&subjectid="+subjectid+"&knowledgeid="+knowledgeid+"&rand="+_rand;
        $(this).attr('href', _url);
    })*/

})

