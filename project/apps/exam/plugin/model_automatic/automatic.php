<?php

class plugin_automatic extends object
{
	private $automatic,$propertys;
	
	public function __construct(& $automatic)
	{
		$this->automatic = $automatic;
        $this->propertys = common_data('property_0', 'brand');
	}
	
	public function after_assemble()
	{

        $subject = $this->automatic->data['subject'];
        if (!$this->automatic->data['tt'] && is_numeric($subject) ) {
            $vals = exam_redis_get('exam_'.$subject);
            $key = array_rand($vals, 1);
            $options = $vals[$key];
            $allcount = 0;
            foreach ($options as $val) {
                $allcount += $val['num'];
            }
        }
        if (!$allcount){
            $knowledge = $this->automatic->data['knowledge'];
            if (is_numeric($subject) && $subject > 0){
                $subject = $this->propertys[$subject];
                $subjectid = $subject['childids'] ?  $subject['childids'] : $subject['proid'];
                $subject_sql = " subjectid in({$subjectid}) AND ";
            }

            if ($knowledge){
                if (is_numeric($knowledge))$knowledge = explode(',', $knowledge);
                $knowledgeid = 0;
                foreach($knowledge as $know) {
                    $know = $this->propertys[$know];
                    $knowledgeid = $know['childids'] ? $knowledgeid . ',' .  $know['childids'] : $know['proid'];
                }
                $knowledge_sql = " knowledgeid in({$knowledgeid}) AND ";
            }
            $qtypes = $this->automatic->data['qtype'];
            $db = factory::db();
            $allcount = 0;
            foreach ($qtypes as $val) {
                $val['num'] = $val['num'] > 0 ? $val['num'] : 10;
                $qtype_sql = '';
                if (is_numeric($val['id']) && $val['id'] > 0){
                    $qtype = $this->propertys[$val['id']];
                    $qtypeid = $qtype['childids'] ?  $qtype['childids'] : $qtype['proid'];
                    $qtype_sql = " qtypeid in({$qtypeid}) ";
                }
                /*$sql = "SELECT t1.questionid
    FROM cms_exam_question AS t1 JOIN (SELECT ROUND(RAND() * (SELECT MAX(questionid) FROM cms_exam_question)) AS questionid) AS t2
    WHERE t1.questionid >= t2.questionid AND {$subject_sql}{$knowledge_sql}{$qtype_sql}
    ORDER BY t1.questionid ASC LIMIT 5; ";
                $question = $db->select($sql);*/
                /**
                 * 不超20W数据下，可以直接用rand()
                 */
                $sql = "SELECT questionid FROM #table_exam_question WHERE {$subject_sql}{$knowledge_sql}{$qtype_sql} AND bandid = 0 ORDER BY rand() LIMIT {$val['num']}";
                $question = $db->select($sql);
                $qval = array();
                foreach($question as $v1) {
                    $qval[] = $v1['questionid'];
                }
                $allcount = $allcount + count($qval);
                if ($qval)$options[] = $this->question_array($val, $qval);
            }
            if (!$options)return $this->automatic->error = '没有数据';
        }
        $exam = $this->exam_array();
        $exam['qtype'] = $options;
        $exam['notwrite'] = true;
        $exam['qcount'] = $allcount;
        $exam_model = loader::model('admin/exam', 'exam');
        $this->automatic->contentid = $exam_model->add($exam);
        $this->automatic->error = $exam_model->error;
	}
    public  function question_array($val, $questions)
    {
        return array(
            'id' => $val['id'],
            'alias' => $val['alias'],
            'num' => $val['num'],
            'qids' => $questions,

        );
    }
    public function exam_array()
    {
        return array(
        'modelid' => $this->automatic->modelid,
        'catid' => $this->automatic->data['catid'],
        'title' => $this->automatic->data['title'],
        'isday' => $this->automatic->data['isday'],
        'subtitle' =>'',
        'description' => $this->automatic->data['description'],
        'tags' =>'',
        'thumb' =>'',
        'examtime' => $this->automatic->data['examtime'],
        'starttime' =>'',
        'endtime' =>'',
        'maxanswers' =>'',
        'minhours' => 24,
        'proids' =>'',
        'weight' => 77, //web 使用
        'sectionids' =>'',
        'placeid' =>'',
        'typeid' => $this->automatic->data['subject'],
        'published' =>'',
        'unpublished' =>'',
        'related_keywords' =>'',
        'allowcomment' => 1,
        'status' => 1,
        'seotitle' =>'',
        'seodescription' =>'',
        'seocode' =>'',
        );
    }
}