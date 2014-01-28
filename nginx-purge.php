<?php
/*
Plugin Name: nginx-purge
Description: nginx cache purge
Author: Christos Kapasakalidis
Version: 0.1
Author URI: 
*/
function _remove_query_strings_1( $src ){
	$rqs = explode( '?ver', $src );
        return $rqs[0];
}
function _remove_query_strings_2( $src ){
	$rqs = explode( '&ver', $src );
        return $rqs[0];
}
add_filter( 'script_loader_src', '_remove_query_strings_1', 15, 1 );
add_filter( 'style_loader_src', '_remove_query_strings_1', 15, 1 );
add_filter( 'script_loader_src', '_remove_query_strings_2', 15, 1 );
add_filter( 'style_loader_src', '_remove_query_strings_2', 15, 1 );

function purge_post_save( $post_id ) {

	$log_file = WP_PLUGIN_DIR. '/nginx-purge/' . "purge.log";
	$post = sprintf('post_id = %s', $post_id);
	$post .= "\n";
	file_put_contents($log_file, $post, FILE_APPEND | LOCK_EX);
	
	$post_url = get_permalink( $post_id );
	$post_url .= "\n";
	file_put_contents($log_file, $post_url, FILE_APPEND | LOCK_EX);

	// If this is just a revision, don't send the email.
	// if ( wp_is_post_revision( $post_id ) )
	// 	return;

	// $post_title = get_the_title( $post_id );
	// $post_url = get_permalink( $post_id );
	// $subject = 'A post has been updated';

	// $message = "A post has been updated on your website:\n\n";
	// $message .= $post_title . ": " . $post_url;

	// // Send email to admin.
	// wp_mail( 'admin@example.com', $subject, $message );
}

function purge_comment_post( $comment_id ) {
	$log_file = WP_PLUGIN_DIR. '/nginx-purge/' . "purge.log";
	$text = sprintf('comment_id = %s', $comment_id);
	$text .= "\n";
	$comment_obj = get_comment( $comment_id );
	$text .= sprintf('comment_post_id = %s', $comment_obj->comment_post_ID) . "\n";
	file_put_contents($log_file, $text, FILE_APPEND | LOCK_EX);
}

add_action( 'save_post', 'purge_post_save' );
add_action( 'comment_post', 'purge_comment_post' );


?>