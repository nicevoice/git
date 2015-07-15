<?php
/*
 * Page template for displaying author's all posts.
 * TODO: add more author infomation (avatar, bio...).
 */

get_header(); ?>
<div id="container">
	<div id="main">
		<?php // get user info
			if(get_query_var("author_name")) {
				$curauth = get_user_by("slug", get_query_var("author_name"));
			} else {
				$curauth = get_userdata(get_query_var("author"));
			}
		?>
		<div class="strong"><?php _e("Post by Author: ", "ultimo"); ?><span class="keyword"><?php echo $curauth->nickname; ?></span></div>
    	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
			
			<div class="post<?php if(function_exists (sticky_class)): sticky_class(); endif; ?>" id="post-<?php the_ID(); ?>">
			
				<div class="title">
					<h2><a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php _e("Permalink to: ", "ultimo"); ?><?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
				</div><!-- end .title -->
				
				<div class="postmeta">
					<span class="meta-date"><?php _e("Posted on ", "ultimo"); ?><?php the_time('Y-m-d'); ?></span>
					<span class="meta-author"><?php _e("by ", "ultimo");?><?php the_author_posts_link(); //use the_author_link() to display the author's profile ?></span>
					<span class="meta-category"><?php _e("in ", "ultimo");?><?php the_category(', '); ?></span>
					<span class="meta-comments"><?php comments_popup_link( __("Leave a comment", "ultimo"), __("1 Comment", "ultimo"), __("% Comments", "ultimo")); ?></span>
				</div><!-- end .postmeta -->
				
				<div class="entry">
					<?php the_excerpt(); ?>
					<div class="clear"></div>
				</div><!-- end .entry -->
			</div><!-- end .post -->
		<?php endwhile; ?>
		<?php endif; ?>
		
		<?php if(function_exists('wp_pagenavi')) : ?>
			<div class="pagenavi">
				<?php wp_pagenavi(); ?>
			</div>
		<?php else : ?>
			<div class="pagination" style="display: none;">
				<span class="left"><?php next_posts_link(__("&laquo; Prev Page", "ultimo")); //next_posts_link points to older posts ?></span>
				<span class="right"><?php previous_posts_link(__("Next Page &raquo;", "ultimo"));//previous_posts_link points to newer posts ?></span>
				<div class="clear"></div>
			</div><!-- end .pagination -->
    	<?php endif; ?>
		
	</div><!-- end #main --> 
<?php get_sidebar(); ?>
<div class="clear"></div>
</div><!-- end #container -->
<div class="clear"></div><!--ie6-->
<?php get_footer(); ?>