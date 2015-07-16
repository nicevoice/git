<?php

class model_automatic extends model implements SplSubject
{
	public $content, $catid, $modelid, $contentid, $data, $fields;
    private $observers = array();
	function __construct()
	{
		parent::__construct();
        /**
         * 会计网
         */
        $user =  login_member();
        $this->_userid = $this->_userid ? $this->_userid : $user['user_id'];
        $this->_username = $this->_username ? $this->_username : $user['name'];
		$this->_table = $this->db->options['prefix'].'exam_automatic';
		$this->_primary = 'automaticid';
		$this->_fields = array('automaticid', 'catid', 'title', 'description', 'pub', 'integral', 'qtype','knowledge', 'subject', 'created', 'createdby', 'examtime','isday');
		$this->_readonly = array('automaticid');
		$this->_create_autofill = array();
		$this->_update_autofill = array();
		$this->_filters_input = array();
		$this->_filters_output = array();
        $this->contentid = 0;

	}

    /**
     * 自动组卷
     * @param $catid
     * @param $subjectid
     * @param int $knowledgeid
     * @param $tt
     * @return int
     */
    public function automatic_exam($catid, $subjectid, $knowledgeid = 0, $tt = false)
    {

        //1，“每日一练”、“组卷模考”、“真题模考”、“专项练习”等试卷标题完善为：”X月X日｛科目名称｝每日一练”、“｛科目名称｝组卷模考”、“｛真题名称｝模考”、“｛科目名称｝｛知识点｝专项练习”。
        $propertys = common_data('property_0', 'brand');
        $qtype =  config::get('exam' , 'qtype');
        $examtimes =  config::get('exam' , 'examtimes');
        $qtypeids = $qtype[$subjectid];
        if ($tt) {
            //来15道的题型
            $qtypeids = array(
                0=>array(
                    'id' => 101001,
                    'alias' =>'',
                    'num' =>'5',
                ),
                1=>array(
                    'id' => 101002,
                    'alias' =>'',
                    'num' =>'5',
                ),
                2=>array(
                    'id' => 101004,
                    'alias' =>'',
                    'num' =>'5',
                )
            );

        }
        $data = array(
            'catid' => $catid,
            'title' => $tt ? $propertys[$subjectid]['name'] . $propertys[$knowledgeid]['name'] .'专项练习':  $propertys[$subjectid]['name'] . '组卷模考',
            'description' =>'',
            'qtype' => $qtypeids,
            'examtime' => $examtimes[$subjectid],
            'integral' =>'',
            'isday' => 0,
            'pub' => 0,
            'tt' => $tt,
            'subject' => $subjectid,
            'knowledge' => $knowledgeid,
        );
        $this->data = $data;
        $this->modelid = modelid('exam');
        $this->event = 'after_assemble';
        $this->notify();

        return $this->contentid;



    }
    public function qtype($subjectid)
    {

    }
    public function attach(SplObserver $observer)
    {
        $this->observers[] = $observer;
    }

    public function detach(SplObserver $observer)
    {
        if($index = array_search($observer, $this->observers, true)) unset($this->observers[$index]);
    }

    public function notify()
    {
        foreach ($this->observers as $observer)
        {
            $observer->update($this);
        }
    }


}