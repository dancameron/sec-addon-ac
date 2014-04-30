<?php

//Path to plugin
define( 'WD_GBS_PATH', WP_PLUGIN_DIR . '/' . basename( dirname( __FILE__ ) ) );

//Required files
require_once WD_GBS_PATH.'/includes/mw-coupon-options-page.php';

// Create Coupon/Deal Selection box on GBS Deal Entry Page
class Group_Buying_Advanced_Coupons extends Group_Buying_Controller {

	public static function init() {
		//add and save metabox hooks
		add_action( 'add_meta_boxes', array( get_class(), 'mw_add_deal_option_metabox' ) );
		add_action( 'save_post', array( get_class(), 'mw_save_deal_option_metabox' ), 10, 2 );

		// admin css
		add_action( 'admin_head', array( get_class(), 'admin_header_css' ) );

		// widgets
		add_action( 'widgets_init', array( get_class(), 'register_widgets' ) );

	}

	//add the metabox
	function mw_add_deal_option_metabox() {
		add_meta_box( 'deal-option', 'Choose Type', array( get_class(), 'mw_deal_option_metabox' ), Group_Buying_Deal::POST_TYPE, 'side', 'high' );
	}

	//the metabox
	function mw_deal_option_metabox() {
		global $post;
		$mw_deal_type = get_post_meta( $post->ID, 'mw-deal-type', true ); ?>
     <p>
     <label for="mw-deal-type">Select Offer Type:</label>
     <select style="width: 50%;" name="mw-deal-type" id="mw-deal-type">
          <option value="">--Select--</option>;
          <option value="deal" <?php if ( isset ( $mw_deal_type ) ) selected( $mw_deal_type, 'deal' ); ?>>Deal</option>;
          <option value="coupon" <?php if ( isset ( $mw_deal_type ) ) selected( $mw_deal_type, 'coupon' ); ?>>Coupon</option>;
     </select>
     </p>
     <p>
     <?php if ( $mw_deal_type ) : ?>
          Current selection: <span style="font-weight: bold; text-transform: uppercase;"><?php echo $mw_deal_type; ?></span>
     <?php else: ?>
          Current selection: <span style="font-weight: bold; text-transform: uppercase;">None</span>
     <?php endif; ?>
     </p>

<?php
	}

	//save the entry when post is saved or updated
	function mw_save_deal_option_metabox( $post_id ) {
		if ( isset( $_POST['mw-deal-type'] ) ) {
			update_post_meta( $post_id, 'mw-deal-type', $_POST['mw-deal-type'] );
		}
	}

	public static function admin_header_css() {
		echo '<style>#adminmenu #toplevel_page_gbs-addon-advanced-coupons-includes-mw-coupon-options-page div.wp-menu-image:before { content: "\f132"; }</style>';
	}

	/**
	 * Register widgets
	 * Addon class includes the class files.
	 * @return  
	 */
	public static function register_widgets(){
		register_widget("GroupBuying_DealsOnlyWidget");
		register_widget("GroupBuying_CouponWidget");
	}

}

//Set deal type on deal page query
add_action( 'pre_get_posts', 'only_show_deal_type' );
function only_show_deal_type( $query ) {
	if ( !is_admin() &&
		$query->is_post_type_archive( gb_get_deal_post_type() ) &&
		$query->is_main_query() ) {
		$meta_query = array(
			array(
				'key' => 'mw-deal-type',
				'value' => 'coupon',
				'compare' => '!='
			),
		);
		$query->set( 'meta_query', $meta_query );
	}
}

//Add Item Type column to deal page in admin
add_filter( 'manage_edit-'.Group_Buying_Deal::POST_TYPE.'_columns', 'new_column' );
add_filter( 'manage_'.Group_Buying_Deal::POST_TYPE.'_posts_custom_column', 'new_column_info', 10, 2 );

function new_column( $columns ) {
	$columns['itemtype'] = 'Item Type';
	return $columns;
}

function new_column_info( $columns, $id ) {
	global $post;
	$hb_deal_type = get_post_meta( $post->ID, 'mw-deal-type', true );

	switch ( $columns ) {

	case 'itemtype':
		echo $hb_deal_type;
		break;

	default:
		// code...
		break;
	}
}

//Create Coupon widget area
register_sidebar( array(
		'name'         => __( 'Coupons Sidebar' ),
		'id'           => 'coupons-sidebar',
		'description'  => __( 'Widgets in this area will be shown on the coupons page.' ),
		'before_title' => '<h2 class="widget-title gb_ff">',
		'after_title'  => '</h2>',
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
	) );
register_sidebar( array(
		'name'         => __( 'Coupon Sidebar' ),
		'id'           => 'coupon-sidebar',
		'description'  => __( 'Widgets in this area will be shown on the coupon page.' ),
		'before_title' => '<h2 class="widget-title gb_ff">',
		'after_title'  => '</h2>',
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
	) );