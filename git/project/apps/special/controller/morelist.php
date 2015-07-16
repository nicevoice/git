<?php
class controller_morelist extends special_controller_abstract
{
    protected $_template = 'special/morelist.html';
    protected $_pagesize = 50;
    protected $_maxpage = 100;

    function __construct(&$app)
    {
        parent::__construct($app);
    }

    function widget()
    {
		$contentid = intval(value($_GET, 'contentid'));
        $widgetid = intval(value($_GET, 'widgetid'));
        $page = intval(value($_GET, 'page'));

        if ($this->system['pagecached'])
		{
			$keyid = md5('pagecached_special_morelist_widget_'.$contentid.'_'.$widgetid.'_'.$page);
			cmstop::cache_start($this->system['pagecachettl'], $keyid);
		}

        if (!$contentid
            || ! $widgetid
            || ! ($special = loader::model('admin/special')->get($contentid))
            || $special['status'] != 6 // 内容未发布
            || ! ($widget = loader::model('admin/widget')->get($widgetid))
            || $widget['status'] < 0)  // 模块已删除
        {
            $this->showmessage('您访问的内容不存在', WWW_URL);
        }

        $setting = decodeData(value($widget, 'setting'));
        if (! $setting
            || ! ($morelist = value($setting, 'morelist'))
            //|| ! ($enabled = intval(value($morelist, 'enabled', 0)))
            || ! ($data = decodeData(value($widget, 'data')))
            || $data['method'] == 1 // 手动维护，无列表
            || ! ($options = value($data, 'options')))
        {
            $this->showmessage('您访问的内容不存在', WWW_URL);
        }

        $widget['setting'] = $setting;
        $widget['data'] = $data;

        if (! ($template = trim(value($special, 'morelist_template'))))
        {
            $template = $this->_template;
        }
        if (! ($pagesize = intval(value($special, 'morelist_pagesize'))))
        {
            $pagesize = $this->_pagesize;
        }
        $maxpage = value($special, 'morelist_maxpage', $this->_maxpage);
        $page = $page ? $page : 1;
        $start = $pagesize * ($page - 1);
        $maxsize = $pagesize * intval($maxpage);
        $urlrule = APP_URL . "morelist.php?contentid={$contentid}&widgetid={$widgetid}&page=" . '{$page}';

        if ($maxpage && $start > ($maxsize - $pagesize))
        {
            $this->showmessage('没有更多内容了');
        }

        if ($data['method'] == 2)
        {
            // 自动调用
			$options['fields'] = '*';
			if ($options['weight']['range'])
			{
				$options['weight'] = $options['weight'][0].','.$options['weight'][1];
			}
			else
			{
				$options['weight'] = $options['weight'][0];
			}
			$orderby = array();
			foreach ($options['orderby'][0] as $i=>$f)
			{
				$s = $options['orderby'][1][$i];
				$orderby = "$f $s";
			}
			$options['orderby'] = implode(',', $orderby);

			$options['size'] = $pagesize;
            $options['page'] = $page;
			$data = tag_content($options);
        }
        else
        {
            // 编辑推荐
            $db = factory::db();
            $sql = "SELECT c.*, d.*
                    FROM `#table_place_data` d
                    LEFT JOIN `#table_content` c
                    ON d.`contentid` = c.`contentid`
                    WHERE d.`placeid` = {$widget['widgetid']}
                    AND d.`status` = 1
                    ORDER BY d.`sort` DESC, d.`time` DESC
                    LIMIT $start, $pagesize";
            $data = $db->select($sql);
        }

        $this->template->assign('special', $special);
        $this->template->assign('widget', $widget);
        $this->template->assign($data);
        $this->template->assign('total', $maxsize ? min($maxsize, $data['total']) : $data['total']);
        $this->template->assign('page', $page);
        $this->template->assign('pagesize', $pagesize);
        $this->template->assign('urlrule', $urlrule);
        $this->template->assign('pos', loader::model('category', 'system')->pos($special['catid']));

        $this->template->display($template);

        if ($this->system['pagecached']) cmstop::cache_end();
    }
}