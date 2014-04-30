<?php

//Path to plugin
define( 'WD_GBS_PATH', WP_PLUGIN_DIR . '/' . basename( dirname( __FILE__ ) ) );

//Required files
require_once WD_GBS_PATH.'/includes/mw-coupon-options-page.php';

// Create Coupon/Deal Selection box on GBS Deal Entry Page
class Group_Buying_Advanced_Coupons extends Group_Buying_Controller {

	public static function init() {
		//add and save metabox hooks
		add_action( 'add_meta_boxes', array( get_class(), 'mw_add_deal_option_metabox' ) );
		add_action( 'save_post', array( get_class(), 'mw_save_deal_option_metabox' ), 10, 2 );

		// admin css
		add_action( 'admin_head', array( get_class(), 'admin_header_css' ) );
	}

	//add the metabox
	function mw_add_deal_option_metabox() {
		add_meta_box( 'deal-option', 'Choose Type', array( get_class(), 'mw_deal_option_metabox' ), Group_Buying_Deal::POST_TYPE, 'side', 'high' );
	}

	//the metabox
	function mw_deal_option_metabox() {
		global $post;
		$mw_deal_type = get_post_meta( $post->ID, 'mw-deal-type', true ); ?>
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

<?php
	}

	//save the entry when post is saved or updated
	function mw_save_deal_option_metabox( $post_id ) {
		if ( isset( $_POST['mw-deal-type'] ) ) {
			update_post_meta( $post_id, 'mw-deal-type', $_POST['mw-deal-type'] );
		}
	}

	public static function admin_header_css() {
		echo '<style>#adminmenu #toplevel_page_gbs-addon-advanced-coupons-includes-mw-coupon-options-page div.wp-menu-image:before { content: "\f132"; }</style>';
	}

}

//Set deal type on deal page query
add_action( 'pre_get_posts', 'only_show_deal_type' );
function only_show_deal_type( $query ) {
	if ( !is_admin() &&
		$query->is_post_type_archive( gb_get_deal_post_type() ) &&
		$query->is_main_query() ) {
		$meta_query = array(
			array(
				'key' => 'mw-deal-type',
				'value' => 'coupon',
				'compare' => '!='
			),
		);
		$query->set( 'meta_query', $meta_query );
	}
}

//Add Item Type column to deal page in admin
add_filter( 'manage_edit-'.Group_Buying_Deal::POST_TYPE.'_columns', 'new_column' );
add_filter( 'manage_'.Group_Buying_Deal::POST_TYPE.'_posts_custom_column', 'new_column_info', 10, 2 );

function new_column( $columns ) {
	$columns['itemtype'] = 'Item Type';
	return $columns;
}

function new_column_info( $columns, $id ) {
	global $post;
	$hb_deal_type = get_post_meta( $post->ID, 'mw-deal-type', true );

	switch ( $columns ) {

	case 'itemtype':
		echo $hb_deal_type;
		break;

	default:
		// code...
		break;
	}
}

//Create Coupon widget area
register_sidebar( array(
		'name'         => __( 'Coupons Sidebar' ),
		'id'           => 'coupons-sidebar',
		'description'  => __( 'Widgets in this area will be shown on the coupons page.' ),
		'before_title' => '<h2 class="widget-title gb_ff">',
		'after_title'  => '</h2>',
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
	) );
register_sidebar( array(
		'name'         => __( 'Coupon Sidebar' ),
		'id'           => 'coupon-sidebar',
		'description'  => __( 'Widgets in this area will be shown on the coupon page.' ),
		'before_title' => '<h2 class="widget-title gb_ff">',
		'after_title'  => '</h2>',
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
	) );

//Create Coupon Widget
class GroupBuying_CouponWidget extends WP_Widget {
	/**
	 * Constructor
	 *
	 * @return void
	 * @author Dan Cameron
	 */
	function GroupBuying_CouponWidget() {
		$widget_ops = array( 'description' => gb__( 'Displays a list of current coupons available.' ) );
		parent::WP_Widget( false, $name = gb__( 'GBS :: Current Coupons' ), $widget_ops );
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
		$coupon_query= null;
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
				) ),
			'posts_per_page' => $deals,
			'post__not_in' => array( $post_not_in )
		);

		$coupon_query = new WP_Query( $args );
		if ( $coupon_query->have_posts() ) {
			echo $before_widget;
			echo $before_title . $title . $after_title;
			while ( $coupon_query->have_posts() ) : $coupon_query->the_post();

			Group_Buying_Controller::load_view( 'widgets/recent-deals.php', array( 'buynow'=>$buynow ) );

			endwhile;
			echo $after_widget;
		}
		$coupon_query = null; $coupon_query = $temp;
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
add_action( 'widgets_init', create_function( '', 'return register_widget("GroupBuying_CouponWidget");' ) );

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
