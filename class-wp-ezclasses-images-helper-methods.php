<?php
/** 
 * Various methods for working with images and WordPress.
 *
 * Long description TODO (@link http://)
 *
 * PHP version 5.3
 *
 * LICENSE: MIT 
 *
 * @package WP ezClasses
 * @dependencies: 
 * @author Mark Simchock <mark.simchock@alchemyunited.com>
 * @since 0.5.0
 * @license MIT
 */
 
/*
 * == Change Log == 
 *
 * --- 
 */


if (!class_exists('Class_WP_ezClasses_Images_Helper_Methods')) {
	class Class_WP_ezClasses_Images_Helper_Methods extends Class_WP_ezClasses_Master_Singleton {
	
			
		protected function __construct(){
			parent::__construct();
		}
		
		
		public function ezc_init($arr_args = NULL){}
	
	
		/**
		 * Returns an array of the current defined image size names, dimensions and crop settings. 
		 */
		public function wp_image_sizes_ez(){
			global $_wp_additional_image_sizes;
		
		
			$arr_img_sizes_defaults = array();
			$arr_wp_image_default_sizes = $this->wp_image_size_stored_as_options();
			foreach( $arr_wp_image_default_sizes as $str_size ){
		
					$arr_img_sizes_defaults[ $str_size ]['width'] = get_option( $str_size . '_size_w', '0' );
					$arr_img_sizes_defaults[ $str_size]['height'] = get_option( $str_size . '_size_h', '0' );
			}
		
			$arr_set_image_sizes = array();
			foreach( get_intermediate_image_sizes() as $str_size ){

				if( isset( $_wp_additional_image_sizes ) && isset( $_wp_additional_image_sizes[ $str_size ] ) ) {
					$arr_set_image_sizes[ $str_size ]['width'] = $_wp_additional_image_sizes[ $str_size ]['width'];
					$arr_set_image_sizes[ $str_size ]['height'] = $_wp_additional_image_sizes[ $str_size ]['height'];
					$arr_set_image_sizes[ $str_size ]['crop'] =  $_wp_additional_image_sizes[ $str_size ]['crop'];
				}
			
			}
			$arr_all = array_merge($arr_img_sizes_defaults, $arr_set_image_sizes);
			unset($arr_all['post-thumbnail']);
			return $arr_all;
		}
	
		/*
		 *
		 */
		protected function wp_image_size_stored_as_options(){
			return array( 'thumbnail', 'medium', 'large' );
		}
	
		/*
		 *
		 */
		protected function post_thumbnail_check() {
			global $post;
			
			if (get_the_post_thumbnail()) {
				  return true; 
			} else { 
				return false; 
			}
		}


		/* 
		 * EXPERIMENTAL - Auto-setting Featured Image (Post Thumbnail)
		 *
		 * Will automatically add the first image attached to a post as the Featured Image if post does not have a Featured Image already set.
		 *
		 * Inspired by a function found in R. Baker's Boilerstrap WP theme
		 */
		function featured_image_autoset() {

			$bool_post_thumbnail = self::post_thumbnail_check();
			if ($bool_post_thumbnail == true ){
				return the_post_thumbnail();
			}
			if ($bool_post_thumbnail == false ){
				$arr_image_args = array(
										'post_type' => 'attachment',
										'numberposts' => 1,
										'post_mime_type' => 'image',
										'post_parent' => $post->ID,
										'order' => 'desc'
									 );
				 
				$arr_attached_images = get_children( $arr_image_args );
				if ($arr_attached_images) {
					foreach ($arr_attached_images as $attachment_id => $attachment) {
						set_post_thumbnail($post->ID, $attachment_id);
					}
					return the_post_thumbnail();
				} else { 
					//TODO - Allow dev to define() a post ID and attachment ID to use as a default. 
					return '';
				}
			}
		}  //end function
	
	}
}

?>