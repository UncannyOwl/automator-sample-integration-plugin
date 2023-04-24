<?php

/**
 * Class Comment_Submitted_Sample
 */
class Comment_Submitted_Sample extends \Uncanny_Automator\Recipe\Trigger {

	protected $helpers;

	/**
	 * This is an anonymous trigger that will fire even if no user is logged in.
	 * Only one such trigger per anonymous (evreyone) recipe can be used.
	 */
	protected function setup_trigger() {

		$this->helpers = array_shift( $this->dependencies );

		// Here we set the trigger type to anonymous so it can run for any site visitor even for non-logged-in
		$this->set_trigger_type( 'anonymous' );

		$this->set_integration( 'SAMPLE_INTEGRATION' );
		$this->set_trigger_code( 'COMMENT_SUBMITTED' );
		$this->set_trigger_meta( 'POST' );
		/* Translators: post */
		$this->set_sentence( sprintf( esc_attr__( 'A comment is submitted to {{a post:%1$s}}', 'automator-sample' ), 'POST' ) );
		/* Translators: post */
		$this->set_readable_sentence( esc_attr__( 'A comment is submitted to {{a post}}', 'automator-sample' ) );

		$this->add_action( 'comment_post', 90, 3 );
	}

	public function options() {

		$post_types_dropdown = array(
			'input_type'      => 'select',
			'option_code'     => 'POST_TYPE',
			'label'           => __( 'Post type', 'automator-sample' ),
			'required'        => true,
			'options'         => $this->helpers->get_post_types(),
		);

		$posts = array(
			'input_type'      => 'select',
			'option_code'     => 'POST',
			'label'           => __( 'Post', 'automator-sample' ),
			'required'        => true,
			'ajax'                  => array(
				'endpoint'      => 'automator_sample_get_posts',
				'event'         => 'parent_fields_change',
				'listen_fields' => array( 'POST_TYPE' ),
			),
		);

		return array(
				$post_types_dropdown,
				$posts
			);
	}	

	/**
	 * @return bool
	 */
	public function validate( $trigger, $hook_args ) {

		// Make sure the trigger has some value selected in the options
		if ( ! isset( $trigger['meta']['POST'] ) || ! isset( $trigger['meta']['POST_TYPE'] ) ) {
			//Something is wrong, the trigger doesn't have the required option value.
			return false;
		}

		// Get the dropdown value
		$selected_post_type = $trigger['meta']['POST_TYPE'];
		$selected_post = $trigger['meta']['POST'];

		// Parse the args from the wp_after_insert_post hook
		$comment_id = array_shift( $hook_args );
		$approved = array_shift( $hook_args );
		$comment_data = array_shift( $hook_args );

		// If the post type selected in the trigger options doesn't match the post type being commented, bail.
		if ( '-1' != $selected_post_type && $selected_post_type != get_post_type( $comment_data['comment_post_ID'] ) ) {
			return false;
		}

		// Make sure the post is being published and not updated or drafted
		if ( '-1' != $selected_post && $selected_post != $comment_data['comment_post_ID'] ) {
			return false;
		}
		// If all conditions were met, return true
		return true;
	}

	/**
	 * define_tokens
	 * 
	 * Alter this method if you want to add some additional tokens.
	 *
	 * @param  mixed $tokens
	 * @param  mixed $trigger - options selected in the current recipe/trigger
	 * @return array
	 */
	public function define_tokens( $trigger, $tokens ) {

		$tokens[] = array(
			'tokenId'         => 'COMMENT_AUTHOR',
			'tokenName'       => __( 'Comment Author', 'automator-sample' ),
			'tokenType'       => 'text',
		);

		$tokens[] = array(
			'tokenId'         => 'COMMENT_EMAIL',
			'tokenName'       => __( 'Comment Email', 'automator-sample' ),
			'tokenType'       => 'text',
		);

		$tokens[] = array(
			'tokenId'         => 'COMMENT_TEXT',
			'tokenName'       => __( 'Comment Text', 'automator-sample' ),
			'tokenType'       => 'text',
		);

		return $tokens;
	}
	
	/**
	 * hydrate_tokens
	 * 
	 * Here you need to pass the values for the trigger tokens.
	 * Note that each token field also has a token that has to be populated in this method.
	 *
	 * @param  mixed $trigger
	 * @param  mixed $hook_args
	 * @return void
	 */
	public function hydrate_tokens( $trigger, $hook_args ) {

		$comment_id = array_shift( $hook_args );
		$approved = array_shift( $hook_args );
		$comment_data = array_shift( $hook_args );

		$token_values = array(
			'POST_TYPE' => get_post_type( $comment_data['comment_post_ID'] ),
			'POST' => get_the_title( $comment_data['comment_post_ID'] ),
			'COMMENT_AUTHOR' => $comment_data['comment_author'],
			'COMMENT_EMAIL' => $comment_data['comment_author_email'],
			'COMMENT_TEXT' => $comment_data['comment_content'],

		); 

		return $token_values;
	}

}