=== Plugin Name ===
Plugin URI: http://caxid.com/get-post-image/
Tags: display post image

Adds the function display_post_image(), giving theme builders easy access to images associated with a post or page.

== Description ==

This plugin provides the template tag `display_post_image()`. Use it to call dynamically created images uploaded via the WordPress media uploader. 

====== INSTALLATION =======

1. Upload the folder `display-post-image` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Place the below code in your templates


= Usage =

`<?php display_post_image($args); ?>`

= Example =

` <?php if(function_exists('display_post_image')) { ?>
        <a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'twentyten' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark">
	  <?php echo display_post_image('width=200px&css=alignleft&parent_id='.$post->ID); ?>
        </a>
  <?php } ?>`