<?php

class model_admin_article extends model implements SplSubject {

    public $content, $catid, $modelid, $contentid, $data, $fields, $order, $action, $category, $filterword;
    private $observers = array();

    function __construct() {
        parent::__construct();
        $this->_table = $this->db->options['prefix'] . 'article';
        $this->_primary = 'contentid';
        $this->_fields = array('contentid', 'subtitle', 'author', 'editor', 'description', 'content', 'pagecount', 'saveremoteimage');
        $this->_readonly = array('contentid');
        $this->_create_autofill = array();
        $this->_update_autofill = array();
        $this->_filters_input = array();
        $this->_filters_output = array();
        $this->_validators = array('contentid' => array('not_empty' => array('内容ID不能为空'),
                'is_numeric' => array('内容ID必须是数字'),
                'max_length' => array(8, '内容ID不得超过8个字节'),
            )
        );
        $this->content = loader::model('admin/content', 'system');
        $this->category = & $this->content->category;
        $this->modelid = modelid('article');
    }

    public function __call($method, $args) {
        if (!priv::aca('article', 'article', $method))
            return true;
        if (in_array($method, array('clear', 'remove', 'restore', 'restores', 'approve', 'pass', 'reject', 'islock', 'lock', 'unlock', 'publish', 'unpublish'), true)) {
            $id = id_format($args[0]);
            if ($id === false) {
                $this->error = "$id 格式不正确";
                return false;
            }
            if (is_array($id))
                return array_map(array($this, $method), $id);
            if (in_array($method, array('clear', 'restores'))) {
                $this->catid = $args[0];
            } else {
                $this->contentid = $args[0];
            }

            $this->event = 'before_' . $method;
            $this->notify();
            $result = $this->content->$method($args[0]);
            if (!$result) {
                $this->error = $this->content->error();
                return false;
            }
            $this->event = 'after_' . $method;
            $this->notify();
            return $result;
        }
    }

    function get($contentid, $fields = '*', $action = null, $table_article = true) {
        if (!in_array($action, array(null, 'get', 'view', 'show')))
            return false;

        $this->contentid = intval($contentid);
        $this->fields = $fields;
        $this->action = $action;
        $this->table_article = $table_article;

        $this->event = 'before_get';
        $this->notify();

        if ($this->table_article) {
            $this->data = $this->db->get("SELECT $this->fields FROM `#table_content`, `#table_article` WHERE `#table_content`.`contentid`=`#table_article`.`contentid` AND `#table_content`.`contentid`=$this->contentid");
            if ($this->data && ($this->action == 'get' || $this->action == 'view' || $this->action == 'show')) {
                if ($this->action != 'show')
                    $this->content->output($this->data);
                $this->content->contentid = & $this->contentid;
                $this->content->action = & $this->action;
                $this->content->data = & $this->data;
                $this->content->event = 'after_get';
                $this->content->notify();
            }
        }
        else {
            $this->data = $this->content->get($this->contentid, $this->fields, $this->action);
        }

        $this->event = 'after_get';
        $this->notify();

        return $this->data;
    }

    function ls($where = null, $fields = '*', $order = '`#table_content`.`contentid` DESC', $page = null, $pagesize = null, $table_article = false) {
        $this->where = $where;
        $this->fields = $fields;
        $this->order = $order;
        $this->page = $page;
        $this->pagesize = $pagesize;
        $this->table_article = $table_article;

        $this->event = 'before_ls';
        $this->notify();

        if ($this->table_article) {
            if (is_array($this->where))
                $this->where = str_replace('WHERE', '', $this->content->where($this->where));
            $this->sql = "SELECT $this->fields FROM `#table_content` c, `#table_article` a WHERE c.`contentid`=a.`contentid`";
            if (!is_null($this->where))
                $this->sql .= ' AND ' . $this->where;
            if ($this->order)
                $this->sql .= ' ORDER BY ' . $this->order;
            $this->data = $this->db->page($this->sql, $this->page, $this->pagesize);
            if ($this->data) {
                array_map(array($this->content, 'output'), $this->data);
                if (!is_null($page)) {
                    $sql = "SELECT count(*) as `count` FROM `#table_content` c, `#table_article` a WHERE c.`contentid`=a.`contentid`";
                    if (!is_null($this->where))
                        $sql .= ' AND ' . $this->where;
                    $r = $this->db->get($sql);
                    $this->total = $r ? $r['count'] : 0;
                }
            }
        }
        else {
            $this->data = $this->content->ls($this->where, $this->fields, $this->order, $this->page, $this->pagesize);
            if (!is_null($page))
                $this->total = $this->content->total;
        }

        $this->event = 'after_ls';
        $this->notify();

        return $this->data;
    }

    function add($data) {
        $data['description'] = htmlspecialchars($data['description']);
        $this->data = $data;
        $this->event = 'before_add';
        $this->notify();

        if ($this->filterword && !isset($_REQUEST['ignoreword'])) {
            $this->error = '内容中存在以下敏感词，是否继续发布？';
            return false;
        }

        $this->contentid = $this->content->add($this->data);
        if (!$this->contentid) {
            $this->error = $this->content->error();
            return false;
        }
        $this->data['contentid'] = $this->contentid;
        $this->data = $this->filter_array($this->data, array('contentid', 'subtitle', 'author', 'editor', 'description', 'content', 'pagecount', 'saveremoteimage'));
        $result = $this->insert($this->data);
        if ($result) {
            $this->event = 'after_add';
            $this->notify();
        }
        $firstid = $this->contentid ? $this->contentid : 0;
        if ($_POST['options']['catid']) { //同时发到其他栏目
            $catids = explode(',', $_POST['options']['catid']);
            foreach ($catids as $catid) {
                $this->content->reference($this->contentid, $catid);
            }
        }
        return $result ? $firstid : false;
    }

    function add_with_contentid($data) {
        $data['description'] = htmlspecialchars($data['description']);
        $this->data = $data;
        $this->event = 'before_add';
        $this->notify();

        if ($this->filterword && !isset($_REQUEST['ignoreword'])) {
            $this->error = '内容中存在以下敏感词，是否继续发布？';
            return false;
        }
        $this->contentid = $this->content->add_same_contentid($this->data);
        if (!$this->contentid) {
            $this->error = $this->content->error();
            return false;
        }
        $this->data['contentid'] = $data['contentid'] = $this->contentid;
        $this->data = $this->filter_array($data, array('contentid', 'subtitle', 'author', 'editor', 'description', 'content', 'pagecount', 'saveremoteimage'));
        $result = $this->insert($data);
        if ($result) {
            $this->event = 'after_add';
            $this->notify();
        }
        $firstid = $this->contentid ? $this->contentid : 0;
        if ($_POST['options']['catid']) { //同时发到其他栏目
            $catids = explode(',', $_POST['options']['catid']);
            foreach ($catids as $catid) {
                $this->content->reference($this->contentid, $catid);
            }
        }
        return $result ? $firstid : false;
    }

    function edit($contentid, $data) {
        $this->contentid = intval($contentid);
        $data['description'] = htmlspecialchars($data['description']);
        $this->data = $data;

        $this->event = 'before_edit';
        $this->notify();

        if ($this->filterword && !isset($_REQUEST['ignoreword'])) {
            $this->error = '内容中存在敏感词，是否仍然发布？';
            return false;
        }

        if (!$this->content->edit($this->contentid, $this->data)) {
            $this->error = $this->content->error();
            return false;
        }

        $this->data = $this->filter_array($this->data, array('subtitle', 'author', 'editor', 'description', 'content', 'pagecount', 'saveremoteimage'));
        $result = $this->update($this->data, $this->contentid);
        if ($result) {
            $this->event = 'after_edit';
            $this->notify();
        }
        return $result;
    }

    function delete($contentid) {
        $contentid = id_format($contentid);
        if ($contentid === false) {
            $this->error = "$contentid 格式不正确";
            return false;
        }
        if (is_array($contentid))
            return array_map(array($this, 'delete'), $contentid);

        $this->contentid = intval($contentid);

        $this->event = 'before_delete';
        $this->notify();

        if (!$this->content->delete($this->contentid)) {
            $this->error = $this->content->error();
            return false;
        }
        $this->event = 'after_delete';
        $this->notify();
        return true;
    }

    function copy($contentid, $catid) {
        $contentid = id_format($contentid);
        if ($contentid === false) {
            $this->error = "$contentid 格式不正确";
            return false;
        }
        if (is_array($contentid))
            return array_map(array($this, 'copy'), $contentid, array_fill(0, count($contentid), $catid));

        $this->contentid = intval($contentid);
        $this->catid = intval($catid);

        $this->event = 'before_copy';
        $this->notify();

        $id = $this->content->copy($this->contentid, $this->catid);
        if (!$id) {
            $this->error = $this->content->error();
            return false;
        }

        if (!$this->copy_by_id($contentid, array('contentid' => $id))) {
            $this->error = "复制失败";
            return false;
        }

        $this->contentid = $id;

        $this->event = 'after_copy';
        $this->notify();

        return $this->contentid;
    }

    function move($contentid, $catid) {
        $contentid = id_format($contentid);
        if ($contentid === false) {
            $this->error = "$contentid 格式不正确";
            return false;
        }

        // 判断当前栏目是否支持此模型
        if (!$cate = value(table('category'), $catid)) {
            $this->error = "栏目不存在";
            return false;
        }

        foreach (unserialize($cate['model']) as $key => $item) {
            if (isset($item['show']) && $item['show']) {
                $model[] = $key;
            }
        }
        if (!in_array($this->modelid, $model)) {
            $this->error = '栏目不支持此模型内容';
            return false;
        }

        if (!priv::aca('article', 'article', 'move'))
            return true;
        if (is_array($contentid))
            return array_map(array($this, 'move'), $contentid, array_fill(0, count($contentid), $catid));

        $this->contentid = $contentid;
        $this->catid = intval($catid);

        $this->event = 'before_move';
        $this->notify();

        if (!$this->content->move($this->contentid, $this->catid)) {
            $this->error = $this->content->error();
            return false;
        }

        $this->event = 'after_move';
        $this->notify();

        return true;
    }

    function reference($contentid, $catid) {
        $this->contentid = $contentid;
        $this->catid = intval($catid);

        $this->event = 'before_reference';
        $this->notify();

        if (!$this->content->reference($this->contentid, $this->catid)) {
            $this->error = $this->content->error();
            return false;
        }

        $this->event = 'after_reference';
        $this->notify();

        return true;
    }

    function html_write($contentid) {
        $contentid = id_format($contentid);
        if ($contentid === false) {
            $this->error = "$contentid 格式不正确";
            return false;
        }
        if (is_array($contentid))
            return array_map(array($this, 'html_write'), $contentid);

        $this->contentid = $contentid;

        $this->event = 'html_write';
        $this->notify();

        return true;
    }

    function statistics($spaceid) {
        $statistics = array();
        $statistics['published'] = $this->content->count("`spaceid`='{$spaceid}' AND `status`='6'");
        $statistics['submitted'] = $this->content->count("`spaceid`='{$spaceid}' AND `status`='3'");
        $statistics['rejected'] = $this->content->count("`spaceid`='{$spaceid}' AND `status`='2'");
        $statistics['drafted'] = $this->content->count("`spaceid`='{$spaceid}' AND `status`='1'");
        return $statistics;
    }

    function clear_contribution() {
        return $this->content->clear_contribution();
    }

    function get_count() {
        $result = array(
            'total' => $this->content->count("`iscontribute` = '1'"),
            'wait' => $this->content->count("`iscontribute` = '1' AND `status` = '3'"),
            'publish' => $this->content->count("`iscontribute` = '1' AND `status` = '6'"),
            'reject' => $this->content->count("`iscontribute` = '1' AND `status` = '2'"),
            'draft' => $this->content->count("`iscontribute` = '1' AND `status` = '1'"),
            'remove' => $this->content->count("`iscontribute` = '1' AND `status` = '0'")
        );
        return $result;
    }

    function count_comment($userid) {
        $sql = 'SELECT SUM(comments) AS comments 
				FROM #table_content 
				WHERE `iscontribute`=1 AND `createdby`=' . $userid . ' AND `status`=6';
        $r = $this->db->get($sql, $data);
        return intval($r['comments']);
    }

    function get_comment($userid, $page, $pagesize) {
        $sql = 'SELECT f.commentid,f.content,f.created,f.createdby,f.nickname,s.title,s.url
				FROM `#table_comment` AS `f`
				LEFT JOIN `#table_content` AS `s`
				ON `f`.`contentid` =`s`.`contentid`
				WHERE `s`.`iscontribute`=1 AND `s`.`status`=6 AND `s`.`createdby`=' . $userid . '
				ORDER BY `f`.`created` DESC';
        $data = $this->db->page($sql, $page, $pagesize);
        return $data;
    }

    function output(& $r) {
        $r['subtitle'] = htmlspecialchars($r['subtitle']);
        $r['editor'] = htmlspecialchars($r['editor']);
        $r['description'] = htmlspecialchars($r['description']);
    }

    public function attach(SplObserver $observer) {
        $this->observers[] = $observer;
    }

    public function detach(SplObserver $observer) {
        if ($index = array_search($observer, $this->observers, true))
            unset($this->observers[$index]);
    }

    public function notify() {
        foreach ($this->observers as $observer) {
            $observer->update($this);
        }
    }

}