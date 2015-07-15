</div><!-- end #page_wrap -->
<div id="footer">
	<div class="footer_wrapper">
		Copyright &copy; <a href="<?php echo home_url("/"); ?>" title="<?php echo esc_attr(get_bloginfo("name")); ?>"><?php bloginfo('name'); ?></a>
		<br />
		<?php echo _e("Proudly powered by ", "ultimo"); ?><a href="http://wordpress.org/" title="<?php _e("CODE IS POETRY", "ultimo"); ?>">WordPress</a> and <a href="http://imotta.cn/" title="imotta.cn"><?php echo _e("Ultimo Theme", "ultimo"); ?></a>
	</div>
</div><!-- end #footer -->

<?php wp_footer(); ?>

<!-- load some javascript -->
<script src="<?php bloginfo('stylesheet_directory'); ?>/scripts/basic.js" type="text/javascript"></script>
<!-- jquery scrollTo plugin -->
<script src="<?php bloginfo('stylesheet_directory'); ?>/scripts/jquery.scrollTo-min.js" type="text/javascript"></script>
</body>
</html>