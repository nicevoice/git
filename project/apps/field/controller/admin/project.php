<?php

/**
 * 方案管理
 *
 * @aca 方案管理
 */
class controller_admin_project extends field_controller_abstract {

    private $project, $field, $category, $pagesize = 15;

    public function __construct(& $app) {
        parent::__construct($app);

        $this->project = loader::model('admin/project');
        $this->field = loader::model('admin/field');
        $this->category = loader::model('admin/category');

        loader::import('lib.fieldEngine');
        $env = get_object_vars($this);
        $env['controller'] = $this;
        fieldEngine::setEnv($env);
    }

    /**
     * 方案浏览
     *
     * @aca 方案浏览
     */
    public function index() {
        $this->view->assign('head', array('title' => '方案管理'));
        $this->view->display("index");
    }

    /**
     * 方案列表
     *
     * @aca 方案浏览
     */
    public function page() {
        // 默认ID升排
        $order = 'projectid';
        $page = max((isset($_GET['page']) ? intval($_GET['page']) : 1), 1);
        $size = max((isset($_GET['pagesize']) ? intval($_GET['pagesize']) : $this->pagesize), 1);
        $data = $this->project->page($where, $order, $page, $size);
        $total = $this->project->count();

        $result = array('total' => $total, 'data' => $data);
        echo $this->json->encode($result);
    }

    /**
     * 添加方案
     *
     * @aca 添加方案
     */
    public function add() {
        if ($this->is_post()) {
            if ($id = $this->project->add($_POST)) {
                $json = array(
                    'state' => true,
                    'data' => $this->project->get_byid($id)
                );
            } else {
                $json = array(
                    'state' => false,
                    'error' => $this->project->error()
                );
            }

            exit($this->json->encode($json));
        }

        $fid = intval($_GET['pid']);
        // 获取需要修改的数据
        if ($field = $this->project->get($fid)) {
            $this->view->assign('field', $field);
        }
        $this->view->display('form');
    }

    /**
     * 设计方案
     *
     * @aca 设计方案
     */
    public function design() {
        $pid = $_GET['pid'];
        $fields = include_once APPS_DIR . 'field/config/field.php';
        $fieldhtmls = $this->field->getHtml($pid);

        $this->view->assign('head', array('title' => '字段设计'));
        $this->view->assign('pid', $pid);
        $this->view->assign('fields', $fields);
        $this->view->assign('fieldhtmls', $fieldhtmls);
        $this->view->display('design');
    }

    /**
     * 添加字段
     *
     * @aca 添加字段
     */
    public function design_add() {
        $pid = (int) $_GET['pid'];
        $type = $_GET['type'];

        if ($this->is_post()) {
            if (count(array_filter($_POST['setting']))) {
                $fid = fieldEngine::addPost($_POST['field'], $_POST);
                if ($fid) {
                    $json = array(
                        'state' => true,
                        'fid' => $fid
                    );
                } else {
                    $json = array(
                        'state' => false,
                        'error' => '添加字段失败'
                    );
                }
            } else {
                $json = array(
                    'state' => false,
                    'error' => '请正确填写配置信息'
                );
            }

            exit($this->json->encode($json));
        } else {
            fieldEngine::addView($type, $pid);
        }
    }

    /**
     * 编辑字段
     *
     * @aca 编辑字段
     */
    public function design_edit() {
        $fid = $_GET['fid'];
        if ($this->is_post()) {
            $id = fieldEngine::editPost($_POST['field'], $_POST['fieldid'], $_POST);
            if ($id) {
                $json = array(
                    'state' => true,
                    'fid' => $id
                );
            } else {
                $json = array(
                    'state' => false,
                    'error' => '字段更新失败'
                );
            }

            exit($this->json->encode($json));
        } else {
            $type = array_shift($this->field->get_by_fieldid($fid, 'field'));
            fieldEngine::editView($type, $fid);
        }
    }

    /**
     * 查看字段
     *
     * @aca 浏览方案
     */
    public function design_view() {
        $fid = (int) $_GET['fid'];
        $field = $this->field->get($fid);
        exit(fieldEngine::render($field['field'], $field));
    }

    /**
     * 方案字段排序
     *
     * @aca 浏览方案
     */
    public function sort() {
        $sort = intval($_GET['sort']);
        $next_sort = intval($_GET['nextsort']);

        $field1 = $this->field->get($sort);
        $field2 = $this->field->get($next_sort);

        $this->field->sort($sort, $field2['sort']);
        $this->field->sort($next_sort, $field1['sort']);
        echo $this->json->encode(array('state' => true));
    }

    /**
     * 删除方案
     *
     * @aca 删除方案
     */
    public function delete() {
        $pid = $_GET['pid'];
        $result = $this->project->delete($pid) ? array('state' => true) : array('state' => false, 'error' => $this->project->error());
        $this->field->delete("projectid = $pid");
        echo $this->json->encode($result);
    }

    /**
     * 获取所有的栏目
     * 导航设置 --> 栏目设置调用
     *
     * @aca 浏览方案
     */
    public function get_project_api() {
        $catid = (int) $_GET['catid'];
        $check = $this->category->get_field('projectid', "catid = '$catid'");
        $projects = $this->project->select();
        $this->view->assign('check', $check);
        $this->view->assign('projects', $projects);
        $this->view->display('field/project');
    }

    /**
     * 方案关联栏目
     *
     * @aca 浏览方案
     */
    public function set_project_api() {
        $pid = $_GET['pid'];
        $parentid = $_GET['parentid'];
        $extend = $_GET['extend'];
        $childids = table('category', $parentid, 'childids');
        $extend && $childids && $parentid = $childids . ',' . $parentid;

        $this->category->delete($parentid); // 先删除已经关联的字段，以最后一次关联为准
        $this->category->set_project_api($pid, $parentid);
    }

    /**
     * 获取指定栏目的字段 --> 文章加载时
     *
     * @aca 浏览方案
     */
    public function get_field_api() {
        $catid = (int) $_GET['catid'];
        $projectid = $this->category->get_by('catid', $catid, 'projectid');
        $data = $this->field->gets_by('projectid', $projectid['projectid'], 'fieldid, field, setting');

        foreach ($data as &$r) {
            $temp = unserialize($r['setting']);
            $r['field'] = fieldEngine::genData($r['field'], $temp, $r['fieldid']);
            $r['name'] = $temp['fieldname'];
            unset($r['setting']);
        }
        //[exception] 增加地区属性和分类属性绑定的输出
        import('helper.tree');
        $tree = new tree('#table_category', 'catid');
        $category = $tree->get($catid, "*");
        $ext = array();
        $ext['typeid'] = $category['typeid'];
        $ext['subtypeid'] = $category['subtypeid'];
        $ext['zoneid'] = $category['zoneid'];
        exit($this->json->encode(array('data' => $data,'ext' => $ext)));
    }

    /**
     * 获取指定栏目的字段 --> 文章修改时
     *
     * @aca 浏览方案
     */
    public function get_editfield_api() {
        $cid = $_GET['contentid'];
        $catid = $_GET['catid'];

        $projectid = $this->category->get_by('catid', $catid, 'projectid');
        $data = $this->field->gets_by('projectid', $projectid['projectid'], 'fieldid, field, setting');
        $cdata = unserialize(table('content_meta', $cid, 'data'));

        $tmp = array();
        foreach ($data as $r) {
            $tmp[] = fieldEngine::genEditData($r['field'], $r, $cdata[$r['fieldid']]);
        }
        $data = $tmp;
        unset($tmp);
        //[exception] 增加地区属性和分类属性绑定的输出
        import('helper.tree');
        $tree = new tree('#table_category', 'catid');
        $category = $tree->get($catid, "*");
        $ext = array();
        $ext['typeid'] = $category['typeid'];
        $ext['subtypeid'] = $category['subtypeid'];
        $ext['zoneid'] = $category['zoneid'];
        
        exit($this->json->encode(array('data' => $data,'ext' => $ext)));
    }

}