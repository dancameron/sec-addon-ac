<?php

// Create Coupon/Deal Selection box on GBS Deal Entry Page
class SeC_Advanced_Coupons extends Group_Buying_Controller {
	const TAX = 'offer_coupons';
	const TERM = 'Coupon';
	const TERM_SLUG = 'coupon';
	const REWRITE_SLUG = 'offered'; // /offered/coupons/
	const QUERY_VAR = 'coupon';
	const OPTION_NAME = 'mw_is_coupon';

	public static function init() {
		add_action( 'init', array( get_class(), 'register_coupon_taxonomy' ) );

		// filter out coupons from main loops
		add_action( 'pre_get_posts', array( get_class(), 'filter_query' ) );

		// templates
		add_filter( 'template_include', array( get_class(), 'override_template' ) );

		//add and save metabox hooks
		add_action( 'add_meta_boxes', array( get_class(), 'mw_add_deal_option_metabox' ) );
		add_action( 'save_post', array( get_class(), 'mw_save_deal_option_metabox' ), 10, 2 );

		// admin css
		add_action( 'admin_head', array( get_class(), 'admin_header_css' ) );

		// widgets
		add_action( 'widgets_init', array( get_class(), 'register_widgets' ) );

		// options page
		add_action( 'admin_menu', array( get_class(), 'mw_options_menu' ) );
		add_action( 'admin_init', array( get_class(), 'mw_register_settings' ) );

		//Add Item Type column to deal page in admin
		add_filter( 'manage_edit-'.Group_Buying_Deal::POST_TYPE.'_columns', array( get_class(), 'new_column' ) );
		add_filter( 'manage_'.Group_Buying_Deal::POST_TYPE.'_posts_custom_column', array( get_class(), 'new_column_info' ), 10, 2 );

		self::register_template_sidebars();
	}

	public static function register_coupon_taxonomy() {
		// register Locations taxonomy
		$taxonomy_args = array(
			'public' => TRUE,
			'show_ui' => TRUE,
			'hierarchical' => TRUE,
			'rewrite' => array(
				'slug' => self::REWRITE_SLUG,
				'with_front' => TRUE,
				'hierarchical' => FALSE,
			),
		);
		register_taxonomy( self::TAX, array( Group_Buying_Deal::POST_TYPE ), $taxonomy_args, 100 );
	}

	public static function filter_query( $query ) {
		if ( !is_admin() &&	$query->is_main_query() && !self::is_coupon_query_page() ) {
			$query->set( 'tax_query', array( array( 'taxonomy' => self::TAX, 'field' => 'slug', 'terms' => array( self::TERM_SLUG ), 'operator' => 'NOT IN' ) ) );
		}
		return $query;
	}

	public static function override_template( $template ) {
		if ( is_single() && Group_Buying_Deal::is_deal_query() ) {
			if ( self::is_coupon() ) { // override the single template for coupons
				$template = self::locate_template( array(
						'offers/single-coupon.php',
						'offers/coupon.php',
						'coupon.php',
						'deals/single-coupon.php',
						'deals/coupon.php',
					), FALSE );
				if ( $template == FALSE ) {
					// method of using default templates from the plugin
					$template = MW_ADVANCED_COUPONS_PATH . '/templates/offers/coupon.php';
				}
			}
		}
		if ( self::is_coupon_query_page() ) {
			$template = self::locate_template( array(
					'coupons/index.php',
					'offers/index-coupons.php',
					'offers/coupons.php',
					'deals/index-coupons.php',
					'deals/coupons.php',
					'page-coupons.php',
				), FALSE );
			if ( $template == FALSE ) {
				// method of using default templates from the plugin
				$template = MW_ADVANCED_COUPONS_PATH . '/templates/page-coupons.php';
			}
		}
		return $template;
	}

	//add the metabox
	public static function mw_add_deal_option_metabox() {
		add_meta_box( 'deal-option', 'Choose Type', array( get_class(), 'mw_deal_option_metabox' ), Group_Buying_Deal::POST_TYPE, 'side', 'high' );
	}

	//the metabox
	public static function mw_deal_option_metabox() {
		include MW_ADVANCED_COUPONS_PATH . '/views/offer-metabox.php';
	}

	//save the entry when post is saved or updated
	public static function mw_save_deal_option_metabox( $post_id ) {
		self::unmake_offer_coupon( $post_id );
		if ( isset( $_POST[self::OPTION_NAME] ) ) {
			self::make_offer_coupon( $post_id );
		}
	}

	public static function admin_header_css() {
		echo '<style>#toplevel_page_sec-addon-advanced-coupons-classes-SeC_Advanced_Coupons div.wp-menu-image:before { content: "\f132"; }</style>';
	}

	/**
	 * Register widgets
	 * Addon class includes the class files.
	 * @return  
	 */
	public static function register_widgets(){
		register_widget("SeC_DealsOnlyWidget");
		register_widget("SeC_CouponWidget");
	}

	// options page
	public static function mw_options_menu() {
		add_menu_page( 'mw-options-page', 'Coupon Settings', 'administrator', __FILE__, array( get_class(), 'mw_settings_page' ) );
	}

	//Register the different settings on the option page
	public static function mw_register_settings() {
		register_setting( 'mw_settings_group', 'sidebar_option' );
		register_setting( 'mw_settings_group', 'merchant_name' );
		register_setting( 'mw_settings_group', 'coupon_savings' );
		register_setting( 'mw_settings_group', 'coupon_countdown' );
	}

	public static function mw_settings_page() {
		include MW_ADVANCED_COUPONS_PATH . '/views/options-page.php';
	}

	// offer columns
	function new_column( $columns ) {
		$columns['itemtype'] = 'Item Type';
		return $columns;
	}

	function new_column_info( $columns, $id ) {
		switch ( $columns ) {
			case 'itemtype':
				$type = ( self::is_coupon( $id ) ) ? 'Coupon' : 'Offer' ;
				echo $type;
				break;

			default:
				// code...
				break;
		}
	}

	// utility functions


	public static function is_coupon_query_page( WP_Query $wp_query = NULL ) {
		if ( is_a( $wp_query, 'WP_Query' ) ) {
			return $wp_query->is_tax( self::TAX, self::TERM_SLUG );	
		}
		$taxonomy = get_query_var( 'taxonomy' );
		if ( $taxonomy == self::TAX ) {
			return TRUE;
		}
	}

	public static function get_url() {
		return get_term_link( self::TERM_SLUG, self::TAX );
	}

	public static function is_coupon( $post_id = 0 ) {
		if ( !$post_id ) {
			global $post;
			$post_id = $post->ID;
		}
		$terms = wp_get_object_terms( $post_id, self::TAX );
		$term = array_pop( $terms );
		if ( !is_object( $term ) ) {
			return FALSE;
		}
		return $term->slug == self::TERM_SLUG;
	}

	public static function make_offer_coupon( $post_id = 0 ) {
		if ( !$post_id ) {
			global $post;
			$post_id = $post->ID;
		}
		wp_set_object_terms( $post_id, self::get_term_slug(), self::TAX );
	}

	public static function unmake_offer_coupon( $post_id = 0 ) {
		if ( !$post_id ) {
			global $post;
			$post_id = $post->ID;
		}
		wp_set_object_terms( $post_id, array(), self::TAX );
	}

	public static function get_term_slug() {
		$term = get_term_by( 'slug', self::TERM_SLUG, self::TAX );
		if ( !empty( $term->slug ) ) {
			return $term->slug;
		} else {
			$term = wp_insert_term(
				self::TERM, // the term
				self::TAX, // the taxonomy
				array(
					'description'=> 'These are suggested deals.',
					'slug' => self::TERM_SLUG )
			);
			return self::TERM_SLUG;
		}
	}

	public static function register_template_sidebars() {
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
	
	}

}