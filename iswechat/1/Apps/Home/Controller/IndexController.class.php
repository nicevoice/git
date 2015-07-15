<?php
namespace Home\Controller;
use Think\Controller;
import('Home.Lib.wechat');
use Home\Model\QuestionModel as Question;
class IndexController extends Controller
{
    private $wechat;
    public function __construct()
    {
        $options = array(
            'token'=>'556afbd29a8e35c6600d', //填写你设定的key
            'encodingaeskey'=>'rpceX8JqtX3pcQJxDXfEGecKhPQadesrqsYbq79jm6i', //填写加密用的EncodingAESKey
        );
        $this->wechat = new \Wechat($options);
    }
    public function index()
    {
        $this->wechat->valid();//明文或兼容模式可以在接口验证通过后注释此句，但加密模式一定不能注释，否则会验证失败
        $type = $this->wechat->getRev()->getRevType();
        switch($type) {
            case \Wechat::MSGTYPE_TEXT:
                $User = new Question();
                $text = $User->getQuestion2Key($this->wechat->getRevContent());
                $str = "[题目]".$text['subject'];
                $str .= "\n\r". strip_tags($text['description']);
                $str .= "\n\r[选项：]";
                foreach($text['op'] as $k=> $op) {
                    $str .= "\n\r".$k . '丶'.$op;
                }
                $str .= "\n\r[答案：{$text['answer']}]";
                $this->wechat->text($str)->reply();
                exit;
                break;
            case \Wechat::MSGTYPE_EVENT:
                break;
            case \Wechat::MSGTYPE_IMAGE:
                break;
            default:
                $this->wechat->text("help info")->reply();
        }
    }

}