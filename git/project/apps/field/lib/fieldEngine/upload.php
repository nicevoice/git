<?php
class fieldEngine_upload extends fieldEngine
{
	public function _render($field)
	{
		$field['setting'] = unserialize($field['setting']);
		return $this->_genHtml($field);
	}

	public function _addView($pid)
	{
		$this->view->assign('pid', $pid);
		$this->view->display('field/upload/add');
	}

	public function _editView($fid)
	{
		$field = $this->field->get($fid);
		$field['setting'] = unserialize($field['setting']);

		$this->view->assign($field);
		$this->view->assign('pid', $field['projectid']);
		$this->view->display('field/upload/edit');
	}

	public function _genData($setting, $fid)
	{
		$input = '<input
			type="text"
			id = "'.$setting['fieldid'].'"
			name="field['.$fid.']['.$setting['var'].']"
			style="width:'.$setting['inputsize'].'px"
			value="'.$setting['defaultvalue'].'"
			maxlength="'.$setting['maxnum'].'">';

		$input .=$this->_genUpload(
			$setting['fieldid'],
			$setting['fieldid'].'_'.$fid,
			$this->uploadtype($setting['uploadtype'])
		);
		return $input;
	}

	public function _genEditData($field, $value)
	{
		$fid = $field['fieldid'];
		$setting = unserialize($field['setting']);

		$input =  '<input
			type="text"
			id = "'.$setting['fieldid'].'"
			name="field['.$fid.']['.$setting['var'].']"
			style="width:'.$setting['inputsize'].'px"
			value="'.$value[$setting['var']].'">';

		$input .=$this->_genUpload(
				$setting['fieldid'],
				$setting['fieldid'].'_'.$fid,
				$this->uploadtype($setting['uploadtype'])
		);
		return array('name' => $setting['fieldname'], 'field' => $input);
	}

	private function uploadtype($type)
	{
		if(strpos($type, '|') !== false) {
            $allow = '';
			foreach(explode('|', $type) as $exts) {
				$allow .=  '*.'.$exts.';';
			}
			$type = $allow;
		} else {
			$type = '*.'.$type.';';
		}
		return $type;
	}

	private function _genUpload($inputid, $handleid, $allow = '*')
	{
$temp = <<<EOF
<div id="$handleid" class="uploader" style="vertical-align:middle;margin-left:5px;"></div>
<script type="text/javascript">
$(function(){
	function callback() {
        $("#$handleid").uploader({
            multi : false,
            script : '?app=editor&controller=filesup&action=upload',
            fileDataName : 'multiUp',
            fileExt : '$allow',
            buttonImg : 'images/multiup.gif',
            complete:function(response, data){
                response =(new Function("","return "+response))();
                if(response.state) {
                    img = response.code.match(/"(http:\/\/.+?)"/i)[1];
                    $('#$inputid').val(img);
                    ct.ok(response.msg);
                } else {
                    ct.error(response.msg);
                }
            }
        });
	}
	if ('uploader' in jQuery) {
	    callback();
	} else {
        $.ajax({type: 'GET', url: '{${ADMIN_URL}}uploader/cmstop.uploader.js', success: callback, dataType: 'script', ifModified: false, cache: true});
	}
});
</script>
EOF;
		return $temp;
	}
}