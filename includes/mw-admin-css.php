<?php
add_action( 'admin_head', 'mw_admin_css' );

function mw_admin_css() {
	echo '<style>

#adminmenu #toplevel_page_gbs-addon-advanced-coupons-includes-mw-coupon-options-page div.wp-menu-image:before {
     content: "\f132";
}

  </style>';
}
?>
