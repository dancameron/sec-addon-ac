<div class="wrap">
<h2>SeC Coupons Settings</h2>

<form method="post" action="options.php">
	<?php settings_fields( 'mw_settings_group' ); ?>

	<?php
		$sidebar_option = get_option( 'sidebar_option' );
		$merchant_name = get_option( 'merchant_name' );
		$coupon_savings = get_option( 'coupon_savings' );
		$coupon_countdown = get_option( 'coupon_countdown' ); ?>

	<div class="mw-options-row first">
		<label for="sidebar_option">
			<input type="checkbox" id="sidebar_option" name="sidebar_option" value="yes" <?php checked( 'yes', $sidebar_option ) ?>  />
			<span>Check to show sidebar on Coupon Page</span>
		</label>
	</div><!-- /mw-options-row -->

	<div class="mw-options-row">
		<label for="merchant_name">
			<input type="checkbox" id="merchant_name" name="merchant_name" value="yes" <?php checked( 'yes', $merchant_name ) ?> />
			<span>Check to show Merchant Name on each coupon item on main coupon page</span>
		</label>
	</div><!-- /mw-options-row -->

	<div class="mw-options-row">
		<label for="coupon_savings">
			<input type="checkbox" id="coupon_savings" name="coupon_savings" value="yes" <?php checked( 'yes', $coupon_savings ) ?> />
			<span>Check to show Coupon Savings on each coupon item on main coupon page</span>
		</label>
	</div><!-- /mw-options-row -->

	<div class="mw-options-row">
		<label for="coupon_countdown">
			<input type="checkbox" id="coupon_countdown" name="coupon_countdown" value="yes" <?php checked( 'yes', $coupon_countdown ) ?> />
			<span>Check to show Countdown on each coupon item on main coupon page</span>
		</label>
	</div><!-- /mw-options-row -->

	<?php submit_button(); ?>

</form>

</div>