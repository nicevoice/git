<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
<head>
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
<!-- modify the title display -->
<title><?php if (is_single() || is_page() || is_archive()) { ?><?php wp_title('',true); ?> | <?php } bloginfo('name'); ?> </title>
<link rel="profile" href="http://gmpg.org/xfn/11" />
<link rel="stylesheet" href="<?php bloginfo('stylesheet_directory'); ?>/css_reset.css" type="text/css" media="screen" />
<link rel="stylesheet" href="<?php bloginfo('stylesheet_directory'); ?>/style.css" type="text/css" media="screen" />
<link rel="shortcut icon" type="image/ico" href="/images/favicon.ico" />
<link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> RSS Feed" href="<?php bloginfo('rss2_url'); ?>" />
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
<?php
	//load build-in jquery
	wp_enqueue_script('jquery');

	//thread comment support
	if ( is_singular() && get_option('thread_comments') )
		wp_enqueue_script( 'comment-reply' );

	//include the wp_head()
	wp_head();
?>
</head>

<body <?php body_class(); ?>>
<div id="page-wrap">
	<div id="topbar">
		<?php if (has_nav_menu('secondary-menu')){ ?>
			<ul class="right">
				<?php wp_nav_menu(array('theme_location' => 'secondary-menu')); ?>
			</ul>
			<div class="clear"></div>
		<?php } ?>
	</div><!-- end #topbar -->
	
	<div id="header">
		<div class="blog-title">
			<h1><a href="<?php bloginfo('url'); ?>"><?php bloginfo('name'); ?></a></h1>
		</div>
		<div class="blog-description">
			<p><?php bloginfo('description') ?></p>
		</div>
		<div class="clear"></div>
	</div><!-- end #header -->
	
	<div id="main-navi">
		<?php wp_nav_menu(array('theme_location' => 'primary-menu')); ?>
		<div class="clear"></div>
	</div><!-- end #main_navi -->
