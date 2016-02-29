<?php

class Papi_Option_Store_Test extends WP_UnitTestCase {

	public function setUp() {
		parent::setUp();

		tests_add_filter( 'papi/settings/directories', function () {
			return [1,  PAPI_FIXTURE_DIR . '/page-types'];
		} );

		$this->store = papi_get_meta_store( 0, 'option' );

		$_GET = [];
	}

	public function tearDown() {
		parent::tearDown();
		unset( $_GET, $this->store );
	}

	public function test_get_property() {
		$this->assertNull( $this->store->get_property( 'fake' ) );

		$property = $this->store->get_property( 'name' );
		$this->assertSame( 'string', $property->get_option( 'type' ) );
		$this->assertSame( 'string', $property->type );
		$this->assertSame( 'papi_name', $property->slug );
		$this->assertSame( 'papi_name', $property->get_option( 'slug' ) );
		$this->assertSame( 'Name', $property->get_option( 'title' ) );
		$this->assertSame( 'Name', $property->title );

		$_GET['page'] = 'papi/option/options/header-option-type';

		$property = $this->store->get_property( 'name' );
		$this->assertSame( 'string', $property->get_option( 'type' ) );
		$this->assertSame( 'string', $property->type );
		$this->assertSame( 'papi_name', $property->slug );
		$this->assertSame( 'papi_name', $property->get_option( 'slug' ) );
		$this->assertSame( 'Name', $property->get_option( 'title' ) );
		$this->assertSame( 'Name', $property->title );

		$_GET['page'] = 'papi/page/modules/top-module-type';
		$this->assertNull( $this->store->get_property( 'name' ) );
	}

	public function test_get_value() {
		$property = $this->store->get_property( 'name' );
		$this->assertEmpty( $property->get_value() );

		update_option( 'name', 'Fredrik' );
		$this->assertSame( 'Fredrik', $this->store->get_value( 'name' ) );

		update_option( 'hello', 'Fredrik' );
		$this->assertNull( $this->store->get_value( 'hello' ) );
	}

	public function test_valid() {
		$this->assertTrue( $this->store->valid() );
	}
}
