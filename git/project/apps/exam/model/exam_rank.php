<?php

class model_exam_rank extends model {

    function __construct() {
        parent::__construct();
        /**
         * 会计网
         */
        $user =  login_member();
        $this->_userid = $this->_userid ? $this->_userid : $user['user_id'];
        $this->_username = $this->_username ? $this->_username : $user['name'];
        $this->_table = $this->db->options['prefix'] . 'exam_member_rank';
        $this->_primary = 'rankid';
        $this->_fields = array('rankid', 'user_id', 'subjectid', 'gold','plantform_id','modified','created');
        $this->_readonly = array('rankid');
        $this->_create_autofill = array();
        $this->_update_autofill = array('modified' => TIME);
        $this->_filters_input = array();
        $this->_filters_output = array();
        $this->_validators = array();
    }

    public function  add($data){
        $psubject = array(301,302,303,305);
        $id_list  = array();
        $this->propertys = common_data('property_0', 'brand');
        foreach($psubject as $subjectid){
            $explode  =explode(',',$this->propertys[$subjectid]['childids']);
            foreach($explode as $sid){
                $id_list[$sid] =$subjectid;
            }
        }
        $data['subjectid']=$id_list[$data['subjectid']];
        $where  =array(   'plantform_id'   =>$data['plantform_id'],
                            'user_id'       =>$data['user_id'],
                            'subjectid'     =>$data['subjectid']
        );
        if(!$data['plantform_id']){
            unset($where['plantform_id']);
            unset($data['plantform_id']);
        }
        $res    = $this->get($where);
        if(!$res) $this->insert($data);
        else{
            $data['gold'] = $res['gold']+$data['gold'];
            $this->update($data,$where);
        }
    }
}
