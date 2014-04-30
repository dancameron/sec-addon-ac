<?php
/*
Plugin Name: Group Buying Addon - Advanced Coupons
Version: 1.0
Description: Creates dropdown during deal creation to choose between deal or coupon and separates coupons into new template.
Plugin URI: http://groupbuyingsite.com/marketplace
Plugin Author: Matt Whiteley
Plugin Author URI: http://www.whiteleydesigns.com
Text Domain: group-buying
*/

define ('MW_ADVANCED_COUPONS_PATH', WP_PLUGIN_DIR . '/' . basename( dirname( __FILE__ ) ) );

// Load after all other plugins since we need to be compatible with groupbuyingsite
add_action( 'plugins_loaded', 'mw_advanced_coupons' );
function mw_advanced_coupons() {
	if ( class_exists( 'Group_Buying_Controller' ) ) {
		require_once 'classes/Group_Buying_Advanced_Coupons_Addon.php';
		add_filter( 'gb_addons', array( 'Group_Buying_Advanced_Coupons_Addon', 'gb_addon' ), 10, 1 );
	}
}
