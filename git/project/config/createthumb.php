<?php

/**
 * 生成缩略图的配置
 */

return array(
    'high_concurrency' => false,          // 高并发优化
    'wait_timeout' => 5,                      // 高并发等待超时时间
    'wh'=>array(//仅生成以下尺寸的缩略图: 宽,高
        array(
            'crop'=>true,//true裁剪 false缩放
            'jpeg_quality'=>80,
            'width'=>300,
            'height'=>184,
        ),
        array(
            'crop'=>false,
            'jpeg_quality'=>80,
            'width'=>120,
            'height'=>82,
        ),
        array(
            'crop'=>false,
            'jpeg_quality'=>80,
            'width'=>145,
            'height'=>109,
        ),
        array(
            'crop'=>false,
            'jpeg_quality'=>80,
            'width'=>106,
            'height'=>82,
        ),
        array(
            'crop'=>false,
            'jpeg_quality'=>80,
            'width'=>90,
            'height'=>50,
        ),
    )

);
