<?php

class plugin_exam_option extends object
{
	private $question, $option,$question_answer;
	
	public function __construct(& $question)
	{
		$this->question = $question;
		$this->option = loader::model('admin/question_option','exam');
		$this->question_answer = loader::model('admin/question_answer','exam');
	}

    public function get_option($question)
    {
        if ($question['type'] == 'read') {
            $val = loader::model('question', 'exam')->select(array('bandid'=>$question['questionid']));
            foreach ($val as $v) {
                $question['questions'][] = $this->get_option($v);
            }
        } else {
            $question['option'] = loader::model('admin/question_option','exam')->select(array('questionid'=>$question['questionid']));
        }

        $answer = loader::model('question_answer','exam')->get($question['answerid'],'answer,analysis');
        $question['description'] = htmlspecialchars_decode($question['description']);
        $answer['analysis'] = htmlspecialchars_decode($answer['analysis']);
        if ($answer)$question = array_merge($question, $answer);

        return $question;
    }

	public function after_add()
	{
        if ($this->question->data['type'] == 'read' && !$this->question->data['nowrite'])$this->write($this->question->questionid);
        if ($this->question->data['analysis']) $this->default_answer();
		if (!isset($this->question->data['option']) || empty($this->question->data['option'])) return false;
		$maxsort = 0;
        $answer = $this->question->data['answer'];
		foreach ($this->question->data['option'] as $k=>$r)
		{
			$maxsort = $r['sort']>$maxsort?$r['sort']:$maxsort;
			$r['questionid'] = $this->question->questionid;
			$id = $this->option->add($r);
            if (in_array($k, $answer) && in_array($this->question->data['type'] ,array('radio', 'checkbox')))$answerid[] = $id;

		}
        $this->default_answer($answerid);
		if($this->question->data['allowfill']) $this->option->add(array('questionid'=>$this->question->questionid,'optionid'=>'','name'=>'其他','image'=>'','sort'=>$maxsort,'isfill'=>1));
	}
	public function default_answer($answerid = '')
    {

        if (!$answerid && !$this->question->data['analysis']){
            if (in_array($this->question->data['type'] ,array('radio', 'checkbox'))) {
                $answerid= 0;
            } else {
                $this->question->data['analysis'] = '';
            }
        }
        $answerid = is_array($answerid) ? implode(',', $answerid) : $answerid;
        if (in_array($this->question->data['type'], array('text','textarea'))) {
            $i = array('analysis'=>$this->question->data['analysis']);
        } else {
            $i = array('answer'=>$answerid);
        }
        if ($this->question->data['answerid']) {
            $this->question_answer->update($i, $this->question->data['answerid']);
        } else {

            $i['type'] = $this->question->data['type'];
            $id = $this->question_answer->insert($i);

            if ($id)$this->question->update(array('answerid'=>$id), $this->question->questionid);
        }
        if (!$this->question->data['nowrite'])$this->write($this->question->questionid);
    }
	public function after_edit()
	{
        if ($this->question->data['type'] == 'read' && !$this->question->data['nowrite'])$this->write($this->question->questionid);
        if ($this->question->data['analysis']) $this->default_answer();
		if (!isset($this->question->data['option']) || empty($this->question->data['option'])) return false;
		$optionid = array();

        $answer = $this->question->data['answer'];
		foreach ($this->question->data['option'] as $k=>$r)
		{		
			if ($r['optionid'])
			{
				$this->option->edit($r['optionid'], $r);
				$optionid[] = $id = $r['optionid'];
			}
			else 
			{
				$r['questionid'] = $this->question->questionid;
				$optionid[] = $id= $this->option->add($r);
			}
            if (in_array($k, $answer) && in_array($this->question->data['type'] ,array('radio', 'checkbox')))$answerid[] = $id;
		}

        $this->default_answer($answerid);
		$this->option->delete_by($this->question->questionid, $optionid ,$this->question->data['allowfill']);


	}
	
	public function after_get()
	{
        $this->question->data = $this->get_option($this->question->data);
        $this->output($this->question->data);
	}
	
	public function after_ls()
	{
		foreach ($this->question->data as $i=>$r)
		{
            $r['description'] = htmlspecialchars_decode($r['description']);
			$r['option'] = loader::model('admin/question_option','exam')->ls($r['questionid']);
            if ($r['answerid']) {
                $answer = loader::model('admin/question_answer','exam')->get($r['answerid']);
                if ($answer)$r= array_merge($r,$answer);
            }
			$this->question->data[$i] = $r;
		}
		$this->question->data = array_map(array($this, 'output'), $this->question->data);
	}
	
	public function after_clear()
	{
        //删除答案
        if ($this->question->data->answerid) loader::model('admin/question_answer', 'exam')->delete($this->question->data->answerid);
        //删除选项
        if ($this->question->data->questionid) loader::model('admin/question_option', 'exam')->delete($this->question->data->questionid);
        //删除试卷里面的问题
        if ($this->question->data->questionid) loader::model('admin/exam_qtype_question', 'exam')->delete(array('questionid'=>$this->question->data->questionid));
        //删除收藏列表的问题
        if ($this->question->data->questionid) loader::model('exam_favorite', 'exam')->delete(array('questionid'=>$this->question->data->questionid));
        //删除描述题答案
        if ($this->question->data->questionid) loader::model('answer_record', 'exam')->delete(array('questionid'=>$this->question->data->questionid));
        //删除选择题答案
        if ($this->question->data->questionid) loader::model('answer_option', 'exam')->delete(array('questionid'=>$this->question->data->questionid));
	}
	
	private function output(& $r)
	{
		/*$option = $this->option->ls($r['questionid']);
		foreach ($option as $k=>$v)
		{
			$option[$k]['percent'] = $r['votes'] ? round($v['votes']/$r['votes']*100, 2) : 0;
		}
		$r['option'] = $option;*/
                return $r;
	}

    public function after_write()
    {
        $this->write($this->question->questionid);
    }
    private function write($questionid)
    {
        unset($this->question->data['option']);
        $question = $this->question->get($questionid);
        if (!$question)return false;
        $md5id = $question['bandid'] ? md5($question['bandid'].'exam') : md5($question['questionid'].'exam');
        $template = factory::template();
        $question['description'] = htmlspecialchars_decode($question['description']);
        $template->assign($question);
        if(!$question['md5id'])$this->question->update(array('md5id'=>$md5id), $questionid);
        $data = $template->fetch('exam/question/show');
        define('EXAM_HTML_PATH', WWW_PATH . 'www/exam' . DS);
        $dir = splitdir($md5id);
        $filename = EXAM_HTML_PATH . 'question/'.$dir.$md5id . '.html';
        folder::create(dirname($filename));
        write_file($filename, $data);
        return true;

    }
}