<div id="sidebar">
	<ul>
		<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar() ) : ?>
		
		<li class="sidebar-search">
			<ul>
				<?php include (TEMPLATEPATH . '/searchform.php'); ?>
			</ul>
		</li>
		
		<li class="recent-post">
			<h3><?php _e("Recent Posts", "ultimo"); ?></h3>
			<ul>
				<?php wp_get_archives('type=postbypost&limit=6'); ?>
			</ul>
		</li>
		
		<li class="recent-comment">
			<h3><?php _e("Recent Comments", "ultimo"); ?></h3>
			<ul>
				<?php rc(); ?>
			</ul>
		</li>
		
		<li class="meta">
				<h3><?php _e("Meta", "ultimo"); ?></h3>
				<ul>
					<?php wp_register(); ?>
					<li><?php wp_loginout(); ?></li>
					<?php wp_meta(); ?>
				</ul>
		</li>
		
		<?php endif; ?>
		
	</ul><!-- end ul -->
</div><!-- end sidebar -->
