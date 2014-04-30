<?php

/**
 * Load via GBS Add-On API
 */
class SeC_Advanced_Coupons_Addon extends Group_Buying_Controller {

	public static function gb_addon( $addons ) {
		$addons['mw_advanced_coupons'] = array(
			'label' => self::__( 'Advanced Coupons' ),
			'description' => self::__( 'Creates dropdown during deal creation to choose between offer or coupon and separates coupons into new template.' ),
			'files' => array(
				MW_ADVANCED_COUPONS_PATH . '/classes/SeC_Advanced_Coupons.php',
				MW_ADVANCED_COUPONS_PATH . '/includes/widgets/SeC_DealsOnlyWidget.php',
				MW_ADVANCED_COUPONS_PATH . '/includes/widgets/SeC_CouponWidget.php',
			),
			'callbacks' => array(
				array( 'SeC_Advanced_Coupons', 'init' ),
			),
			'active' => TRUE,
		);
		return $addons;
	}

}
