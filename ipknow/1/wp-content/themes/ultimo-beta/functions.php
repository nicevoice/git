<?php
	if ( function_exists('register_sidebar') ) {
		register_sidebar();
	}
	
/*
 *	Add a custom avatar
 */
add_filter( 'avatar_defaults', 'u_addgravatar' );
function u_addgravatar( $avatar_defaults ) {
$myavatar = get_bloginfo('template_directory') . '/images/avatar.png';
$avatar_defaults[$myavatar] = 'ultimo';
return $avatar_defaults;
}

/*
 *	Add search form to widget
 */
if ( function_exists('wp_register_sidebar_widget') )
    wp_register_sidebar_widget(1 ,__('Search'), 'widget_mytheme_search');


/*
 *	Display comment list
 */
function mytheme_comment($comment, $args, $depth) {
	$GLOBALS['comment'] = $comment; 
?>
	<li <?php comment_class(); ?> id="li-comment-<?php comment_ID() ?>">
		<div id="comment-<?php comment_ID(); ?>">
			<div class="comment-author vcard">
				<div class="left">
					<?php echo get_avatar($comment,$size='48'); ?>
				</div>
			</div><!-- end vcard -->
		
			<div class="right">
				<?php if ($comment->comment_approved == '0') : ?>
		        	<em><?php _e("Your comment is awaiting moderation.", "ultimo" ); ?></em>
		         	<br />
		        <?php endif; ?>
				
				<div class="comment-meta">
					<span class="commentmeta"><?php comment_author_link(); ?> <?php _e("at", "ultimo"); ?> <?php comment_time('H:i'); ?> <?php comment_date('Y.m.d'); ?></span>
					<span class="reply hide">
						<?php comment_reply_link(array_merge( $args, array('depth' => $depth, 'max_depth' => $args['max_depth']))); ?>
					</span>
					<div class="clear"></div>
				</div>
				
				<?php comment_text(); ?>
			</div>
			<div class="clear"></div>
		</div>
<?php }

/*
 *	Archives page function
 */
function ultimo_archives(){
	global $wpdb;
	
	$aresults = $wpdb->get_results("SELECT DISTINCT YEAR(post_date) AS year, MONTH(post_date) AS month, count(ID) as posts FROM " . $wpdb->posts . " WHERE post_date <'" . current_time('mysql') . "' AND post_status='publish' AND post_type='post' AND post_password='' GROUP BY YEAR(post_date), MONTH(post_date) ORDER BY post_date DESC");
	
	if($aresults){
		foreach($aresults as $aresult) {
			$url = get_month_link($aresult->year, $aresult->month);
    		$text = sprintf('%d-%s', $aresult->year, zeroise($aresult->month,2));
    		echo get_archives_link($url, $text, '','<h3 class="ah3">','</h3>');

			$thismonth   = zeroise($aresult->month,2);
			$thisyear = $aresult->year;

        	$aresults2 = $wpdb->get_results("SELECT ID, post_date, post_title, comment_status FROM " . $wpdb->posts . " WHERE post_date LIKE '$thisyear-$thismonth-%' AND post_status='publish' AND post_type='post' AND post_password='' ORDER BY post_date DESC");

        	if ($aresults2) {
        		echo "<ul class=\"postspermonth\">\n";
            	foreach ($aresults2 as $aresult2) {
               		if ($aresult2->post_date != '0000-00-00 00:00:00') {
                 		$url = get_permalink($aresult2->ID);
                 		$arc_title = $aresult2->post_title;

                 		if ($arc_title) $text = strip_tags($arc_title);
                    	else $text = $aresult2->ID;

                   		echo "<li>".get_archives_link($url, $text, '');
						$comments = mysql_query("SELECT * FROM " . $wpdb->comments . " WHERE comment_post_ID=" . $aresult2->ID);
						$comments_count = mysql_num_rows($comments);
						if ($aresult2->comment_status == "open" OR $comments_count > 0) echo '&nbsp;('.$comments_count.')';
						echo "</li>\n";
                 	}
            	}
            	echo "</ul>\n";
        	}
		}
	}	
}

/*
 *	Rencent comment function
 */
function rc(){
	global $wpdb;
	
	$sql = "SELECT comment_author, comment_author_email, comment_author_url, comment_ID, comment_post_ID, comment_content, comment_type, post_title
			FROM $wpdb->comments c, $wpdb->posts p 
			WHERE comment_approved = '1' AND c.comment_post_ID = p.ID AND post_status = 'publish' AND post_password = '' AND comment_author != 'motta' AND comment_type != 'pingback' AND comment_type != 'trackback'
			ORDER BY comment_date_gmt DESC LIMIT 6";
			
	$comments = $wpdb->get_results($sql);
	$output = '';
	
	if($comments){
		foreach($comments as $comment){
			$comment_excerpt = preg_replace('/(\r\n)|(\n)/', '', $comment->comment_content);
			$comment_excerpt = preg_replace('/\<(.+?)\>/', '', $comment_excerpt);
			$comment_excerpt = rc_sub($comment_excerpt, 20);
			
			$before = '<li class="rc"> ';
			$after = '</li>';
			
			$output .= $before . '<a href="' . get_permalink($comment->comment_post_ID) . '#comment-' . $comment->comment_ID . '" title="'. $comment->comment_author . ' on '.$comment->post_title . '">' . strip_tags($comment->comment_author) . '</a>' . ': ' . '<span class="excerpt">' . $comment_excerpt . '</span>' . $after;
		}
	}
	
	echo $output;
}

function rc_sub($str, $length=5){
	$pa = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|\xe0[\xa0-\xbf][\x80-\xbf]|[\xe1-\xef][\x80-\xbf][\x80-\xbf]|\xf0[\x90-\xbf][\x80-\xbf][\x80-\xbf]|[\xf1-\xf7][\x80-\xbf][\x80-\xbf][\x80-\xbf]/";
	
	preg_match_all($pa, $str, $str2);
	
	if(count($str2[0]) > $length){
		$dot = '...';
		$str = join('', array_slice($str2[0], 0, $length)) . $dot;
	}
	
	return $str;
}

/*
 *	Add a "home" link at the nav menu.
 */
function home_page_menu_args( $args ) {
    $args['show_home'] = true;
    return $args;
}
add_filter( 'wp_page_menu_args', 'home_page_menu_args' );

/*
 *	There are two menu positions, navigation bar and top right.
 */
add_action('init', 'register_my_menus');

function register_my_menus(){
	register_nav_menus(
		array(
			'primary-menu' => __( 'Navigation Bar' ),
			'secondary-menu' => __( 'Top Right Bar' )
		)
	);
}

?>
