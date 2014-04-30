<?php

/**
 * Load via GBS Add-On API
 */
class Group_Buying_Advanced_Coupons_Addon extends Group_Buying_Controller {

	public static function init() {
		// Hook this plugin into the GBS add-ons controller
		add_filter( 'gb_addons', array( get_class(), 'gb_addon' ), 10, 1 );
	}

	public static function gb_addon( $addons ) {
		$addons['mw_advanced_coupons'] = array(
			'label' => self::__( 'Advanced Coupons' ),
			'description' => self::__( 'Creates dropdown during deal creation to choose between deal or coupon and separates coupons into new template.' ),
			'files' => array(
				__FILE__,
				dirname( __FILE__ ) . '/mw-advanced-coupons.controller.class.php',
			),
			'callbacks' => array(
				array( 'Group_Buying_Advanced_Coupons', 'init' ),
			),
			'active' => TRUE,
		);
		return $addons;
	}

}
