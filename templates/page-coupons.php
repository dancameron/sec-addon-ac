<?php
/*
Template Name: Coupons Page
*/

get_header(); ?>

		<div id="deals_loop" class="container prime main clearfix coupons-loop">
			
			<?php
			$sidebar_option = get_option('sidebar_option');
			?>

               <?php if( $sidebar_option == yes ) : ?>
                    <div id="content" class="clearfix coupons-page">
               <?php else : ?>
                    <div id="content" class="clearfix full coupons-page">
               <?php endif; ?>

				<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>

					<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
						<div class="page_title"><!-- Begin #page_title -->
							<h1 class="entry_title gb_ff"><?php the_title(); ?></h1>
						</div><!-- End #page_title -->

						<div class="entry_content">
							<?php the_content(); ?>
							<?php wp_link_pages( array( 'before' => '<div class="page-link">' . gb__( 'Pages:' ), 'after' => '</div>' ) ); ?>
						</div><!-- .entry_content -->
					</div><!-- #post-## -->
					
					<div class="coupon-sort-wrapper">

                              <div class="category-sorter button">Sort by Category</div>
                              <?php gb_list_categories(); ?>
                         
                         </div><!-- /coupon-sort-wrapper -->
					
					<script>
					jQuery(document).ready(function() {
                              // Show/Hide the list of categories when button is clicked
                              jQuery('.category-sorter').click(function() {
                                   jQuery('.categories-ul').slideToggle();
                              });
                              // Show/Hide coupon items based on their cateqory
                              jQuery('.categories-ul li a').click(function(event) {
                                   // Stop the links from linking
                                   event.preventDefault();
                                   // Alter the catgeory slug to remove text before and after category name
                                   var categoryslug = jQuery(this).attr('id');
                                   var catnoslug = categoryslug.replace('category_slug_','');
                                   var category = catnoslug.slice(0,-1);
                                   // Add hide-coupon or show-coupon class based on selection
                                   jQuery('.post.coupon').addClass('hide-coupon').removeClass('show-coupon');
                                   jQuery('.post.coupon.' + category).removeClass('hide-coupon').addClass('show-coupon');
                                   // Animate the hiding and showing of coupons based on selection
                                   jQuery('.post.coupon.hide-coupon').animate({
                                        width: '0',
                                        height: '0'
                                   }, 500);
                                   if (jQuery(window).width() > 699) {
                                   jQuery('.post.coupon.show-coupon').animate({
                                        width: '31.33%',
                                        height: '100%'
                                   }, 500);
                                   }
                                   else {
                                   jQuery('.post.coupon.show-coupon').animate({
                                        width: '98%',
                                        height: '100%'
                                   }, 500);
                                   }
                                   // Slide the category options back up after making a selection
                                   jQuery('.categories-ul').slideUp();
                              });
                              // Add all option to category dropdown to allow option to show all coupons
                              var allitems = '<li class="show-all"><a>All</a></li>';
                              jQuery('.categories-ul').prepend(allitems);
                              // Show all the coupons when the all option is clicked
                              jQuery('.categories-ul li.show-all a').click(function() {
                                   jQuery('.post.coupon').removeClass('hide-coupon').addClass('show-coupon');
                                   if (jQuery(window).width() > 699) {
                                   jQuery('.post.coupon.show-coupon').animate({
                                        width: '31.33%',
                                        height: '100%'
                                   }, 500);
                                   }
                                   else {
                                   jQuery('.post.coupon.show-coupon').animate({
                                        width: '98%',
                                        height: '100%'
                                   }, 500);
                                   }
                                   // Slide the categories back up after making a selection
                                   jQuery('.categories-ul').slideUp();
                              });

                         });
					</script>

				<?php endwhile;
                	
				$deal_query= null;
				$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
				$args=array(
					'post_type' => gb_get_deal_post_type(),
					'post_status' => 'publish',
					'paged' => $paged,
					'posts_per_page' => 100,
					'meta_query' => array(
						array(
							'key' => '_expiration_date',
							'value' => array(0, current_time('timestamp')),
							'compare' => 'NOT BETWEEN'
						),
                              array(
                                   'key' => 'mw-deal-type',
                                   'value' => 'coupon'
                              )),

				);
				$deal_query = new WP_Query($args);
				?>
                
				<?php if ( ! $deal_query->have_posts() ) : ?>
                
					<?php get_template_part( 'deals/no-deals', 'deals/index' ); ?>
                
				<?php endif; ?>
                
				<?php $count; while ( $deal_query->have_posts() ) : $deal_query->the_post(); $count++; $zebra = ($count % 2) ? ' odd' : ' even'; ?>
                
					<?php get_template_part( 'inc/coupon-item', 'inc/coupon-item' ); ?>
                
				<?php endwhile; ?>
                
				<?php if (  $deal_query->max_num_pages > 1 ) : ?>
					<div id="nav-below" class="navigation clearfix">
						<div class="nav-previous"><?php next_posts_link( gb__( '<span class="meta-nav">&larr;</span> Older deals' ), $deal_query->max_num_pages ); ?></div>
						<div class="nav-next"><?php previous_posts_link( gb__( 'Newer deals <span class="meta-nav">&rarr;</span>' ), $deal_query->max_num_pages ); ?></div>
					</div><!-- #nav-below -->
				<?php endif; ?>
                
				<?php wp_reset_query(); ?>

			</div><!-- #content_wrap -->
			
			<?php if( $sidebar_option == yes ) : ?>
                    <div id="page_sidebar" class="sidebar clearfix">
			          <?php do_action('gb_above_default_sidebar') ?>
				     <?php dynamic_sidebar( 'coupons-sidebar' );?>
				     <?php do_action('gb_below_default_sidebar') ?>
			     </div>
               <?php endif; ?>

		</div><!-- #single_page -->

<?php
get_footer();
