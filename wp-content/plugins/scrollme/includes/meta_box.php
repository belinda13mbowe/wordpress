<?php
// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) exit;


function scrollme_meta_boxes() {
	add_meta_box( 'scrollme_height_box', esc_html__( 'Container', 'scrollme' ), 'scrollme_height_box', 'scrollme', 'side', 'low' );

}

// The function that outputs the metabox html
function scrollme_height_box() {

    $sel = get_post_meta( get_the_ID(), 'scrollme_height', true ) != '' ? get_post_meta( get_the_ID(), 'scrollme_height', true ) : 300;
	?>
        <label for="scrollme_height"><?php esc_html_e('Min Height (px)', 'scrollme'); ?>
            <input name="scrollme_height" id="scrollme_height" type="number" min="0" max="2000" step="10" value="<?php echo esc_attr($sel); ?>"/>
        </label>
	<?php
}

/**
 * Save Custom MetaBox fields
 *
 * @since 0.1
 * @return boolean
 */
function scrollme_save_meta_boxes( $post_id ) {
	
	// Checks save status
    $is_autosave = wp_is_post_autosave( $post_id );
    $is_revision = wp_is_post_revision( $post_id );

	if (isset($_POST['scrollme_height'])) {
        update_post_meta($post_id, 'scrollme_height', $_POST['scrollme_height']);
    }
    else
        delete_post_meta($post_id, 'scrollme_height');

}
add_action( 'save_post', 'scrollme_save_meta_boxes' );



?>