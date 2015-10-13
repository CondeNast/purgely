<?php
if ( ! class_exists( 'Purgely_Command' ) ) :
/**
 * Define the "fast" WP CLI command.
 *
 * @since 1.0.0
 */
class Purgely_Command extends WP_CLI_Command {
	/**
	 * Purge a URL, post ID(s), surrogate key, or all.
	 *
	 * ## OPTIONS
	 *
	 * [<url>]
	 * : The URL to purge.
	 *
	 * [<id>...]
	 *
	 * : The ID or IDs of the post URL to purge.
	 *
	 * [<surrogate-key>]
	 *
	 * : The surrogate key to purge.
	 *
	 * [--all]
	 *
	 * : Purge all of the cached object.
	 *
	 * [--related]
	 *
	 * : Purged URLs related to the passed URL, ID, or IDs.
	 *
	 * [--soft]
	 *
	 * : Issue a Fastly "soft purge" for the purge request or requests.
	 *
	 * ## EXAMPLES
	 *
	 *   # Purge a url
	 *   wp fp http://www.wired.com/category/design/
	 *
	 *   # Purge a key
	 *   wp fp section-front
	 *
	 *   # Purge all
	 *   wp fp all
	 *
	 *   # Add purge args
	 *   wp fp http://www.wired.com/category/design/ --soft
	 *   wp fp section-front --soft
	 *
	 *   # Purge related URLs
	 *   wp fp http://www.wired.com/2015/10/apple-google-war --related
	 *
	 *   # Purge a list of posts
	 *   wp fp 471930 27501 23857 24581 66038
	 *
	 *   # Purge a list of posts and related URLs
	 *   wp fp 471930 27501 23857 24581 66038 --related
	 *
	 *   # Purge the 10 latest published posts
	 *   wp fp $(wp post list --field=ID --post_status=publish --posts_per_page=10)
	 *
	 * @since  1.0.0
	 *
	 * Note that we are using __invoke in order to allow for this to be deployed without an unnecessary subcommand. A
	 * subcommand is just not needed and would cause additional typing.
	 *
	 * @param  array    $args          The unflagged args.
	 * @param  array    $assoc_args    The flagged args.
	 * @return void
	 */
	public function __invoke( $args, $assoc_args ) {
		// Collect the main arguments
		$thing = ( isset( $args[0] ) ) ? $args[0] : '';

		// Collect the secondary arguments
		$purge_args               = array();
		$purge_args['soft-purge'] = ( isset( $assoc_args['soft'] ) ) ? true : false;
		$purge_args['related']    = ( isset( $assoc_args['related'] ) ) ? true : false;

		// Determine the type of request
		if ( true === $this->_is_ids( $args ) ) {
			$type = 'ids';
		} elseif ( true === $this->_is_url( $thing ) ) {
			$type = 'url';
		} elseif ( true === $this->_is_key( $thing ) ) {
			$type = 'key';
		} elseif ( ( isset( $assoc_args['all'] ) ) ) {
			$type = 'all';
		} else {
			$type = 'unknown';
		}

		if ( empty( $type ) ) {
			WP_CLI::error( __( 'purge type is unknown', 'purgely' ) );
		}

		if ( 'url' === $type ) {
			$result = $this->_purge_url( $thing, $purge_args );

			if ( 'success' === $result ) {
				WP_CLI::success( sprintf( __( 'purged %s' ), esc_url( $thing ) ) );
			} else {
				WP_CLI::error( __( 'URL could not be purged', 'purgely' ) );
			}
		} else if ( 'key' === $type ) {
			$result = $this->_purge_key( $thing, $purge_args );

			if ( 'success' === $result ) {
				WP_CLI::success( sprintf( __( 'purged %s' ), purgely_sanitize_surrogate_key( $thing ) ) );
			} else {
				WP_CLI::error( __( 'key could not be purged', 'purgely' ) );
			}
		} else if ( 'all' === $type ) {
			$result = $this->_purge_all( $purge_args );

			if ( 'success' === $result ) {
				WP_CLI::success( __( 'purged all' ) );
			} else {
				WP_CLI::error( __( 'cache could not be purged', 'purgely' ) );
			}
		} else if ( 'ids' === $type ) {
			foreach ( $args as $id ) {
				$result = $this->_purge_id( $id, $purge_args );

				if ( 'success' === $result ) {
					WP_CLI::success( sprintf( __( 'purged %d %s', 'purgely' ), absint( $id ), get_permalink( $id ) ) );
				} else {
					// Where other tasks error out, only warn here so other posts can be purged
					WP_CLI::warning( __( sprintf( __( 'could not purge %d %s', 'purgely' ), absint( $id ), get_permalink( $id )  ) ) );
				}
			}
		}
	}

	/**
	 * Determine if the args represent a list of IDs.
	 *
	 * @param  array    $args    The list of args passed to the command.
	 * @return bool              True if the args are a list of IDs, false if not.
	 */
	private function _is_ids( $args ) {
		$are_ids = true;

		if ( is_array( $args ) && count( $args ) > 0 ) {
			foreach ( $args as $id ) {
				if ( ! is_numeric( $id ) || absint( $id ) != $id ) {
					$are_ids = false;
					break;
				}
			}
		} else {
			$are_ids = false;
		}

		return $are_ids;
	}

	/**
	 * Determine if the first arg is a URL.
	 *
	 * @param  string    $thing    The first argument passed to the function.
	 * @return bool                True if the thing is a URL, false if not.
	 */
	private function _is_url( $thing ) {
		return 0 === strpos( $thing, 'http' ) && $thing === esc_url_raw( $thing );
	}

	/**
	 * Determine if the first arg is a surrogate key.
	 *
	 * @param  string    $thing    The first argument passed to the function.
	 * @return bool                True if the thing is a surrogate key, false if not.
	 */
	private function _is_key( $thing ) {
		return ! empty( $thing ) && $thing === purgely_sanitize_surrogate_key( $thing );
	}

	/**
	 * Purge a URL.
	 *
	 * @since  1.0.0.
	 *
	 * @param  string                 $url           The URL to purge.
	 * @param  array                  $purge_args    Additional args to pass to the purge request.
	 * @return array|bool|WP_Error                   The purge response.
	 */
	private function _purge_url( $url, $purge_args ) {
		return purgely_purge_url( $url, $purge_args );
	}

	/**
	 * Purge a surrogate key.
	 *
	 * @since  1.0.0.
	 *
	 * @param  string                 $key           The surrogate key to purge.
	 * @param  array                  $purge_args    Additional args to pass to the purge request.
	 * @return array|bool|WP_Error                   The purge response.
	 */
	private function _purge_key( $key, $purge_args ) {
		return purgely_purge_surrogate_key( $key, $purge_args );
	}

	/**
	 * Purge the whole cache.
	 *
	 * @since  1.0.0.
	 *
	 * @param  array                  $purge_args    Additional args to pass to the purge request.
	 * @return array|bool|WP_Error                   The purge response.
	 */
	private function _purge_all( $purge_args ) {
		return purgely_purge_all( $purge_args );
	}

	/**
	 * Purge a post by ID.
	 *
	 * @since  1.0.0.
	 *
	 * @param  int                    $id            The ID to purge.
	 * @param  array                  $purge_args    Additional args to pass to the purge request.
	 * @return array|bool|WP_Error                   The purge response.
	 */
	private function _purge_id( $id, $purge_args ) {
		$url = get_permalink( $id );
		return $this->_purge_url( $url, $purge_args );
	}
}
endif;

WP_CLI::add_command( 'fp', 'Purgely_Command' );