<?php

class model_answer_option extends model
{
	function __construct()
	{
        parent::__construct();
        /**
         * 会计网
         */
        $user =  login_member();
        $this->_userid = $this->_userid ? $this->_userid : $user['user_id'];
        $this->_username = $this->_username ? $this->_username : $user['name'];
		$this->_table = $this->db->options['prefix'].'exam_answer_option';
		$this->_primary = 'answerid';
		$this->_fields = array('answerid', 'questionid', 'optionid', 'wrong' , 'isdel');
		$this->_readonly = array('answerid');
		$this->_create_autofill = array();
		$this->_update_autofill = array();
		$this->_filters_input = array();
		$this->_filters_output = array();
		$this->_validators = array();
	}
	
	function vote($questionid, $answerid, $optionid, $wrong)
	{
		if (is_array($optionid))
		{
			foreach ($optionid as $id)
			{
				$result = $this->add($questionid, $answerid, $id, $wrong);
			}
		}
		else 
		{
            echo $questionid,'---',$answerid,'---',$optionid,'---',$wrong,'<hr>';
			$result = $this->add($questionid, $answerid, $optionid, $wrong);
		}
		//$question = loader::model('question','exam');
		//$question->set_inc('votes', $questionid, $votes);
		return $result;
	}

    function vote_update($questionid, $answerid, $optionid, $wrong)
    {
        if (is_array($optionid))
        {
            foreach ($optionid as $id)
            {
                $result = $this->update(array( 'optionid'=>$id, 'wrong'=>$wrong),array('questionid'=>$questionid, 'answerid'=>$answerid));
            }
        }
        else
        {
            $result = $this->update(array('optionid'=>$optionid, 'wrong'=>$wrong),array('questionid'=>$questionid, 'answerid'=>$answerid));
        }
        //$question = loader::model('question','exam');
        //$question->set_inc('votes', $questionid, $votes);
        return $result;
    }
	
	private function add($questionid, $answerid, $optionid, $wrong)
	{
		$result = $this->insert(array('questionid'=>$questionid, 'answerid'=>$answerid, 'optionid'=>$optionid, 'wrong'=>$wrong));
		if ($result)
		{
			//$question_option = loader::model('question_option','exam');
			//$question_option->set_inc('votes', $optionid);
		}
	}

    public function error_remove($qid, $uid = 0)
    {
        $this->_userid = $uid > 0 ? $uid : $this->_userid;
        if (empty($qid))return '';
        $sql = "UPDATE #table_exam_answer a,#table_exam_answer_option o SET o.isdel=1 WHERE a.answerid=o.answerid AND createdby={$this->_userid} AND o.questionid in({$qid})";
        $result = $this->db->query($sql);
		if(!empty($result)){
			return 1;
		}else{
			return 0;
		}
    }
}