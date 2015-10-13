<?php

class Papi_Property_Image_Test extends Papi_Property_Test_Case {

	protected $same = false;

	public $slug = 'image_test';

	public function get_value() {
		return 23;
	}

	public function get_expected() {
		return 23;
	}

	public function test_property_convert_type() {
		$this->assertEquals( 'object', $this->property->convert_type );
	}

	public function test_property_default_value() {
		$this->assertEquals( [], $this->property->default_value );
	}

	public function test_property_format_value() {
		$post_id = $this->factory->post->create( ['post_type' => 'attachment', 'post_mime_type' => 'image/jpeg'] );
		$meta    = [
			'width'      => 2900,
			'height'     => 1559,
			'file'       => '2011/12/press_image.jpg',
			'sizes'      => [
				'thumbnail' => [
					'file'      => 'press_image-150x150.jpg',
					'width'     => 150,
					'height'    => 150,
					'mime-type' => 'image/jpeg'
				]
			],
			'image_meta' => [
				'aperture'          => 5,
				'credit'            => '',
				'camera'            => 'Super',
				'caption'           => '',
				'created_timestamp'	=> 1323190643,
				'copyright'         => '',
				'focal_length'      => 35,
				'iso'               => 800,
				'shutter_speed'     => 0.016666666666667,
				'title'             => ''
			]
		];

		update_post_meta( $post_id, '_wp_attachment_metadata', $meta );
		update_post_meta( $post_id, '_wp_attachment_image_alt', 'alt text' );
		update_post_meta( $post_id, '_wp_attached_file', '2011/12/press_image.jpg' );
		$thumbnail_url = home_url( '/wp-content/uploads/2011/12/press_image-150x150.jpg' );

		tests_add_filter( 'image_downsize', function( $image, $attachment_id, $size ) use ( $thumbnail_url ) {
			return [$thumbnail_url, 150, 150, false];
		}, 10, 3 );

		$image = $this->property->format_value( $post_id, '', $post_id );

		$this->assertTrue( is_object( $image ) );
		$this->assertEquals( 2900, $image->width );
		$this->assertEquals( 1559, $image->height );
		$this->assertEquals( '2011/12/press_image.jpg', $image->file );

		$this->assertTrue( is_array( $image->sizes ) );
		$this->assertTrue( isset( $image->sizes['thumbnail'] ) );
		$this->assertEquals( 'press_image-150x150.jpg', $image->sizes['thumbnail']['file'] );
		$this->assertEquals( 150, $image->sizes['thumbnail']['width'] );
		$this->assertEquals( 150, $image->sizes['thumbnail']['height'] );
		$this->assertEquals( 'image/jpeg', $image->sizes['thumbnail']['mime-type'] );
		$this->assertEquals( $thumbnail_url, $image->sizes['thumbnail']['url'] );

		$this->assertTrue( is_array( $image->image_meta ) );
		$this->assertEquals( 5, $image->image_meta['aperture'] );
		$this->assertEquals( '', $image->image_meta['credit'] );
		$this->assertEquals( 'Super', $image->image_meta['camera'] );
		$this->assertEquals( '', $image->image_meta['caption'] );
		$this->assertEquals( 1323190643, $image->image_meta['created_timestamp'] );
		$this->assertEquals( '', $image->image_meta['copyright'] );
		$this->assertEquals( 35, $image->image_meta['focal_length'] );
		$this->assertEquals( 800, $image->image_meta['iso'] );
		$this->assertEquals( 0.016666666666667, $image->image_meta['shutter_speed'] );
		$this->assertEquals( '', $image->image_meta['title'] );
	}

	public function test_property_import_value() {
		$this->assertEmpty( $this->property->import_value( $this->get_value(), '', 0 ) );
	}

	public function test_property_options() {
		$this->assertEquals( 'image', $this->property->get_option( 'type' ) );
		$this->assertEquals( 'Image test', $this->property->get_option( 'title' ) );
		$this->assertEquals( 'papi_image_test', $this->property->get_option( 'slug' ) );
	}

	public function test_property_settings() {
		$this->assertFalse( $this->property->get_setting( 'multiple' ) );
	}
}
