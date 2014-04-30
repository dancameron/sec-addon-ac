<p>
<?php 
	$checked = SeC_Advanced_Coupons::is_coupon( get_the_ID() ); 
	printf( '<p><label><input type="checkbox" name="%s" %s /> %s</label></p>', SeC_Advanced_Coupons::OPTION_NAME, checked( $checked, TRUE, FALSE ), gb__( 'This is a coupon.' ) ); ?>
</p>