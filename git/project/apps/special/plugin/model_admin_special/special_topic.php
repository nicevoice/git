<?php

class plugin_special_topic extends object
{
    private $special;

	function __construct(& $special)
	{
		$this->special = $special;
		$this->topic = loader::model('topic', 'comment');
	}

    function after_add()
    {
        $this->update();
    }

    function after_edit()
    {
        $this->update();
    }

    protected function update()
    {
        $contentid = $this->special->contentid;
        $db = & factory::db();
        if ($contentid && ($content = $db->get("SELECT `url`, `topicid` FROM `#table_content` WHERE `contentid` = $contentid")))
        {
            if ($content['topicid'])
            {
                $this->topic->update(array('url' => $content['url']), array('topicid' => $content['topicid']));
            }
        }
    }
}