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