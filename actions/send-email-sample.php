<?php

/**
 * Class Send_Email_Sample
 */
class Send_Email_Sample extends \Uncanny_Automator\Recipe\Action {

	/**
	 *
	 */
	protected function setup_action() {

		// Define the Actions's info
		$this->set_integration( 'SAMPLE_INTEGRATION' );
		$this->set_action_code( 'SEND_EMAIL_SAMPLE' );
		$this->set_action_meta( 'EMAIL_TO' );

		// Define the Action's sentence
		$this->set_sentence( sprintf( esc_attr__( 'Send an email to {{email address:%1$s}} from Sample Integration', 'automator-sample' ), $this->get_action_meta() ) );
		$this->set_readable_sentence( esc_attr__( 'Send an {{email}} from Sample Integration', 'automator-sample' ) );
	}
	
	/**
	 * Define the Action's options
	 *
	 * @return void
	 */
	public function options() {

		return array(
			Automator()->helpers->recipe->field->text(
				array(
					'option_code' => 'EMAIL_FROM',
					'label'       => 'From',
					'description' => 'Sample description',
					'placeholder' => 'Enter from email',
					'input_type'  => 'email',
					'default'     => 'john@doe.com',
				)
			),
			Automator()->helpers->recipe->field->text(
				array(
					'option_code' => 'EMAIL_TO',
					'label'       => 'To',
					'input_type'  => 'email',
				)
			),
			Automator()->helpers->recipe->field->text(
				array(
					'option_code' => 'EMAIL_SUBJECT',
					'label'       => 'Subject',
					'input_type'  => 'text',
				)
			),
			Automator()->helpers->recipe->field->text(
				array(
					'option_code' => 'EMAIL_BODY',
					'label'       => 'Body',
					'input_type'  => 'textarea',
				)
			),
		);
	}
	
	/**
	 * define_tokens
	 *
	 * @return array
	 */
	public function define_tokens() {
		return array(
			'STATUS' => array(
				'name' => __( 'Send status', 'automator-sample' ),
				'type' => 'text',
			),
		);
	}

	/**
	 * @param int $user_id
	 * @param array $action_data
	 * @param int $recipe_id
	 * @param array $args
	 * @param $parsed
	 */
	protected function process_action( $user_id, $action_data, $recipe_id, $args, $parsed ) {

		$action_meta = $action_data['meta'];

		// Get the field values
		$to = sanitize_email( Automator()->parse->text( $action_meta['EMAIL_TO'], $recipe_id, $user_id, $args ) );
		$from = sanitize_email( Automator()->parse->text( $action_meta['EMAIL_FROM'], $recipe_id, $user_id, $args ) );
		$subject = sanitize_text_field( Automator()->parse->text( $action_meta['EMAIL_SUBJECT'], $recipe_id, $user_id, $args ) );
		$body = wp_filter_post_kses( stripslashes( ( Automator()->parse->text( $action_meta['EMAIL_BODY'], $recipe_id, $user_id, $args ) ) ) );
		
		//Set email headers
		$headers = array( 
			'Content-Type: text/html; charset=utf-8',
			'From: ' . get_bloginfo('name') . ' <' . $from . '>',
			'Reply-To: ' . get_bloginfo('name') . ' <' . $from . '>',
		 );

		// Send the email. Returns true or false
		$status = wp_mail( $to, $subject, $body, $headers ); 

		// Convert true or false into string error
		$status_string = $status ? __( 'Email was sent', 'automator-sample' ) : __( 'Email was not sent', 'automator-sample' );

		// Populate the custom token value
		$this->hydrate_tokens( 
			array( 
				'STATUS' => $status_string 
				) 
		);

		// Handle errors
		if ( ! $status ) {

			$this->add_log_error( $status_string );

			return false; // Return false if error ocurred during the action completion
		}

		// Always return true if everything was okay
		return true;
	}
}