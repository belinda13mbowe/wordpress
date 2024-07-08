<?php
/*
* Module - Scrollme Section
*/
if(!class_exists('Scrollme_VC')){
	class Scrollme_VC{
		function __construct(){
			add_shortcode('common_scrollme',array($this,'scrollme_shortcode'));
			add_action('init',array($this,'scrollme_shortcode_mapper'));
		}/* end constructor*/
		function scrollme_shortcode($atts){

			$output = '';
			extract(shortcode_atts(array(
				"scrollme_id" => "0",
			),$atts));

			$output = do_shortcode('[scrollme id="'.esc_attr($scrollme_id).'"]');

			echo $output;
		} /* end scrollme_shortcode()*/
		function scrollme_shortcode_mapper(){
			if(function_exists('vc_map')){
				$args = array( 'post_type' => 'scrollme');
				$scrollme_forms = get_posts($args);
				$scrollme = array();
				if(empty($scrollme_forms['errors'])){
					foreach($scrollme_forms as $form){
						$scrollme[$form->post_title] = $form->ID;
					}
				}

				vc_map(
					array(
						"name" => esc_html__("Scrollme", 'scrollme'),
						"base" => "common_scrollme",
						"class" => "pix-theme-icon",
						"category" => esc_html__("Common", 'scrollme'),
						"params" => array(
							array(
								'type' => 'dropdown',
								'heading' => esc_html__('Scrollme Form', 'scrollme'),
								'param_name' => 'scrollme_id',
								'value' => $scrollme,
								'description' => esc_html__('Select scrollme form to show', 'scrollme'),
							),
						)
					)
				);
			} /* end vc_map check*/
		}/*end scrollme_shortcode_mapper()*/
	} /* end class Ultimate_Animation*/
	// Instantiate the class
	new Scrollme_VC;
	if ( class_exists( 'WPBakeryShortCodes' ) ) {
		class WPBakeryShortCode_common_scrollme extends WPBakeryShortCodes {
		}
	}
}