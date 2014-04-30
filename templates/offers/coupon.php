<?php
	do_action('gb_deal_view');
	get_header(); ?>

	<div id="deal_single" class="container prime main clearfix">

		<div id="content" class="full clearfix">

			<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>

					<div class="single-coupon-wrapper">

					 <h1 class="page_title gb_ff clearfix">

						  <span><?php gb_e('Coupon:') ?></span>
						  <?php the_title(); ?>

					 </h1>

					 <div class="deal_thumbnail">
						  <?php if ( gb_has_featured_content() ) :?>
						   <div class="featured_content">
							  <?php gb_featured_content(); ?>
						   </div>
						 <?php elseif ( has_post_thumbnail() ) : ?>
							 <?php the_post_thumbnail('gbs_700x400'); ?>
						 <?php else: ?>
							 <div class="deal_thumb no_featured_image" style="background: url(<?php gb_header_logo(); ?>) no-repeat 50px center;"></div>
						 <?php endif; ?>
					 </div>
					 
					 <div class="coupon-deal-information">

						  <?php if ( gb_has_active_merchant() ): ?>
						  <div class="single-coupon-merchant">
							   <?php gb_merchant_name(gb_get_merchant_id()); ?>
						  </div><!-- /single-coupon-merchant -->
						  <?php endif; ?>
						  
						  <div class="single-coupon-savings">
							   <span class="single-coupon-savings-header"><?php gb_e('Savings'); ?>:</span>
							   <span class="single-coupon-saved"><?php echo str_replace('.00','',gb_get_formatted_money(gb_get_deal_worth() - gb_get_price())) ?></span>
						  </div><!-- /single-coupon-savings -->
						  
						  <div class="single-coupon-downloads">
							   <span class="single-coupon-downloads-header"><?php gb_e('Downloads'); ?>:</span>
							   <span class="single-coupon-saved"><?php $purchases = gb_get_number_of_purchases(); echo $purchases; ?></span>
						  </div><!-- /single-coupon-savings -->
						  
						  <div class="gb_ff social_share bold font_x_small">
							   <?php get_template_part('inc/social-share') ?>
						  </div>

					 </div><!-- /coupon-deal-information -->
					 
					 <div class="coupon-description">
						  <h4 class="font_large gb_ff"><?php gb_e('Coupon Description'); ?>:</h4>
						  <?php the_content(); ?>
					 </div><!-- /coupon description -->
					 
					 <?php if ( gb_deal_availability() && gb_has_expiration() ): ?>
						<div id="deal_countdown" class="clearfix">
							<?php gb_deal_countdown(); ?>
							<noscript>
								<?php gb_get_deal_end_date(); ?>
							</noscript>
						</div><!-- #label -->
					<?php endif ?>
					
					<div class="purchase_options">
					<?php if (function_exists('gb_deal_has_attributes') && gb_deal_has_attributes()): ?>
						<div id="deal_attributes_wrap" class="section ">
							<?php gb_add_to_cart_form() ?>
						</div>
					<?php else: ?>
						<div class="buy_button gb_ff font_x_large">
							<?php if ( gb_deal_availability() || !gb_is_deal_complete() ): ?>
								<a href="<?php gb_add_to_cart_url(); ?>" class="button"><?php gb_e('Download Now'); ?></a>
							<?php elseif ( gb_is_sold_out() ) : ?>
								<a class="button"><?php gb_e('Sold Out!') ?></a>
							<?php else : ?>
								<a class="button"><?php gb_e('It&rsquo;s over!') ?></a>
							<?php endif ?>
						</div>
					<?php endif ?>
					</div>

				</div><!-- /single-coupon-wrapper -->

			<?php endwhile; // end of the loop. ?>

			<div id="sidebar" class="sidebar clearfix">
				<?php do_action('gb_above_default_sidebar') ?>
				<?php dynamic_sidebar( 'coupon-sidebar' ); ?>
				<?php do_action('gb_below_default_sidebar') ?>
			</div><!-- // #sidebar -->

		</div><!-- // #content -->

	</div><!-- // #container-->

<?php get_footer(); ?>