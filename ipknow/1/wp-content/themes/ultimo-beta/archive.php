<?php get_header(); ?>
<div id="container">
	<div id="main">
		
		<?php if(is_tag()) : ?>
		<div class="strong"><?php _e("Tags: ", "ultimo"); ?><span class="keyword"><?php single_tag_title(); ?></span></div>
			
		<?php elseif(is_category()) : ?>
		<div class="strong"><?php _e("Category: ", "ultimo"); ?><span class="keyword"><?php single_cat_title(); ?></span></div>
			
		<? elseif(is_day()) : ?>
		<div class="strong"><?php _e("Daily Archives: ", "ultimo"); ?><span class="keyword"><?php the_time(__('F jS, Y','ultimo')); ?></span></div>
			
		<? elseif(is_month()) : ?>
		<div class="strong"><?php _e("Monthly Archives: ", "ultimo"); ?><span class="keyword"><?php the_time(__('F, Y','ultimo')); ?></span></div>
			
		<? elseif(is_year()) : ?>
		<div class="strong"><?php _e("Yearly Archives: ", "ultimo"); ?><span class="keyword"><?php the_time(__('Y','ultimo')); ?></span></div>
			
		<?php endif; ?>
		
		<?php if (have_posts()) : while (have_posts()) : the_post(); ?>	
		
			<div class="post<?php if(function_exists (sticky_class)): sticky_class(); endif; ?>" id="post-<?php the_ID(); ?>">
			
				<div class="title">
					<h2><a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php _e("Permalink to: ", "ultimo"); ?><?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
				</div><!-- end .title -->
				
				<div class="postmeta">
					<span class="meta-date"><?php _e("Posted on ", "ultimo"); ?><?php the_time("Y-m-d"); ?></span>
					<span class="meta-author"><?php _e("by ", "ultimo");?><?php the_author_posts_link(); //use the_author_link() to display the author's profile ?></span>
					<span class="meta-category"><?php _e("in ", "ultimo");?><?php the_category(', '); ?></span>
					<span class="meta-comments"><?php comments_popup_link( __("Leave a comment", "ultimo"), __("1 Comment", "ultimo"), __("% Comments", "ultimo")); ?></span>
				</div><!-- end .postmeta -->
				
				<div class="entry">
                    <?php //the_content(__("Continue Reading &raquo;", "ultimo")); ?>
                    <!--<?php the_content(__('Read more...', 'ultimo')); ?>-->
					<div class="clear"></div>
				</div><!-- end .entry -->
				
				<?php $tag = get_the_tags(); if($tag){ ?>
				<div class="postmeta-bottom">
					<span class="tag"><?php _e("Tags: ", "ultimo"); ?><?php the_tags('<span>',' ','</span>'); ?></span>
					<div class="clear"></div>
				</div><!-- end .postmeta_bottom -->
				<?php } ?>
			</div><!-- end .post -->
		<?php endwhile; ?>

	    <?php else : ?>
	    	<div class="post not-found">
				<div class="title">
					<h2><?php _e("Not Found", "ultimo"); ?></h2>
				</div>
				<div class="clear"></div>
				
				<div class="entry no-result">
					<p class="no_result"><?php _e("Apologies, but no results were found for the requested archive. Perhaps searching will help find a related post.", "ultimo"); ?></p>
					<?php //TODO: include a search form ?>
				</div>
			</div><!-- end .post -->
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