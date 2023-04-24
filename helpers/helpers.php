<?php 

class Helpers {

    /**
	 * get_post_types
	 *
	 * Returns an array of registered post types.
	 * 
	 * @return void
	 */
	public function get_post_types() {

		$options = array();
		$options[] = array(
			'text' => __( 'Any post type', 'automator-sample' ),
			'value' => '-1'
		);

		$post_types = get_post_types();

		foreach ( $post_types as $type ) {

			$options[] = array(
				'text' => $type,
				'value' => $type
			);
		}

		return $options;
	}

	/**
	 * ajax_get_posts
	 *
	 * Returns an array of registered post types.
	 * 
	 * @return void
	 */
	public function ajax_get_posts() {

		// This method will cvalidate the nonce in the request.
		Automator()->utilities->ajax_auth_check();
		
		$values = automator_filter_input_array( 'values', INPUT_POST );

		$options = array();
		$options[] = array(
			'text' => __( 'Any post', 'automator-sample' ),
			'value' => '-1'
		);

		if ( empty( $values['POST_TYPE'] ) ) {
			wp_send_json( 
				array(
					'success' => false,
					'error'   => esc_html__( "Please select the post type first.", 'uncanny-automator' ),
					'options' => $options
				)	
			);
		}

		$args = array(
			'post_type' => $values['POST_TYPE'],
			'numberposts' => -1
		);

		$posts = get_posts( $args );

		foreach ( $posts as $post ) {

			$options[] = array(
				'text' => $post->post_title,
				'value' => $post->ID
			);
		}

		wp_send_json( 
			array(
				'success' => true,
				'options' => $options
			)	
		);
	}

    /**
	 * post_is_being_published
	 * 
	 * Checks if the post status changed from non 'publish' to 'publish'
	 *
	 * @param  mixed $post
	 * @param  mixed $post_before
	 * @return void
	 */
	public function post_is_being_published( $post, $post_before ) {

		// If this post is not published yet, bail
		if ( 'publish' !== $post->post_status ) {
			return false;
		}

		// If this post was published before, bail
		if ( ! empty( $post_before->post_status ) && 'publish' === $post_before->post_status ) {
			return false;
		}
		
		// Otherwise, return true
		return true;
	}
}