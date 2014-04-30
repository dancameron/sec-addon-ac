<?php

/**
 * Load via GBS Add-On API
 */
class Group_Buying_Advanced_Coupons_Addon extends Group_Buying_Controller {

	public static function gb_addon( $addons ) {
		$addons['mw_advanced_coupons'] = array(
			'label' => self::__( 'Advanced Coupons' ),
			'description' => self::__( 'Creates dropdown during deal creation to choose between deal or coupon and separates coupons into new template.' ),
			'files' => array(
				MW_ADVANCED_COUPONS_PATH . '/Group_Buying_Advanced_Coupons.php',
			),
			'callbacks' => array(
				array( 'Group_Buying_Advanced_Coupons', 'init' ),
			),
			'active' => TRUE,
		);
		return $addons;
	}

}
