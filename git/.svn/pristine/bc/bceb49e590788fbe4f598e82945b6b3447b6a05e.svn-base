<?php

require_once 'common.php';

$published_num = 10;


$robot_log = cache_read('robot_log');

$cur_pub_num = 0; //当前已发布数目
if($robot_log) {
    if(count($robot_log)>1)
    foreach($robot_log as $r) {
        if($r['has_publised']) {

        } else {
            data_process($r['contentids']);//处理已采集但没发的数据
            data_publish($r['contentids']); //发布这些数据
        }
        $cur_pub_num += count($r['contentids']);
    }
} else {
    cache_write('robot_log',array());
}

if($cur_pub_num>=$published_num) { //新采集数多于了要发布的数目，直接exit
    exit('new record published'.$cur_pub_num);
}



$publish_log = cache_read('publish_log');

if(!$publish_log) {
    $publish_log['published_num']=0;
    $publish_log['last_pulished_time'] = 0;

}

$day = date('d',TIME);//当前的日期

if($publish_log['last_pulished_time'] && date('d',$publish_log['last_pulished_time']) != $day) {
    $published_log['published_num'] =0;
}

if($publish_log['published_num']>$published_num) {
    exit('todays corn is done.');
} else {
    if(rand(0,1)) { //要不要发老的
        data_publish_old(ceil($published_num/24*2));
    }

}



exit('no published.');

function data_process($contentids = null){    //数据处理
      $contentModel = loader::model('admin/content', 'system');
      $articleModel = loader::model('admin/article', 'article');
      $btinfoModel = loader::model('admin/btinfo', 'article');
      if(is_null($contentids)) {

      } else {
          foreach($contentids as $id){
            $r = $contentModel->get($id);
            if($r) {
                if(!empty($r['subtitie'])) {
                    countinue;
                }
                $t = $r['title'];
                $nt = array();//新标题
                $index=$frame=$resolution=$charset=$suffix = null;
                $index_regs = array('/第(\d+)集/','/第(\d+)话/','/\[(\d+)\]/', '/\【(\d+)\】/','/ (\d+) /');
                $frame_regs = array('/(\d+)p/i');
                $resolution_regs = array('/\d+X\d+/'); //分辨率
                $charset_regs = array('/繁体/','/简体/','/简繁外挂/', '/繁简内挂/','/GB/','/BIG5/','/简\/繁/');
                $suffix_regs = array('/mp4/i','/rmvb/i', '/mkv/i','/mp3/i','/avi/i');

                $data = array();
                $manhuaid = table('article_btinfo',$id,'manhuaid');
                if($manhuaid) {

                    $manhuaname = table('manhua',$manhuaid,'manhuaname') ;


                    foreach($index_regs as $r) {
                        if(preg_match($r, $t, $matches)){
                            $index = $matches[1];
                            break;
                        };

                    }
                    $nt[] = $manhuaname.$index.'bt下载';
                    $nt[] = $manhuaname.$index.'bt';
                    $nt[] = $manhuaname.$index.'下载';

                    foreach($frame_regs as $r) {
                        if(preg_match($r, $t, $matches)){
                            $nt[] = $matches[1]."P";
                            break;
                        };
                    }
                    foreach($resolution_regs as $r) {
                        if(preg_match($r, $t, $matches)){
                            $nt[] = $matches[0];
                            break;
                        };
                    }
                    foreach($charset_regs as $r) {
                        if(preg_match($r, $t, $matches)){
                            $nt[] = $matches[0];
                            break;
                        };
                    }

                    foreach($suffix_regs as $r) {
                        if(preg_match($r, $t, $matches)){
                            $nt[] = $matches[0];
                            break;
                        };
                    }

                    $data['subtitle'] = implode('_',$nt);
                    $data['keywords'] = $nt[0].','.$nt[1].','.$nt[2];      //
                    $data['description'] = '975k动漫分享为您提供'.$nt[0].'，'.$nt[1].'以及'.$nt[2].'，975k动漫分享为您第一时间发布bt下载资源，请记住本站网址bt.975k.com。本页内容提供'.$nt[0].','.$nt[1].','.$nt[2]."。";  //未完成
                }  else {

                    $data['keywords'] = '';      //
                    $data['description'] = implode('_',$nt);  //未完成
                }


                $btinfoModel->update($data,$r['contentid']);

            }
          }
      }
}

function data_publish($contentids){   //发布新的文章

}

function data_publish_old(){     // 发布以前的文章

}

