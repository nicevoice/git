<?php
/**
 * Tag 内链
 *
 * @aca public Tag 内链
 */
class controller_admin_tag extends editor_controller_abstract
{
	private $tag,$case =array(),$box =array(),$i=0;
	
	function __construct(& $app)
	{
		parent::__construct($app);
		$this->tag = loader::model('admin/tag','system');
	}
	
	function index()
	{
		$content = $_POST['content'];
		import('helper.segment');
		$segment = new segment();
		$segment->set_text($content);
		$result = $segment->get_keywords(5);
		if($result)
		{
			$tags = table('tag',null,'tag');
			$fintags = array(); 
			foreach ($tags as $k=>$v)
			{
				in_array($v['tag'],$result) && $fintags[]=$v['tag'];
			}
			$fintags = implode('|',$fintags);
			if($result)
			{
				$content = preg_replace("/(<.*>)/seU",'$this->_store("\1")',$content);
				$content = preg_replace("/($fintags)/seU",'$this->_kr("\1")',$content);
				$content = preg_replace("/__ct__/se",'$this->_output()',$content);
			}
		}
        echo $content;
	}
	
	function _kr($tag)
	{
		if(in_array($tag,$this->case))  return $tag;
		$this->case[]=$tag;
		return '<a class="ct__tag__" href="'.APP_URL.'?app=search&controller=index&action=search&wd='.$tag.'">'.$tag.'</a>';
	}

	function _store($ht)
	{
		$this->box[] = $ht;
		return '__ct__';
	}
	
	function _output()
	{
		return $this->box[$this->i++];
	}
}