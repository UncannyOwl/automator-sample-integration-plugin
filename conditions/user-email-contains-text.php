<?php

/**
 * Class User_Email_Contains_Text
 */

class User_Email_Contains_Text extends \Uncanny_Automator_Pro\Action_Condition {

	public function define_condition() {

		// Which integration this condition belongs to
		$this->integration = 'SAMPLE_INTEGRATION';

		// The condition name
		$this->name         = __( 'User email contains text', 'automator-sample' );

		// Condition code
		$this->code         = 'USER_EMAIL_CONTAINS_TEXT';

		// Dynamic name
		$this->dynamic_name = sprintf(
			esc_html__( 'User email contains {{text:%s}}', 'automator-sample' ),
			'TEXT'
		);

		// This particular condition requires a user
		$this->requires_user = true;
	}

	/**
	 * Method fields
	 *
	 * @return array
	 */
	public function fields() {

		return array(
			// The text field
			$this->field->text(
				array(
					'option_code'            => 'TEXT',
					'label'                  => esc_html__( 'Text', 'automator-sample' ),
					'show_label_in_sentence' => true,
					'placeholder'            => esc_html__( 'Text', 'automator-sample' ),
					'input_type'             => 'text',
					'required'               => true,
					'description'            => '',
				)
			)
		);
	}

	/**
	 * Evaluate_condition
	 *
	 * Has to use the $this->condition_failed( $message ); method if the condition is not met.
	 *
	 * @return void
	 */
	public function evaluate_condition() {

		// Get the text that users entered in the condition option
		$text = mb_strtolower( $this->get_parsed_option( 'TEXT' ) );

		// Get the WP user object
		// Note that $this->user is not always the current user
        $user_data = get_userdata( $this->user_id );
        
		// Get the user email
        $user_email = mb_strtolower( $user_data->user_email );

		// If the email address doesn't contain the text
		if ( false === strpos( $user_email, $text ) ) {

			// Create any error string
			$log_error = sprintf( __( 'User email "%s" doesn\'t contain "%s"', 'automator-sample' ), $user_email, $text );

			// Pass the error to the condition_failed method
			// This will prevent the action from running
			$this->condition_failed( $log_error );

		}

		// If the condition is met, do nothing and let the action run.
	}
}
