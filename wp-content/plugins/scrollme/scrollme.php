<?php
/***********************************
 *
 * Plugin Name:  Scrollme
 * Plugin URI:   http://red-carlos.com/
 * Description:  I will write description later
 * Version:      1.0
 * Author:       TaHUoP
 * Author URI:   http://red-carlos.com/
 * License:      GPLv2 or later
 * Text Domain:  Scrollme
 * Domain Path:  /languages/
 ***********************************/
if (!defined('ABSPATH')) {
    exit; // disable direct access
}

require_once(__DIR__ . '/includes/scrollme_db.php');

if (!class_exists('Scrollme')) :

    class Scrollme
    {
		var $vc_dir;

        public function __construct()
        {

            // Register the post type.
            add_action('init', array($this, 'scrollme_post_type_init'));

            $this->vc_dir = plugin_dir_path( __FILE__ ).'vc/';
            add_action('after_setup_theme',array($this,'scrollme_vc_init'));

            add_action('admin_enqueue_scripts', array($this, 'scrollme_admin_enqueue_scripts'));
            add_action('wp_enqueue_scripts', array($this, 'scrollme_enqueue_scripts'));

            add_action('add_meta_boxes', array($this, 'scrollme_add_metabox'));
            add_action('save_post', array($this, 'scrollme_save_postdata'));
            add_action('delete_post', array($this, 'scrollme_delete_postdata'));
            add_shortcode('scrollme', array($this, 'scrollme_shortcode_output'));

            register_activation_hook(__FILE__, array($this, 'scrollme_create_db'));
//            register_deactivation_hook(__FILE__, array($this, ''));
        }

        public function scrollme_create_db()
        {
            global $wpdb;

            $table_name = $wpdb->prefix . "sm_items";
            if ($wpdb->get_var("show tables like '$table_name'") != $table_name) {

                $sql = "CREATE TABLE " . $table_name . " (
                    id mediumint NOT NULL AUTO_INCREMENT,
                    post_id  mediumint NOT NULL,
                    item_id  mediumint NOT NULL,
                    sm_pos_x smallint NOT NULL,
                    sm_pos_y smallint NOT NULL,
                    sm_pos ENUM(\"default\", \"center\", \"top\", \"bottom\", \"right\", \"left\") NOT NULL,
                    sm_z_index smallint NOT NULL,
                    sm_width smallint,
                    sm_height smallint,
                    sm_src varchar(200) NOT NULL,
                    sm_when ENUM(\"enter\", \"exit\", \"span\") NOT NULL,
                    sm_from DECIMAL(2,1),
                    sm_to DECIMAL(2,1),  
                    sm_easing ENUM(\"easeout\", \"easein\", \"easeinout\", \"linear\") NOT NULL,
                    sm_crop ENUM(\"true\", \"false\") NOT NULL,
                    sm_opacity DECIMAL(2,1),
                    sm_scalex DECIMAL(4,1),
                    sm_scaley DECIMAL(4,1),
                    sm_scalez DECIMAL(4,1),
                    sm_rotatex smallint,
                    sm_rotatey  smallint,
                    sm_rotatez smallint,
                    sm_translatex smallint,
                    sm_translatey smallint,
                    sm_translatez smallint,
                    UNIQUE KEY id (id)
	            );";

                require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
                dbDelta($sql);
            }
        }

        // Register scrollme post type
        public function scrollme_post_type_init()
        {
            $labels = array(
                    'name' => esc_html__('Scrollme', 'scrollme'),
                    'singular_name' => esc_html__('Scrollme', 'scrollme'),
                    'add_new' => esc_html__('Add New item', 'scrollme'),
                    'add_new_item' => esc_html__('Add New item', 'scrollme'),
                    'edit_item' => esc_html__('Edit item', 'scrollme'),
                    'new_item' => esc_html__('New item', 'scrollme'),
                    'view_item' => esc_html__('View item', 'scrollme'),
                    'search_items' => esc_html__('Search items', 'scrollme'),
                    'not_found' => esc_html__('Not found', 'scrollme'),
                    'not_found_in_trash' => esc_html__('Not found in Trash', 'scrollme'),
                    'parent_item_colon' => ''
            );

            $args = array(
                    'labels' => $labels,
                    'hierarchical' => false,
                    'supports' => array('title'),
                    'public' => true,
                    'show_ui' => true,
                    'show_in_menu' => true,
                    'show_in_nav_menus' => false,
                    'publicly_queryable' => false,
                    'exclude_from_search' => true,
                    'has_archive' => true,
                    'query_var' => true,
                    'can_export' => true,
                    'rewrite' => true,
                    'capability_type' => 'post',
		            'register_meta_box_cb'	=> 'scrollme_meta_boxes'
            );
            register_post_type('scrollme', $args);

            include ( plugin_dir_path( __FILE__ ) . 'includes/meta_box.php' );
        }

		function scrollme_vc_init()
		{
			require_once($this->vc_dir."vc_shortcode_scrollme.php");

		}// end aio_init

        // Adding metaabox for scrollme post type
        public function scrollme_add_metabox()
        {
            add_meta_box('scrollme_description_metabox', esc_html__('Info', 'scrollme'), array($this, 'scrollme_description_output_function'), 'scrollme');
            add_meta_box('scrollme_main_metabox', esc_html__('Main', 'scrollme'), array($this, 'scrollme_main_metabox_output'), 'scrollme');
            add_meta_box('scrollme_properties_metabox', esc_html__('ScrollMe properties', 'scrollme'), array($this, 'scrollme_properties_metabox_output'), 'scrollme');
        }

        //Outputs the content of the description meta box
        function scrollme_description_output_function($post)
        {
            ?>
            <span><?php echo esc_html__('To output content of the plugin use [scrollme id="' . $post->ID . '"] shortcode', 'scrollme'); ?></span>
            <?php
        }

        //Outputs the content of the description meta box
        function scrollme_main_metabox_output($post)
        { ?>
            <div id="scroll_main_block" class="main_block"></div>
            <button id="add-button" class="button"><?php esc_html_e('Add item', 'scrollme'); ?></button>
            <button id="delete-button" class="button"><?php esc_html_e('Delete item', 'scrollme'); ?></button>
            <button id="edit-image-button" class="button"><?php esc_html_e('Edit item image', 'scrollme'); ?></button>
            <?php
        }

        //Outputs the content of the description meta box
        function scrollme_properties_metabox_output($post)
        {
            ?>
            <p>
                <label for="sm_when"><?php esc_html_e('When', 'scrollme'); ?></label>
                <select name="sm_when" id="sm_when">
                    <option>enter</option>
                    <option>exit</option>
                    <option>span</option>
                </select>
            </p>
            <p>
                <label for="sm_from"><?php echo esc_html__('From', 'scrollme'); ?></label>
                <input name="sm_from" id="sm_from" type="number" min="0" max="1" step="0.1" value="1"/>  
            </p>
            <p>
                <label for="sm_to"><?php echo esc_html__('To', 'scrollme'); ?></label>
                <input name="sm_to" id="sm_to" type="number" min="0" max="1" step="0.1" value="0"/>
            </p>
            <p>
                <label for="sm_pos"><?php echo esc_html__('Position', 'scrollme'); ?></label>
                <select name="sm_pos" id="sm_pos">
                    <option>default</option>
                    <option>center</option>
                    <option>left</option>
                    <option>right</option>
                    <option>top</option>
                    <option>bottom</option>
                </select>
            </p>
            <p>
                <label for="sm_easing"><?php echo esc_html__('Easing', 'scrollme'); ?></label>
                <select name="sm_easing" id="sm_easing">
                    <option>easeout</option>
                    <option>easein</option>
                    <option>easeinout</option>
                    <option>linear</option>
                </select>
            </p>
            <p>
                <label for="sm_crop"><?php echo esc_html__('Crop', 'scrollme'); ?></label>
                <select name="sm_crop" id="sm_crop">
                    <option>true</option>
                    <option>false</option>
                </select>
            </p>

            <p>
                <label for="sm_opacity"><?php echo esc_html__('Opacity', 'scrollme'); ?></label>
                <input name="sm_opacity" id="sm_opacity" type="number" min="0" max="1" step="0.1" value="0"/>
            </p>

            <p>
                <label for="sm_scalex"><?php echo esc_html__('ScaleX', 'scrollme'); ?></label>
                <input name="sm_scalex" id="sm_scalex" type="number" min="-9999" step="0.1" value="0"/>
            </p>
            <p>
                <label for="sm_scaley"><?php echo esc_html__('ScaleY', 'scrollme'); ?></label>
                <input name="sm_scaley" id="sm_scaley" type="number" min="-9999" step="0.1" value="0"/>
            </p>
            <p>
                <label for="sm_scalez"><?php echo esc_html__('ScaleZ', 'scrollme'); ?></label>
                <input name="sm_scalez" id="sm_scalez" type="number" min="-9999" step="0.1" value="1"/>
            </p>

            <p>
                <label for="sm_rotatex"><?php echo esc_html__('RotateX', 'scrollme'); ?></label>
                <input name="sm_rotatex" id="sm_rotatex" type="number" min="-9999" max="360" step="1" value="1"/>
            </p>
            <p>
                <label for="sm_rotatey"><?php echo esc_html__('RotateY', 'scrollme'); ?></label>
                <input name="sm_rotatey" id="sm_rotatey" type="number" min="-9999" max="360" step="1" value="1"/>
            </p>
            <p>
                <label for="sm_rotatez"><?php echo esc_html__('RotateZ', 'scrollme'); ?></label>
                <input name="sm_rotatez" id="sm_rotatez" type="number" min="-9999" max="360" step="1" value="0"/>
            </p>

            <p>
                <label for="sm_translatex"><?php echo esc_html__('TranslateX', 'scrollme'); ?></label>
                <input name="sm_translatex" id="sm_translatex" type="number" min="-9999" step="1" value="0"/>
            </p>
            <p>
                <label for="sm_translatey"><?php echo esc_html__('TranslateY', 'scrollme'); ?></label>
                <input name="sm_translatey" id="sm_translatey" type="number" min="-9999" step="1" value="0"/>
            </p>
            <p>
                <label for="sm_translatez"><?php echo esc_html__('TranslateZ', 'scrollme'); ?></label>
                <input name="sm_translatez" id="sm_translatez" type="number" min="-9999" step="1" value="0"/>
            </p>
            <p>
                <label for="sm_z_index"><?php echo esc_html__('Z-index', 'scrollme'); ?></label>
                <input name="sm_z_index" id="sm_z_index" type="number" min="-9999" step="1" value="0"/>
            </p>

            <?php
        }

        public function scrollme_save_postdata($post_id)
        {
            if(get_post_type( $post_id ) == 'scrollme'){
                // Checks save status
                $is_autosave = wp_is_post_autosave($post_id);
                $is_revision = wp_is_post_revision($post_id);
                $is_valid_nonce = (isset($_POST['scrollme_nonce']) && wp_verify_nonce($_POST['scrollme_nonce'], basename(__FILE__))) ? 'true' : 'false';

                if (isset($_POST['sm_post_data'])) {
		            $sm_post_data = json_decode(str_replace("\\", "", $_POST['sm_post_data']), true);

		            Scrollme_db::updateItems($sm_post_data, $post_id);
	            }


                // Exits script depending on save status
                if ($is_autosave || $is_revision || !$is_valid_nonce) {
                    return;
                }
                // Checks for input and saves if needed
//            if (!empty($_POST['meta-order-num'])) {
//                update_post_meta($post_id, 'meta-order-num', $_POST['meta-order-num']);
//            }
            }
        }

        public function scrollme_delete_postdata($post_id)
        {
            if(get_post_type( $post_id ) == 'scrollme'){
                Scrollme_db::deleteItems( $post_id);
                delete_post_meta('scrollme_height', $post_id);
            }
        }

        //Shortcode output
        public function scrollme_shortcode_output($atts)
        {
            global $wpdb;
            
            $content = '';
            
            if (!empty($atts['id'])) {
                $items = Scrollme_db::getItemsArray((int)$atts['id']);

                if (!empty($items)) {
	                $min_height = get_post_meta($atts['id'], 'scrollme_height', 1) != '' ? get_post_meta($atts['id'], 'scrollme_height', 1) : 300;
                    $content .= '<div class="scrollme" style="min-height:'.esc_attr($min_height).'px">';

                        foreach ($items as $item) {

                            $margin_left = '';

                            if($item['sm_pos'] == 'center'){
                                $item['sm_pos_x'] = 50;
                                $margin_left = 'margin-left: -'.($item['sm_width']/2).'px;';
                            }

                            $content .= '<img
                                    src="' .  $item['sm_src'] . '"
                                    class="animateme"
                                    data-when="' . $item['sm_when'] . '"
                                    data-from="' . $item['sm_from'] . '"
                                    data-to="' . $item['sm_to'] . '"
                                    data-easing="' . $item['sm_easing'] . '"
                                    data-crop="' . $item['sm_crop'] . '"
                                    data-opacity="' . $item['sm_opacity'] . '"
                                    data-scalex="' . $item['sm_scalex'] . '"
                                    data-scaley="' . $item['sm_scaley'] . '"
                                    data-scalez="' . $item['sm_scalez'] . '"
                                    data-rotatex="' . $item['sm_rotatex'] . '"
                                    data-rotatey="' . $item['sm_rotatey'] . '"
                                    data-rotatez="' . $item['sm_rotatez'] . '"
                                    data-translatex="' . $item['sm_translatex'] . '"
                                    data-translatey="' . $item['sm_translatey'] . '"
                                    data-translatez="' . $item['sm_translatez'] . '"
                                    style="position: absolute; top: ' .  $item['sm_pos_y'] . '%; left: ' .  $item['sm_pos_x'] . '%; z-index: ' .  $item['sm_z_index'] . '; width: ' .  $item['sm_width'] . 'px; '.$margin_left.' height: ' .  $item['sm_height'] . 'px;"
                            >';
                        }
                    $content .= '</div>';
                }
            }
            
            return $content;

        }

        //Load Admin Scripts
        public function scrollme_admin_enqueue_scripts()
        {
            global $typenow;
            if ($typenow == 'scrollme') {
                wp_enqueue_style('scrollme_admin_styles', plugins_url('/css/admin/style.css', __FILE__));
                wp_enqueue_style('scrollme_jquery_ui', plugins_url('/css/admin/jquery-ui.min.css', __FILE__));

                wp_enqueue_script('jquery');
                wp_enqueue_script('jquery-ui-core');
                wp_enqueue_script('jquery-ui-draggable');
                wp_enqueue_script('jquery-ui-resizable');


                wp_enqueue_media();

                wp_enqueue_script('scrollme_admin_main', plugins_url('/js/admin/main.js', __FILE__));

                global $post;

                if (!is_null($post)) {

                    $items = Scrollme_db::getItemsArray($post->ID);

                    wp_localize_script('scrollme_admin_main', 'items', $items);

                    wp_localize_script('scrollme_admin_main', 'postID', array($post->ID));
                }
            }
        }

        //Load Scripts
        public function scrollme_enqueue_scripts()
        {
            wp_register_style('scrollme_styles', plugins_url('/css/style.css', __FILE__), array(), '1.0.0', 'screen, all');
            wp_enqueue_style('scrollme_styles');
//
            wp_register_script('scrollme_main', plugins_url('/js/jquery.scrollme.min.js', __FILE__), array('jquery'), '1.0.0', true);
            wp_enqueue_script('scrollme_main');
        }


    }

endif;

new Scrollme();