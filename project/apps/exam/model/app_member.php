<?php

class model_app_member extends Model {

    public function __construct() {
        $this->db = & factory::db(load_dsn(4));
		$this->db->options['prefix']="kj_";
        $this->_table = $this->db->options['prefix'] . 'member';
        $this->_primary = 'member_id';
        $this->_fields = array('member_id', 'name', 'email', 'mobile', 'real_name', 'sex', 'is_email', 'is_mobile', 'is_sina_weibo', 'is_qq_connect', 'reg_time', 'log_time');
        $this->_readonly = array('member_id', 'name');
        load_uc();
    }
	
	
	public function add($member_id, $name, $email) {
        $data = array();
        $data['member_id'] = intval($member_id);
        $data['name'] = trim($name);
        $data['email'] = trim($email);
        $data['reg_time'] = $data['log_time'] = time();
        return $this->insert($data);
    }
	
	
	public function register($name, $passwd, $email, $mobile) {
		if ($this->count("mobile = '" . $mobile . "'")) {//判断手机是否注册
            $this->error = '该号码已经被注册';
            return false;
        }
		/* $setting = setting::get('system') ; 
		if(!$setting['passport_reg']){
			$this->error = $setting['reg_colse_reason'] ; 
			return false ; 
		} */

        $uc_result = uc_user_register($name, $passwd, $email);
        switch ($uc_result) {
            case -1:
                $this->error = '用户名不合法';
                return false;
            case -2:
                $this->error = '包含不允许注册的词语';
                return false;
            case -3:
                $this->error = '用户名已经存在';
                return false;
            case -4:
                $this->error = '号码格式有误';
                return false;
            case -5:
                $this->error = '号码不允许注册';
                return false;
            case -6:
                $this->error = '该号码已经被注册';
                return false;
			case -999:
                $this->error = '暂时不支持注册';
                return false;
            default:
                $member_id = intval($uc_result);
                break;
        }
		$uid=$this->add($member_id, $name, $email) ? $member_id : 0;////同步到uc的kj_member
		$data=array(
		            'user_id'=>$member_id,
		            'name'=>$name,
		            'email'=>$email,
		            'mobile'=>$mobile,
		            'is_mobile'=>1
					);
        load_rpc('my')->member_add($data);//同步到kj_brand的kj_member
		$this->bind_mobile($member_id, $mobile);//手机绑定
        return $uid;
    }
	
	public function bind_mobile($member_id, $mobile) {
        $data = array(
            'mobile' => $mobile,
            'is_mobile' => 1
        );
        $this->update($data, 'member_id = ' . $member_id);
        return true;
    }
}