var i = 0;

var question =
{
	init: function ()
	{
		$('#questions dl').find("img").show();
		$('#questions dl:first-child').find("img:nth-child(3)").hide();
		$('#questions dl:last-child').find("img:nth-child(4)").hide();
	},
	
	add: function (type)
	{
		i = 0;
		asort = 0;
		ct.form('添加题目', '?app=exam&controller=question&action=add&type='+type, 800, 600, function (response){
			if (response.state)
			{
				var questionid = response.questionid;
				$.get('?app=exam&controller=question&action=view&questionid='+questionid, function (response){
					$('#questions').append(response);
					question.init();
					
					var no = $('#questions dl').length;
					$('#no_'+questionid).html(no);
					$.getJSON('?app=exam&controller=question&action=sort&sort['+questionid+']='+no);
				});
				return true;
			}
			else
			{
				ct.error(response.error);
			}
		}, function (dialog){
			if (type == 'radio' || type == 'checkbox' || type == 'select')
			{
				option.add();
				option.add();
			}
			question.upload();
			$('#image').floatImg({url: UPLOAD_URL});
		});
	},
    band: function (type)
    {
        i = 0;
        asort = 0;
        var subjectid = $('#subjectid').val();
        if (!subjectid) {
            alert('请先选择科目')
            return false;
        }
        ct.form('添加题目', '?app=exam&controller=question&action=band&type='+type + '&subjectid='+subjectid, 800, 600, function (response){
            if (response.state)
            {
                if (response.question) {
                    n = response.question;
                    var html = '';

                    for (i in n ){
                        if ($('#bandid_'+n[i].questionid).html() == null)html += '<dd id="bandid_'+ n[i].questionid+'">'+n[i].subject + '<input type="hidden" value="'+ n[i].questionid+'" name="qids[]"><a style="margin-left: 10px;" href="javascript:void(0);" onclick="question.removeband('+ n[i].questionid+')">删除</a><hr></dd>';
                    }
                    $('#bind_list td').append(html);
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
    get : function(type, subjectid){
        var key = $('#q_key').val();
        if (!key)return false;
        $.getJSON('?app=exam&controller=question&getJSON=1&action=band&type='+type + '&subjectid='+subjectid + '&keyword='+key, function (response){
            if (response)
            {
                var html = '';
                for (i in response) {
                    html += '<dl class="list_2">' +
                        '<dt class="c_blue b f_14"><span></span>'+response[i].subject+' <span><input type="checkbox" value="'+response[i].questionid+'" name="questionid[]"></span></dt>' +
                        ' </dl>';
                }
                $('#lists-band').html(html);

            }
            else
            {
                ct.error('没有数据');
            }
        });
    },
    removeband : function(id , t)
    {
        ct.confirm('您真的要删除?',function(){
        var t = t ? t  : 0;
        if (t) $.get('?app=exam&controller=question&action=delband&questionid='+id, function (html){});
            $('#bandid_'+id).remove();
        })
    },
    edit: function (questionid)
	{
		i = 0;
		ct.form('修改题目', '?app=exam&controller=question&action=edit&questionid='+questionid, 800, 600, function (response){
			if (response.state)
			{
				$.get('?app=exam&controller=question&action=view&questionid='+questionid, function (html){
					var s = $(html);
					var prevdl = $('#'+questionid).prev();
					var prevno = prevdl.is('dl')?(parseInt(prevdl.find("span[id^='no']").html())+1):1;
					s.find('#no_'+questionid).html(prevno);
					$('#'+questionid).replaceWith(s);
				});
				return true;
			}
			else
			{
				ct.error(response.error);
			}
		}, function (dialog){	
			question.upload();
			dialog.find('#image').floatImg({url: UPLOAD_URL});
			dialog.find('input[maxlength]').maxLength();
		});
	},
	
	up: function (i)
	{
		var obj = $('#'+i);
		if (obj.prev().is('dl'))
		{
			var prev_id = obj.prev().attr('id');
			var no = $('#no_'+i).html();
			var prev_no = $('#no_'+prev_id).html();

			$('#no_'+i).html(prev_no);
			$('#no_'+prev_id).html(no);
			
			obj.insertBefore(obj.prev());
			question.init();
			
			$.getJSON('?app=exam&controller=question&action=sort&sort['+i+']='+prev_no+'&sort['+prev_id+']='+no);
		}
	},
	
	down: function (i)
	{
		var obj = $('#'+i);
		if (obj.next().is('dl'))
		{
			var next_id = obj.next().attr('id');
			var no = $('#no_'+i).html();
			var next_no = $('#no_'+next_id).html();

			$('#no_'+i).html(next_no);
			$('#no_'+next_id).html(no);
			
			obj.insertAfter(obj.next());
			question.init();
			
			$.getJSON('?app=exam&controller=question&action=sort&sort['+i+']='+next_no+'&sort['+next_id+']='+no);
		}
	},
	
	remove: function (i)
	{
		ct.confirm('您真的要删除此问题?',function(){
			$.getJSON('?app=exam&controller=question&action=delete&questionid='+i, function (response){
				if (response.state)
				{

                    (ct.ok('删除完毕'), tableApp.deleteRow(i))
					//$('#'+i).remove();
					//question.init();
				}
				else
				{
					ct.error(response.error);
				}
			});
			return true;
		},function(){return true});
		
	},
	
	upload: function ()
	{
		$("#uploadimage").uploader({
			script        : '?app=exam&controller=question&action=upload',
			fileDesc		 : '注意:您只能上传jpeg,png,gif格式的文件!',
			fileExt		 : '*.jpg;*.jpeg;*.gif;*.png;',
			buttonImg	 	 :'images/upload.gif',
			multi			:false,
			complete:function(response,data)
			 {
			 	if(response != 0)
			 	{
			 		var img = response.split('|');
			 		var aid = img[0];
			 		var img = img[1];
                    $('#image').val(img);
			 	}
			 	else
			 	{
			 		ct.error('对不起！您上传文件过大而失败!');
			 	}
			 },
			 error:function(data)
			 {
			 	ct.error(date.error.type +':'+ data.error.info);
			 }
		});	
	}
}
