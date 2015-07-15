<?php
/*
Template Name: Page without Comment
*/
?>

<?php get_header(); ?>
<div id="container">
	<div id="main">
    	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
			<div class="post" id="post-<?php the_ID(); ?>">
				<div class="title">
					<h2><?php the_title(); ?></h2>
				</div><!-- end .title -->
				
				<div class="entry">
					<?php the_content(); ?>
					<?php wp_link_pages(array('before' => '<div class="page-link">' . __('Pages:','ultimo'), 'after' => '</div>')); ?>
					<div class="clear"></div>
				</div><!-- end .entry -->
			
			</div><!-- end .post -->
		<?php endwhile; ?>
		<?php endif; ?>
	</div><!-- end main --> 
<?php get_sidebar(); ?>
<div class="clear"></div>
</div><!-- end container -->
<div class="clear"></div><!--ie6-->
<?php get_footer(); ?>