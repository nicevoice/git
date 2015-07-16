<?php

class controller_panel extends member_controller_abstract
{

	private $member;

	function __construct(&$app)
	{
		parent::__construct($app);
		
		$session = & factory::session();
		$session->start();
		
		if (!$this->_userid) {
			$this->redirect(url('member/index/login'));
			exit;
		}
		$this->member = loader::model('member_front');

	}

	function index() {
		$this->profile();
	}

	function profile() {
		if ($this->is_post()) {
			$data = $_POST;
			$data['birthday'] = implode('-', $_POST['birthday']); //生日
			$data = htmlspecialchars_deep($data);
			if (!$this->member->update_detail($data, "`userid`={$this->_userid}")) {
				$result = array('state' => false, 'message' => $this->member->error());
			} else {
				$result = array('state' => true, 'message' => '保存成功');
			}
			echo $this->json->encode($result);
		} else {
			$data = $this->member->getProfile($this->_userid);

			$this->template->assign('member', $data);
			$this->template->display('member/panel/profile.html');
		}
	}

	function password() {
		if ($this->is_post()) {
			if (!$this->_validate_password()) {
				$result = array('state' => false, 'message' => $this->error);
			} else {
				if ($this->member->password($this->_userid, $_POST['password'], $_POST['last_password'])) {
					$result = array('state' => true, 'message' => '密码修改成功');
				} else {
					$result = array('state' => false, 'message' => $this->member->error());
				}
			}
			echo $this->json->encode($result);
		} else {
			$member = $this->member->get($this->_userid);

			$this->template->assign('member', $member);
			$this->template->display('member/panel/password.html');
		}
	}

	function email() {
		if ($this->is_post()) {
			if (!$this->_validate_email()) {
				$result = array('state' => false, 'message' => $this->error);
			} else {
				if ($this->member->email($this->_userid, $_POST['password'], $_POST['email'])) {
					$result = array('state' => true, 'message' => 'E-mail修改成功');
				} else {
					$result = array('state' => false, 'message' => $this->member->error());
				}
			}
			echo $this->json->encode($result);
		} else {
			$this->template->display('member/panel/email.html');
		}
	}

	function avatar() {
		if ($_FILES['photo']) {
			import('attachment.upload');

			list($photo_path, $rename) = $this->member->set_photo_path($this->_userid);
			$upload = new upload(UPLOAD_PATH . 'avatar/' . $photo_path, 'gif|jpg|jpeg', 2048);
			if (!$photo = $upload->execute('photo', $rename . '.jpg')) {
				$this->showmessage($upload->error());
			} else {
				//删除旧的缩略图
				$old_thumbs = glob(UPLOAD_PATH . 'avatar/' . $photo_path . '/*_' . $rename . '.jpg');
				if (!empty($old_thumbs)) {
					foreach ($old_thumbs as $v) {
						@unlink($v);
					}
				}
				$this->member->set_field('avatar', '1', $this->_userid);
			}
		}
		$this->template->display('member/panel/avatar.html');
	}

	//私有验证
	private function _validate_password() {
		if (empty($_POST['last_password'])) {
			$this->error = '必须提供原密码';
			return false;
		}

		if ($_POST['password'] == '' || $_POST['password_check'] == '') {
			$this->error = '新密码都必须填写';
			return false;
		}
		if ($_POST['password'] != $_POST['password_check']) {
			$this->error = '新密码不一致';
			return false;
		}
		return true;
	}

	private function _validate_email() {
		if (empty($_POST['password'])) {
			$this->error = '提供登录密码';
			return false;
		}
		if ($_POST['email'] == '' || $_POST['email_check'] == '') {
			$this->error = '新E-mail都必须填写';
			return false;
		}
		if ($_POST['email'] != $_POST['email_check']) {
			$this->error = '新E-mail不一致';
			return false;
		}
		return true;
	}

}