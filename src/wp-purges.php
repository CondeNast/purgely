<?php
/**
 * Singleton for registering default WP purges.
 */

class Purgely_Purges {
	/**
	 * The one instance of Purgely_Purges.
	 *
	 * @var Purgely_Purges
	 */
	private static $instance;

	/**
	 * Instantiate or return the one Purgely_Purges instance.
	 *
	 * @return Purgely_Purges
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Initiate actions.
	 *
	 * @return Purgely_Purges
	 */
	public function __construct() {
		foreach ( $this->_purge_actions() as $action ) {
			add_action( $action, array( $this, 'purge_url' ), 10, 3 );
		}
	}

	/**
	 * Callback for post changing events to purge URLs.
	 *
	 * @param  int     $post_id Post ID.
	 * @param  WP_Post $post    Post object.
	 * @param  bool    $update  Whether this is an existing post being updated or not.
	 * @return void
	 */
	public function purge_url( $post_id, $post, $update ) {
		if ( ! in_array( get_post_status( $post_id ), array( 'publish', 'trash' ) ) ) {
			return;
		}

		purgely_purge_url( get_permalink( $post_id ), array( 'related' => true ) );
	}

	/**
	 * A list of actions to purge URLs.
	 *
	 * @return array    List of actions.
	 */
	private function _purge_actions() {
		return array(
			'save_post',
			'deleted_post',
			'trashed_post',
			'delete_attachment',
		);
	}
}

/**
 * Instantiate or return the one Purgely_Purges instance.
 *
 * @return Purgely_Purges
 */
function get_purgely_purges_instance() {
	return Purgely_Purges::instance();
}

get_purgely_purges_instance();
