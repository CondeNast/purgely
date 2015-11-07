<?php

class SurrogateKeyCollectionTest extends PHPUnit_Framework_TestCase {
	public function setUp() {
		\WP_Mock::setUp();
		\WP_Mock::wpPassthruFunction( 'absint' );
	}

	public function tearDown() {
		\WP_Mock::tearDown();
	}

	public function test_single_post_keys() {
		$wp_query = $this->getMockBuilder( 'WP_Query' )
			->setMethods( array(
				'get_queried_object',
				'get',
				'is_single',
				'is_preview',
				'is_page',
				'is_archive',
				'is_date',
				'is_year',
				'is_month',
				'is_day',
				'is_time',
				'is_author',
				'is_category',
				'is_tag',
				'is_tax',
				'is_search',
				'is_feed',
				'is_comment_feed',
				'is_trackback',
				'is_home',
				'is_404',
				'is_comments_popup',
				'is_paged',
				'is_admin',
				'is_attachment',
				'is_singular',
				'is_robots',
				'is_posts_page',
				'is_post_type_archive',
			) )
			->getMock();

		$template_type = 'single';

		$wp_query->expects( $this->any() )
			->method( 'is_single' )
			->will( $this->returnValue( true ) );

		$wp_query->post              = $this->getMockBuilder( 'WP_Post' )->getMock();
		$wp_query->post->ID          = 10;
		$wp_query->post->post_author = 23;

		$wp_query->posts = array(
			clone $wp_query->post,
		);

		\WP_Mock::wpFunction( 'get_taxonomies', array(
			'args'   => array(),
			'times'  => 1,
			'return' => array(
				'category'      => 'category',
				'post_tag'      => 'post_tag',
				'nav_menu'      => 'nav_menu',
				'link_category' => 'link_category',
				'post_format'   => 'post_format',
			)
		) );

		$categories = array(
			(object) array(
				'term_id'          => '4',
				'name'             => 'Ham',
				'slug'             => 'ham',
				'term_group'       => '',
				'term_taxonomy_id' => '23',
				'taxonomy'         => 'category',
				'description'      => '',
				'parent'           => '',
				'count'            => '64'
			),
			(object) array(
				'term_id'          => '1',
				'name'             => 'Bologne',
				'slug'             => 'bologne',
				'term_group'       => '',
				'term_taxonomy_id' => '25',
				'taxonomy'         => 'category',
				'description'      => '',
				'parent'           => '',
				'count'            => '10'
			)
		);

		$post_tag = array(
			(object) array(
				'term_id'          => '4',
				'name'             => 'Ham',
				'slug'             => 'ham',
				'term_group'       => '',
				'term_taxonomy_id' => '23',
				'taxonomy'         => 'post_tag',
				'description'      => '',
				'parent'           => '',
				'count'            => '64'
			),
			(object) array(
				'term_id'          => '1',
				'name'             => 'Bologne',
				'slug'             => 'bologne',
				'term_group'       => '',
				'term_taxonomy_id' => '25',
				'taxonomy'         => 'post_tag',
				'description'      => '',
				'parent'           => '',
				'count'            => '10'
			)
		);

		\WP_Mock::wpFunction( 'get_queried_object', array(
			'args'   => array(),
			'times'  => 5,
			'return' => $wp_query->post
		) );

		\WP_Mock::wpFunction( 'get_the_terms', array(
			'args' => array(
				\WP_Mock\Functions::type( 'int' ),
				\WP_Mock\Functions::type( 'string' ),
			),
			'times' => 5,
			'return_in_order' => array(
				$categories,
				$post_tag,
				false,
				false,
				false,
			)
		) );

		$object = new Purgely_Surrogate_Key_Collection( $wp_query );
		$object->set_key( 'test-key' );

		$expected_keys = array (
			0 => 'post-' . $wp_query->post->ID ,
			1 => 'template-' . $template_type,
			2 => 'category-' . $categories[0]->slug,
			3 => 'category-' . $categories[1]->slug,
			4 => 'post_tag-' . $post_tag[0]->slug,
			5 => 'post_tag-' . $post_tag[1]->slug,
			6 => 'author-' . $wp_query->post->post_author,
			7 => 'test-key',
		);

		$this->assertEquals( $expected_keys, $object->get_keys() );
	}

	public function test_category_page_keys() {
		$wp_query = $this->getMockBuilder( 'WP_Query' )
			->setMethods( array(
				'get_queried_object',
				'get',
				'is_single',
				'is_preview',
				'is_page',
				'is_archive',
				'is_date',
				'is_year',
				'is_month',
				'is_day',
				'is_time',
				'is_author',
				'is_category',
				'is_tag',
				'is_tax',
				'is_search',
				'is_feed',
				'is_comment_feed',
				'is_trackback',
				'is_home',
				'is_404',
				'is_comments_popup',
				'is_paged',
				'is_admin',
				'is_attachment',
				'is_singular',
				'is_robots',
				'is_posts_page',
				'is_post_type_archive',
			) )
			->getMock();

		$template_type = 'category';

		$wp_query->expects( $this->any() )
			->method( 'is_category' )
			->will( $this->returnValue( true ) );

		$wp_query->post              = $this->getMockBuilder( 'WP_Post' )->getMock();
		$wp_query->post->ID          = 10;
		$wp_query->post->post_author = 23;

		$post2              = $this->getMockBuilder( 'WP_Post' )->getMock();
		$post2->ID          = 11;
		$post2->post_author = 54;

		$wp_query->posts = array(
			clone $wp_query->post,
			clone $post2,
		);

		$category = (object) array(
			'term_id'          => '4',
			'name'             => 'Ham',
			'slug'             => 'ham',
			'term_group'       => '',
			'term_taxonomy_id' => '23',
			'taxonomy'         => 'category',
			'description'      => '',
			'parent'           => '',
			'count'            => '64'
		);

		\WP_Mock::wpFunction( 'get_queried_object', array(
			'args'   => array(),
			'times'  => 1,
			'return' => $category
		) );

		$object = new Purgely_Surrogate_Key_Collection( $wp_query );

		$expected_keys = array (
			0 => 'post-' . $wp_query->post->ID ,
			1 => 'post-' . $post2->ID ,
			2 => 'template-' . $template_type,
			3 => 'category-' . $category->slug,
		);

		$this->assertEquals( $expected_keys, $object->get_keys() );
	}

	public function test_tag_page_keys() {
		$wp_query = $this->getMockBuilder( 'WP_Query' )
			->setMethods( array(
				'get_queried_object',
				'get',
				'is_single',
				'is_preview',
				'is_page',
				'is_archive',
				'is_date',
				'is_year',
				'is_month',
				'is_day',
				'is_time',
				'is_author',
				'is_category',
				'is_tag',
				'is_tax',
				'is_search',
				'is_feed',
				'is_comment_feed',
				'is_trackback',
				'is_home',
				'is_404',
				'is_comments_popup',
				'is_paged',
				'is_admin',
				'is_attachment',
				'is_singular',
				'is_robots',
				'is_posts_page',
				'is_post_type_archive',
			) )
			->getMock();

		$template_type = 'tag';

		$wp_query->expects( $this->any() )
			->method( 'is_tag' )
			->will( $this->returnValue( true ) );

		$wp_query->post              = $this->getMockBuilder( 'WP_Post' )->getMock();
		$wp_query->post->ID          = 10;
		$wp_query->post->post_author = 23;

		$post2              = $this->getMockBuilder( 'WP_Post' )->getMock();
		$post2->ID          = 11;
		$post2->post_author = 54;

		$wp_query->posts = array(
			clone $wp_query->post,
			clone $post2,
		);

		$tag = (object) array(
			'term_id'          => '4',
			'name'             => 'Ham',
			'slug'             => 'ham',
			'term_group'       => '',
			'term_taxonomy_id' => '23',
			'taxonomy'         => 'post_tag',
			'description'      => '',
			'parent'           => '',
			'count'            => '64'
		);

		\WP_Mock::wpFunction( 'get_queried_object', array(
			'args'   => array(),
			'times'  => 1,
			'return' => $tag
		) );

		$object = new Purgely_Surrogate_Key_Collection( $wp_query );

		$expected_keys = array (
			0 => 'post-' . $wp_query->post->ID ,
			1 => 'post-' . $post2->ID ,
			2 => 'template-' . $template_type,
			3 => 'post_tag-' . $tag->slug,
		);

		$this->assertEquals( $expected_keys, $object->get_keys() );
	}

	public function test_home_page_keys() {
		$wp_query = $this->getMockBuilder( 'WP_Query' )
			->setMethods( array(
				'get_queried_object',
				'get',
				'is_single',
				'is_preview',
				'is_page',
				'is_archive',
				'is_date',
				'is_year',
				'is_month',
				'is_day',
				'is_time',
				'is_author',
				'is_category',
				'is_tag',
				'is_tax',
				'is_search',
				'is_feed',
				'is_comment_feed',
				'is_trackback',
				'is_home',
				'is_404',
				'is_comments_popup',
				'is_paged',
				'is_admin',
				'is_attachment',
				'is_singular',
				'is_robots',
				'is_posts_page',
				'is_post_type_archive',
			) )
			->getMock();

		$template_type = 'home';

		$wp_query->expects( $this->any() )
			->method( 'is_home' )
			->will( $this->returnValue( true ) );

		$wp_query->post              = $this->getMockBuilder( 'WP_Post' )->getMock();
		$wp_query->post->ID          = 10;
		$wp_query->post->post_author = 23;

		$post2              = $this->getMockBuilder( 'WP_Post' )->getMock();
		$post2->ID          = 11;
		$post2->post_author = 54;

		$wp_query->posts = array(
			clone $wp_query->post,
			clone $post2,
		);

		$object = new Purgely_Surrogate_Key_Collection( $wp_query );

		$expected_keys = array (
			0 => 'post-' . $wp_query->post->ID ,
			1 => 'post-' . $post2->ID ,
			2 => 'template-' . $template_type,
		);

		$this->assertEquals( $expected_keys, $object->get_keys() );
	}
}