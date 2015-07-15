<?php
/**
 * 关键词链接
 *
 * @aca public 关键词链接
 */
class controller_admin_taglink extends editor_controller_abstract
{
	private $taglink;
	
	function __construct(& $app)
	{
		parent::__construct($app);
		$this->taglink = loader::model('admin/keylink','system');
	}
	
	function index()
	{
		$this->view->display('taglink');
	}
	
	function getlink()
	{
		$title = $_GET['title'];
		echo  $this->taglink->get_field('url',"name='$title'");
	}
	
	function update()
	{
		
		foreach ($_POST as $key => $value)
		{
			if(substr($key,-6) == '_ctup_')
			{
				$key = substr($key,0,-6);
				$this->taglink->update(array(
						'name'=>$key,
						'url'=>$value),"name='$key'");
			}
			else 
			{
				$this->taglink->add(array('name'=>$key,'url'=>$value));
			}
		}
	}
}