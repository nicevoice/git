<?php

class plugin_topic extends object 
{
	private $comment, $topicid;
	
	public function __construct(& $comment)
	{
		$this->comment = $comment;
        $this->topicid = $this->comment->topicid;
	}
	
	public function after_add()
	{
        $db = & factory::db();
		if ($this->comment->data['status'] == 1)
		{
    		$db->update("UPDATE #table_comment_topic SET comments_pend=comments_pend+1 WHERE topicid=?", array($this->topicid));
        }
        elseif($this->comment->data['status'] == 2)
		{
            //更新话题表
            $db->update("UPDATE #table_comment_topic SET comments=comments+1 WHERE topicid=?", array($this->topicid));
            //更新内容表
            $db->update("UPDATE #table_content SET comments=comments+1 WHERE topicid=?", array($this->topicid));
            //更新会员表
            if ($this->comment->_userid)
            {
                $db->update("UPDATE #table_member SET comments=comments+1 WHERE userid=?", array($this->comment->_userid));
            }
            //更新专栏的
            $spaceinfo = $db->get("SELECT spaceid FROM #table_content WHERE topicid=?", array($this->topicid));
            if (isset($spaceinfo['spaceid']) && $spaceinfo['spaceid'])
            {
                $db->update("UPDATE #table_space SET comments=comments+1 WHERE spaceid=?", array($spaceinfo['spaceid']));
            }
		}
    }
}