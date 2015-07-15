<?php get_header(); ?>
<div id="container">
	<div id="main">
    	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
			<div class="post" id="post-<?php the_ID(); ?>">
				<div class="title">
					<h2><?php the_title(); ?></h2>
				</div><!-- end .title -->
				
				<div class="postmeta">
					<span class="meta-date"><?php _e("Posted on ", "ultimo"); ?><?php the_time('Y-m-d'); ?></span>
					<span class="meta-author"><?php _e("by ", "ultimo");?><?php the_author_posts_link(); //use the_author_link() to display the author's profile ?></span>
					<span class="meta-category"><?php _e("in ", "ultimo");?><?php the_category(', '); ?></span>
					<span class="meta-edit"><?php edit_post_link(__("Edit", "ultimo")); ?></span>
					<span class="meta-lac"><a href="#respond"><?php _e("Leave a comment", "ultimo"); ?></a></span>
				</div><!-- end .postmeta -->
					
				<div class="entry">
					<?php the_content(); ?>
                    
					<?php wp_link_pages(array('before' => '<div class="page-link">' . __('Pages:','ultimo'), 'after' => '</div>')); ?>
					<div class="clear"></div>
				</div><!-- end .entry -->
				
				<?php $tag = get_the_tags(); if($tag){ ?>
				<div class="postmeta-bottom">
					<span class="tag"><?php _e("Tags: ", "ultimo"); ?><?php the_tags('<span>',' ','</span>'); ?></span>
					<div class="clear"></div>
				</div><!-- end .postmeta_bottom -->
				<?php } ?>
			</div><!-- end .post -->
			
		<?php comments_template('', true); ?>
		<?php endwhile; ?>
		<?php endif; ?>
	</div><!-- end #main --> 

<?php get_sidebar(); ?>
<div class="clear"></div>
</div><!-- end container -->
<div class="clear"></div>
<?php get_footer(); ?>