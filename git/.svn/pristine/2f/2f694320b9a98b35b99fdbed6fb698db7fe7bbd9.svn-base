// JavaScript Document

$(function(){
    /*考试倒计时*/

    var reduceTime='';
    function reduce(){
        var sec = parseInt($('#second').html())
        var minutes = $('#minutes').html()
        if(minutes==0&&sec==0){
            clearInterval(reduceTime);
            return ;
        }
        if(sec == 0){
            sec=60
            var allTime = $('#minutes').html()
            $('#minutes').html(--allTime);
            $('#second').html(--sec)
        }else{
            if(sec<=10){
                --sec;
                sec='0'+sec;
                $('#second').html(sec)
            }else{
                --sec;
                $('#second').html(sec)
            }
        }
        $('#examtime').val(minutes+':'+sec);
        reduceTime=setTimeout(reduce,1000)

    }
    //var reduceTime = setInterval(reduce,1000)
	//$('.tk-selectarea li:nth-child(5n)').css({'marginRight':'15px'})

	/*$('.tk-tagleft').css({'height':25,'overflow':'hidden'})
	$('.tk-tagleft').click(function(){
		if(!$(this).attr('rel')){
            $(this).attr('rel',true)
            $(this).find('em').addClass('tk-tagicon_u');
            $('.tk-tagleft').css({'height':25,'overflow':'hidden'})
            $('.tk-answer').addClass('fn-dn')
		}else{
            $(this).find('em').removeClass('tk-tagicon_u');
            $(this).removeAttr('rel')
            $('.tk-tagleft').css({'height':35})
            $('.tk-answer').removeClass('fn-dn')
		}
	})*/
	$('[rel="select"]').hover(function(){
		var ind = $('[rel="select"]').index(this)
		$('[rel="choose"]').eq(ind).addClass('usermouseover')
	},
		function(){
			$('[rel="choose"]').removeClass('usermouseover')
		}
	)
    $('[rel="choose"]').click(function(){
        if (!auth)return login();

        var a = $('.tk-selectarea ul li')
        var v = $(this).parents('.tk-examtimu').attr('data-id');
        var t = $(this).parents('.tk-examtimu').attr('data-type');
        var $id = $(this).attr('id');
        var $ids = $id.split("_");
        if(!$(this).attr('data-answer')){
            if (t != 'checkbox'){
                $(this).parents('.tk-examtimu').find('[rel="choose"]').removeClass('mdown')
                $(this).parents('.tk-examtimu').find('[rel="choose"]').removeAttr('data-answer')
            }
            if (t == 'radio'){
                $(this).addClass('mdown')
            } else {
                $(this).removeClass('selectmore')
                $(this).addClass('selected')
            }
            $(this).attr('data-answer','select')
            if (t == 'checkbox'){
                var answer_it = $(this).parents('.tk-examtimu').find('.answer-input').val();
                $ids[1] = answer_it ? answer_it + ',' + $ids[1] : $ids[1];
            }
            $(this).parents('.tk-examtimu').find('.answer-input').val($ids[1]);
        } else {
            if (t == 'radio'){
                $(this).removeClass('mdown')
            } else {
                $(this).addClass('selectmore')
                $(this).removeClass('selected')
            }
            $(this).removeAttr('data-answer')
            if (t == 'checkbox'){
                $(this).parents('.tk-examtimu').find('[class="selected"]').each(function(){
                    op = $(this).attr('id').split('_');
                    answer_it = answer_it ? answer_it + ',' + op[1] : op[1]
                });

            }
            $(this).parents('.tk-examtimu').find('.answer-input').val(answer_it);
        }
        var select_ed = false;
        if ($(this).parents('.tk-examtimu').find('.answer-input').val() != '' && $(this).parents('.tk-examtimu').find('.answer-input').val() != '0')select_ed = true;
        if (!select_ed) {
            a.eq(--v).removeClass('tk-select');
        } else {
            a.eq(--v).addClass('tk-select');
        }
    })


	$('[rel="select"]').click(function(){
        if (!auth)return login();
        var a = $('.tk-selectarea ul li')
        var t = $(this).parents('.tk-examtimu').attr('data-type');
        var v = $(this).parents('.tk-examtimu').attr('data-id');
        var op_id = $(this).attr('optionid');
        var select_ed = false;
		if(!$(this).attr('data-answer')){
            if (t != 'checkbox'){
                $(this).parents('.tk-examtimu').find('[rel="choose"]').removeClass('mdown')
                $('[rel="select"]').removeAttr('data-answer');
            }
            if (t == 'checkbox'){
                var answer_it = $(this).parents('.tk-examtimu').find('.answer-input').val();
                op_id = answer_it ? answer_it + ',' + op_id : op_id;
            }
			var i = $(this).attr('data-tm')	;

            if (t == 'checkbox'){
                $(this).parents('.tk-examtimu').find('[rel="choose"]').eq(i).removeClass('selectmore')
                $(this).parents('.tk-examtimu').find('[rel="choose"]').eq(i).addClass('selected')
            } else {
                $(this).parents('.tk-examtimu').find('[rel="choose"]').eq(i).addClass('mdown')
            }
			$(this).attr('data-answer','select')
            $('#op_'+op_id).attr('data-answer','select')
            $(this).parents('.tk-examtimu').find('.answer-input').val(op_id);
            select_ed = true;
		}else{
			var i = $(this).attr('data-tm')	;
			$(this).removeAttr('data-answer')
            if (t == 'checkbox'){
                $('#op_'+op_id).removeClass('selected')
                $('#op_'+op_id).addClass('selectmore')

                $(this).parents('.tk-examtimu').find('[class="selected"]').each(function(){
                    op = $(this).attr('id').split('_');
                    answer_it = answer_it ? answer_it + ',' + op[1] : op[1]
                });

            } else {
                $('#op_'+op_id).removeClass('mdown')
            }
            $(this).parents('.tk-examtimu').find('.answer-input').val(answer_it);
		}

        if ($(this).parents('.tk-examtimu').find('.answer-input').val() != '' && $(this).parents('.tk-examtimu').find('.answer-input').val() != '0')select_ed = true;
        if (!select_ed) {
            a.eq(--v).removeClass('tk-select');
        } else {
            a.eq(--v).addClass('tk-select');
        }
		
	})
    $('.next').click(function(){
        var i = $(this).attr('data-id');
        i = i ? parseInt(i) : 1;
        n = eval(i + 1);
        u = eval(i - 1);
        var v = $('.qtype-div');
        var q = $('#sel_li_qtype li');
        if (v.eq(i).html()) {
            $('.qtype-div').hide();
            v.eq(i).show();
            $('#sel_li_qtype li').removeClass('on')
            q.eq(i).addClass('on');
            $('.up').attr('data-id', u);
            $(this).attr('data-id', n);
            $('.up').parents('.fn-fr').removeClass('fn-dn')
            if (n == v.size())$('.next').parents('.fn-fr').addClass('fn-dn')
            $("html, body").animate({scrollTop: 140}, 120);
        }

    })
    $('.up').click(function(){
        var i = $(this).attr('data-id');
        if (!i)return false;
        i = i ? parseInt(i) : 1;
        n = eval(i + 1);
        u = i > 1 ? eval(i - 1) : 0;
        var v = $('.qtype-div');
        var q = $('#sel_li_qtype li');
        if (v.eq(i).html()) {
            $('.qtype-div').hide();
            v.eq(i).show();
            $('#sel_li_qtype li').removeClass('on')
            q.eq(i).addClass('on');
            $('.next').attr('data-id', n);
            $(this).attr('data-id', u);
            $('.next').parents('.fn-fr').removeClass('fn-dn')
            if(i == 0)$('.up').parents('.fn-fr').addClass('fn-dn')
            $("html, body").animate({scrollTop: 140}, 120);
        }

    })

    $('#sel_li_qtype li').click(function(){
        var i = $(this).attr('data-id');
        var q = $('#sel_li_qtype li');
        var v = $('.qtype-div');
        if (v.eq(i).html()) {
            $('.qtype-div').hide();
            v.eq(i).show();
            $('#sel_li_qtype li').removeClass('on')
            q.eq(i).addClass('on');
            $('.tk-nextbtn').attr('data-id', ++i);


            if (!$(this).next('li').html()){
                $('.up').parents('.fn-fr').removeClass('fn-dn')
                $('.next').parents('.fn-fr').addClass('fn-dn')
            }
        }


    })
    $('#donext').click(function(){
        var _f = false;
        $('.tk-selectarea .tk-select').each(function(){
            _f = true;
        })
        if (!_f) {
            alert('没有开始做题');
            return false;
        }
        if ( $('#form_finish').attr('ajax_donext')){
            alert('不能重复提交！')
            return false;
        }
        if (!auth)return login();
        if(confirm('你确定下次再做么？')){
            $('#form_finish').attr('ajax_donext', '1');
            clearTimeout(reduceTime);
            $('#isfinish').val(1);
            $('#form_finish').submit();
            return false;
        }
    })
    $('#finish').click(function(){
        if (!auth)return login();
        clearTimeout(reduceTime);
        var _c = 0;
        $('.tk-selectarea .tk-select').each(function(){
            ++_c;
        })
        if (_c == 0){
            alert('还未答题不能交卷！');
            return false;
        }
        var h = $('.tk-selectarea li').size() - _c;

        if (h == 0) {
            $('#form_finish').submit();
            return false;
        }
        var html = '<div class="fn-tac pt20">'
            +'<div class="fts14">还剩<span> ' + h + '</span>道题未答完，确定要交卷吗？</div>'
            +'<div class="mt20 pt20">'
            +'<input type="button" class="submitexam">'
            +'</div>'
            +'<div class="mt20">'
            +'<input type="button" class="jixudati j-remove">'
            +'</div>'
        +'</div>'
        exam.dialog({width:370,height:240,titleclass:'fn-dn'}, html);
        return false;
    })
    $('.submitexam').live('click', function(){
        if ( $('#form_finish').attr('ajax_finish')){
            alert('不能重复提交！')
            return false;
        }
        $('#form_finish').attr('ajax_finish', '1');
        //$('#examtime').val(0);
        $('#form_finish').submit();
        return false;
    })





    $('#pause').click(function(){
        clearTimeout(reduceTime);
        var html = '<div class="tk-parseinfo ">'
            +'<div class="tk-parsemsg">休息一下</div>'
            +'<div class="fn-tac pt20"><input type="button" class="tk-jixubtn j-remove"></div>'
            +'</div>';
        exam.dialog({width:617,height:313,titleclass:'fn-dn'}, html);

    })


    $('.j-remove').live('click',function(){
        reduce()
        $.dialogClose()
    })



    $(".tk-books li:nth-child(6n)").css({'border-right':'none'})
    $(".tk-books li").hover(
        function(){
            $(this).addClass('bookon');
        },
        function(){
            $(this).removeClass('bookon');
        }
    )

    if (auth) {
        $.getJSON(kj_url + "exam/my/donext.html?jsoncallback=?",{contentid:contentid, check:1}, function(json){
            window.state = json.state;
            if (json.state) {
                var html = '<div class="fn-tac pt20 fts14">'
                    +'<div style="margin: 10px">'+title+'，上次还有你没做完的题目？ </div>'
                    +'<div class="mt20 pt20">'
                    +'<a href="javascript:void(0)" class="btnbluebg" id="g_up_exam">继续上一次的答案</a>'
                    +'</div>'
                    +'<div  class="mt20">'
                    +'<a href="javascript:void(0)" class="btngraybg" id="g_news_exam">重新做新的试卷</a>'
                    +'</div>'
                    +'</div>';
                exam.dialog({width:300,height:220,titleclass:'fn-dn'}, html);

                return false;
            } else {
                var html = '<div class="fn-tac pt20 fts14">'
                    +'<div style="width: 500px;margin: 0 auto;">会计网题库依据考试大纲为你生成了《'+title+'》，是否开始模考答题</div>'
                    +'<div class="mt20 pt20">'
                    +'<input type="button" class="startbtn j-remove">'
                    +'</div>'
                    +'<div  class="mt20">'
                    +'<a href="'+kj_url+'exam/project.html" class="cancelbtn"></a>'
                    +'</div>'
                    +'</div>';
                exam.dialog({width:565,height:225,titleclass:'fn-dn'}, html);
            }
        })
    }
    $('#g_up_exam').live('click', function(){
        $.dialogClose()
        $('body').append('<div style="width:200px; height:200px" class="loading">正在努力的加载上一次的数据...</div>');
        $.getJSON(kj_url + "exam/my/donext.html?jsoncallback=?",{contentid:contentid}, function(json){
            if (json.state) {

                var option = json.info.option;
                if (option) {
                    for (i in option) {
                        var question = $('.question_'+option[i].questionid);
                        question.find('.answer-input').val(option[i].optionid)
                        if (option[i].optionid){
                            optionid = option[i].optionid.split(',');
                            for (a in optionid) {
                                if ($('.question_'+option[i].questionid).attr('data-type') == 'radio') {

                                    $('#op_'+optionid[a]).attr({'class':'mdown', 'data-answer':'select'});
                                } else {
                                    $('#op_'+optionid[a]).removeClass('selectmore');
                                    $('#op_'+optionid[a]).attr({'class':'selected', 'data-answer':'select'});
                                }

                                question.find('[optionid="'+optionid[a]+'"]').attr({'data-answer':'select'});

                            }
                            $('#an_'+option[i].questionid).addClass('tk-select');
                        }

                    }
                }

                $('#minutes').html(json.info.examtime_m);
                $('#second').html(json.info.examtime_s);
                reduce()
                $('#form_finish').append('<input type="hidden" name="answerid" value="'+json.info.answerid+'">');
            } else {
                alert('未能加载到数据...请重新刷新页面加载！')
            }
            $('.loading').remove();
        })

    })
    $('#g_news_exam').live('click', function(){
        $.dialogClose()
        var html = '<div class="fn-tac pt20 fts14">'
            +'<div style="width: 500px;margin: 0 auto;">会计网题库依据考试大纲为你生成了《'+title+'》，是否开始模考答题</div>'
            +'<div class="mt20 pt20">'
            +'<input type="button" class="startbtn j-remove">'
            +'</div>'
            +'<div  class="mt20">'
            +'<a href="'+kj_url+'exam/project.html" class="cancelbtn"></a>'
            +'</div>'
            +'</div>';
        exam.dialog({width:565,height:225,titleclass:'fn-dn'}, html);
    })
    $('.tk-selectarea li').click(function(){
        var id = $(this).attr('data-id');
        $('.qtype-div').hide();
        $('.question_'+id).parents('.qtype-div').show();
        //maodian(id);

    })
})
//閿氱偣
function maodian(d){
    $("html, body").animate({scrollTop: $("a[name='"+ d +"']").offset().top}, 150);
    //$(window).scrollTop($("a[name='"+ d +"']").offset().top-60);
}