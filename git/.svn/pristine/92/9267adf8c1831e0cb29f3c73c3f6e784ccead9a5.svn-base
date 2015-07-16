var versionid;

function version_add()
{
	$('#content_version_add').ajaxForm('add_ok');
}

function add_ok(response)
{
	if (response.state)
	{
		ct.ok('保存成功');
		window.location.reload();
	}
	else
	{
		ct.error(response.error);
	}
}

function version_restore(id)
{
	$.getJSON('?app=system&controller=content_version&action=restore&versionid='+id, function(response){
		if (response.state)
		{
			ct.ok('恢复成功');
		}
		else
		{
			ct.error(response.error);
		}
	});
}

function version_delete(id)
{
	versionid = id;
	ct.confirm('此操作不可恢复，确认删除吗？', version_delete_submit, function(){return true;});
}

function version_delete_submit()
{
	$.getJSON('?app=system&controller=content_version&action=delete&versionid='+versionid, function(response){
		if (response.state)
		{
			$('#tr_'+versionid).remove();
			ct.ok('刪除成功');
		}
		else
		{
			ct.error(response.error);
		}
	});
}