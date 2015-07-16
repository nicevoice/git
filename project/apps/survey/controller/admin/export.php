<?php
/**
 * 导出
 *
 * @aca whole 导出
 */
final class controller_admin_export extends survey_controller_abstract
{
	private $survey, $question, $answer, $answer_record, $answer_option;
	
	function __construct(& $app)
	{
		parent::__construct($app);
		if (!license('survey')) cmstop::licenseFailure();
		$this->survey = loader::model('admin/survey');
		$this->question = loader::model('admin/question');
		$this->question_option = loader::model('admin/question_option');
		$this->answer = loader::model('admin/answer');
		$this->answer_record = loader::model('admin/answer_record');
		$this->answer_option = loader::model('admin/answer_option');
		$this->export = loader::model('admin/export');
	}
	
	function index()
	{
		empty($_GET['contentid']) && exit('没有contentid');
		$contentid = $_GET['contentid'];
		$questionid = empty($_GET['questionid'])?null:$_GET['questionid'];
		$questions = $this->question->ls($contentid);
		$titlekeys = array();
		$quesrefact = array();
		$questionmap = array();
		foreach ($questions as $key=>$val)
		{
			$quesrefact[$val['questionid']] = array();	
			$questionmap[$val['questionid']] = $val;
			$titlekeys[$val['questionid']] = $val['subject'];
		}
		$titlekeys['created'] = '报名时间';

		$optionmap = array();
		foreach ($questions as $k1 => $v1)
		{ 
			if(empty($v1['option'])) continue;
			foreach ($v1['option'] as $k2 => $v2)
			{
				
				$optionmap[$v2['optionid']] = $v2['name']; 
			}		
		}
	    $answerids = $this->export->getanswers($contentid);
	    $queslen = 0;
	    foreach ($answerids as $k => $v)
	    {
	    	$optionids= $this->export->getoptions($v['answerid']);
	    	foreach ($optionids as $k2 => $v2)
	    	{
	    		if(is_null($quesrefact[$v2['questionid']][$queslen]))
	    		$quesrefact[$v2['questionid']][$queslen] = $optionmap[$v2['optionid']];
	    		else 
	    		{
	    			$quesrefact[$v2['questionid']][$queslen] .=','.$optionmap[$v2['optionid']];
	    		}	    	
	    	}
	    	
	    	$records = $this->export->getrecodes($v['answerid']);
	    	foreach ($records as $k1 => $v1)
	    	{
	    		$rcontent = !empty($v1['content'])?$v1['content']:' ';
	    		if($questionmap[$v1['questionid']]['type'] == 'checkbox')
	    		$quesrefact[$v1['questionid']][$queslen] = is_null($quesrefact[$v1['questionid']][$queslen])?$rcontent:$quesrefact[$v1['questionid']][$queslen].$rcontent;
	    		else $quesrefact[$v1['questionid']][$queslen] = $rcontent;
	    	}
	    	$quesrefact['created'][$queslen] = date('Y-m-d H:i:s',$v['created']); 	
	    	
	 	    if(!is_null($questionid) && is_null($quesrefact[$questionid][$queslen]))
	    	{
	    		foreach ($quesrefact as $k3 => $v3)
	    		{
	    			unset($quesrefact[$k3][$queslen]);
	    		}
	    	}
	    	else
	    	{
	    		//处理未填项防止错位
	    		foreach ($quesrefact as $k4 => $v4)
	    		{
	    			if(count($v4)!=$queslen+1) $quesrefact[$k4][$queslen]=" ";
	    		}
	    		$queslen++;
	    	}
	    	
	    }    
	    $quesrefact = $this->export->array_colrow($quesrefact);
	    $surveyname = $this->survey->get($contentid,'title');
	    $title = '调查_'.(is_null($questionid)?$surveyname['title']:$surveyname['title'].'_'.$titlekeys[$questionid]).'.xls';
	    
	    $this->export->toexcel($title,$titlekeys,$quesrefact,$this->_userid);
	}
	
}