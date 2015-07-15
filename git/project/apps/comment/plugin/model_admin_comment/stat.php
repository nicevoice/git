<?php

class plugin_stat extends object 
{
	private $comment;

	public function __construct(& $comment)
	{
		$this->comment = $comment;
	}

	public function after_check()
	{
        $ids = explode(',' ,$this->comment->commentids);
        if(!$ids) return false;
        $db = & factory::db();
        foreach($ids as $id)
        {
            $topicinfo = $db->get("SELECT topicid FROM #table_comment WHERE commentid=?", array($id));
            if(!$topicinfo) continue;
            $topicid = $topicinfo['topicid'];
            $db->update("UPDATE #table_comment_topic SET comments_pend=comments_pend-1 WHERE topicid=?", array($topicid));
        }
        $sql = "SELECT createdby AS userid, topicid FROM #table_comment WHERE status=2 AND commentid IN (" .$this->comment->commentids .")";
        $data = $db->select($sql);
        $userids = $topicids = array();
        foreach ($data AS $v)
        {
            $userids[$v['userid']]++;
            $topicids[$v['topicid']]++;
        }
        if($userids)
        {
            $userids = array_keys($userids);
            $userids = implode(',', $userids);
            $sql = "SELECT createdby AS userid, COUNT(*) AS comments FROM #table_comment WHERE status = 2 AND createdby IN ($userids) GROUP BY createdby";
            $data = $db->select($sql);
            foreach ($data AS $v)
            {
                $userid = intval($v['userid']);
                $comments = intval($v['comments']);
                if($userid)
                {
                    //更新会员
                    $db->update("UPDATE #table_member SET comments=$comments WHERE userid=?", array($userid));
                    //更新专栏
                    $db->update("UPDATE #table_space SET comments=$comments WHERE userid=?", array($userid));
                }
            }
        }
        if($topicids)
        {
            foreach ($topicids as $topicid => $dd)
            {
                //更新话题表
                $db->update("UPDATE #table_comment_topic SET comments=comments+$dd WHERE topicid=?", array($topicid));
                //更新内容表
                $db->update("UPDATE #table_content SET comments=comments+$dd WHERE topicid=?", array($topicid));
            }
        }
        return true;
	}

	public function before_delete()
	{
        $ids = explode(',' ,$this->comment->commentids);
        if(!$ids) return false;
        $db = & factory::db();
        foreach($ids as $id)
        {
            $topicinfo = $db->get("SELECT topicid,status FROM #table_comment WHERE commentid=?", array($id));
            if(!$topicinfo) continue;
            $topicid = $topicinfo['topicid'];
            if($topicid && $topicinfo['status'] == 2)
            {
                $db->update("UPDATE #table_comment_topic SET comments_pend=comments_pend-1 WHERE topicid=?", array($topicid));
            }
        }
        $sql = "SELECT createdby AS userid, topicid FROM #table_comment WHERE status=2 AND commentid IN (" .$this->comment->commentids .")";
        $data = $db->select($sql);
        $userids = $topicids = array();
        foreach ($data AS $v)
        {
            $userids[$v['userid']]++;
            $topicids[$v['topicid']]++;
        }
        if($userids)
        {
            $userids = array_keys($userids);
            $userids = implode(',', $userids);
            $sql = "SELECT createdby AS userid, COUNT(*) AS comments FROM #table_comment WHERE status = 2 AND createdby IN ($userids) GROUP BY createdby";
            $data = $db->select($sql);
            foreach ($data AS $v)
            {
                $userid = intval($v['userid']);
                $comments = intval($v['comments']);
                if($userid)
                {
                    //更新会员
                    $db->update("UPDATE #table_member SET comments=$comments WHERE userid=?", array($userid));
                    //更新专栏
                    $db->update("UPDATE #table_space SET comments=$comments WHERE userid=?", array($userid));
                }
            }
        }
        if($topicids)
        {
            foreach ($topicids as $topicid => $dd)
            {
                $topicinfo = $db->get("SELECT comments FROM #table_comment_topic WHERE topicid=?", array($topicid));
                $comments = $topicinfo['comments'];
                $comments = ($comments > $dd) ? ($comments - $dd) : 0;
                //更新话题表
                $db->update("UPDATE #table_comment_topic SET comments=$comments WHERE topicid=?", array($topicid));
                //更新内容表
                $db->update("UPDATE #table_content SET comments=$comments WHERE topicid=?", array($topicid));
            }
        }
        return true;
	}
}