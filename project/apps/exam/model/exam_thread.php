<?php

class model_exam_thread extends model {
    private $brand_member;
    function __construct() {
        parent::__construct();
        /**
         * 会计网
         *
         */
        $user =  login_member();
        $this->_userid = $this->_userid ? $this->_userid : $user['user_id'];
        $this->_username = $this->_username ? $this->_username : $user['name'];
        $this->_table = $this->db->options['prefix'] . 'exam_thread';
        $this->_primary = 'threadid';
        $this->_fields = array('threadid', 'user_id', 'subjectid', 'tid', 'title','created');
        $this->_readonly = array('threadid');
        $this->_create_autofill = array('logtime'=>TIME);
        $this->_update_autofill = array();
        $this->_filters_input = array();
        $this->_filters_output = array();
        $this->_validators = array();
        $this->brand_member = loader::model('member', 'exam');
    }
    /**
     * 获取用户信息
     *
     * @param $uid
     * @param string $type
     * @return bool
     */
    function getUser($uid)
    {
        /**
         * member_info in kj_brand
         */

        $result = $this->brand_member->get($uid);

        $_result = $this->get($uid);
        if ($_result)$result = array_merge($result, $_result);
        return $result;
    }

    /**
     * 更新用户信息
     */
    function updateUser($uid,$data)
    {
        if (!$data['type'] || !$data['val'] || !$uid) return false;
        if (in_array($data['type'], $this->_fields)) {
            if ($_result = $this->get($uid)) {
                return $this->update(array($data['type']=>$data['val']), $uid);
            } else {
                return $this->insert(array('user_id'=>$uid, $data['type']=>$data['val']));
            }
        } else {
            return $this->brand_member->update(array($data['type']=>$data['val']), $uid);
        }

    }
}