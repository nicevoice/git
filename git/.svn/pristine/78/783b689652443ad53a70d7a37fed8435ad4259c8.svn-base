<?php
return array(
    'base64' => array(
        'name' => 'BASE64防采集改写',
        'rulers' => array(
//            "|([0-9]{4})/([0-9]{4})/([0-9]+).shtml|i" => array('path'=>'/show/{$_pd}/{$date}/{$m[1]}.shtml', 'metche'=>array('_pd'=>array(1=>'base64_encode'),'_mp'=>array(1=>'base64_encode'),)),

            // 视频专辑的内页
            "|/album/([0-9]+).html|i" => array('path'=>'/video/album/{$_mp}/{$m[1]}.html', 'metche'=>array('_pd'=>array(1=>'@simple'),'_mp'=>array(1=>'contentid_dir'),)),
            "|/album/([0-9]+)_([0-9]+).html|i" => array('path'=>'/video/album/{$_mp}/{$m[1]}_{$m[2]}.html', 'metche'=>array('_pd'=>array(1=>'@simple'),'_mp'=>array(1=>'contentid_dir'),)),
            
			// 视频专辑的内页
            "|/kefu/([0-9]+).html|i" => array('path'=>'/www/kefu/{$_mp}/{$m[1]}_1.shtml', 'metche'=>array('_pd'=>array(1=>'@simple'),'_mp'=>array(1=>'contentid_dir'),)),
            "|/kefu/([0-9]+)_([0-9]+).html|i" => array('path'=>'/www/kefu/{$_mp}/{$m[1]}_{$m[2]}.shtml', 'metche'=>array('_pd'=>array(1=>'@simple'),'_mp'=>array(1=>'contentid_dir'),)),

			// 视频的内页
			"|/view/([0-9]+).html|i" => array('path'=>'/video/view/{$_mp}/{$m[1]}_1.shtml', 'metche'=>array('_pd'=>array(1=>'@simple'),'_mp'=>array(1=>'contentid_dir'),)),
            "|/view/([0-9]+)_([0-9]+).html|i" => array('path'=>'/video/view/{$_mp}/{$m[1]}_{$m[2]}.shtml', 'metche'=>array('_pd'=>array(1=>'@simple'),'_mp'=>array(1=>'contentid_dir'),)),
			
			// 资讯的内页
			"|/news/([0-9]+)_([0-9]+)|i" => array('path'=>'/news/news/{$_mp}/{$m[1]}_{$m[2]}.shtml', 'metche'=>array('_pd'=>array(1=>'@simple'),'_mp'=>array(1=>'contentid_dir'),)),
            "|/news/([0-9]+)|i" => array('path'=>'/news/news/{$_mp}/{$m[1]}_1.shtml', 'metche'=>array('_pd'=>array(1=>'@simple'),'_mp'=>array(1=>'contentid_dir'),)),
			// 资讯实务的内页
			"|/shiwu/([0-9]+)_([0-9]+)|i" => array('path'=>'/news/shiwu/{$_mp}/{$m[1]}_{$m[2]}.shtml', 'metche'=>array('_pd'=>array(1=>'@simple'),'_mp'=>array(1=>'contentid_dir'),)),
            "|/shiwu/([0-9]+)|i" => array('path'=>'/news/shiwu/{$_mp}/{$m[1]}_1.shtml', 'metche'=>array('_pd'=>array(1=>'@simple'),'_mp'=>array(1=>'contentid_dir'),)),

			// 资讯法规的内页
			"|/fagui/([0-9]+)_([0-9]+)|i" => array('path'=>'/news/fagui/{$_mp}/{$m[1]}_{$m[2]}.shtml', 'metche'=>array('_pd'=>array(1=>'@simple'),'_mp'=>array(1=>'contentid_dir'),)),
            "|/fagui/([0-9]+)|i" => array('path'=>'/news/fagui/{$_mp}/{$m[1]}_1.shtml', 'metche'=>array('_pd'=>array(1=>'@simple'),'_mp'=>array(1=>'contentid_dir'),)),

            // 财考二级域名的文章
            "|/([0-9]+)_([0-9]+)|i"=>array('path'=>'/news/{$domain}/{$_mp}/{$m[1]}_{$m[2]}.shtml','metche'=>array('_pd'=>array(1=>'@simple'),'_mp'=>array(1=>'contentid_dir'),)),
            "|/([0-9]+)|i"         =>array('path'=>'/news/{$domain}/{$_mp}/{$m[1]}_1.shtml',      'metche'=>array('_pd'=>array(1=>'@simple'),'_mp'=>array(1=>'contentid_dir'),)),


			// 资讯的内页
            "|/([0-9]+).html|i" => array('path'=>'/news/show/{$_mp}/{$m[1]}_1.shtml', 'metche'=>array('_pd'=>array(1=>'@simple'),'_mp'=>array(1=>'contentid_dir'),)),
            "|/([0-9]+)_([0-9]+).html|i" => array('path'=>'/news/show/{$_mp}/{$m[1]}_{$m[2]}.shtml', 'metche'=>array('_pd'=>array(1=>'@simple'),'_mp'=>array(1=>'contentid_dir'),)),

        )
    ),
    'book' => array(
        'name' => 'book防采集改写',
        'rulers' => array(
            "|/([0-9]+).html|i" => array('path'=>'/book/show/{$_mp}/{$m[1]}_1.shtml', 'metche'=>array('_pd'=>array(1=>'@simple'),'_mp'=>array(1=>'contentid_dir'),)),
            "|/([0-9]+)_([0-9]+).html|i" => array('path'=>'/book/show/{$_mp}/{$m[1]}_{$m[2]}.shtml', 'metche'=>array('_pd'=>array(1=>'@simple'),'_mp'=>array(1=>'contentid_dir'),)),

        )
    )
);
