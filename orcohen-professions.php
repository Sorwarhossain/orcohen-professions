<?php
/**
 * Plugin Name:       Orcohen Professions
 * Plugin URI:        https://devsgram.com
 * Description:       A basic plugin to show the professions list and the professions search form.
 * Version:           1.0.0
 * Author:            Saroar Hossain
 * Author URI:        https://devsgram.com
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       op
 * Domain Path:       /languages
 */


// enqueue all asset files
function op_enqueue_asset_files() {

	wp_enqueue_style( 'op-fontawesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.14.0/css/all.min.css');

	wp_enqueue_style( 'op-styles', plugins_url('/assets/css/op-styles.css', __FILE__), false, '1.0.0', 'all');


	$args = array(
		'post_type' => 'profession',
		'post_status' => 'publish',
		'posts_per_page' => -1,
		'orderby' => 'title',
		'order' => 'ASC'
	);
	$op_posts = get_posts( $args );
	$op_prof_list = array();
	if(!empty($op_posts)){
		foreach($op_posts as $key => $post){
			$op_prof_list[$key]['id'] = $post->ID;
			$op_prof_list[$key]['title'] = $post->post_title;
		}
	}

    wp_enqueue_script( 'op-scripts', plugins_url('/assets/js/op-scripts.js', __FILE__), array('jquery'), '1.0.0', true );
	wp_localize_script( 'op-scripts', 'op_data',
        array( 
            'prof_list' => json_encode($op_prof_list),
            'ajax_url' => admin_url( 'admin-ajax.php' ),
        )
    );
    //wp_enqueue_script( 'op-fontawesome', 'https://use.fontawesome.com/f86215a54f.js', array('jquery'), '5.0', false,  );

    

}

// use the registered jquery and style above
add_action('wp_enqueue_scripts', 'op_enqueue_asset_files');




/**
 * Register a custom post type 
 *
 * @see get_post_type_labels() for label keys.
 */
function op_post_types_init() {
    $labels = array(
        'name'                  => _x( 'Professions', 'Post type general name', 'op' ),
        'singular_name'         => _x( 'Profession', 'Post type singular name', 'op' ),
        'menu_name'             => _x( 'Professions', 'Admin Menu text', 'op' ),
        'name_admin_bar'        => _x( 'Profession', 'Add New on Toolbar', 'op' ),
        'add_new'               => __( 'Add New', 'op' ),
        'add_new_item'          => __( 'Add New Profession', 'op' ),
        'new_item'              => __( 'New Profession', 'op' ),
        'edit_item'             => __( 'Edit Profession', 'op' ),
        'view_item'             => __( 'View Profession', 'op' ),
        'all_items'             => __( 'All Professions', 'op' ),
        'search_items'          => __( 'Search Professions', 'op' ),
        'parent_item_colon'     => __( 'Parent Professions:', 'op' ),
        'not_found'             => __( 'No professions found.', 'op' ),
        'not_found_in_trash'    => __( 'No professions found in Trash.', 'op' ),
    
    );
 
    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array( 'slug' => 'profession' ),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => null,
        'menu_icon'      => 'dashicons-smiley',
        'supports'           => array( 'title', 'editor', 'thumbnail' ),
    );
 
    register_post_type( 'profession', $args );
}
 
add_action( 'init', 'op_post_types_init' );




// op professions list output shortcode
function op_professions_shortcode_func( $atts ) {
	$attr = shortcode_atts( array(
		'title' => 'תחומים מובילים',
	), $atts );

	$output = '';

	$output .= '<div class="op-professions-container">';

		$output .= '<div class="op-professions-list-header">';

			$output .= '<h2>'. $attr['title'] .'</h2>';

		$output .= '</div>'; // op-professions-list-header

		$output .= '<div class="op-professions-list-content">';
		$loop = new WP_Query(
			array(
				'post_type' => 'profession',
				'post_status' => 'publish',
				'posts_per_page' => -1,
				'orderby' => 'title',
				'order' => 'ASC'
			)
		);

		
		if($loop->have_posts()) {
			while($loop->have_posts()){
				$loop->the_post();

				$output .= '<div class="op-professions-list-item">';
					$output .= '<a href="'. get_the_permalink() .'"><i class="'. profession_additional_information_get_meta( 'profession_additional_information_profession_icon' ) .'"></i> '. get_the_title() .'</a>';
				$output .= '</div>';

			}
		}
		wp_reset_query();
			

		$output .= '</div>'; // op-professions-list-content

	$output .= '</div>'; // op-professions-container

	return $output;
}
add_shortcode( 'op_professions', 'op_professions_shortcode_func' );



// op professions list output shortcode
function op_profession_search_shortcode_func( $atts ) {
	$attr = shortcode_atts( array(
		'button' => 'Search',
		'placeholder' => 'What you looking for?'
	), $atts );


	



	$output = '';

	$output .= '<div class="op-profession-search-wrap">';

		$output .= '<form autocomplete="off" action="" class="op-profession-search-form">';
			$output .= '<div class="autocomplete" >';
				$output .= '<input id="op_input_value" type="text" placeholder="'. $attr['placeholder'] .'">';
				$output .= '<input id="op_input_id" type="hidden" >';
			$output .= '</div>';
			$output .= '<input id="op_search_submit" type="submit" value="'. $attr['button'] .'">';
		$output .= '</form>';

	$output .= '</div>'; // op-profession-search-wrap

	return $output;
}
add_shortcode( 'op_profession_search', 'op_profession_search_shortcode_func' );




// echo var_dump(get_option('test'));


// Additional Profession Information Meta Box

function profession_additional_information_get_meta( $value ) {
	global $post;

	$field = get_post_meta( $post->ID, $value, true );
	if ( ! empty( $field ) ) {
		return is_array( $field ) ? stripslashes_deep( $field ) : stripslashes( wp_kses_decode_entities( $field ) );
	} else {
		return false;
	}
}

function profession_additional_information_add_meta_box() {
	add_meta_box(
		'profession_additional_information-profession-additional-information',
		__( 'Profession Additional Information', 'profession_additional_information' ),
		'profession_additional_information_html',
		'profession',
		'side',
		'high'
	);
}
add_action( 'add_meta_boxes', 'profession_additional_information_add_meta_box' );

function profession_additional_information_html( $post) {
	wp_nonce_field( '_profession_additional_information_nonce', 'profession_additional_information_nonce' ); ?>

	<p>
		<h4><label for="profession_additional_information_profession_icon"><?php _e( 'Profession Icon', 'profession_additional_information' ); ?></label></h4>
		<input type="text" name="profession_additional_information_profession_icon" id="profession_additional_information_profession_icon" value="<?php echo profession_additional_information_get_meta( 'profession_additional_information_profession_icon' ); ?>">
		<p>Use fontawesome free icon class: https://fontawesome.com/icons?d=gallery&m=free. Example: fas fa-book</i></p>
		p
	</p><?php
}

function profession_additional_information_save( $post_id ) {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
	if ( ! isset( $_POST['profession_additional_information_nonce'] ) || ! wp_verify_nonce( $_POST['profession_additional_information_nonce'], '_profession_additional_information_nonce' ) ) return;
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	if ( isset( $_POST['profession_additional_information_profession_icon'] ) ){
		update_post_meta( $post_id, 'profession_additional_information_profession_icon', esc_attr( $_POST['profession_additional_information_profession_icon'] ) );
	}
}
add_action( 'save_post', 'profession_additional_information_save' );






function op_redirect_to_proffession_page_ajax_handler(){

    $op_input_value = $_POST['op_input_value'];
    $op_input_id = $_POST['op_input_id'];

    if(get_permalink($op_input_id)){
    	echo get_permalink($op_input_id);
    }
    
	die; // here we exit the script and even no wp_reset_query() required!
}
 
 
 
add_action('wp_ajax_op_redirect_to_proffession_page', 'op_redirect_to_proffession_page_ajax_handler'); // wp_ajax_{action}
add_action('wp_ajax_nopriv_op_redirect_to_proffession_page', 'op_redirect_to_proffession_page_ajax_handler'); // wp_ajax_nopriv_{action}

