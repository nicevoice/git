<?php
class myQueue {
	
	protected $stat = NULL;
	protected $memcache = NULL;
	
	function __construct()
	{
		$this->memcache = new myMemcache();
	}
	
	function __destruct()
	{
		$this->memcache = NULL;
		$this->stat = NULL;
	}
	
	/*
	 * 读取队列中的一个值，返回一个数组
	 * @return array
	 */
	function read($key='')
	{
		$data = FALSE;
		// 如果指定了KEY，直接返回该KEY队列的内容
		if($key)
		{
			$data = $this->memcache->get($key);
			return array('id'=>$key, 'data'=>$data);
		}
		// 读取队列的状态信息
		$this->stat = $this->memcache->get('stat');
		// 判断队列中是否还有未处理的数据
		if ($this->stat['curid'] == $this->stat['maxid'])
		{
			// -- 如果没有，重置stat
			if ($this->stat['curid'])
			{
				$this->memcache->set('stat', array('maxid'=>0, 'curid'=>0));
			}
		}
		else
		{
			// -- 如果有，+1处理
			if (empty($this->stat['curid']))
			{
				$this->stat['curid'] = 1;
			}
			else 
			{
				$this->stat['curid']++;
			}
			if($data = $this->memcache->get($this->stat['curid']))
			{
				$this->memcache->set('stat', $this->stat);
			}
		}
		return array('id'=>$this->stat['curid'], 'data'=>$data);
	}
	
	/*
	 * 向队列中写入一个值
	 * @param string $value
	 * @return bolean
	 */
	function write($value='')
	{
		// 读取文件检测KEY，判断该内容是否已经在队列中
		if($id = $this->check($value))
		{
			return $id;
		}
		// 读取队列的状态信息
		$this->stat = $this->memcache->get('stat');
		if (empty($this->stat['maxid']))
		{
			$this->stat['curid'] = 0;
			$this->stat['maxid'] = 1;
		}
		else
		{
			$this->stat['maxid']++;
		}
		// 写入队列，如果成功，更新stat
		if ($this->memcache->set($this->stat['maxid'], $value))
		{
			// 写入内容检测KEY
			$this->memcache->set(md5($value), $this->stat['maxid']);
			$this->memcache->set('stat', $this->stat);
		}
		return $this->stat['maxid'];
	}

	/*
	 * 删除一个队列
	 * @param string $key
	 * @return bolean
	 */
	function delete($key='')
	{
		if(empty($key))
		{
			return FALSE;
		}
		$data = $this->read($key);
		if($this->memcache->rm($key))
		{
			// 清理掉文件检测KEY
			$this->memcache->rm(md5($data['data']));
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}
	/*
	 * 更新执行状态
	 * @param intval $value
	 * @return bolean
	 */
	function updateStat($value=0)
	{
		$stat = $this->stat();
		if ($stat['maxid'] == $value)
		{
			$stat['maxid'] = $stat['curid'] = 0;
		}
		else
		{
			$stat['curid'] = $value;
		}
		$this->memcache->set('stat',$stat);
		return TRUE;
	}
	
	/*
	 * 检查指定的内容是否已经存在队列中，如果存在返回该内容的队列编号
	 * @param string $value
	 * @return intval
	 */
	function check($value='')
	{
		if(empty($value))
		{
			return FALSE;
		}
		if($id = $this->memcache->get(md5($value)))
		{
			return $id;
		}
		else
		{
			return FALSE;
		}
	}
	
	/*
	 * 取得队列的统计信息
	 * @param intval $type
	 * @return array
	 */
	function stat($type='simple')
	{
		switch ($type)
		{
			case 'simple':
				return $this->memcache->get('stat');
				break;
			case 'detail':
				$this->stat = $this->memcache->get('stat');
				for ($i=$this->stat['curid']+1;$i<=$this->stat['maxid'];$i++)
				{
					$queue[] = $this->read($i);
				}
				return array('stat'=>$this->stat, 'queue'=>$queue);
			default:
				break;
		}
		return ;
	}
		
	/*
	 * 清空队列
	 * 
	 */
	function clean()
	{
		$stat = $this->stat();
		if($stat['curid'] < $stat['maxid'])
		{
			for($i=$stat['curid']+1;$i<=$stat['maxid'];$i++)
			{
				$this->delete($i);
			}
		}
		$this->memcache->set('stat', array('maxid'=>0, 'curid'=>0));
		return ($stat['maxid'] - $stat['curid']);
	}
}
