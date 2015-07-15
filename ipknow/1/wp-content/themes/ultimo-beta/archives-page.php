<?php
/*
Template Name: Archives
*/
?>

<?php get_header(); ?>
<div id="container">
	<div id="main">
	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
    	<div class="post">
			<div class="title">
				<h2><?php _e("Archives", "ultimo"); ?></h2>
			</div><!-- end .title -->
			<div class="clear"></div>
				
			<div class="entry archives-page">
				<?php ultimo_archives(); ?>
			</div><!-- end entry -->
		</div><!-- end post -->
	
	<?php endwhile;  endif;?>
	
	</div><!-- end main --> 
<?php get_sidebar(); ?>
<div class="clear"></div>
</div><!-- end container -->
<div class="clear"></div><!--ie6-->
<?php get_footer(); ?>