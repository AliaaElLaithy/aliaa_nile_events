<?php  
/**
 * Plugin Name: Events
 * Plugin URI: #
 * Description: Plugin for events
 * Version: 1.0
 * Author: Aliaa ElLaithy
 * Author URI: #
 */
/**
 * Registers a stylesheet.
 */
wp_register_style("a1_custom", site_url() . "/wp-content/plugins/aliaa_nile_events/assets/css/an_events.css");
wp_enqueue_style('a1_custom');

wp_register_style("a2_custom", site_url() . "/wp-content/plugins/aliaa_nile_events/assets/css/events_shortcode.css");
wp_enqueue_style('a2_custom');


/**
 * include settings widget.
 */
include 'settings.php';
include 'shortcodes/events_shortcode_en.php';
include 'shortcodes/events_shortcode_ar.php';
/**
 * Registers the event post type.
 */
function wpt_event_post_type() {

	$labels = array(
		'name'               => __( 'Events' ),
		'singular_name'      => __( 'Event' ),
		'add_new'            => __( 'Add New Event' ),
		'add_new_item'       => __( 'Add New Event' ),
		'edit_item'          => __( 'Edit Event' ),
		'new_item'           => __( 'Add New Event' ),
		'view_item'          => __( 'View Event' ),
		'search_items'       => __( 'Search Event' ),
		'not_found'          => __( 'No events found' ),
		'not_found_in_trash' => __( 'No events found in trash' )
	);

	$supports = array(
		'title',
		'editor',
		'thumbnail',
		//'comments',
		//'revisions',
	);

	$args = array(
		'labels'               => $labels,
		'supports'             => $supports,
		'public'               => true,
		'capability_type'      => 'post',
		'rewrite'              => array( 'slug' => 'a_events' ),
		'has_archive'          => true,
		'menu_position'        => 30,
		'menu_icon'            => 'dashicons-calendar-alt',
		'register_meta_box_cb' => 'wpt_add_event_metaboxes',
	);

	register_post_type( 'a_events', $args );

}
add_action( 'init', 'wpt_event_post_type' );

/**
 * Adds a metabox to the right side of the screen under the Publish box
 */
function wpt_add_event_metaboxes() {

	add_meta_box(
		'wpt_events_date',
		'Event Date',
		'wpt_events_date',
		'a_events',
		'normal',
		'high'
	);
	add_meta_box(
		'wpt_events_excerpt',
		'Event Excerpt',
		'wpt_events_excerpt',
		'a_events',
		'normal',
		'default'
	);


}

/**
 * Output the HTML for the metabox.
 */
function wpt_events_date() {
	global $post;

	// Nonce field to validate form request came from current site
	wp_nonce_field( basename( __FILE__ ), 'event_fields1' );
	
	// Get the Date  if it's already been entered
    $start_date = get_post_meta( $post->ID, 'start_date', true );
	$end_date = get_post_meta( $post->ID, 'end_date', true );

	// Output the field
    echo '
            <div class="a-events-date">
				<div class="a-events-date-item ">
						<label for="a_sdate">Start Date</label>
						<input type="date" name="start_date" id="a_sdate" value="' . $start_date  . '"  required>
						
                </div>
				<div class="a-events-date-item">
					<label for="a_edate">End Date</label>
                    <input type="date" name="end_date" id="a_edate" value="' . $end_date  . '"  required>
                </div>
            </div>
		';
	
//	update_post_meta($post->ID,'start_date',$start_date);
	
}

function wpt_events_excerpt() {
	global $post;

	// Nonce field to validate form request came from current site
	//wp_nonce_field( basename( __FILE__ ), 'event_fields2' );

	// Get the Excerpt  if it's already been entered
    $event_excerpt = get_post_meta( $post->ID, 'event_excerpt', true );

	// Output the field
    echo '
            <div class="a-events-excerpt">
				<div class="a-events-excerpt-item ">
                    <textarea name="event_excerpt" id="event-excerpt-id" required>' . esc_textarea( $event_excerpt )  . '</textarea>
                </div>
            </div>
        ';

}

/**
 * Save the metabox data
 */
function wpt_save_events_meta( $post_id, $post ) {

	// Return if the user doesn't have edit permissions.
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return $post_id;
	}

	// Verify this came from the our screen and with proper authorization,
	// because save_post can be triggered at other times.
	if ( ! isset( $_POST['start_date'] ) || ! isset( $_POST['end_date'] ) || ! isset( $_POST['event_excerpt'] ) || ! wp_verify_nonce( $_POST['event_fields1']  , basename(__FILE__) ) ) {
		return $post_id;
	}

	// Now that we're authenticated, time to save the data.
	// This sanitizes the data from the field and saves it into an array $events_meta.
	$events_meta['start_date'] = $_POST['start_date'] ;
	$events_meta['end_date'] = $_POST['end_date'] ;
	$events_meta['event_excerpt'] = $_POST['event_excerpt'] ;

	// Cycle through the $events_meta array.
	// Note, in this example we just have one item, but this is helpful if you have multiple.
	foreach ( $events_meta as $key => $value ) :

		// Don't store custom data twice
		if ( 'revision' === $post->post_type ) {
			return;
		}

		if ( get_post_meta( $post_id, $key, false ) ) {
			// If the custom field already has a value, update it.
			update_post_meta( $post_id, $key, $value );
		} else {
			// If the custom field doesn't have a value, add it.
			add_post_meta( $post_id, $key, $value);
		}

		if ( ! $value ) {
			// Delete the meta key if there's no value
			delete_post_meta( $post_id, $key );
		}

	endforeach;

}
add_action( 'save_post', 'wpt_save_events_meta', 1, 2 );



 
