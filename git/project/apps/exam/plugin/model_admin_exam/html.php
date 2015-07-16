<?php

class plugin_html extends object 
{
	private $exam, $category, $uri, $template, $json;
	
	public function __construct(& $exam)
	{
		$this->exam = $exam;
		$this->template = factory::template();
		$this->json = factory::json();
		$this->category = loader::model('category', 'system');
		$this->uri = loader::lib('uri', 'system');
		
		import('helper.folder');
	}
	
	public function after_add()
	{
		$this->write($this->exam->contentid);
	}
	
	public function after_edit()
	{
		$this->write($this->exam->contentid);
	}
	
	public function after_publish()
	{
		$this->write($this->exam->contentid);
	}
	
	public function after_unpublish()
	{
		$this->unlink($this->exam->contentid);
	}
	
	public function after_remove()
	{
		$this->unlink($this->exam->contentid);
	}
	
	public function before_delete()
	{
		$this->unlink($this->exam->contentid);
		//$this->delrelated($this->exam->contentid);
	}
	
	public function after_restore()
	{
		$this->write($this->exam->contentid);
	}
	
	public function after_pass()
	{
		$this->write($this->exam->contentid);
	}
	
	public function before_move()
	{
		$this->unlink($this->exam->contentid);
	}
	
	public function after_move()
	{
		$this->write($this->exam->contentid);
	}
	
	public function html_write()
	{
		$this->write($this->exam->contentid);
	}
	
	private function write($contentid)
	{
        $r = loader::model('exam', 'exam')->get($contentid);
		if (!$r)
		{
			$this->error = $this->exam->error();
			return false;
		}
        $count = 0;
        foreach ($r['question'] as $question) {
           // echo $question['type'];
            foreach ($question['question'] as $q1) {
                if ($q1['type'] == 'read') {

                    $count =  $count + count($q1['questions']);
                } else {
                    ++$count;
                }
            }
        }
        if ($r['qcount'] != $count)$this->exam->update(array('qcount'=>$count), $r['contentid']);
		//if ($r['status'] != 6) return false;
		$template = $r['template'] ? $r['template'] : $this->exam->content->template($r['catid'], $r['modelid']);
        if (!$template) $template = 'exam/show.html';
        $this->template->ext = '';
		$this->template->assign($r);
		$this->template->assign('head', array('title'=>$r['title']));
		$this->template->assign('pos', $this->category->pos($r['catid']));
		$data = $this->template->fetch($template);


        $dir = splitdir($r['md5id']);
		//$r = $this->uri->content($contentid);
		//$filename = $r['path'];
        define('EXAM_HTML_PATH', WWW_PATH . 'www/exam' . DS);
        $filename = EXAM_HTML_PATH . 'show/'.$dir.$r['md5id'] . '.html';

        if ($r['url'] != WWW_URL.'exam/show/'.$r['md5id'] . '.html')loader::model('admin/content', 'system')->update(array('url'=>WWW_URL.'exam/show/'.$r['md5id'] . '.html'), $r['contentid']);
		folder::create(dirname($filename));
		write_file($filename, $data);
		return true;
	}

    private function unlink($contentid)
	{
		$r = $this->uri->content($contentid);
		if (!$r)
		{
			$this->error = '题库不存在';
			return false;
		}
		return @unlink($r['path']);
	}

}
