<?php

class plugin_comment extends object 
{
	private $comment;

	public function __construct(& $comment)
	{
		$this->comment = $comment;
	}

	/**
	 * 超过一定楼层后隐藏
	 * 
	 * @todo 后台添加自定义选项 楼层
	 */
	public function after_addfloorno()
	{
		$fcount = count($this->comment->floorary);
		// 获取后台楼层配置信息
		$floorno = setting('comment', 'floorno');
		if($fcount > $floorno)
		{
			$html = '<div class="citation" onclick="comment.display(this)">已有'.($fcount-1).'层回复点击查看全部</div>';
			array_unshift($this->comment->floorary, '<div class="hide">');
			array_push($this->comment->floorary, '</div>');
			array_push($this->comment->floorary, $html);
		}
	}

	public function after_ls()
	{
		foreach ($this->comment->data as $key => $item)
		{
			$item['anonymous'] && $this->comment->data[$key]['nickname'] = '匿名网友';
		}
	}
}