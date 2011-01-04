<?php
/*
Plugin Name: Display Post Image
Plugin URI: http://caxid.com/get-post-image/
Description: Display the image for your WordPress blog post.
Author: Ramesh
Author URI: http://caxid.com/
*/

function display_post_image($args){
	global $wpdb, $post;
	
	$defaults = array(
		'width' => null,
		'height' => null,
		'css' => '',
		'parent_id' => '',
		'post_id' => '',
		'filename' => '',
		'return_html' => true
	);
	$r = wp_parse_args( $args, $defaults );
	extract( $r, EXTR_SKIP );	
	
	if(empty($width) && empty($height)) $width = 200;
	if(empty($parent_id)) $parent_id = $post->ID;
	if(!empty($filename)){
		$sql = 'SELECT * FROM ' . $wpdb->posts . ' WHERE post_name="'.$filename.'" AND post_type="attachment" AND post_mime_type LIKE "imag%"';	
	} else if(!empty($post_id)){
		$sql = 'SELECT * FROM ' . $wpdb->posts . ' WHERE ID='.$post_id;	
	} else {
		$sql = 'SELECT * FROM ' . $wpdb->posts . ' WHERE post_parent='.$parent_id.' AND post_type="attachment" AND post_mime_type LIKE "imag%" ORDER BY menu_order';
	}
	$image = $wpdb->get_row($sql);
	if($image){
		$image->guid_path = str_replace(basename($image->guid),'',$image->guid);
		$image->filename = basename($image->guid);
		$image->basepath = ABSPATH.str_replace(get_bloginfo('wpurl').'/','',$image->guid);
		$image->abspath = str_replace($image->filename,'',$image->basepath);		
		$image->ext = substr($image->filename, -4, 4);
		$imagesize = getimagesize($image->basepath);
		$image->org_width = $imagesize[0]; // width of original image
		$image->org_height = $imagesize[1]; // height of original image
		if($image->org_width <= $width || $image->org_height <= $height){
			// attempting to scale an image past its original dimensions, populate neccessary vars but don't resize:
			$dimensions = wp_constrain_dimensions( $image->org_width, $image->org_height, $image->org_width, $image->org_height );
			$image->new_filename = $image->filename;
		} else {
			$dimensions = wp_constrain_dimensions( $image->org_width, $image->org_height, $width, $height );
			$image->new_filename = $image->filename;
		}
		$image->checkimagepath = $image->abspath.$image->new_filename;
		if(!file_exists($image->checkimagepath)){
			// image doesn't exist, create it:
			require_once(ABSPATH.'wp-admin/includes/image.php');
			$image->destfilename = image_resize( $image->basepath, $width, $height); 
			$image->resize_msg = 'Image was resized.';
		} else {
			$image->resize_msg = 'Image already existed. No resize neccessary.';	
		}
		$image_html = '<img src="'.$image->guid_path.$image->new_filename.'" class="wp-image-'.$image->ID.' '.$css.'" style="width: '.$dimensions[0].'px; height: '.$dimensions[1].'px;" alt="'.$image->post_title.'" />';	
		if(stristr($css,'thickbox')){
			$original_src = wp_get_attachment_url($image->ID);
			$image_html = '<a title="'.esc_attr($image->post_title).'" href="'.$original_src.'" class="thickbox">'.$image_html.'</a>';
		}
		if($return_html === true){
			return $image_html;
		} else {
			return $image->guid_path.$image->new_filename;	
		}
	} else {
		return false;	
	}
}
?>