<?php

return array(
    'brand' => array(
        'exam' => array(
            'domain_url' => WWW_URL,
            'rewrite' => array(
                'show' => array('rule' => '/exam/show/{$examid}.html', 'pattern' => array('examid' => '[a-z0-9_]*')),
                'action' => array('rule' => '/exam/{$action}.html', 'pattern' => array('action'=>'[a-z0-9_]*')),
                'action_id' => array('rule' => '/exam/{$action}_{$id}.html', 'pattern' => array('action'=>'[a-z0-9_]*','id' => '[1-9][0-9]*')),

                'my/action' => array('rule' => '/exam/my/{$action}.html', 'pattern' => array('action'=>'[a-z0-9_]*')),
                'my/action_id' => array('rule' => '/exam/my/{$action}/{$id}.html', 'pattern' => array('action'=>'[a-z0-9_]*','id' => '[1-9][0-9]*')),
                'my/action_id_subjectid_page' => array('rule' => '/exam/my/{$action}_{$id}_{$subjectid}_{$page}.html', 'pattern' => array('action'=>'[a-z0-9_]*','id' => '[1-9][0-9]*','subjectid' => '[1-9][0-9]*', 'page' => '[0-9]+')),
                'my/action/page' => array('rule' => '/exam/my/{$action}_{$page}.html', 'pattern' => array('action'=>'[a-z0-9_]*','page' => '[0-9]+')),
                'question/action' => array('rule' => '/exam/question/{$action}.html', 'pattern' => array('action'=>'[a-z0-9_]*')),
                'question/action_id' => array('rule' => '/exam/question/{$action}/{$id}.html', 'pattern' => array('action'=>'[a-z0-9_]*','id' => '[1-9][0-9]*')),
                'question/show' => array('rule' => '/exam/question/{$id}.html', 'pattern' => array('id' => '[a-z0-9_]*')),
            ),
        )

    ),
);