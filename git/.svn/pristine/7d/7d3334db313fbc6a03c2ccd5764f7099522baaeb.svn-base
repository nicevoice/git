<?php
class model_sign extends model
{
	public $signid,$data;
	private $cache;

	function __construct()
	{
		parent::__construct();

		$this->_table = $this->db->options['prefix'].'activity_sign';
		$this->_primary = 'signid';
		$this->_fields = array('contentid','name', 'sex', 'photo', 'identity', 'company', 'job', 'telephone','mobile','email','qq','msn','site','address','zipcode','aid','note','created','createdby','ip','state');	
		$this->_readonly = array('signid');
		$this->_create_autofill = array('createdby'=>$this->_userid, 'created'=>TIME, 'ip'=>IP ,'state'=>0);
		$this->_update_autofill = array();
		$this->_filters_input = array();
		$this->_filters_output = array();
		$this->_validators = array('name' => array('max_length'=>array('50','名字不得超过50个字节')),
		                           'company'=> array('max_length'=>array('100','所在单位信息不得超过100个字节')),
		                           'job'=> array('max_length'=>array('50','职业信息不得超过50个字节')),
		                           'address'=> array('max_length'=>array('100','地址信息不得超过100个字节'))
		);
		$this->activity = loader::model('admin/activity','activity');
		$this->cache = factory::cache();
	}

	function add($data)
	{
		$data = htmlspecialchars_deep($data);
		$data['photo'] = '';
		$data['aid'] = null;
		$contentid = intval($data['contentid']);
		$this->data = $data;
		
		if(!$this->submit_check()) return false;
		$signid = $this->insert($this->data);
		if($signid)
		{
			$r = $this->db->exec("UPDATE `#table_activity` SET `total`=`total`+1 WHERE `contentid`=$contentid");
			return $signid;
		}
		else return false;
	}
	
	function get($signid)
	{	
        $data = parent::get($signid);
        if(!$data)
        {
        	return false;
        }
        
        return $data;
	}
	
	function submit_check()
	{
		if(!$signmsg = loader::model('admin/activity','activity')->get($_POST['contentid'])) 
		{
		   	$this->error = '活动不存在';
		   	return false;
		}
		
		if(!$this->checkip($signmsg))
		{
			return false;
		}
		
		$signmsg['signend'] = is_null($signmsg['signend'])?TIME+3600:$signmsg['signend'];
		$signmsg['maxpersons'] = $signmsg['maxpersons']?$signmsg['maxpersons']:$signmsg['checkeds']+1;
		if(TIME<$signmsg['signstart'])
		{
			$this->error = '报名未开始';
			return  false;
		}
		else 
		{
			if($signmsg['signstoped'] && $signmsg['checkeds']<$signmsg['maxpersons'] && TIME<$signmsg['signend'])
			{
				$this->error = '活动被暂停';
				return  false;
			}
			if(TIME>$signmsg['signend'] || $signmsg['checkeds']>=$signmsg['maxpersons'])
			{
				$this->error = '报名已结束';
				return  false;
			}
		}
		
		import('helper.seccode');
		$seccode = new seccode();
		if(!$seccode->valid())
		{
			$this->error = '验证码不正确';
			return false;
		}

		$required = explode(',',$signmsg['required']);
		
		if(empty($this->data['name']) && in_array('name',$required))
		{
			
			$this->error = '姓名不得空';
			return false;
		}
		
		if(empty($_FILES['photo']['name']) && in_array('photo',$required))
		{
			$this->error = '照片不得为空';
			return false;
		}
		elseif($_FILES['photo']['name'])
		{
			if(!$this->upload('photo')) return false;
		}
		
		if(empty($this->data['identity']) && in_array('identity',$required))
		{
			$this->error = '身份证号码不得为空！';
			return false;
		}
		elseif($this->data['identity']) 
		{
			if(!preg_match('/^[1-9]\d{5}[1-9]\d{3}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}[\d|x]$/',$_POST['identity'])) 
			{
				$this->error = '身份证号码格式不正确！';
				return false;
			}
		}
		
		if(empty($this->data['company']) && in_array('company',$required))
		{
			$this->error = '所在单位信息不得为空';
			return false;
		}
		
		if(empty($this->data['job']) && in_array('job',$required))
		{
			$this->error = '工作信息不得为空';
			return false;
		}
		
		if(empty($this->data['telephone']) && in_array('telephone',$required))
		{
			$this->error = '电话号码不得为空';
			return false;
		}
		elseif ($this->data['telephone'])
		{
			if(!preg_match('/^(86)?(\d{3,4}-)?(\d{7,8})$/',$_POST['telephone'])) 
			{
				$this->error = '电话号码格式不正确！';
				return false;
			}
		}
		
		if(empty($this->data['mobile']) && in_array('mobile',$required))
		{
			$this->error = '手机号码不得为空';
			return false;
		}
		elseif($this->data['mobile'])
		{
			if(!preg_match('/^1\d{10}$/',$this->data['mobile'])) 
			{
				$this->error = '手机号码格式不正确！';
				return false;
			}
		}
		
		if(empty($this->data['email']) && in_array('email',$required))
		{
			$this->error = '电子邮箱不得为空';
			return false;
		}
		elseif($this->data['email'])
		{
			if(!preg_match('/^[\w\-\.]+@[\w\-\.]+(\.\w+)+$/',$this->data['email'])) 
			{
				$this->error = '电子邮箱格式不正确！';
				return false;
			}
		}
		
		if(empty($this->data['qq']) && in_array('qq',$required))
		{
			$this->error = 'qq号码不得为空';
			return false;
		}
		elseif($this->data['qq'])
		{
			if(!preg_match('/^[1-9]\d{4,20}$/',$this->data['qq'])) 
			{
				$this->error = 'qq格式不正确！';
				return false;
			}
		}
		
		if(empty($this->data['msn']) && in_array('msn',$required))
		{
			$this->error = 'msn不得为空';
			return false;
		}
		elseif($this->data['msn'])
		{
			if(!preg_match('/^[\w\-\.]+@[\w\-\.]+(\.\w+)+$/',$this->data['msn'])) 
			{
				$this->error = 'msn格式不正确！';
				return false;
			}
		}
		
		if(empty($this->data['site']) && in_array('site',$required))
		{
			$this->error = '个人主页不得为空';
			return false;
		}
		elseif($this->data['site'])
		{
			if(!preg_match('/http(s)?:\/\/([\w-]+\.)+[\w-]+(\/[\w- .\/?%&=]*)?/',$this->data['site'])) 
			{
				$this->error = '网址格式不正确！';
				return false;
			}
		}
		
		if(empty($this->data['address']) && in_array('address',$required))
		{
			$this->error = '地址信息不得为空';
			return false;
		}
		
		if(empty($this->data['zipcode']) && in_array('zipcode',$required))
		{
			$this->error = '邮政编码不得为空';
			return false;
		}
		elseif($this->data['zipcode'])
		{
			if(!preg_match('/^[0-9]\d{5}$/',$this->data['zipcode'])) 
			{
				$this->error = '邮政编码格式不正确！';
				return false;
			}
		}
		
		if(empty($_FILES['aid']['name']) && in_array('aid',$required))
		{
			$this->error = '附件不得为空';
			return false;
		}
		elseif($_FILES['aid'])
		{
			if(!$this->upload('aid'))
			return false;
			
		}
		
		if(empty($this->data['note']) && in_array('note',$required))
		{
			$this->error = '附加说明不得为空';
			return false;
		}

		$this->data = htmlspecialchars_deep($this->data);
		return true;
	}
	
	function upload($filename)
	{
		if(!$filename) return ;
		$attachment = loader::model('admin/attachment', 'system');
		if($filename == 'photo') $ext = 'jpg|gif|png';
		else $ext = 'gif|jpg|rar|txt|zip|swf|gif|doc|docx|bmp|wmv|avi|mpg|rm';
		if($file = $attachment->upload($filename,true, null,$ext,20*1024))
		{
			if($filename == 'photo')
			{
				$this->data['photo'] = $file;
			}
			else 
			{
				$this->data['aid'] = array_pop($attachment->aid);
			}
			return true;		
		}
		else
		{
			$this->error = $attachment->error;
			return false;
		}
	}
	
	/**
	 * IP地址重复提交检测
	 * @param array $data 此条活动的数据信息
	 * @return boolean
	 */
	private function checkip($data)
	{
		if($data['mininterval'])
		{
			$_key 			= 'activity_ipcheck_' .$data['contentid'];
			$_key_time 		= 'activity_ipcheck_time_' .$data['contentid'];
			$key_data 		= $this->cache->get($_key);
			$key_data_time 	= $this->cache->get($_key_time);
			if(!$key_data)
			{
				$key_data = array();
			}
			if(!$key_data_time)
			{
				$key_data_time = array();
			}
			if(in_array(IP, $key_data))
			{
				if(isset($key_data_time[IP]))
				{
					$_last_time = $key_data_time[IP];
					$_n = floor((TIME - $_last_time) / 3600);
					if($_n >= $data['mininterval'])
					{
						// 已经过期, 重新记录
						$key_data_time[IP] = TIME;
						$this->cache->set($_key_time,$key_data_time);
					}else{
						// 尚未过期，返回失败
						$_n = $data['mininterval'] - $_n;
						$this->error = '你的IP地址已经参与过报名，请'.$_n.'小时后再来';
						return false;
					}
				}else{
					// 没有这个IP的缓存时间，记录一次
					$key_data_time[IP] = TIME;
					$this->cache->set($_key_time,$key_data_time);
				}
			}else{
				// 没有这个IP的记录，第一次记录
				$key_data[] = IP;
				$key_data_time[IP] = TIME;
				$this->cache->set($_key,$key_data);
				$this->cache->set($_key_time,$key_data_time);
			}
		}
		return true;
	}
}