<?php

/**
 * Class Tribe__Tickets__REST__V1__Post_Repository
 *
 * The base Ticket object repository, a decorator of the base one.
 *
 * @since TBD
 */
class Tribe__Tickets__REST__V1__Ticket_Repository
	extends Tribe__Repository__Decorator
	implements Tribe__Repository__Formatter_Interface {

	/**
	 * @var Tribe__Tickets__Ticket_Repository
	 */
	protected $decorated;

	/**
	 * @var bool
	 */
	protected $did_add_related_post_clauses = false;

	/**
	 * Tribe__Tickets__REST__V1__Ticket_Repository constructor.
	 *
	 * @since TBD
	 */
	public function __construct() {
		$this->decorated = tribe( 'tickets.ticket-repository' );
		$this->decorated->set_formatter( $this );
		$this->decorated->set_query_builder( $this );
		$this->decorated->set_default_args( array_merge(
			$this->decorated->get_default_args(),
			array( 'order' => 'ASC', 'orderby' => array( 'id', 'title' ) )
		) );
	}

	/**
	 * Returns the ticket in the REST API format.
	 *
	 * @since TBD
	 *
	 * @param int|WP_Post $id
	 *
	 * @return array|null The ticket information in the REST API format or
	 *                    `null` if the ticket is invalid.
	 */
	public function format_item( $id ) {
		/**
		 * For the time being we use **another** repository to format
		 * the tickets objects to the REST API format.
		 * If this implementation gets a thumbs-up this class and the
		 * `Tribe__Tickets__REST__V1__Post_Repository` should be merged.
		 */
		/** @var Tribe__Tickets__REST__V1__Post_Repository $repository */
		$repository = tribe( 'tickets.rest-v1.repository' );

		$formatted = $repository->get_ticket_data( $id );

		return $formatted instanceof WP_Error ? null : $formatted;
	}

	/**
	 * An override of the default query building process to add JOIN
	 * and WHERE clauses to only get tickets related to an existing
	 * post.
	 *
	 * @since TBD
	 *
	 * @return WP_Query
	 */
	public function build_query() {
		$this->add_related_post_clauses();

		return $this->decorated->build_query();
	}

	/**
	 * Whatever query is running tickets should not appear in REST
	 * results if not related to an existing post.
	 *
	 * @since TBD
	 */
	protected function add_related_post_clauses() {
		/** @var wpdb $wpdb */
		global $wpdb;
		$this->decorated->join_clause( "JOIN {$wpdb->posts} related_event
			ON {$wpdb->posts}.ID != related_event.ID" );
		$this->decorated->join_clause( "JOIN {$wpdb->postmeta} related_event_meta
			ON {$wpdb->posts}.ID = related_event_meta.post_id" );
		$keys = array();
		foreach ( $this->decorated->ticket_to_event_keys() as $key ) {
			$keys[] = $wpdb->prepare( '%s', $key );
		}
		$keys_in = sprintf( '(%s)', implode( ',', $keys ) );
		$this->decorated->where_clause( "related_event_meta.meta_key IN {$keys_in} 
			AND related_event.ID = related_event_meta.meta_value" );

		$this->decorated->set_query_builder( null );
	}
}
