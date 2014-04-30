<?php
//Create coupon options page in admin
add_action( 'admin_menu', 'mw_options_menu' );
function mw_options_menu() {
     add_menu_page( 'mw-options-page', 'Coupon Settings', 'administrator', __FILE__, 'mw_settings_page' );
     add_action( 'admin_init', 'mw_register_settings' );
}

//Register the different settings on the option page
function mw_register_settings() {
     register_setting( 'mw_settings_group', 'sidebar_option' );
     register_setting( 'mw_settings_group', 'merchant_name' );
     register_setting( 'mw_settings_group', 'coupon_savings' );
     register_setting( 'mw_settings_group', 'coupon_countdown' );
}

//Create the options page
function mw_settings_page() {
?>
<div class="wrap">
<h2>GBS Coupons Settings</h2>

<form method="post" action="options.php">
     <?php settings_fields( 'mw_settings_group' ); ?>

     <?php
     $sidebar_option = get_option( 'sidebar_option' );
     $merchant_name = get_option( 'merchant_name' );
     $coupon_savings = get_option( 'coupon_savings' );
     $coupon_countdown = get_option( 'coupon_countdown' );
?>

     <div class="mw-options-row first">
          <label for="sidebar_option">
               <?php if ( $sidebar_option == "yes" ) { $sidebar_option_checked = 'checked="checked"'; } ?>
               <input type="checkbox" id="sidebar_option" name="sidebar_option" value="yes" <?php echo $sidebar_option_checked; ?> />
               <span>Check to show sidebar on Coupon Page</span>
          </label>
     </div><!-- /mw-options-row -->

     <div class="mw-options-row">
          <label for="merchant_name">
               <?php if ( $merchant_name == "yes" ) { $merchant_name_checked = 'checked="checked"'; } ?>
               <input type="checkbox" id="merchant_name" name="merchant_name" value="yes" <?php echo $merchant_name_checked; ?> />
               <span>Check to show Merchant Name on each coupon item on main coupon page</span>
          </label>
     </div><!-- /mw-options-row -->

     <div class="mw-options-row">
          <label for="coupon_savings">
               <?php if ( $coupon_savings == "yes" ) { $coupon_savings_checked = 'checked="checked"'; } ?>
               <input type="checkbox" id="coupon_savings" name="coupon_savings" value="yes" <?php echo $coupon_savings_checked; ?> />
               <span>Check to show Coupon Savings on each coupon item on main coupon page</span>
          </label>
     </div><!-- /mw-options-row -->

     <div class="mw-options-row">
          <label for="coupon_countdown">
               <?php if ( $coupon_countdown == "yes" ) { $coupon_countdown_checked = 'checked="checked"'; } ?>
               <input type="checkbox" id="coupon_countdown" name="coupon_countdown" value="yes" <?php echo $coupon_countdown_checked; ?> />
               <span>Check to show Countdown on each coupon item on main coupon page</span>
          </label>
     </div><!-- /mw-options-row -->

     <?php submit_button(); ?>

</form>

</div>
<?php } ?>
