<?php

class model_admin_91question extends model {

    public $question,$mysubject,$exam,$propertys,$alphabet;
    function __construct() {
        parent::__construct();
        $this->_table = $this->db->options['prefix'] . '91_question';
        $this->_primary = 'id';
        $this->_fields = array('id', 'type','body','subitems', 'catalogs', 'courseid');
        $this->_readonly = array('id');
        $this->_update_autofill = array();
        $this->_filters_input = array();
        $this->_filters_output = array();
        $this->_validators = array();
        $this->question = loader::model('admin/question');
        $this->exam = loader::model('admin/exam');
        $this->propertys = common_data('property_0', 'brand');
        $this->db = factory::db();
        $this->alphabet = array('A','B','C','D','E','F','G');
        $this->mysubject = array(
            735 => 401,
            730 => 402,
            731 => 403,
            736 => 412,
            744 => 413,
            745 => 416,
            747 => 415,
            746 => 414,
            881 => 420,
            879 => 424,
            882 => 423,
            884 => 422,
            883 => 421,
            970 => 425

        );
		$this->gdsubject = array(
            '会计基础' => 401,
            '财经法规' => 402,
            '初级会计电算化' => 403,
            '初级会计实务' => 412,
            '经济法基础' => 413,
            '财务管理' => 416,
            '中级会计实务' => 415,
            '中级经济法' => 414,
            '会计' => 420,
            '审计' => 421,
            '税法' => 422,
            '经济法' => 423,
            '财务成本管理' => 424,
            '公司战略与风险管理' => 425

        );
    }
    function dispose($val)
    {

        $val['body'] = $val['body'];
        $val['subitems'] = $val['subitems'] ? unserialize($val['subitems']) : '';
        $val['catalogs'] = $val['catalogs'] ? unserialize($val['catalogs']) : '';
        if ($val['type'] == '判断题')$val = $this->dispose_wr($val);
        $qtype = $this->getqtype($val['type']);

        if (!$qtype){
            return false;
        }
        $val['qtypeid'] = $qtype['proid'];
        if ($val['body'])$qtype['title'] = '综合题';

        if (in_array($qtype['title'], array('单项选择题', '判断题'))){
            $question = $this->dispose_radio($val);

        }
        if (in_array($qtype['title'], array('多项选择题'))){
            $question = $this->dispose_checkbox($val);

        }
        if (in_array($qtype['title'], array('简答题', '计算分析题')) || in_array($val['type'], array('简答题','案例分析题'))){

            $question = $this->dispose_text($val);

        }

        if (!$question && in_array($qtype['title'], array('综合题','案例分析题'))){

            $question = $this->dispose_read($val);
        }

        //if ($val['id'] == 1135281)printR($question);
        if (is_array($question)){
        	
           $question['nowrite'] = 1;
           $questionid = $this->question->add($question);

        } else {
            $questionid = $question;
        }
        return $questionid;

    }

	function process_zdtk_edit($arr){
		foreach($arr as $k=>$v){
			$v['cailiao'] = preg_replace("#\{[A-Za-z0-9]+\}#s",'',$v['cailiao']);
			$subject=mb_substr(strip_tags($v['cailiao']),0,30,'utf-8');
			$subject=$this->trimall($subject);
			$sql_a='select * from cms_exam_question where `qtypeid`=101003 and `subject`="'.$subject.'"';
			$row_xt=$this->db->get($sql_a);
			if(is_array($row_xt)){
				$questionid=$row_xt['questionid'];
				$row_xt['description']=$v['cailiao'];
				$this->question->edit($questionid, $row_xt);
			}
		}
		return true;
	}
	
	
	
	
	
	
	
	//中大题库数据处理函数，把采集到的数据处理成我们的数据结构
	//$arr=array();
	function process_zdtk($arr){
			$dn=array('A','B','C','D','a','b','c','d','1','0');
			$dct=array('对','错');
			foreach($arr as $k=>$v){
				if(!$v['cailiao']){
					//$v['title']是题目和选项,$v[answers]是答案,$v[parsing]是解释,$v[knowledgeid_title]考点,$v[kemu]科目
					$v['answers']=explode(",",strip_tags($v['answers']));
					$v['title'] = str_replace("，",',',$v['title']);
					$v['title'] = str_replace("。",'.',$v['title']);
					$v['title'] = $this->trimall(preg_replace("#\{[A-Za-z0-9]+\}#s",'',$v['title']));//去掉类似{TSE}这样的标签，同时删除空白
					if(in_array($v['answers'][0], $dn)){//判断是否为选择题和判断题
						if(count($v['answers'])>1){//多选题，获取题目和选项
							//利用正则把题目分割成题目和选项,$v['title'][0]为题目，其他为选项
							$v['title'] = preg_split("#[A-Z]\.|[A-Z]\．#s", $v['title']);
							if ($id = $this->check($v['title'][0], $this->gdsubject[$v['kemu']])){
								$tiku[$k] = $id;
							}else{
								$tiku[$k]['type']='checkbox';//题目类型checkbox为多选
								$tiku[$k]['subject']=$v['title'][0];
								$tiku[$k]['subjectid']=$this->gdsubject[$v['kemu']];
								$tiku[$k]['knowledgeid']=$this->getproperty(array($v['subject_title'],$v['knowledgeid_title']),$this->gdsubject[$v['kemu']]);
								$tiku[$k]['qtypeid']=101002;
								$tiku[$k]['source']='互联网';
								$tiku[$k]['image']='';
								$tiku[$k]['description']=$v['parsing'];
								for($i=0; $i<count($v['title']); $i++){//选项
									if($i>0){
									$tiku[$k]['option'][$i]['optionid']='';
									$tiku[$k]['option'][$i]['sort']=$i;
									$tiku[$k]['option'][$i]['name']=$v['title'][$i];
									$tiku[$k]['option'][$i]['image']='';
									}
								}
								for($i=0;$i<count($v['answers']);$i++){//答案
								switch ($v['answers'][$i]){
									case 'A':
									$answer=1;
									break;
									case 'B':
									$answer=2;
									break;
									case 'C':
									$answer=3;
									break;
									case 'D':
									$answer=4;
									break;
								}
								$tiku[$k]['answer'][$i]=$answer;
								}
								$tiku[$k]['minoptions']='';
								$tiku[$k]['maxoptions']='';
							}
						}else{
							if(($v['answers'][0]=='1')||($v['answers'][0]=='0')){//对错题，获取题目和选项，1表示对，0表示错
								if ($id = $this->check($v['title'], $this->gdsubject[$v['kemu']])){//判断题库中是否已经有该题目
									$tiku[$k] = $id;
								}else{
									$tiku[$k]['type']='radio';
									$tiku[$k]['subject']=$v['title'];
									$tiku[$k]['subjectid']=$this->gdsubject[$v['kemu']];
									$tiku[$k]['knowledgeid']=$this->getproperty(array($v['subject_title'],$v['knowledgeid_title']),$this->gdsubject[$v['kemu']]);
									$tiku[$k]['qtypeid']=101004;//判断题
									$tiku[$k]['source']='互联网';
									$tiku[$k]['image']='';
									$tiku[$k]['description']=$v['parsing'];
									for($i=1,$j=0; $i<3; $i++,$j++){//得到选项
										$tiku[$k]['option'][$i]['optionid']='';
										$tiku[$k]['option'][$i]['sort']=$i;
										$tiku[$k]['option'][$i]['name']=$dct[$j];
										$tiku[$k]['option'][$i]['image']='';
									}
									$tiku[$k]['answer'][0]=($v['answers'][0]=='1')?1:2;//答案
									$tiku[$k]['minoptions']='';
									$tiku[$k]['maxoptions']='';
								}
							}else{//单选题，获取题目和选项
								//$v['title'][0]为题目，其他元素为答案
								$v['title'] = str_replace("，",',',$v['title']);
								$v['title'] = str_replace("。",'.',$v['title']);
								$v['title'] = preg_split("/[A-Z]\./", $v['title']);
								if ($id = $this->check($v['title'][0], $this->gdsubject[$v['kemu']])){//判断题库中是否已经有该题目
									$tiku[$k] = $id;
								}else{
									$tiku[$k]['type']='radio';//题目类型radio为单选
									$tiku[$k]['subject']=$v['title'][0];
									$tiku[$k]['subjectid']=$this->gdsubject[$v['kemu']];
									$tiku[$k]['knowledgeid']=$this->getproperty(array($v['subject_title'],$v['knowledgeid_title']),$this->gdsubject[$v['kemu']]);
									$tiku[$k]['qtypeid']=101001;//单选题
									$tiku[$k]['source']='互联网';
									$tiku[$k]['image']='';
									$tiku[$k]['description']=$v['parsing'];
									for($i=0; $i<count($v['title']); $i++){//选项
										if($i>0){
										$tiku[$k]['option'][$i]['optionid']='';
										$tiku[$k]['option'][$i]['sort']=$i;
										$tiku[$k]['option'][$i]['name']=$v['title'][$i];
										$tiku[$k]['option'][$i]['image']='';
										}
									}
									for($i=0;$i<count($v['answers']);$i++){//答案
										switch ($v['answers'][$i]){
											case 'A':
											$answer=1;
											break;
											case 'B':
											$answer=2;
											break;
											case 'C':
											$answer=3;
											break;
											case 'D':
											$answer=4;
											break;
										}
										$tiku[$k]['answer'][$i]=$answer;
									}
									$tiku[$k]['minoptions']='';
									$tiku[$k]['maxoptions']='';
								}
							}
						}
					}
				}else{
					//材料题
					$sql_a='select * from zdwx where `link_qid`="'.$v['qid'].'"';
					//$sql_a='select * from zdwx where `link_qid`="6cf5f95a-0f15-451c-a46f-90804bb24a51"';
					$row_xt=$this->db->select($sql_a);
					$tmid=array();
					$result=array();
					$result[]=$v;
					foreach($row_xt as $xt){
						$result[] = $xt;
					}
					foreach($result as $k_dt=>$v_dt){
						//$v['title']是题目和选项,$v[answers]是答案,$v[parsing]是解释,$v[knowledgeid_title]考点,$v[kemu]科目
						$v_dt['answers']=explode(",",strip_tags($v_dt['answers']));
						$v_dt['title'] = str_replace("，",',',$v_dt['title']);
						$v_dt['title'] = str_replace("。",'.',$v_dt['title']);
						$v_dt['title'] = $this->trimall(preg_replace("#\{[A-Za-z0-9]+\}#s",'',$v_dt['title']));
						//去掉类似{TSE}这样的标签，同时删除空白
						if(in_array($v_dt['answers'][0], $dn)){//判断是否为选择题和判断题,先把小题添加入库，然后记得小题id
							if(count($v_dt['answers'])>1){//多选题，获取题目和选项
								//利用正则把题目分割成题目和选项,$v['title'][0]为题目，其他为选项
								$v_dt['title'] = preg_split("#[A-Z]\.|[A-Z]\．#s", $v_dt['title']);
								if ($id = $this->check($v_dt['title'][0], $this->gdsubject[$v_dt['kemu']])){
									$tmid[$k_dt] = $id;
								}else{
									$tiku_dt['type']='checkbox';//题目类型checkbox为多选
									$tiku_dt['subject']=$v_dt['title'][0];
									$tiku_dt['subjectid']=$this->gdsubject[$v_dt['kemu']];
									$tiku_dt['knowledgeid']=$this->getproperty(array($v_dt['subject_title'],$v_dt['knowledgeid_title']),$this->gdsubject[$v_dt['kemu']]);
									$tiku_dt['qtypeid']=101003;//不定项选择题
									$tiku_dt['source']='互联网';
									$tiku_dt['image']='';
									$tiku_dt['description']=$v_dt['parsing'];
									for($i=0; $i<count($v_dt['title']); $i++){//选项
										if($i>0){
										$tiku_dt['option'][$i]['optionid']='';
										$tiku_dt['option'][$i]['sort']=$i;
										$tiku_dt['option'][$i]['name']=$v_dt['title'][$i];
										$tiku_dt['option'][$i]['image']='';
										}
									}
									for($i=0;$i<count($v_dt['answers']);$i++){//答案
									switch ($v_dt['answers'][$i]){
										case 'A':
										$answer=1;
										break;
										case 'B':
										$answer=2;
										break;
										case 'C':
										$answer=3;
										break;
										case 'D':
										$answer=4;
										break;
									}
									$tiku_dt['answer'][$i]=$answer;
									}
									$tiku_dt['minoptions']='';
									$tiku_dt['maxoptions']='';
									$tmid[$k_dt] = $this->question->add($tiku_dt);
								}
							}else{
								if(($v_dt['answers'][0]=='1')||($v_dt['answers'][0]=='0')){//对错题，获取题目和选项，1表示对，0表示错
								//大题没有用到此处，本人懒没有修改这里
									if ($id = $this->check($v_dt['title'], $this->gdsubject[$v_dt['kemu']])){//判断题库中是否已经有该题目
										$tmid[$k_dt] = $id;
									}else{
										$tiku_dt['type']='radio';
										$tiku_dt['subject']=$v_dt['title'];
										$tiku_dt['subjectid']=$this->gdsubject[$v_dt['kemu']];
										$tiku_dt['knowledgeid']=$this->getproperty(array($v_dt['subject_title'],$v_dt['knowledgeid_title']),$this->gdsubject[$v_dt['kemu']]);
										$tiku_dt['qtypeid']=101003;//不定项选择题
										$tiku_dt['source']='互联网';
										$tiku_dt['image']='';
										$tiku_dt['description']=$v_dt['parsing'];
										for($i=1,$j=0; $i<3; $i++,$j++){//得到选项
											$tiku_dt['option'][$i]['optionid']='';
											$tiku_dt['option'][$i]['sort']=$i;
											$tiku_dt['option'][$i]['name']=$dct[$j];
											$tiku_dt['option'][$i]['image']='';
										}
										$tiku_dt['answer'][0]=($v_dt['answers'][0]=='1')?1:2;//答案
										$tiku_dt['minoptions']='';
										$tiku_dt['maxoptions']='';
										$tmid[$k_dt] = $this->question->add($tiku_dt);
									}
								//大题没有用到此处，本人懒没有修改这里
								}else{//单选题，获取题目和选项
									//$v['title'][0]为题目，其他元素为答案
									$v_dt['title'] = str_replace("，",',',$v_dt['title']);
									$v_dt['title'] = str_replace("。",'.',$v_dt['title']);
									$v_dt['title'] = preg_split("/[A-Z]\./", $v_dt['title']);
									if ($id = $this->check($v_dt['title'][0], $this->gdsubject[$v_dt['kemu']])){//判断题库中是否已经有该题目
										$tmid[$k_dt] = $id;
									}else{
										$tiku_dt['type']='radio';//题目类型radio为单选
										$tiku_dt['subject']=$v_dt['title'][0];
										$tiku_dt['subjectid']=$this->gdsubject[$v_dt['kemu']];
										$tiku_dt['knowledgeid']=$this->getproperty(array($v_dt['subject_title'],$v_dt['knowledgeid_title']),$this->gdsubject[$v_dt['kemu']]);
										$tiku_dt['qtypeid']=101003;//不定项选择题
										$tiku_dt['source']='互联网';
										$tiku_dt['image']='';
										$tiku_dt['description']=$v_dt['parsing'];
										for($i=0; $i<count($v_dt['title']); $i++){//选项
											if($i>0){
											$tiku_dt['option'][$i]['optionid']='';
											$tiku_dt['option'][$i]['sort']=$i;
											$tiku_dt['option'][$i]['name']=$v_dt['title'][$i];
											$tiku_dt['option'][$i]['image']='';
											}
										}
										for($i=0;$i<count($v_dt['answers']);$i++){//答案
											switch ($v_dt['answers'][$i]){
												case 'A':
												$answer=1;
												break;
												case 'B':
												$answer=2;
												break;
												case 'C':
												$answer=3;
												break;
												case 'D':
												$answer=4;
												break;
											}
											$tiku_dt['answer'][$i]=$answer;
										}
										$tiku_dt['minoptions']='';
										$tiku_dt['maxoptions']='';
										$tmid[$k_dt] = $this->question->add($tiku_dt);
									}
								}
							}
						}
					}
					$v['answers']=explode(",",strip_tags($v['answers']));
					if(in_array($v['answers'][0], $dn)){//判断是否为选择题和判断题
					$v['cailiao'] = preg_replace("#\{[A-Za-z0-9]+\}#s",'',$v['cailiao']);
					$tiku[$k]['type']='read';
					$tiku[$k]['subject']=mb_substr(strip_tags($v['cailiao']),0,30,'utf-8');
					$tiku[$k]['subjectid']=$this->gdsubject[$v['kemu']];
					$tiku[$k]['knowledgeid']=$this->getproperty(array($v['subject_title'],$v['knowledgeid_title']),$this->gdsubject[$v['kemu']]);
					$tiku[$k]['qtypeid']=101003;//阅读题类型，不定项选择题
					$tiku[$k]['source']='互联网';
					$tiku[$k]['description']=$v['cailiao'];
					$tiku[$k]['qids']=$tmid;
					}
				}
			}

			
			
		return $tiku;
	}
	
	
	
	
	//高顿题库数据处理函数，把采集到的数据处理成我们的数据结构
	//$arr=array();
	function process_gdtk($arr){
		$dn=array('A','B','C','D','a','b','c','d','对','错');
		$dct=array('对','错');
		foreach($arr as $k=>$v){//$v['answers'][0]是题目和选项,$v['answers'][1-5]是答案,$v['parsing']是解释
			$v['answers']=explode(",",strip_tags($v['answers']));
			if(in_array($v['answers'][0], $dn)){//判断是否为选择题和判断题
				if(count($v['answers'])>1){//多选题，获取题目和选项
					//$v['title'][0]为题目，其他元素为答案
					$v['title'] = str_replace("，",',',$v['title']);
					$v['title'] = str_replace("。",'.',$v['title']);
					$v['title'] = preg_split("#[A-Z]\.|[A-Z]\．|[A-Z]、#s", $v['title']);
					if ($id = $this->check($v['title'][0], $this->gdsubject[$v['kemu']])){
						$tiku[$k] = $id;
					}else{
						$tiku[$k]['type']='checkbox';//题目类型checkbox为多选
						$tiku[$k]['subject']=$v['title'][0];
						$tiku[$k]['subjectid']=$this->gdsubject[$v['kemu']];
						$tiku[$k]['knowledgeid']=$this->getproperty(array($v['subject_title'],$v['knowledgeid_title']),$this->gdsubject[$v['kemu']]);
						$tiku[$k]['qtypeid']=101002;
						$tiku[$k]['source']='互联网';
						$tiku[$k]['image']='';
						$tiku[$k]['description']=$v['parsing'];
						for($i=0; $i<count($v['title']); $i++){//选项
							if($i>0){
							$tiku[$k]['option'][$i]['optionid']='';
							$tiku[$k]['option'][$i]['sort']=$i;
							$tiku[$k]['option'][$i]['name']=$v['title'][$i];
							$tiku[$k]['option'][$i]['image']='';
							}
						}
						for($i=0;$i<count($v['answers']);$i++){//答案
						switch ($v['answers'][$i]){
							case 'A':
							$answer=1;
							break;
							case 'B':
							$answer=2;
							break;
							case 'C':
							$answer=3;
							break;
							case 'D':
							$answer=4;
							break;
						}
						$tiku[$k]['answer'][$i]=$answer;
						}
						$tiku[$k]['minoptions']='';
						$tiku[$k]['maxoptions']='';
					}
				}else{
					if(($v['answers'][0]=='对')||($v['answers'][0]=='错')){//对错题，获取题目和选项
						$v['title']=mb_substr($this->trimall(strip_tags($v['title'])),0,-2,'utf-8');//题目
						if ($id = $this->check($v['title'], $this->gdsubject[$v['kemu']])){//判断题库中是否已经有该题目
							$tiku[$k] = $id;
						}else{
							$tiku[$k]['type']='radio';
							$tiku[$k]['subject']=$v['title'];
							$tiku[$k]['subjectid']=$this->gdsubject[$v['kemu']];
							$tiku[$k]['knowledgeid']=$this->getproperty(array($v['subject_title'],$v['knowledgeid_title']),$this->gdsubject[$v['kemu']]);
							$tiku[$k]['qtypeid']=101004;//判断题
							$tiku[$k]['source']='互联网';
							$tiku[$k]['image']='';
							$tiku[$k]['description']=$v['parsing'];
							for($i=1,$j=0; $i<3; $i++,$j++){//得到选项
								$tiku[$k]['option'][$i]['optionid']='';
								$tiku[$k]['option'][$i]['sort']=$i;
								$tiku[$k]['option'][$i]['name']=$dct[$j];
								$tiku[$k]['option'][$i]['image']='';
							}
							$tiku[$k]['answer'][0]=($v['answers'][0]==$tiku[$k]['option'][1]['name'])?1:2;//答案
							$tiku[$k]['minoptions']='';
							$tiku[$k]['maxoptions']='';
						}
					}else{//单选题，获取题目和选项
						//$v['title'][0]为题目，其他元素为答案
						$v['title'] = str_replace("，",',',$v['title']);
						$v['title'] = str_replace("。",'.',$v['title']);
						$v['title'] = preg_split("#[A-Z]\.|[A-Z]\．|[A-Z]、#s", $v['title']);
						if ($id = $this->check($v['title'][0], $this->gdsubject[$v['kemu']])){//判断题库中是否已经有该题目
							$tiku[$k] = $id;
						}else{
							$tiku[$k]['type']='radio';//题目类型radio为单选
							$tiku[$k]['subject']=$v['title'][0];
							$tiku[$k]['subjectid']=$this->gdsubject[$v['kemu']];
							$tiku[$k]['knowledgeid']=$this->getproperty(array($v['subject_title'],$v['knowledgeid_title']),$this->gdsubject[$v['kemu']]);
							$tiku[$k]['qtypeid']=101001;//单选题
							$tiku[$k]['source']='互联网';
							$tiku[$k]['image']='';
							$tiku[$k]['description']=$v['parsing'];
							for($i=0; $i<count($v['title']); $i++){//选项
								if($i>0){
								$tiku[$k]['option'][$i]['optionid']='';
								$tiku[$k]['option'][$i]['sort']=$i;
								$tiku[$k]['option'][$i]['name']=$v['title'][$i];
								$tiku[$k]['option'][$i]['image']='';
								}
							}
							for($i=0;$i<count($v['answers']);$i++){//答案
								switch ($v['answers'][$i]){
									case 'A':
									$answer=1;
									break;
									case 'B':
									$answer=2;
									break;
									case 'C':
									$answer=3;
									break;
									case 'D':
									$answer=4;
									break;
								}
								$tiku[$k]['answer'][$i]=$answer;
							}
							$tiku[$k]['minoptions']='';
							$tiku[$k]['maxoptions']='';
						}
					}
				}
			}
		}
		return $tiku;
	}
	
	//删除空格
	function trimall($str)
	{
		$qian=array(" ","　","\t","\n","\r");$hou=array("","","","","");
		return str_replace($qian,$hou,$str);   
	}
	
	
	
    /**
     * 阅读题
     * @param $val
     * @return array
     */
    function dispose_read($val)
    {
        $Body = new_addslashes(strip_tags($val['body']));
        $len = strlen($Body);
        $exp = htmlspecialchars(new_addslashes($val['subitems'][0]['Explanation']));
        $exp = $exp ? $exp : ($len > 200 ? htmlspecialchars(new_addslashes($val['body'])) : '');
        $subject = $len > 200 ? str_cut($Body, 200, '') : $Body;

        
        $ids = array();
        if (count($val['subitems']) > 1) {
            foreach ($val['subitems'] as $items) {
                $n_val = $val;
                unset($n_val['subitems']);
                $n_val['body'] = null;
                $n_val['type'] = $items['QuestionTypeName'];
                $n_val['subitems'] = serialize(array($items));
                $n_val['catalogs'] = serialize($val['catalogs']);
                $ids[] = $this->dispose($n_val);
            }


        }
        if ($id = $this->check($subject, $this->mysubject[$val['courseid']])){
        	//
        	$questionids = implode(',', $ids);
        	$sql = "update #table_exam_question set `bandid`={$id} WHERE questionid in({$questionids})";
        	$this->db->update($sql);
        	return $id;
        }
        foreach ($val['catalogs'] as $catalogs) {
            $cat[] = $catalogs['Title'];
        }
        $knowledgeid = $this->getproperty($cat, $this->mysubject[$val['courseid']]);
        $read = array(
            'type' => 'read',
            '91id' => $val['id'],
            'subject' => $subject,
            'subjectid' => $this->mysubject[$val['courseid']],
            'knowledgeid' => $knowledgeid,
            'qtypeid' => $val['qtypeid'],
            'source' => '互联网',
            'description' => $exp,
            'qids'=>$ids,
        );
        return $read;
    }



    /**
     * 多行文本
     * @param $val
     * @return array
     */
    function dispose_text($val)
    {
        $Body = new_addslashes(strip_tags($val['subitems'][0]['Body']));
        $len = strlen($Body);
        $exp = htmlspecialchars(new_addslashes($val['subitems'][0]['Explanation']));
        $exp = $exp ? $exp : ($len > 200 ? htmlspecialchars(new_addslashes($val['subitems'][0]['Body'])) : '');
        $subject = $len > 200 ? str_cut($Body, 200, '') : $Body;
        if ($id = $this->check($subject, $this->mysubject[$val['courseid']]))return $id;
        foreach ($val['catalogs'] as $catalogs) {
            $cat[] = $catalogs['Title'];
        }
        if ($val['subitems'][0]['Explanation'] && $i = $this->getImgs($val['subitems'][0]['Explanation'])) {

            $val['subitems'][0]['Explanation'] = $i;
        }
        if ($val['subitems'][0]['Answer'] && $i = $this->getImgs($val['subitems'][0]['Answer'])) {
            $val['subitems'][0]['Answer'] = $i;
        }
        $knowledgeid = $this->getproperty($cat, $this->mysubject[$val['courseid']]);
        $text = array(
            'type' => 'textarea',
            '91id' => $val['id'],
            'subject' => $subject,
            'subjectid' => $this->mysubject[$val['courseid']],
            'knowledgeid' => $knowledgeid,
            'qtypeid' => $val['qtypeid'],
            'source' => '互联网',
            'description' => $exp,
            'analysis' => htmlspecialchars(new_addslashes($val['subitems'][0]['Answer'])),
        );
        return $text;
    }

    /**
     * 单项选择题
     * @param $val
     * @return array
     */

    function dispose_radio($val)
    {
        if ($id = $this->check(new_addslashes(strip_tags($val['subitems'][0]['Body'])), $this->mysubject[$val['courseid']]))return $id;

        foreach ($val['catalogs'] as $catalogs) {
            $cat[] = $catalogs['Title'];
        }
        $knowledgeid = $this->getproperty($cat, $this->mysubject[$val['courseid']]);
        $answer = array_search($val['subitems'][0]['Answer'], $this->alphabet);


        foreach ($val['subitems'][0]['Options'] as $k =>$v) {
            $option[$k+1]['sort'] = $k+1;
            $option[$k+1]['name'] = strip_tags($v);
        }
        $radio = array(
            'type' => 'radio',
            '91id' => $val['id'],
            'subject' => new_addslashes(strip_tags($val['subitems'][0]['Body'])),
            'subjectid' => $this->mysubject[$val['courseid']],
            'knowledgeid' => $knowledgeid,
            'qtypeid' =>  $val['qtypeid'],
            'source' => '互联网',
            'description' => htmlspecialchars(new_addslashes($val['subitems'][0]['Explanation'])),
            'answer' => array($answer+1),
            'option' => $option,
        );
        return $radio;
    }
    /**
     * 多项选择题
     * @param $val
     * @return array
     */

    function dispose_checkbox($val)
    {

        if ($id = $this->check(new_addslashes(strip_tags($val['subitems'][0]['Body'])), $this->mysubject[$val['courseid']]))return $id;
        foreach ($val['catalogs'] as $catalogs) {
            $cat[] = $catalogs['Title'];
        }
        $knowledgeid = $this->getproperty($cat, $this->mysubject[$val['courseid']]);
        $size = strlen($val['subitems'][0]['Answer']);
        for ($i=0; $i < $size; $i++) {
            $answers[] = substr($val['subitems'][0]['Answer'], $i, 1);
        }
        foreach ($answers as $k=>$a) {
            $_answers[] = array_search($a, $this->alphabet)+1;
        }
        foreach ($val['subitems'][0]['Options'] as $k =>$v) {
            $option[$k+1]['sort'] = $k+1;
            $option[$k+1]['name'] = strip_tags($v);
        }

        $radio = array(
            'type' => 'checkbox',
            '91id' => $val['id'],
            'subject' => new_addslashes(strip_tags($val['subitems'][0]['Body'])),
            'subjectid' => $this->mysubject[$val['courseid']],
            'knowledgeid' => $knowledgeid,
            'qtypeid' =>  $val['qtypeid'],
            'source' => '互联网',
            'description' => htmlspecialchars(new_addslashes($val['subitems'][0]['Explanation'])),
            'answer' => $_answers,
            'option' => $option,
        );

        return $radio;
    }

    /**
     * 下载图片
     * @param $content
     * @return mixed
     */
    function getImgs($content){
        return $content;
        $attachment = loader::model('attachment', 'video');
        return $attachment->download_by_content($content);
    }

    /**
     * 判断题的选项
     * @param $val
     * @return mixed
     */
    function dispose_wr($val)
    {
        $val['subitems'][0]['Answer'] = $val['subitems'][0]['Answer'] == '对' ? 'A' : 'B';
        $val['subitems'][0]['Options'][0] = '对';
        $val['subitems'][0]['Options'][1] = '错';
        return $val;
    }

    /**
     * 获取题型
     * @param $qtype
     * @return bool
     */
    function getqtype($qtype)
    {
        $colle = config::get('exam' , 'colle');
        $no = config::get('exam' , 'no');
        if (in_array($qtype, $no))return false;
        foreach($colle as $k =>$v) {
            if (in_array($qtype, $v)){
                $qtype = $k;
                break;
            }
        }
        //printR($qtype);
        $sql = "SELECT proid,title FROM #table_property WHERE title ='{$qtype}'";
        $_property = $this->db->get($sql);
        return $_property;
    }
	
    /**
     * 获取知识点
     * @param $cat $cat = array('总论', '会计概述');
     * @param $subjectid 科目ID
     * @return mixed
     */
    function getproperty($cat, $subjectid)
    {
        $knowledge = config::get('exam' , 'knowledge');
        $pid =  $knowledge[$subjectid];
        $c = $cat[0];
        if ($cat[1]) {
            $c = $cat[1];
            unset($cat[1]);
            $pid = $this->getproperty($cat, $subjectid);
        }

        #$property = $this->propertys[$knowledge[$subjectid]];
        $sql = "SELECT * FROM #table_property WHERE proid ={$knowledge[$subjectid]}";
        $property = $this->db->get($sql);
        $propertyid = $property['childids'] ? $property['proid'].','.$property['childids'] : $property['proid'];
        $sql = "SELECT proid FROM #table_property WHERE name ='{$c}' AND parentid in({$propertyid})";
        $_property = $this->db->get($sql);
       // if ($c == '其他相关税收法律制度')
        if (!$_property) {

            $param = array(
                'parentid' => $pid,
                'name' => $c,
                'title' => $c,
                'description' => $c,
                'sort' => 0,
            );
            $proid = $this->exam->add_property($param);

        }
        return $_property['proid'] ? $_property['proid'] : $proid;

    }

    /**
     * 查看是否已存在
     * @param $subject
     * @param $subjectid
     * @return mixed
     */
    function check($subject, $subjectid)
    {
        $sql = "SELECT questionid FROM #table_exam_question WHERE subject ='{$subject}' AND subjectid={$subjectid}";
        $question = $this->db->get($sql);
        return $question['questionid'];
    }




    function disexam($file)
    {

        $val = file_get_contents($file);
        $val = unserialize($val);
        $exam['catid'] = $this->getcatid($this->mysubject[$val['subjectid']]);
        $exam['title'] = new_addslashes($val['title']);
        $sql = "SELECT contentid FROM #table_content WHERE title='{$exam['title']}' AND catid={$exam['catid']} LIMIT 1";
        if ($this->db->get($sql))return false;
        foreach ($val['Batches'] as $k=>$bat) {
            $_91id = implode(',', $bat);
            $sql = "SELECT qtypeid,questionid FROM #table_exam_question WHERE 91id  in ({$_91id}) AND bandid=0";
            $question = $this->db->select($sql);
            if (!$question)continue;
            $qids = array();
            foreach($question as $q) {
                $qids[] = $q['questionid'];
            }
           // if ($k ==4)printR($this->db->sql);
            $val['num'] = count($qids);
            $val['id'] = $question[0]['qtypeid'];
            $option[] = $this->question_array($val, $qids);
        }
        if (!$option)return false;

        $exam['subjectid'] = $this->mysubject[$val['subjectid']];
        $exams = $this->exam_array($exam);
        $exams['qtype'] = $option;
        $exam_model = loader::model('admin/exam', 'exam');
        $exam_model->add($exams);
    }
    function disexam2question($file)
    {
        $val = file_get_contents($file);
        $val = unserialize($val);
        $this->question($val);
    }
    public  function question($exam)
    {
        foreach ($exam['question'] as $e) {
            $exams['courseid'] = $exam['courseid'];
            $exams['id'] = $e['Id'];
            $exams['type'] = $e['Question']['QuestionTypeName'];
            $exams['body'] = $e['Question']['ComplexBody'];
            $exams['subitems'] = serialize($e['Question']['SubItems']);
            $exams['catalogs'] = serialize($e['Question']['Catalogs']);
            if (!$this->get($e['Id']))$this->insert($exams);
        }
    }
    public  function question_array($val, $questions)
    {
        return array(
            'id' => $val['id'],
            'alias' => '',
            'num' => $val['num'],
            'qids' => $questions,

        );
    }
    public function exam_array($val)
    {
        $examtimes =  config::get('exam' , 'examtimes');
        return array(
            'modelid' =>  modelid('exam'),
            'catid' => $val['catid'],
            'title' => $val['title'],
            'isday' =>  0,
            'subtitle' =>'',
            'description' => $val['description'],
            'tags' =>'',
            'integral' =>  0,
            'thumb' =>'',
            'examtime' => $examtimes[$val['subjectid']],
            'starttime' =>'',
            'endtime' =>'',
            'maxanswers' =>'',
            'minhours' => 24,
            'proids' =>'',
            'typeid' => $val['subjectid'],
            'weight' => 80,
            'sectionids' =>'',
            'placeid' =>'',
            'published' =>'',
            'unpublished' =>'',
            'related_keywords' =>'',
            'allowcomment' => 1,
            'status' => 6,
            'seotitle' =>'',
            'seodescription' =>'',
            'seocode' =>'',
        );
    }
    public function  getcatid($proid)
    {
        $exam_catid = array(11100,11200,11300,11500);
        $content = loader::model('content', 'system');
        $propertys = common_data('property_0', 'brand');
        $category = & $content->category;
        $pro = $propertys[$proid];
        foreach($exam_catid as $cat) {
            $cats = $category[$cat];
            if (in_array($cats['typeid'], explode(',', $pro['parentids'])))return $cats['catid'];

        }

    }
}
