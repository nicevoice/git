<?php
/*
 * Page template for 404 error page.
 */

get_header(); ?>
<div id="container">
	<div id="main">
    	<div class="post">
			<div class="title">
				<h2><?php _e("Not Found", "ultimo"); ?></h2>
			</div><!-- end .title -->
			<div class="clear"></div>
			
			<div class="entry">
				<p><?php _e("Sorry, but the page you requested could not be found. You may go back to the home page, or use the search function.", "ultimo"); ?></p>
			</div><!-- end .entry -->
		</div><!-- end .post -->
	</div><!-- end #main --> 
<?php get_sidebar(); ?>
<div class="clear"></div>
</div><!-- end #container -->
<div class="clear"></div><!--ie6-->
<?php get_footer(); ?>