<?php

// Create Coupon/Deal Selection box on GBS Deal Entry Page
class SeC_Advanced_Coupons extends Group_Buying_Controller {

	public static function init() {
		//add and save metabox hooks
		add_action( 'add_meta_boxes', array( get_class(), 'mw_add_deal_option_metabox' ) );
		add_action( 'save_post', array( get_class(), 'mw_save_deal_option_metabox' ), 10, 2 );

		// filter out coupons from main loops
		add_action( 'pre_get_posts', array( get_class(), 'only_show_deal_type' ) );

		// admin css
		add_action( 'admin_head', array( get_class(), 'admin_header_css' ) );

		// widgets
		add_action( 'widgets_init', array( get_class(), 'register_widgets' ) );

		// options page
		add_action( 'admin_menu', array( get_class(), 'mw_options_menu' ) );
		add_action( 'admin_init', array( get_class(), 'mw_register_settings' ) );


		//Add Item Type column to deal page in admin
		add_filter( 'manage_edit-'.Group_Buying_Deal::POST_TYPE.'_columns', array( get_class(), 'new_column' ) );
		add_filter( 'manage_'.Group_Buying_Deal::POST_TYPE.'_posts_custom_column', array( get_class(), 'new_column_info' ), 10, 2 );
	}

	//add the metabox
	public static function mw_add_deal_option_metabox() {
		add_meta_box( 'deal-option', 'Choose Type', array( get_class(), 'mw_deal_option_metabox' ), Group_Buying_Deal::POST_TYPE, 'side', 'high' );
	}

	//the metabox
	public static function mw_deal_option_metabox() {
		include MW_ADVANCED_COUPONS_PATH . '/views/offer-metabox.php';
	}

	//save the entry when post is saved or updated
	public static function mw_save_deal_option_metabox( $post_id ) {
		if ( isset( $_POST['mw-deal-type'] ) ) {
			update_post_meta( $post_id, 'mw-deal-type', $_POST['mw-deal-type'] );
		}
	}

	public static function only_show_deal_type( $query ) {
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

	public static function admin_header_css() {
		echo '<style>#toplevel_page_sec-addon-advanced-coupons-classes-SeC_Advanced_Coupons div.wp-menu-image:before { content: "\f132"; }</style>';
	}

	/**
	 * Register widgets
	 * Addon class includes the class files.
	 * @return  
	 */
	public static function register_widgets(){
		register_widget("SeC_DealsOnlyWidget");
		register_widget("SeC_CouponWidget");
	}

	// options page
	public static function mw_options_menu() {
		add_menu_page( 'mw-options-page', 'Coupon Settings', 'administrator', __FILE__, array( get_class(), 'mw_settings_page' ) );
	}

	//Register the different settings on the option page
	public static function mw_register_settings() {
		register_setting( 'mw_settings_group', 'sidebar_option' );
		register_setting( 'mw_settings_group', 'merchant_name' );
		register_setting( 'mw_settings_group', 'coupon_savings' );
		register_setting( 'mw_settings_group', 'coupon_countdown' );
	}

	public static function mw_settings_page() {
		include MW_ADVANCED_COUPONS_PATH . '/views/options-page.php';
	}

	// offer columns
	function new_column( $columns ) {
		$columns['itemtype'] = 'Item Type';
		return $columns;
	}

	function new_column_info( $columns, $id ) {
		switch ( $columns ) {
			case 'itemtype':
				$hb_deal_type = get_post_meta( $id, 'mw-deal-type', true );
				echo $hb_deal_type;
				break;

			default:
				// code...
				break;
		}
	}

}