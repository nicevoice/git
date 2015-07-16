<?php
/**
 * 字段管理
 *
 * @aca 字段管理
 */
class controller_admin_field extends field_controller_abstract
{
	private $project, $field, $pagesize = 15;
	public function __construct(& $app)
	{
		parent::__construct($app);

		$this->field  = loader::model('admin/field');
	}

	/**
     * 字段删除
     *
     * @aca 字段删除
     */
	public function delete()
	{
		$fid = $_GET['fid'];
		$result = $this->field->delete($fid)
					? array('state'=>true) 
					: array('state'=>false,'error'=>$this->field->error());
		echo $this->json->encode($result);
	}
}