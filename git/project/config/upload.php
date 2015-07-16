<?php
$today = date('Ymd');
return array(
    'picture' => array(
        /* 课程 courses*/
        1 => array(
            'upload_dir' => PIC_PATH . 'tmp/' . $today . '/',
            'upload_url' => PIC_URL . 'tmp/' . $today . '/',
            'accept_file_types' => '/\.(jpg)$/i',
            'max_file_size' => 1048576,
            'min_width' => 450,
            'min_height' => 270,
            'image_versions' => array(
                '450x270' => array(
                    'crop' => true,
                    'max_width' => 450,
                    'max_height' => 270,
                    'jpeg_quality' => 80
                ),
                '210x126' => array(
                    'crop' => true,
                    'max_width' => 210,
                    'max_height' => 126,
                    'jpeg_quality' => 80
                ),
                '80x48' => array(
                    'crop' => true,
                    'max_width' => 80,
                    'max_height' => 48,
                    'jpeg_quality' => 80
                ),
            ),
        ),

        /* 资料 materials*/
        2 => array(
            'upload_dir' => PIC_PATH . 'tmp/' . $today . '/',
            'upload_url' => PIC_URL . 'tmp/' . $today . '/',
            'accept_file_types' => '/\.(jpg)$/i',
            'max_file_size' => 1048576,
            'min_width' => 200,
            'min_height' => 270,
            'image_versions' => array(
                '200x270' => array(
                    'crop' => true,
                    'max_width' => 200,
                    'max_height' => 270,
                    'jpeg_quality' => 80
                ),
                '120x160' => array(
                    'crop' => true,
                    'max_width' => 120,
                    'max_height' => 160,
                    'jpeg_quality' => 80
                ),
                '36x48' => array(
                    'crop' => true,
                    'max_width' => 36,
                    'max_height' => 48,
                    'jpeg_quality' => 80
                ),
            ),
        ),

        /* 试听课 video*/
        3 => array(
            'upload_dir' => PIC_PATH . 'tmp/' . $today . '/',
            'upload_url' => PIC_URL . 'tmp/' . $today . '/',
            'accept_file_types' => '/\.(jpg)$/i',
            'max_file_size' => 1048576,
            'min_width' => 450,
            'min_height' => 270,
            'image_versions' => array(
                '450x270' => array(
                    'crop' => true,
                    'max_width' => 450,
                    'max_height' => 270,
                    'jpeg_quality' => 80
                ),
                '210x126' => array(
                    'crop' => true,
                    'max_width' => 210,
                    'max_height' => 126,
                    'jpeg_quality' => 80
                ),
                '80x48' => array(
                    'crop' => true,
                    'max_width' => 80,
                    'max_height' => 48,
                    'jpeg_quality' => 80
                ),
            ),
        ),

        /* 活动 activitys*/
        4 => array(
            'upload_dir' => PIC_PATH . 'tmp/' . $today . '/',
            'upload_url' => PIC_URL . 'tmp/' . $today . '/',
            'accept_file_types' => '/\.(jpg)$/i',
            'max_file_size' => 1048576,
            'min_width' => 450,
            'min_height' => 270,
            'image_versions' => array(
                '450x270' => array(
                    'crop' => true,
                    'max_width' => 450,
                    'max_height' => 270,
                    'jpeg_quality' => 80
                ),
                '210x126' => array(
                    'crop' => true,
                    'max_width' => 210,
                    'max_height' => 126,
                    'jpeg_quality' => 80
                ),
                '80x48' => array(
                    'crop' => true,
                    'max_width' => 80,
                    'max_height' => 48,
                    'jpeg_quality' => 80
                ),
            ),
        ),

        /* 校区 schools*/
        5 => array(
            'upload_dir' => PIC_PATH . 'tmp/' . $today . '/',
            'upload_url' => PIC_URL . 'tmp/' . $today . '/',
            'accept_file_types' => '/\.(jpg)$/i',
            'max_file_size' => 1048576,
            'min_width' => 360,
            'min_height' => 270,
            'image_versions' => array(
                '360x270' => array(
                    'crop' => true,
                    'max_width' => 360,
                    'max_height' => 270,
                    'jpeg_quality' => 80
                ),
                '210x118' => array(
                    'crop' => true,
                    'max_width' => 210,
                    'max_height' => 118,
                    'jpeg_quality' => 80
                ),
                '88x66' => array(
                    'crop' => true,
                    'max_width' => 88,
                    'max_height' => 66,
                    'jpeg_quality' => 80
                ),
            ),
        ),

        /* 老师 teachers*/
        11 => array(
            'upload_dir' => PIC_PATH . 'tmp/' . $today . '/',
            'upload_url' => PIC_URL . 'tmp/' . $today . '/',
            'accept_file_types' => '/\.(jpg)$/i',
            'max_file_size' => 1048576,
            'min_width' => 1,
            'min_height' => 1,
            'image_versions' => array(
                '300x300' => array(
                    'crop' => true,
                    'max_width' => 300,
                    'max_height' => 300,
                    'jpeg_quality' => 80
                ),
            ),
        ),

        /* 口碑 praise*/
        12 => array(
            'upload_dir' => PIC_PATH . 'tmp/' . $today . '/',
            'upload_url' => PIC_URL . 'tmp/' . $today . '/',
            'accept_file_types' => '/\.(jpg)$/i',
            'max_file_size' => 1048576,
            'min_width' => 1,
            'min_height' => 1,
            'image_versions' => array(
                '300x300' => array(
                    'crop' => true,
                    'max_width' => 300,
                    'max_height' => 300,
                    'jpeg_quality' => 80
                ),
            ),
        ),

        /* 培训班套页*/
        13 => array(
            'upload_dir' => PIC_PATH . 'cover/' . $today . '/',
            'upload_url' => PIC_URL . 'cover/' . $today . '/',
            'max_file_size' => 1048576,
            'min_width' => 1,
            'min_height' => 1,
            'param_name' => 'imgs',
            'image_versions' => array(),
        ),

/* ======================     以上是旧机构平台     ===================== */

/* ======================     以下是新机构平台     ===================== */

        /* 新机构平台 课程 courses*/
        21 => array(
            'upload_dir' => PIC_PATH . 'tmp/' . $today . '/',
            'upload_url' => PIC_URL . 'tmp/' . $today . '/',
            'accept_file_types' => '/\.(jpg)$/i',
            'max_file_size' => 1048576,
            'min_width' => 450,
            'min_height' => 270,
            'image_versions' => array(
                '450x270' => array(
                    'crop' => true,
                    'max_width' => 450,
                    'max_height' => 270,
                    'jpeg_quality' => 80
                ),
                '204x122' => array(
                    'crop' => true,
                    'max_width' => 204,
                    'max_height' => 122,
                    'jpeg_quality' => 80
                ),
            ),
        ),

        /* 资料 material */
        22 => array(
            'upload_dir' => PIC_PATH . 'tmp/' . $today . '/',
            'upload_url' => PIC_URL . 'tmp/' . $today . '/',
            'accept_file_types' => '/\.(jpg)$/i',
            'max_file_size' => 1048576,
            'min_width' => 450,
            'min_height' => 270,
            'image_versions' => array(
                '450x270' => array(
                    'crop' => true,
                    'max_width' => 450,
                    'max_height' => 270,
                    'jpeg_quality' => 80
                ),
                '204x122' => array(
                    'crop' => true,
                    'max_width' => 204,
                    'max_height' => 122,
                    'jpeg_quality' => 80
                ),
            ),
        ),

        /* 店铺首页幻灯片 */
        33 => array(
            'upload_dir' => PIC_PATH . 'tmp/' . $today . '/',
            'upload_url' => PIC_URL . 'tmp/' . $today . '/',
            'accept_file_types' => '/\.(jpg)$/i',
            'max_file_size' => 1048576,
            'min_width' => 1,
            'min_height' => 1,
            'image_versions' => array(),
        ),

        /* 校区环境 */
        34 => array(
            'upload_dir' => PIC_PATH . 'tmp/' . $today . '/',
            'upload_url' => PIC_URL . 'tmp/' . $today . '/',
            'accept_file_types' => '/\.(jpg)$/i',
            'max_file_size' => 1048576,
            'min_width' => 1,
            'min_height' => 1,
            'image_versions' => array(),
        ),

    ),




);
