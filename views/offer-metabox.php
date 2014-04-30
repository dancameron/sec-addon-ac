<?php 
	$mw_deal_type = get_post_meta( get_the_ID(), 'mw-deal-type', true ); ?>
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
