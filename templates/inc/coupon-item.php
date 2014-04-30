<?php
$merchant_name = get_option('merchant_name');
$coupon_savings = get_option('coupon_savings');
$coupon_countdown = get_option('coupon_countdown');
?>

<div id="post_content <?php the_ID() ?>" class="
<?php $cats = gb_get_deal_categories( $post_id );
     foreach ( $cats as $cat ) { $catslugs[] .= $cat->slug; }
     echo implode(" ", $catslugs);
?> post <?php if ($coupon_savings == yes || $merchant_name == yes ) : ?>with-savings<?php endif; ?> <?php if( $coupon_savings == yes ) : ?>with-all<?php endif; ?> coupon loop_deal background_alt clearfix deal_status-<?php echo gb_get_status(); ?>">

     <div class="coupon-loop-image">

     <a href="<?php echo the_permalink(); ?>">
          <?php if( has_post_thumbnail() ) : ?>
               <?php the_post_thumbnail(); ?>
          <?php else : ?>
               <img src="<?php echo gb_header_logo(); ?>">
          <?php endif; ?>
     </a>

     <?php if( $coupon_countdown == yes ) : ?>
          <?php if ( gb_deal_availability() && gb_has_expiration() ): ?>
               <div id="deal_countdown" class="clearfix">
                    <?php gb_deal_countdown(); ?>
                    <noscript>
                    <?php gb_get_deal_end_date(); ?>
                    </noscript>
               </div><!-- #label -->
          <?php endif ?>
     <?php endif; ?>

     </div><!-- /coupon-loop-image -->

     <?php if( $merchant_name == yes && gb_has_active_merchant() ) : ?>
          <div class="coupon-loop-merchant">
               <h4><?php gb_merchant_name(gb_get_merchant_id()); ?></h4>
          </div><!-- /coupon-loop-merchant -->
     <?php endif; ?>

     <div class="coupon-loop-title">
          <a class="gb_ff" href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
     </div><!-- /coupon-loop-title -->
     
     <?php if( $coupon_savings == yes ) : ?>
     <div class="coupon-loop-savings gb_ff">
          <span class="loop-coupon-savings-label"><?php gb_e('Savings'); ?>:</span>
          <span class="loop-coupon-savings"><?php echo str_replace('.00','',gb_get_formatted_money(gb_get_deal_worth() - gb_get_price())) ?></span>
     </div><!-- /coupon-loop-savings -->
     <?php endif; ?>
     
     <div class="coupon-loop-view">
          <a class="button" href="<?php the_permalink(); ?>"><?php gb_e('View Coupon'); ?></a>
     </div><!-- /coupon-loop-view -->

</div>
