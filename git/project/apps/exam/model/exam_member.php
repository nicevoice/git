<?php

class model_exam_member extends model {
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
        $this->_table = $this->db->options['prefix'] . 'exam_member';
        $this->_primary = 'user_id';
        $this->_fields = array('user_id', 'city', 'nickname','intro', 'logtime');
        $this->_readonly = array('user_id');
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
        !$result && $result = array();
        if ($_result)$result = array_merge($result, $_result);
        return $result;
    }
    function getUsers($uids)
    {
        $uids    = ' user_id in('.trim($uids,','). ')';
        $result  = $this->brand_member->select($uids,'user_id,real_name,name');
        $_result = $this->select($uids,'nickname,user_id');
        !$result && $result = array();
        if ($_result)$result = array_merge($result, $_result);
        $r  = array();
        foreach ($result as $v){
            if(isset($v['nickname'])) $v['name'] = $v['nickname']?$v['nickname']:'';
            if(isset($v['name'])) $v['name']      = $v['name']?$v['name']:$v['real_name'];
            unset($v['real_name']);
            unset($v['nickname']);
            $r[$v['user_id']] = $v;
        }
        return $r;
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

    /**
     *
     */
}