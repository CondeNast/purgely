<?php

class RelatedUrlsTest extends PHPUnit_Framework_TestCase {
	public function setUp() {
		\WP_Mock::setUp();
	}

	public function tearDown() {
		\WP_Mock::tearDown();
	}

	public function test_object_sets_up_correctly() {
		$id       = 5;
		$url      = 'http://example.org/2015/05/test-post';
		$post     = $this->getMockBuilder( 'WP_Post' )->getMock();
		$post->ID = $id;

		$object = $this->setup_object( 'url', $url, $id, $post );

		$this->assertEquals( $id, $object->get_post_id() );
		$this->assertEquals( $url, $object->get_url() );
		$this->assertEquals( $post, $object->get_post() );

		$id       = 5;
		$url      = 'http://example.org/2015/05/test-post';
		$post     = $this->getMockBuilder( 'WP_Post' )->getMock();
		$post->ID = $id;

		$object = $this->setup_object( 'id', $url, $id, $post );

		$this->assertEquals( $id, $object->get_post_id() );
		$this->assertEquals( $url, $object->get_url() );
		$this->assertEquals( $post, $object->get_post() );

		$id       = 5;
		$url      = 'http://example.org/2015/05/test-post';
		$post     = $this->getMockBuilder( 'WP_Post' )->getMock();
		$post->ID = $id;

		$object = $this->setup_object( 'post', $url, $id, $post );

		$this->assertEquals( $id, $object->get_post_id() );
		$this->assertEquals( $url, $object->get_url() );
		$this->assertEquals( $post, $object->get_post() );
	}

	public function test_generating_term_urls() {
		$id       = 5;
		$url      = 'http://example.org/2015/05/test-post';
		$post     = $this->getMockBuilder( 'WP_Post' )->getMock();
		$post->ID = $id;
		$taxonomy = 'category';

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

		$object = $this->setup_object( 'url', $url, $id, $post );

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

		$object->locate_terms_urls( $id, $taxonomy );

		$this->assertEquals( array( $term1_link, $term2_link ), $object->get_related_urls( $taxonomy ) );
	}

	public function test_generating_term_urls_when_terms_lookup_fails() {
		$id       = 5;
		$url      = 'http://example.org/2015/05/test-post';
		$post     = $this->getMockBuilder( 'WP_Post' )->getMock();
		$post->ID = $id;
		$taxonomy = 'category';

		$object = $this->setup_object( 'url', $url, $id, $post );

		\WP_Mock::wpFunction( 'get_the_terms', array(
			'args' => array(
				$id,
				$taxonomy,
			),
			'times' => 1,
			'return' => false,
		) );

		$object->locate_terms_urls( $id, $taxonomy );

		$this->assertEquals( array(), $object->get_related_urls( $taxonomy ) );
	}

	public function test_generating_term_urls_when_links_are_errors() {
		$id       = 5;
		$url      = 'http://example.org/2015/05/test-post';
		$post     = $this->getMockBuilder( 'WP_Post' )->getMock();
		$post->ID = $id;
		$taxonomy = 'category';

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

		$object = $this->setup_object( 'url', $url, $id, $post );

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
			'return' => true,
		) );

		\WP_Mock::wpFunction( 'is_wp_error', array(
			'args' => array(
				$term2_link,
			),
			'times' => 1,
			'return' => false,
		) );

		$object->locate_terms_urls( $id, $taxonomy );

		$this->assertEquals( array( $term2_link ), $object->get_related_urls( $taxonomy ) );
	}

	public function test_author_urls_get_set() {
		$id                = 5;
		$author_id         = 10;
		$url               = 'http://example.org/2015/05/test-post';
		$post              = $this->getMockBuilder( 'WP_Post' )->getMock();
		$post->ID          = $id;
		$post->post_author = $author_id;

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

		$object = $this->setup_object( 'url', $url, $id, $post );
		$urls   = $object->locate_author_urls( $post );

		$this->assertEquals( array( $posts_url, $feed_url ), $urls );
		$this->assertEquals( array( $posts_url, $feed_url ), $object->get_related_urls( 'author' ) );
	}

	public function test_archive_urls_get_set() {
		$id                = 5;
		$author_id         = 10;
		$post_type         = 'record';
		$url               = 'http://example.org/2015/05/test-post';
		$post              = $this->getMockBuilder( 'WP_Post' )->getMock();
		$post->ID          = $id;
		$post->post_author = $author_id;
		$post->post_type   = $post_type;

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

		$object = $this->setup_object( 'url', $url, $id, $post );
		$urls   = $object->locate_post_type_archive_url( $post );

		$this->assertEquals( array( $archive_link, $archive_feed_link ), $urls );
		$this->assertEquals( array( $archive_link, $archive_feed_link ), $object->get_related_urls( 'post-type-archive' ) );
	}

	public function test_archive_urls_set_correctly_when_archive_is_false() {
		$id                = 5;
		$author_id         = 10;
		$post_type         = 'record';
		$url               = 'http://example.org/2015/05/test-post';
		$post              = $this->getMockBuilder( 'WP_Post' )->getMock();
		$post->ID          = $id;
		$post->post_author = $author_id;
		$post->post_type   = $post_type;

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
			'return' => false,
		) );

		\WP_Mock::wpFunction( 'get_post_type_archive_feed_link', array(
			'args'   => array(
				$post_type,
			),
			'times'  => 1,
			'return' => $archive_feed_link,
		) );

		$object = $this->setup_object( 'url', $url, $id, $post );
		$urls   = $object->locate_post_type_archive_url( $post );

		$this->assertEquals( array( $archive_feed_link ), $urls );
		$this->assertEquals( array( $archive_feed_link ), $object->get_related_urls( 'post-type-archive' ) );
	}

	public function test_archive_urls_set_correctly_when_archive_feed_is_false() {
		$id                = 5;
		$author_id         = 10;
		$post_type         = 'record';
		$url               = 'http://example.org/2015/05/test-post';
		$post              = $this->getMockBuilder( 'WP_Post' )->getMock();
		$post->ID          = $id;
		$post->post_author = $author_id;
		$post->post_type   = $post_type;

		$archive_link = 'http://example.org/' . $post_type;

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
			'return' => false,
		) );

		$object = $this->setup_object( 'url', $url, $id, $post );
		$urls   = $object->locate_post_type_archive_url( $post );

		$this->assertEquals( array( $archive_link ), $urls );
		$this->assertEquals( array( $archive_link ), $object->get_related_urls( 'post-type-archive' ) );
	}

	public function test_archive_urls_set_correctly_links_are_false() {
		$id                = 5;
		$author_id         = 10;
		$post_type         = 'record';
		$url               = 'http://example.org/2015/05/test-post';
		$post              = $this->getMockBuilder( 'WP_Post' )->getMock();
		$post->ID          = $id;
		$post->post_author = $author_id;
		$post->post_type   = $post_type;

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
			'return' => false,
		) );

		\WP_Mock::wpFunction( 'get_post_type_archive_feed_link', array(
			'args'   => array(
				$post_type,
			),
			'times'  => 1,
			'return' => false,
		) );

		$object = $this->setup_object( 'url', $url, $id, $post );
		$urls   = $object->locate_post_type_archive_url( $post );

		$this->assertEquals( array(), $urls );
		$this->assertEquals( array(), $object->get_related_urls( 'post-type-archive' ) );
	}

	public function test_feed_links_are_set_correctly() {
		$id       = 5;
		$url      = 'http://example.org/2015/05/test-post';
		$post     = $this->getMockBuilder( 'WP_Post' )->getMock();
		$post->ID = $id;

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

		$object = $this->setup_object( 'url', $url, $id, $post );
		$urls   = $object->locate_feed_urls( $post );

		$this->assertEquals( array_values( $feed_urls ), $urls );
		$this->assertEquals( array_values( $feed_urls ), $object->get_related_urls( 'feed' ) );
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
		if ( 'url' === $type ) {
			$thing = $url;
		} else if ( 'id' === $type ) {
			$thing = $id;
		} else {
			$thing = $post;
		}

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

		return new Purgely_Related_Urls( array( $type => $thing ) );
	}
}