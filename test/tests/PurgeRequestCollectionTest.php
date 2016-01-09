<?php

class PurgeRequestCollectionTest extends PHPUnit_Framework_TestCase {
	public function setUp() {
		\WP_Mock::setUp();
	}

	public function tearDown() {
		\WP_Mock::tearDown();
	}

	public function test_setup_object() {
		$url        = 'http://example.com/2015/09/my-url';
		$purge_args = array( 'soft-purge' => true );
		$object     = $this->setup_standard_collection( $url, $purge_args );

		$this->assertEquals( $purge_args, $object->get_purge_args() );
		$this->assertEquals( $this->get_url_collection(), $object->get_urls() );
	}

	public function test_unused_getters_and_setters() {
		$url        = 'http://example.com/2015/09/my-url';
		$purge_args = array( 'soft-purge' => true );
		$object     = $this->setup_standard_collection( $url, $purge_args );

		$fake_requests = array( 1, 2 );

		$object->set_purge_requests( $fake_requests );
		$this->assertEquals( $fake_requests, $object->get_purge_requests() );

		$response = (object) array( 'test' );
		$object->set_response( $response );
		$this->assertEquals( array( $response ), $object->get_responses() );

		$object->set_url( 'http://example.org', 'test' );
		$urls = $object->get_urls();
		$this->assertEquals( array( 'http://example.org' ), $urls['test'] );
	}

	public function test_purge_related() {
		$url    = 'http://example.com/2015/09/my-url';
		$object = $this->setup_standard_collection( $url );

		$this->assertEquals( $this->get_url_collection(), $object->get_urls() );

		// We are going to issue a number of remote requests, which need some mocking
		\WP_Mock::wpFunction( 'wp_remote_request', array(
			'args'   => array(
				\WP_Mock\Functions::type( 'string' ),
				\WP_Mock\Functions::type( 'array' ),
			),
			'times'  => 15, // One for each URL in the collection
			'return' => MockData::purge_url_response_200(),
		) );

		\WP_Mock::wpFunction( 'get_option', array(
			'args'   => array(
				'purgely-settings',
				array()
			),
			'times'  => 1,
			'return' => array()
		) );

		$object->purge_related();

		$responses = array();

		foreach ( $this->get_url_collection() as $category => $urls ) {
			foreach ( $urls as $url ) {
				$responses[ $url ] = MockData::purge_url_response_200();
			}
		}

		$this->assertEquals( $responses, $object->get_responses() );

		// Ensure that all request objects are indeed request objects
		foreach ( $object->get_purge_requests() as $purge_request ) {
			$this->assertInstanceOf( 'Purgely_Purge', $purge_request );
		}
	}

	public function test_purge_related_when_there_are_no_urls_to_purge() {
		$url    = 'http://example.com/2015/09/my-url';
		$object = $this->setup_standard_collection( $url );

		$object->set_urls( array() );
		$object->purge_related();

		$this->assertEquals( array(), $object->get_urls() );
		$this->assertEquals( array(), $object->get_responses() );
	}

	public function test_purge_related_result_when_everything_is_a_success() {
		$url    = 'http://example.com/2015/09/my-url';
		$object = $this->setup_standard_collection( $url );

		// We are going to issue a number of remote requests, which need some mocking
		\WP_Mock::wpFunction( 'wp_remote_request', array(
			'args'   => array(
				\WP_Mock\Functions::type( 'string' ),
				\WP_Mock\Functions::type( 'array' ),
			),
			'times'  => 15, // One for each URL in the collection
			'return' => MockData::purge_url_response_200(),
		) );

		\WP_Mock::wpFunction( 'is_wp_error', array(
			'args'   => array(
				'*',
			),
			'times'  => 15, // One for each response
			'return' => false,
		) );

		\WP_Mock::wpFunction( 'wp_remote_retrieve_response_code', array(
			'args'   => array(
				'*',
			),
			'times'  => 15, // One for each response
			'return' => '200',
		) );

		$object->purge_related();
		$result = $object->get_result();

		$this->assertEquals( 'success', $result );
	}

	public function test_purge_related_result_when_there_are_no_requests() {
		$url    = 'http://example.com/2015/09/my-url';
		$object = $this->setup_standard_collection( $url );

		$object->set_urls( array() );
		$object->purge_related();

		$this->assertEquals( 'success', $object->get_result() );
	}

	public function test_purge_related_result_when_one_request_fails() {
		$url    = 'http://example.com/2015/09/my-url';
		$object = $this->setup_standard_collection( $url );

		// We are going to issue a number of remote requests, which need some mocking
		\WP_Mock::wpFunction( 'wp_remote_request', array(
			'args'   => array(
				\WP_Mock\Functions::type( 'string' ),
				\WP_Mock\Functions::type( 'array' ),
			),
			'times'  => 14, // One for each URL in the collection
			'return' => MockData::purge_url_response_200(),
		) );

		\WP_Mock::wpFunction( 'wp_remote_request', array(
			'args'   => array(
				\WP_Mock\Functions::type( 'string' ),
				\WP_Mock\Functions::type( 'array' ),
			),
			'times'  => 1, // One for each URL in the collection
			'return' => MockData::purge_url_response_405(),
		) );

		\WP_Mock::wpFunction( 'is_wp_error', array(
			'args'   => array(
				'*',
			),
			'times'  => 15, // One for each response
			'return' => false,
		) );

		\WP_Mock::wpFunction( 'wp_remote_retrieve_response_code', array(
			'args'   => array(
				'*',
			),
			'times'  => 14, // One for each response
			'return' => '200',
		) );

		\WP_Mock::wpFunction( 'wp_remote_retrieve_response_code', array(
			'args'   => array(
				'*',
			),
			'times'  => 1, // One for each response
			'return' => '405',
		) );

		$object->purge_related();
		$result = $object->get_result();

		$this->assertEquals( 'failure', $result );
	}

	public function test_purge_related_result_when_one_request_produces_a_wp_error() {
		$url    = 'http://example.com/2015/09/my-url';
		$object = $this->setup_standard_collection( $url );

		// We are going to issue a number of remote requests, which need some mocking
		\WP_Mock::wpFunction( 'wp_remote_request', array(
			'args'   => array(
				\WP_Mock\Functions::type( 'string' ),
				\WP_Mock\Functions::type( 'array' ),
			),
			'times'  => 14, // One for each URL in the collection
			'return' => MockData::purge_url_response_200(),
		) );

		\WP_Mock::wpFunction( 'wp_remote_request', array(
			'args'   => array(
				\WP_Mock\Functions::type( 'string' ),
				\WP_Mock\Functions::type( 'array' ),
			),
			'times'  => 1, // One for each URL in the collection
			'return' => MockData::purge_url_response_405(),
		) );

		\WP_Mock::wpFunction( 'is_wp_error', array(
			'args'   => array(
				'*',
			),
			'times'  => 14, // One for each response
			'return' => false,
		) );

		\WP_Mock::wpFunction( 'is_wp_error', array(
			'args'   => array(
				'*',
			),
			'times'  => 1, // One for each response
			'return' => true,
		) );

		\WP_Mock::wpFunction( 'wp_remote_retrieve_response_code', array(
			'args'   => array(
				'*',
			),
			'times'  => 14, // One for each response
			'return' => '200',
		) );

		$object->purge_related();
		$result = $object->get_result();

		$this->assertEquals( 'failure', $result );
	}

	private function get_url_collection() {
		return array(
			'url'               =>
				array(
					0 => 'http://example.com/2015/09/my-url',
				),
			'category'          =>
				array(
					0 => 'http://example.org/category/bologne',
					1 => 'http://example.org/category/ham',
				),
			'post_tag'          =>
				array(
					0 => 'http://example.org/post_tag/bologne',
					1 => 'http://example.org/post_tag/ham',
				),
			'author'            =>
				array(
					0 => 'http://example.org/author/author_name',
					1 => 'http://example.org/author/author_name/feed',
				),
			'post-type-archive' =>
				array(
					0 => 'http://example.org/record',
					1 => 'http://example.org/record/feed',
				),
			'feed'              =>
				array(
					0 => 'http://example.org/feed/rdf',
					1 => 'http://example.org/feed/rss',
					2 => 'http://example.org/feed/rss2',
					3 => 'http://example.org/feed/atom',
					4 => 'http://example.org/feed/comments_rss2',
					5 => 'http://example.com/2015/09/my-url/comments/feed',
				),
		);
	}

	/**
	 * Sets up a collection object to be used with most tests.
	 *
	 * @param  string $url The URL to purge.
	 * @param  array $purge_args The set of arguments to pass to the purge requests.
	 * @return Purgely_Purge_Request_Collection
	 */
	private function setup_standard_collection( $url, $purge_args = array() ) {
		$this->setup_locate_all( $url );
		return new Purgely_Purge_Request_Collection( $url, $purge_args );
	}

	/**
	 * Sets up the locate_all() method.
	 *
	 * Prepares for the Purgely_Purge_Request_Collection constructor to call locate_all(). This call always happens and
	 * you must mock this in order to be able to test the request collection.
	 *
	 * @param string $url The URL to purge.
	 */
	private function setup_locate_all( $url ) {
		$id                = 5;
		$author_id         = 10;
		$post_type         = 'record';
		$post              = $this->getMockBuilder( 'WP_Post' )->getMock();
		$post->ID          = $id;
		$post->post_author = $author_id;
		$post->post_type   = $post_type;

		$urls = array();

		$taxonomy          = 'category';

		$term1 = (object) array(
			'term_id'          => '1',
			'name'             => 'Bologne',
			'slug'             => 'bologne',
			'term_group'       => '',
			'term_taxonomy_id' => '25',
			'taxonomy'         => $taxonomy,
			'description'      => '',
			'parent'           => '',
			'count'            => '10'
		);

		$term1_link = 'http://example.org/' . $taxonomy . '/' . $term1->slug;

		$term2 = (object) array(
			'term_id'          => '4',
			'name'             => 'Ham',
			'slug'             => 'ham',
			'term_group'       => '',
			'term_taxonomy_id' => '23',
			'taxonomy'         => $taxonomy,
			'description'      => '',
			'parent'           => '',
			'count'            => '64'
		);

		$term2_link = 'http://example.org/' . $taxonomy . '/' . $term2->slug;

		\WP_Mock::wpFunction( 'get_the_terms', array(
			'args' => array(
				$id,
				$taxonomy,
			),
			'times' => 1,
			'return' => array(
				$term1,
				$term2,
			),
		) );

		\WP_Mock::wpFunction( 'get_term_link', array(
			'args' => array(
				$term1,
				$taxonomy,
			),
			'times' => 1,
			'return' => $term1_link,
		) );

		\WP_Mock::wpFunction( 'get_term_link', array(
			'args' => array(
				$term2,
				$taxonomy,
			),
			'times' => 1,
			'return' => $term2_link,
		) );

		\WP_Mock::wpFunction( 'is_wp_error', array(
			'args' => array(
				$term1_link,
			),
			'times' => 1,
			'return' => false,
		) );

		\WP_Mock::wpFunction( 'is_wp_error', array(
			'args' => array(
				$term2_link,
			),
			'times' => 1,
			'return' => false,
		) );

		$urls[ $taxonomy ] = array(
			$term1_link,
			$term2_link,
		);

		$taxonomy          = 'post_tag';

		$term1 = (object) array(
			'term_id'          => '1',
			'name'             => 'Bologne',
			'slug'             => 'bologne',
			'term_group'       => '',
			'term_taxonomy_id' => '25',
			'taxonomy'         => $taxonomy,
			'description'      => '',
			'parent'           => '',
			'count'            => '10'
		);

		$term1_link = 'http://example.org/' . $taxonomy . '/' . $term1->slug;

		$term2 = (object) array(
			'term_id'          => '4',
			'name'             => 'Ham',
			'slug'             => 'ham',
			'term_group'       => '',
			'term_taxonomy_id' => '23',
			'taxonomy'         => $taxonomy,
			'description'      => '',
			'parent'           => '',
			'count'            => '64'
		);

		$term2_link = 'http://example.org/' . $taxonomy . '/' . $term2->slug;

		\WP_Mock::wpFunction( 'get_the_terms', array(
			'args' => array(
				$id,
				$taxonomy,
			),
			'times' => 1,
			'return' => array(
				$term1,
				$term2,
			),
		) );

		\WP_Mock::wpFunction( 'get_term_link', array(
			'args' => array(
				$term1,
				$taxonomy,
			),
			'times' => 1,
			'return' => $term1_link,
		) );

		\WP_Mock::wpFunction( 'get_term_link', array(
			'args' => array(
				$term2,
				$taxonomy,
			),
			'times' => 1,
			'return' => $term2_link,
		) );

		\WP_Mock::wpFunction( 'is_wp_error', array(
			'args' => array(
				$term1_link,
			),
			'times' => 1,
			'return' => false,
		) );

		\WP_Mock::wpFunction( 'is_wp_error', array(
			'args' => array(
				$term2_link,
			),
			'times' => 1,
			'return' => false,
		) );

		$urls[ $taxonomy ] = array(
			$term1_link,
			$term2_link,
		);

		$posts_url = 'http://example.org/author/author_name';
		$feed_url  = $posts_url . '/feed';

		\WP_Mock::wpFunction( 'get_author_posts_url', array(
			'args' => array(
				$author_id,
			),
			'times' => 1,
			'return' => $posts_url,
		) );

		\WP_Mock::wpFunction( 'get_author_feed_link', array(
			'args' => array(
				$author_id,
			),
			'times' => 1,
			'return' => $feed_url,
		) );

		$urls['author'] = array(
			$posts_url,
			$feed_url,
		);

		$archive_link      = 'http://example.org/' . $post_type;
		$archive_feed_link = $archive_link . '/feed';

		\WP_Mock::wpFunction( 'get_post_type', array(
			'args'   => array(
				$post,
			),
			'times'  => 1,
			'return' => $post_type,
		) );

		\WP_Mock::wpFunction( 'get_post_type_archive_link', array(
			'args'   => array(
				$post_type,
			),
			'times'  => 1,
			'return' => $archive_link,
		) );

		\WP_Mock::wpFunction( 'get_post_type_archive_feed_link', array(
			'args'   => array(
				$post_type,
			),
			'times'  => 1,
			'return' => $archive_feed_link,
		) );

		$urls['post-type-archive'] = array(
			$archive_link,
			$archive_feed_link,
		);

		$base = 'http://example.org/feed/';

		$feed_urls = array(
			'rdf_url'           => $base . 'rdf',
			'rss_url'           => $base . 'rss',
			'rss2_url'          => $base . 'rss2',
			'atom_url'          => $base . 'atom',
			'comments_rss2_url' => $base . 'comments_rss2',
			'post_comments'     => $url . '/comments/feed',
		);

		\WP_Mock::wpFunction( 'get_bloginfo_rss', array(
			'args'   => array(
				'rdf_url',
			),
			'times'  => 1,
			'return' => $feed_urls['rdf_url'],
		) );

		\WP_Mock::wpFunction( 'get_bloginfo_rss', array(
			'args'   => array(
				'rss_url',
			),
			'times'  => 1,
			'return' => $feed_urls['rss_url'],
		) );

		\WP_Mock::wpFunction( 'get_bloginfo_rss', array(
			'args'   => array(
				'rss2_url',
			),
			'times'  => 1,
			'return' => $feed_urls['rss2_url'],
		) );

		\WP_Mock::wpFunction( 'get_bloginfo_rss', array(
			'args'   => array(
				'atom_url',
			),
			'times'  => 1,
			'return' => $base . 'atom',
		) );

		\WP_Mock::wpFunction( 'get_bloginfo_rss', array(
			'args'   => array(
				'comments_rss2_url',
			),
			'times'  => 1,
			'return' => $feed_urls['comments_rss2_url'],
		) );

		\WP_Mock::wpFunction( 'get_post_comments_feed_link', array(
			'args'   => array(
				$id,
			),
			'times'  => 1,
			'return' => $feed_urls['post_comments'],
		) );

		$urls['feed'] = array_values( $feed_urls );

		$this->setup_object( 'url', $url, $id, $post );
	}

	/**
	 * Setup a Purgely_Related object.
	 *
	 * Helper function to handle the bootstrapping of all of the wp functions needed to set up the object.
	 *
	 * @param  string               $type The type of lookup.
	 * @param  string               $url  The url for the post.
	 * @param  int                  $id   The ID of the post.
	 * @param  object               $post The post object.
	 * @return Purgely_Related_Urls
	 */
	private function setup_object( $type, $url, $id, $post ) {
		\WP_Mock::wpPassthruFunction( 'absint' );

		if ( 'url' === $type ) {
			\WP_Mock::wpFunction( 'url_to_postid', array(
				'args'   => array(
					$url
				),
				'times'  => 1,
				'return' => $id
			) );
		}

		if ( in_array( $type, array( 'id', 'url' ) ) ) {
			\WP_Mock::wpFunction( 'get_post', array(
				'args'   => array(
					$id
				),
				'times'  => 1,
				'return' => $post
			) );
		}

		\WP_Mock::wpFunction( 'get_permalink', array(
			'args'   => array(
				$post
			),
			'times'  => 1,
			'return' => $url
		) );
	}
}