<?php
/*
Plugin Name: Cat Picks
Plugin URI: http://fearghal.com
Description: Test Plugin for WJCT
Author: Fearghal
Version: 1.0
Author URI: http://fearghal.com
*/


class CatPicks {
    public function __construct() {
        add_action( 'init', array( __CLASS__, 'create_cat_picks' ) );
		add_action('add_meta_boxes', array( __CLASS__,  'cp_add_meta_box'));
		add_action('save_post_catpicks', array( __CLASS__, 'cp_save_meta_box'));
    }


	// Register Custom Post Type itself 
	public static function create_cat_picks() {

	$labels = array(
		'name'                  => _x( 'Cat Picks', 'Post Type General Name', 'wjct' ),
		'singular_name'         => _x( 'Cat Pick', 'Post Type Singular Name', 'wjct' ),
		'menu_name'             => __( 'Cat Picks', 'wjct' ),
		'name_admin_bar'        => __( 'Cat Picks', 'wjct' ),
		'archives'              => __( 'Cat Pick Archives', 'wjct' ),
		'attributes'            => __( 'Cat Pick Attributes', 'wjct' ),
		'parent_Cat'     => __( 'Parent Cat Pick:', 'wjct' ),
		'all_Cat'             => __( 'All Cat Picks', 'wjct' ),
		'add_new_Cat'          => __( 'Add New Cat Pick', 'wjct' ),
		'add_new'               => __( 'Add New', 'wjct' ),
		'new_Cat'              => __( 'New Cat Pick', 'wjct' ),
		'edit_Cat'             => __( 'Edit Cat Pick', 'wjct' ),
		'update_Cat'           => __( 'Update Cat Pick', 'wjct' ),
		'view_Cat'             => __( 'View Cat Pick', 'wjct' ),
		'view_Cat'            => __( 'View Cat Picks', 'wjct' ),
		'search_Cat'          => __( 'Search Cat Pick', 'wjct' ),
		'not_found'             => __( 'Not found', 'wjct' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'wjct' ),
		'featured_image'        => __( 'Featured Image', 'wjct' ),
		'set_featured_image'    => __( 'Set featured image', 'wjct' ),
		'remove_featured_image' => __( 'Remove featured image', 'wjct' ),
		'use_featured_image'    => __( 'Use as featured image', 'wjct' ),
		'insert_into_Cat Pick'      => __( 'Insert into Cat Pick', 'wjct' ),
		'uploaded_to_this_Cat Pick' => __( 'Uploaded to this Cat Pick', 'wjct' ),
		'Cat Picks_list'            => __( 'Cat Picks list', 'wjct' ),
		'Cat Picks_list_navigation' => __( 'Cat Picks list navigation', 'wjct' ),
		'filter_Cat Picks_list'     => __( 'Filter Cat Picks list', 'wjct' ),
	);
	$args = array(
		'label'                 => __( 'Cat Pick', 'wjct' ),
		'description'           => __( 'Cat Picks', 'wjct' ),
		'labels'                => $labels,
		'supports'              => array( 'title', 'editor' ),
		'taxonomies'            => array( 'category', 'post_tag' ),
		'hierarchical'          => false,
		'public'                => true,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'menu_position'         => 5,
		'show_in_admin_bar'     => true,
		'show_in_nav_menus'     => true,
		'can_export'            => true,
		'has_archive'           => true,
		'exclude_from_search'   => false,
		'publicly_queryable'    => true,
		'capability_type'       => 'page',
	);
	register_post_type( 'catpicks', $args );

	
	}


	//lets add custom meta field -------------
	public static function  cp_add_meta_box() {
		add_meta_box(
			'cp_details',
			'Cat Pick Details',
			array( __CLASS__, 'cp_meta_box_callback'),
			'catpicks',
			'normal',
			'default'
		);
	}

	//add the meta form box with callback to the admin edit side with the current value 
	public static  function cp_meta_box_callback($post) {
		//get current values if any
		$value = get_post_meta($post->ID,'featured_by', true);
		wp_nonce_field('save_cp_meta', 'cp_meta_nonce');
		echo '<label>Featured By</label><br><input type="text" id="featured_by" name="featured_by" value="' . $value . '" style="width:100%;">';
	}

	//let check and save meta data
	public static function cp_save_meta_box($post_id) {
		
		// lets check user has permission 
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
	
		//lets check nonce 
		if (!isset($_POST['cp_meta_nonce'])) {
			return;
		}
		if (!wp_verify_nonce($_POST['cp_meta_nonce'], 'save_cp_meta')) {
			return;
		}

		//if its a revision, lets skip	
		if ( wp_is_post_revision( $post_id ) ) {
			return;
		}

		//if autosave, lets also skip
		if ( wp_is_post_autosave( $post_id ) ) {
			return;
		}

		//all good with nonce and other checks, lets go ahead and update meta value	
		if (isset($_POST['featured_by'])) {
			$featured_by = sanitize_text_field($_POST['featured_by']);
			update_post_meta($post_id,'featured_by',$featured_by);
		}
	}

	//finally lets render the output for the single view template
	public function render_cp_featured_by($post_id) {
		$featured = get_post_meta($post_id,'featured_by', true);
		if ($featured) { 
		?>	
		<p class="cp-featured-by" style="font-size:24px;">Featured by <?php echo _e($featured); ?></p>
		<?php 
		} 
	}

}

$cp = new CatPicks();

