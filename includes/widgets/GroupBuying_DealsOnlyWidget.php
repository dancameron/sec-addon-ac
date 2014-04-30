<?php
//Create sidebar widget that excludes coupons
class GroupBuying_DealsOnlyWidget extends WP_Widget {
	/**
	 * Constructor
	 *
	 * @return void
	 * @author Dan Cameron
	 */
	function GroupBuying_DealsOnlyWidget() {
		$widget_ops = array( 'description' => gb__( 'Lists current deals excluding coupons.' ) );
		parent::WP_Widget( false, $name = gb__( 'GBS :: Deals Only (no coupons)' ), $widget_ops );
	}

	function widget( $args, $instance ) {
		do_action( 'pre_recent_deals', $args, $instance );
		wp_reset_query();
		global $gb, $wp_query;
		$temp = null;
		extract( $args );

		$title = apply_filters( 'widget_title', $instance['title'] );
		$buynow = empty( $instance['buynow'] ) ? 'Buy Now' : $instance['buynow'];
		$deals = apply_filters( 'gb_recent_deals_widget_show', $instance['deals'] );
		if ( is_single() ) {
			$post_not_in = $wp_query->post->ID;
		}
		$count = 1;
		$coupon2_query= null;
		$args=array(
			'post_type' => gb_get_deal_post_type(),
			'post_status' => 'publish',
			'meta_query' => array(
				array(
					'key' => '_expiration_date',
					'value' => array( 0, current_time( 'timestamp' ) ),
					'compare' => 'NOT BETWEEN'
				),
				array(
					'key' => 'mw-deal-type',
					'value' => 'coupon',
					'compare' => '!=',
				) ),
			'posts_per_page' => $deals,
			'post__not_in' => array( $post_not_in )
		);

		$coupon2_query = new WP_Query( $args );
		if ( $coupon2_query->have_posts() ) {
			echo $before_widget;
			echo $before_title . $title . $after_title;
			while ( $coupon2_query->have_posts() ) : $coupon2_query->the_post();

			Group_Buying_Controller::load_view( 'widgets/recent-deals.php', array( 'buynow'=>$buynow ) );

			endwhile;
			echo $after_widget;
		}
		$coupon2_query = null; $coupon2_query = $temp;
		wp_reset_query();
		do_action( 'post_recent_deals', $args, $instance );
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['buynow'] = strip_tags( $new_instance['buynow'] );
		$instance['deals'] = strip_tags( $new_instance['deals'] );
		$instance['show_expired'] = strip_tags( $new_instance['show_expired'] );
		return $instance;
	}

	function form( $instance ) {
		$title = esc_attr( $instance['title'] );
		$buynow = esc_attr( $instance['buynow'] );
		$deals = esc_attr( $instance['deals'] );
		$show_expired = esc_attr( $instance['show_expired'] );
?>
            <p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php gb_e( 'Title:' ); ?> <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" /></label></p>
            <p><label for="<?php echo $this->get_field_id( 'buynow' ); ?>"><?php gb_e( 'Buy now link text:' ); ?> <input class="widefat" id="<?php echo $this->get_field_id( 'buynow' ); ?>" name="<?php echo $this->get_field_name( 'buynow' ); ?>" type="text" value="<?php echo $buynow; ?>" /></label></p>
            <p><label for="<?php echo $this->get_field_id( 'deals' ); ?>"><?php gb_e( 'Number of deals to display:' ); ?>
            	<select id="<?php echo $this->get_field_id( 'deals' ); ?>" name="<?php echo $this->get_field_name( 'deals' ); ?>">
					<option value="1">1</option>
					<option value="2"<?php if ( $deals=="2" ) {echo ' selected="selected"';} ?>>2</option>
					<option value="3"<?php if ( $deals=="3" ) {echo ' selected="selected"';} ?>>3</option>
					<option value="4"<?php if ( $deals=="4" ) {echo ' selected="selected"';} ?>>4</option>
					<option value="5"<?php if ( $deals=="5" ) {echo ' selected="selected"';} ?>>5</option>
					<option value="10"<?php if ( $deals=="10" ) {echo ' selected="selected"';} ?>>10</option>
					<option value="15"<?php if ( $deals=="15" ) {echo ' selected="selected"';} ?>>15</option>
					<option value="-1"<?php if ( $deals=="-1" ) {echo ' selected="selected"';} ?>>All</option>
				 </select>
            </label></p>
        <?php
	}
}
add_action( 'widgets_init', create_function( '', 'return register_widget("GroupBuying_DealsOnlyWidget");' ) );
