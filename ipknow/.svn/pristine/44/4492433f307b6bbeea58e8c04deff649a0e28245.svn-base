<?php
/**
 * @package WordPress
 * @subpackage Default_Theme
 */

// Do not delete these lines
	if (!empty($_SERVER['SCRIPT_FILENAME']) && 'comments.php' == basename($_SERVER['SCRIPT_FILENAME']))
		die ('Please do not load this page directly. Thanks!');

	if ( post_password_required() ) { ?>
		<p class="nocomments"><?php _e("This post is password protected. Enter the password to view comments.", "ultimo"); ?></p>
	<?php
		return;
	}
?>

<!-- You can start editing here. -->

<?php if ( comments_open() ) : ?>
<div id="comments">
	<span class="commentlist current"><?php comments_number(__("No comment", "ultimo"), __("1 comment", "ultimo"), __("% comments", "ultimo"));?></span>
	<div class="clear"></div>
</div>
<?php endif; ?>

<?php if ( have_comments() ) : ?>

<ol class="commentlist">
	<?php
		wp_list_comments('type=all&callback=mytheme_comment');
	?>
	
	<?php
		//wp_list_comments('type=pings&callback=mytheme_pings');
	?>
</ol>

<!--paged comment goes here-->
<div class="comments-navi">
	<?php echo $max_page; ?>
	<span><?php previous_comments_link(__("&laquo; older comments", "ultimo")); ?></span>
	<span><?php next_comments_link(__("newer comments &raquo;", "ultimo")); ?></span>
	<div class="clear"></div>
</div>

<?php else : // this is displayed if there are no comments so far ?>

	<?php if ('open' == $post->comment_status) : ?>
		<!-- If comments are open, but there are no comments. -->

	 <?php else : // comments are closed ?>
		<!-- If comments are closed. -->
		<p class="nocomments"><?php _e("Comments are closed.", "ultimo") ?></p>
	<?php endif; ?>
<?php endif; ?>


<?php if ('open' == $post->comment_status) : ?>

<div id="respond">

<h3><?php if(($comment_author != '') && !$user_ID) {
		_e("Welcome back ", "ultimo");
		echo $comment_author . ".";
	?>
	<span class="notyou">(<a href="#"><?php _e("Not you?", "ultimo"); ?></a>)</span>
	<?php } else {
	_e("Leave a comment", "ultimo");
	} ?>
</h3>

<div class="cancel-reply">
	<span><?php cancel_comment_reply_link(__("click to cancel reply", "ultimo")); ?></span>
</div>

<?php if ( get_option('comment_registration') && !$user_ID ) : ?>
<p><?php _e("You must be ", "ultimo"); ?><a href="<?php echo get_option('siteurl'); ?>/wp-login.php?redirect_to=<?php echo urlencode(get_permalink()); ?>"><?php _e("logged in", "ultimo"); ?></a><?php _e(" to post a comment.", "ultimo"); ?></p>
<?php else : ?>

<form action="<?php echo get_option('siteurl'); ?>/wp-comments-post.php" method="post" id="commentform">

<?php if ( $user_ID ) : ?>

<p><?php _e("Logged in as ", "ultimo"); ?><a href="<?php echo get_option('siteurl'); ?>/wp-admin/profile.php"><?php echo $user_identity; ?></a>. <a href="<?php echo wp_logout_url(get_permalink()); ?>" title="<?php _e('Log out of this account', 'ultimo'); ?>"><?php _e("Log out &raquo;", "ultimo"); ?></a></p>

<div class="input-area"><textarea name="comment" id="comment" cols="100%" rows="5" tabindex="1" class="message-input" onkeydown="if((event.ctrlKey&&event.keyCode==13)){document.getElementById('submit').click();return false};" ></textarea></div>

<?php else : ?>


<div class="input-area"><textarea name="comment" id="comment" cols="100%" rows="5" tabindex="1" class="message-input" onkeydown="if((event.ctrlKey&&event.keyCode==13)){document.getElementById('submit').click();return false};" ></textarea></div>
	
<div class="user-info">
	<div class="single-field">
		<label for="author" class="desc"><?php _e("Name", "ultimo"); ?>
			<?php if ($req)
				echo "<abbr title=\"";
				_e("Required", "ultimo");
				echo "\">:<sup>&#8224;</sup></abbr>";
			?>
		</label>
		
		<input type="text" name="author" id="author" value="<?php echo $comment_author; ?>" size="22" tabindex="2" class="comment-input" <?php if ($req) echo "aria-required='true'"; ?> />
	</div>
	
	<div class="single-field">
		<label for="email" class="desc"><?php _e("Email", "ultimo"); ?>
			<?php if ($req)
				echo "<abbr title=\"";
				_e("Required, will not be published", "ultimo");
				echo "\">:<sup>&#8224;</sup></abbr>";
			?>
		</label>
		
		<input type="text" name="email" id="email" value="<?php echo $comment_author_email; ?>" size="22" tabindex="3" class="comment-input" <?php if ($req) echo "aria-required='true'"; ?> />
	</div>

	<div class="single-field">
		<label for="url" class="desc"><?php _e("Website", "ultimo"); ?>:<sup style="color: #fff;">&#8224;</sup></label>
		<input type="text" name="url" id="url" value="<?php echo $comment_author_url; ?>" size="22" tabindex="4" class="comment-input" />
	</div>
	<div class="clear"></div>
</div>

<?php endif; ?>

<!--<p><small><strong>XHTML:</strong>&nbsp;You&nbsp;can&nbsp;use&nbsp;these&nbsp;tags:<code><?php //echo allowed_tags(); ?></code></small></p>-->

<div class="submit-button">
	<input name="submit" type="submit" id="submit" tabindex="5" value="" class="button" /><!--<span class="key">(Support Ctrl+Enter quick submit)</span>-->
	<?php comment_id_fields(); ?>
	<?php do_action('comment_form', $post->ID); ?>
	<div class="clear"></div>
</div>

</form>

<?php endif; // If registration required and not logged in ?>
</div>

<?php endif; // if you delete this the sky will fall on your head ?>
