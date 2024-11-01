<?php

/*
Plugin Name: Smart Post Like
Plugin URI: http://www.emballageproff.dk/
Description: A post like system for wordpress.
Tags: Post like, like, post, page
Version: 1.0.0
Author: Kjeld Hansen
Author URI: #
Requires at least: 4.0
Tested up to: 4.7
Text Domain: smart-post-like
*/

if ( ! defined( 'ABSPATH' ) ) exit; 
 
add_action('admin_menu','smart_post_like_admin_menu');
function smart_post_like_admin_menu() { 
    add_menu_page(
		"Smart Like",
		" Post Like",
		8,
		__FILE__,
		"smart_post_like_admin_menu_list","","post-like"/*,
		plugins_url( 'images/plugin-icon.png', __FILE__) */
	); 
}
function smart_post_like_admin_menu_list(){
	?> <h2>Post Like</h2>
	<div class="spl-wrap">	
		&lt;?php echo do_shortcode('[smart-post-like]'); ?>
        <h2>Output : </h2>
	<?php
	 echo do_shortcode('[smart-post-like]');
	echo '</div>';
}




	
function smart_post_like_scripts_js() { 
	//wp_enqueue_script( 'spl-jquery', 'https://ajax.googleapis.com/ajax/libs/jquery/1.3/jquery.min.js', array( 'jquery' ), '1.0.0', true );
	wp_enqueue_script( 'smart-post-like-jquery', plugins_url( '/spr.js', __FILE__), array( 'jquery' ), '1.0.0', true );
    wp_enqueue_style( 'smart-post-like-css', plugins_url( '/spr.css', __FILE__) );
}
add_action( 'wp_enqueue_scripts', 'smart_post_like_scripts_js' );

//intval($_POST['pcat']); sanitize_text_field($_POST['ftype']);

add_shortcode('smart-post-like', 'smart_post_like_fun');
function smart_post_like_fun(){ 
$resp='';
	if(isset($_POST['smprt']) && $_POST['smprt']!=''){
		$rate = intval($_POST['smprt']); $userID = wp_get_current_user()->ID; 
		if($rate==1){
			if(add_post_meta( get_the_ID(), 'ri_spost_like',  $userID )){
				if(add_user_meta( $userID, 'ri_spost_like_'.get_the_ID(),  $rate )){ $resp = 'Success!'; }
			}
		}else{
			if(add_post_meta( get_the_ID(), 'ri_spost_dislike',  $userID )){
				if(add_user_meta( $userID, 'ri_spost_like_'.get_the_ID(),  $rate )){ $resp = 'Success!'; }
			}
		}
	}
$postlike = '
<div class="riquickContact">
	<p>Likes :';
		$likes = get_post_meta( get_the_ID(), 'ri_spost_like');
		$postlike .= $likes[0];
	$postlike .= ' </p>';
	
	if(is_user_logged_in()){
		$rateMeta = get_post_meta( get_the_ID(), 'ri_spost_like', false ); $f=0;
		foreach($rateMeta as $rmt){ if($rmt==wp_get_current_user()->ID){ $f=1; } }
		if($f == 1){
			//update_post_meta( get_the_ID(), 'ri_spost_like',  wp_get_current_user()->ID );
			$resp = 'You Liked';
		}else{
			$postlike .= '
			<form id="smprtf" method="post" action="">
				<div class="star-like">
					<input type="hidden" id="rateval" name="smprt" value="" />
					<div class="stars">
						<a class="star" title="1"></a>
						<a class="star" title="-1"></a>
					</div>
				</div>
				<input type="submit" name="sub" value="OK" />
			</form>';
		}
	}else{
		$postlike .= '<a href="/login" class="rifancybox1">Login</a>';
		$args = array(
			'echo'           => true,
			'remember'       => true,
			'redirect'       => ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'],
			'form_id'        => 'loginform_rate1',
			'id_username'    => 'user_login1',
			'id_password'    => 'user_pass1',
			'id_remember'    => 'rememberme1',
			'id_submit'      => 'wp-submit1',
			'label_username' => __( 'Username' ),
			'label_password' => __( 'Password' ),
			'label_remember' => __( 'Remember Me' ),
			'label_log_in'   => __( 'Log In' ),
			'value_username' => '',
			'value_remember' => false
		);
		wp_login_form( $args );
	}
	
	
	$postlike .= $resp.'
</div>

';
/*<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.3/jquery.min.js"></script>
<script type="text/javascript" src="'.plugins_url( '/spr.js', __FILE__).'"></script>*/


return $postlike;
}

function spl_add_to_content( $content ) {    
    if( is_single() ) {
        $content .= smart_post_like_fun();
    }
    return $content;
}
add_filter( 'the_content', 'spl_add_to_content' );





