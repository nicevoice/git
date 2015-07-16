<?php

/**
 * 问卷报告
 *
 * @aca 问卷报告
 */
final class controller_admin_report extends exam_controller_abstract {

    private $exam, $question, $answer, $answer_record, $answer_option, $pagesize = 10;

    function __construct(& $app) {
        parent::__construct($app);
        if (!license('exam'))
            cmstop::licenseFailure();
        $this->exam = loader::model('admin/exam');
        $this->question = loader::model('admin/question');
        $this->answer = loader::model('admin/answer');
        $this->answer_record = loader::model('admin/answer_record');
        $this->answer_option = loader::model('admin/answer_option');
    }

    /**
     * 分析报告
     *
     * @aca 浏览
     */
    function index() {
        $contentid = $_GET['contentid'];
        $exam = $this->exam->get($contentid);
        if (!$exam)
            $this->showmessage('题库不存在');

        $this->priv_category($exam['catid']);

        $this->view->assign($exam);
        $this->view->assign('head', array('title' => '分析报告：' . $exam['title']));
        $this->view->display('report/index');
    }

    /**
     * 分项报表
     *
     * @aca 分项报表
     */
    function question() {
        if (isset($_GET['report']) && $_GET['report']) {
            $questionid = $_GET['questionid'];
            $question = $this->question->get($questionid);

            $this->priv_category(table('content', $question['contentid'], 'catid'));

            if (in_array($question['type'], array('radio', 'checkbox', 'select'))) {
                @header('Content-type: text/xml; charset=' . $CONFIG['charset']);
                $xml_data = $this->xml_data($question['option']);
                $xml_data['subject'] = $question['subject'];
                $xml_data = array($xml_data);
                $this->view->assign('report_name', 'exam');
                $this->view->assign('data', $xml_data);
                $this->view->display('pie_report', 'system');
            } else {
                $page = isset($_GET['page']) ? intval($_GET['page']) : 0;
                $pagesize = isset($_GET['pagesize']) ? intval($_GET['pagesize']) : $this->pagesize;
                $keywords = isset($_GET['keywords']) ? $_GET['keywords'] : '';

                $data = $this->answer_record->ls($questionid, $page, $pagesize, $keywords);

                $result = array('total' => $this->answer_record->count("questionid=$questionid"), 'data' => $data);
                echo $this->json->encode($result);
            }
        } else {
            $contentid = $_GET['contentid'];
            $exam = $this->exam->get($contentid);
            if (!$exam)
                $this->showmessage('题库不存在');

            $this->priv_category($exam['catid']);

            $questionid = isset($_GET['questionid']) ? $_GET['questionid'] : (isset($exam['question'][0]['questionid']) ? $exam['question'][0]['questionid'] : 0);
            $this->view->assign('questionid', $questionid);
            $this->view->assign($exam);
            $this->view->assign('head', array('title' => '分项报表：' . $exam['title']));
            $this->view->display('report/question');
        }
    }

    /**
     * 全部数据
     *
     * @aca 全部数据
     */
    function answer() {
        if (isset($_GET['report']) && $_GET['report']) {
            $this->priv_category(table('content', $_GET['contentid'], 'catid'));

            $contentid = $_GET['contentid'];
            $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
            $pagesize = isset($_GET['pagesize']) ? intval($_GET['pagesize']) : $this->pagesize;

            $data = $this->answer->page("`contentid`=$contentid", '*', 'answerid DESC', $page, $pagesize);
            $total = $this->answer->count("contentid=$contentid");

            if ($this->is_post()) {
                $data = $this->answer->search($_POST, $contentid);
                $total = count($data);
                $offset = ($page - 1) * $pagesize;
                $data = array_slice($data, $offset, $pagesize);
            }

            $result = array('total' => $total, 'data' => $data);
            echo $this->json->encode($result);
        } else {
            $contentid = $_GET['contentid'];
            $exam = $this->exam->get($contentid);
            if (!$exam)
                $this->showmessage('题库不存在');

            $this->priv_category($exam['catid']);
            $this->view->assign($exam);
            $this->view->assign('head', array('title' => '全部数据：' . $exam['title']));
            $this->view->display('report/answer');
        }
    }

    /**
     * 题库记录
     *
     * @aca 题库记录
     */
    function option() {
        $optionid = $_GET['optionid'];
        if (isset($_GET['report']) && $_GET['report']) {
            $page = isset($_GET['page']) ? intval($_GET['page']) : 0;
            $pagesize = isset($_GET['pagesize']) ? intval($_GET['pagesize']) : $this->pagesize;

            $data = $this->answer_option->ls($optionid, $page, $pagesize);
            $result = array('total' => $this->answer_option->count("optionid=$optionid"), 'data' => $data);
            echo $this->json->encode($result);
        } else {
            $option = loader::model('admin/question_option');
            $name = $option->get_field('name', $optionid);
            $this->view->assign('head', array('title' => '题库记录：' . $name));
            $this->view->display('report/option');
        }
    }

    /**
     * 查看问卷
     *
     * @aca 查看问卷
     */
    function view() {
        $answerid = $_GET['answerid'];
        $answer = $this->answer->get($answerid);

        $contentid = $answer['contentid'];
        $exam = $this->exam->get($contentid);

        if (!$exam)
            $this->showmessage('题库不存在');

        $this->priv_category($exam['catid']);

        $optionid = $record = array();
        $option_data = $this->answer_option->gets_by('answerid', $answerid);
        foreach ($option_data as $o) {
            $optionid[$o['questionid']][] = $o['optionid'];
        }

        $record_data = $this->answer_record->gets_by('answerid', $answerid);
        foreach ($record_data as $r) {
            $record[$r['questionid']] = $r['content'];
        }

        $this->view->assign($exam);
        $this->view->assign('optionid', $optionid);
        $this->view->assign('record', $record);
        $this->view->assign('head', array('title' => '查看问卷：' . $exam['title']));
        $this->view->display('report/view');
    }

    /**
     * 删除
     *
     * @aca 删除
     */
    function delete() {
        $answerid = $_GET['answerid'];
        $contentid = $_GET['contentid'];
        if ($this->answer->delete($answerid, $contentid)) {
            echo 1;
        } else {
            echo 0;
        }
    }

    /**
     * 搜索
     *
     * @aca 浏览
     */
    function search() {

        $contentid = $_GET['contentid'];
        $exam = $this->exam->get($contentid);
        if ($this->is_post()) {
            print_r($exam);
        } else {
            $this->view->assign($exam);
            $this->view->assign('head', array('title' => '高级检索：' . $exam['title']));
            $this->view->display('report/search');
        }
    }

    private function xml_data($options) {
        $name_colors = array(0xFF0F00, 0xFF6600, 0xFF9E01, 0xFCD202, 0xF8FF01, 0xB0DE09, 0x04D215, 0x0D8ECF, 0xFF1F11);
        $xml_data = $defaultsate = $option_name_color = $showtitle = $options_name = array();
        foreach ($options as $o => $option) {
            $options_name[] = $option['name'];
            $showtitle[] = 'true';
            $option_name_color[] = $name_colors[$o];
            $defaultsate[] = 'false';
            $votes[] = $option['votes'];
        }
        $xml_data['options'] = implode(',', $options_name);
        $xml_data['showtitle'] = implode(',', $showtitle);
        $xml_data['colors'] = implode(',', $option_name_color);
        $xml_data['defaultsate'] = implode(',', $defaultsate);
        $xml_data['votes'] = implode(',', $votes);
        return $xml_data;
    }

}