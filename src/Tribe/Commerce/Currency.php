<?php
class Tribe__Tickets__Commerce__Currency {

	/**
	 * @var string
	 */
	public $currency_code;

	/* Currency mapping code to symbol and position */
	public $currency_code_options_map = array();

	/**
	 * Class constructor
	 *
	 * @since TBD
	 */
	public function __construct() {
		$this->currency_code = tribe_get_option( 'ticket-commerce-currency-code', 'USD' );
		$this->generate_default_currency_map();
	}

	/**
	 * Hooks the actions and filters required by the class.
	 *
	 * @since TBD
	 */
	public function hook(  ) {
		add_filter( 'tribe_currency_symbol', array( $this, 'get_currency_symbol' ), 10, 2 );
		add_filter( 'tribe_reverse_currency_position', array( $this, 'reverse_currency_symbol_position' ), 10, 2 );
		add_filter( 'get_post_metadata', array( $this, 'filter_cost_meta' ), 10, 4 );
	}

	/**
	 * Get and allow filtering of the currency symbol
	 * @param int|null $post_id
	 *
	 * @return string
	 */
	public function get_currency_symbol( $post_id = null ) {
		$symbol = $this->currency_code_options_map[ $this->currency_code ]['symbol'];
		return apply_filters( 'tribe_commerce_currency_symbol', $symbol, $post_id );
	}

	/**
	 * Get and allow filtering of the currency symbol
	 * @param int|null $post_id
	 *
	 * @return string
	 */
	public function filter_currency_symbol( $unused_currency_symbol, $post_id = null ) {
		return $this->get_currency_symbol( $post_id );
	}

	/**
	 * Get and allow filtering of the currency symbol position
	 * @param int|null $post_id
	 *
	 * @return string
	 */
	public function get_currency_symbol_position( $post_id = null ) {
		if ( ! isset( $this->currency_code_options_map[ $this->currency_code ]['position'] ) ) {
			$currency_position = 'prefix';
		} else {
			$currency_position = $this->currency_code_options_map[ $this->currency_code ]['position'];
		}

		return apply_filters( 'tribe_commerce_currency_symbol_position', $currency_position, $post_id );
	}

	/**
	 * Filters of the currency symbol position on event displays
	 * @param int|null $post_id
	 *
	 * @return string
	 */
	public function reverse_currency_symbol_position( $unused_reverse_position, $post_id = null ) {

		return $this->get_currency_symbol_position( $post_id ) !== 'prefix';
	}

	/**
	 * Format the currency using the currency_code_options_map
	 * @param      $cost
	 * @param null $post_id
	 *
	 * @return string
	 */
	public function format_currency( $cost, $post_id = null ) {
		$post_id = Tribe__Main::post_id_helper( $post_id );
		$currency_symbol   = $this->get_currency_symbol( $post_id );
		$currency_position = $this->get_currency_symbol_position( $post_id );

		$cost = $currency_position === 'prefix' ? $currency_symbol . $cost : $cost . $currency_symbol;

		return $cost;
	}

	/**
	 * Generates the default map and allows for filtering
	 *
	 * @since TBD
	 */
	public function generate_default_currency_map() {
		$default_map = array(
			'AUD' => array(
				'name'   => __( 'Australian Dollar (AUD)', 'event-tickets' ),
				'symbol' => '&#x41;&#x24;',
			),
			'BRL' => array(
				'name'   => __( 'Brazilian Real  (BRL)', 'event-tickets' ),
				'symbol' => '&#82;&#x24;',
			),
			'CAD' => array(
				'name'   => __( 'Canadian Dollar (CAD)', 'event-tickets' ),
				'symbol' => '&#x24;',
			),
			'CHF' => array(
				'name'   => __( 'Swiss Franc (CHF)', 'event-tickets' ),
				'symbol' => '&#x43;&#x48;&#x46;',
			),
			'CZK' => array(
				'name'     => __( 'Czech Koruna (CZK)', 'event-tickets' ),
				'symbol'   => '&#x4b;&#x10d;',
				'position' => 'postfix',
			),
			'DKK' => array(
				'name'   => __( 'Danish Krone (DKK)', 'event-tickets' ),
				'symbol' => '&#107;&#114;',
			),
			'EUR' => array(
				'name'   => __( 'Euro (EUR)', 'event-tickets' ),
				'symbol' => '&#8364;',
			),
			'GBP' => array(
				'name'   => __( 'Pound Sterling (GBP)', 'event-tickets' ),
				'symbol' => '&#163;',
			),
			'HKD' => array(
				'name'   => __( 'Hong Kong Dollar (HKD)', 'event-tickets' ),
				'symbol' => '&#x24;',
			),
			'HUF' => array(
				'name'   => __( 'Hungarian Forint (HUF)', 'event-tickets' ),
				'symbol' => '&#x46;&#x74;',
			),
			'ILS' => array(
				'name'   => __( 'Israeli New Sheqel (ILS)', 'event-tickets' ),
				'symbol' => '&#x20aa;',
			),
			'JPY' => array(
				'name'   => __( 'Japanese Yen (JPY)', 'event-tickets' ),
				'symbol' => '&#165;',
			),
			'MYR' => array(
				'name'   => __( 'Malaysian Ringgit (MYR)', 'event-tickets' ),
				'symbol' => '&#82;&#77;',
			),
			'MXN' => array(
				'name'   => __( 'Mexican Peso (MXN)', 'event-tickets' ),
				'symbol' => '&#x24;',
			),
			'NOK' => array(
				'name'   => __( 'Norwegian Krone (NOK)', 'event-tickets' ),
				'symbol' => '',
			),
			'NZD' => array(
				'name'   => __( 'New Zealand Dollar (NZD)', 'event-tickets' ),
				'symbol' => '&#x24;',
			),
			'PHP' => array(
				'name'   => __( 'Philippine Peso (PHP)', 'event-tickets' ),
				'symbol' => '&#x20b1;',
			),
			'PLN' => array(
				'name'   => __( 'Polish Zloty (PLN)', 'event-tickets' ),
				'symbol' => '&#x7a;&#x142;',
			),
			'SEK' => array(
				'name'   => __( 'Swedish Krona (SEK)', 'event-tickets' ),
				'symbol' => '&#x6b;&#x72;',
			),
			'SGD' => array(
				'name'   => __( 'Singapore Dollar (SGD)', 'event-tickets' ),
				'symbol' => '&#x53;&#x24;',
			),
			'THB' => array(
				'name'   => __( 'Thai Baht (THB)', 'event-tickets' ),
				'symbol' => '&#x0e3f;',
			),
			'TWD' => array(
				'name'   => __( 'Taiwan New Dollar (TWD)', 'event-tickets' ),
				'symbol' => '&#x4e;&#x54;&#x24;',
			),
			'USD' => array(
				'name'   => __( 'U.S. Dollar (USD)', 'event-tickets' ),
				'symbol' => '&#x24;',
			),
		);

		/**
		 * Filters the currency code options map.
		 *
		 * @since TBD
		 *
		 * @param array $default_map An associative array mapping currency codes
		 *                           to their respective name and symbol.
		 */
		$this->currency_code_options_map = apply_filters( 'tribe_tickets_commerce_currency_code_options_map', $default_map );
	}

	/**
	 * Creates the array for a currency drop-down using only code & name
	 *
	 * @since TBD
	 *
	 * @return array
	 */
	public function generate_currency_code_options() {
		$options = array_combine(
			array_keys( $this->currency_code_options_map ),
			wp_list_pluck( $this->currency_code_options_map, 'name' )
		);

		/**
		 * Filters the currency code options shown to the user in the settings.
		 *
		 * @since TBD
		 *
		 * @param array $options
		 */
		return apply_filters( 'tribe_tickets_commerce_currency_code_options', $options );
	}

	/**
	 * Filters the symbol and symbol position meta to use the Tickets currency code when an event
	 * has tickets.
	 *
	 * @param null|mixed $meta      The original meta value.
	 * @param int        $object_id The current post ID
	 * @param string     $meta_key  The requested meta key.
	 * @param bool       $single    Whether this is a request to fetch only one meta value or all.
	 *
	 * @return string
	 */
	public function filter_cost_meta( $meta, $object_id, $meta_key, $single ) {
		if ( ! in_array( $meta_key, array( '_EventCurrencySymbol', '_EventCurrencyPosition' ), true ) ) {
			return $meta;
		}

		if ( ! tribe_events_has_tickets( $object_id ) ) {
			return $meta;
		}

		if ( $single ) {
			$default_provider = Tribe__Tickets__Tickets::get_event_ticket_provider( $object_id );

			switch ( $meta_key ) {
				case '_EventCurrencySymbol':
					return $this->get_provider_symbol( $default_provider, $object_id );
				case '_EventCurrencyPosition':
					return $this->get_provider_symbol_position( $default_provider, $object_id );
			}
		}

		return $meta;
	}

	/**
	 * Returns the currency symbol depending on the provider.
	 *
	 * @since TBD
	 *
	 * @param string $provider  The ticket provider class name
	 * @param int    $object_id The post ID
	 *
	 * @return string
	 */
	protected function get_provider_symbol( $provider, $object_id ) {
		if ( ! class_exists( $provider ) ) {
			return $this->get_currency_symbol( $object_id );
		}

		if ( 'Tribe__Tickets_Plus__Commerce__WooCommerce__Main' === $provider ) {
			return get_woocommerce_currency_symbol();
		}

		if ( 'Tribe__Tickets_Plus__Commerce__EDD__Main' === $provider ) {
			return edd_currency_symbol();
		}

		return $this->get_currency_symbol( $object_id );
	}

	/**
	 * Returns the currency symbol position depending on the provider.
	 *
	 * @since TBD
	 *
	 * @param string $provider  The ticket provider class name
	 * @param int    $object_id The post ID
	 *
	 * @return string
	 */
	protected function get_provider_symbol_position( $provider, $object_id ) {
		if ( ! class_exists( $provider ) ) {
			return $this->get_currency_symbol( $object_id );
		}

		if ( 'Tribe__Tickets_Plus__Commerce__WooCommerce__Main' === $provider ) {
			$position = get_option( 'woocommerce_currency_pos' );

			return in_array( $position, array( 'left', 'left_space' ) )
				? 'prefix'
				: 'postfix';
		}

		if ( 'Tribe__Tickets_Plus__Commerce__EDD__Main' === $provider ) {
			$position = edd_get_option( 'currency_position', 'before' );

			return 'before' === $position ? 'prefix' : 'postfix';
		}

		return $this->get_currency_symbol( $object_id );
	}
}
