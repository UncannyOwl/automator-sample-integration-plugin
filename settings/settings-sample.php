<?php

/**
 * Sample Integration Settings
 */
class Sample_Integration_Settings extends \Uncanny_Automator\Settings\Premium_Integration_Settings {

	public $helpers;
	private $api_key;
	private $is_connected;

	public function set_properties() {

		// The unique page ID that will be added to the URL
		$this->set_id( 'sample-integration' );

		// The integration icon will be used for the settings page, so set this option to the integration ID
		$this->set_icon( 'SAMPLE_INTEGRATION' );

		// The name of the settings tab
		$this->set_name( 'Sample integration' );

		// Use this method to register an option for each field your settings page will have
		$this->register_option( 'sample_api_key' );

		// Handle the disconnect button action
		add_action( 'init', array( $this, 'disconnect' ) );

	}

	public function get_status() {

		// Not connected by default
		$this->is_connected = false;

		// Get the API key
		$this->api_key = get_option( 'sample_api_key', false );
		
		// If there is an API key, we are connected
		if ( false !== $this->api_key ) {

			// Store the connected status for later use
			$this->is_connected = true;

			// Return this string to show the green checkmark
			return 'success';
		}

		// Return an empty string is not connected
		return '';
	}

	/**
	 * Creates the output of the settings page
	 */
	public function output_panel_content() {

		// If the integration is not connected, output the field
		if ( ! $this->is_connected ) {

			$args = array(
				'id'       => 'sample_api_key',
				'value'    => $this->api_key,
				'label'    => 'API key',
				'required' => true,
			);

			$this->text_input( $args );

		} else { // If the integration is connected, output the success message
			?> 
			<p> <?php echo __( 'You have successfully connected!', 'automator-sample' ); ?> </p>
			<?php
		}

	}

	public function output_panel_bottom_right() {

		// If we are connected, output the Save button
		if ( ! $this->is_connected ) {
			$button_label = __( 'Save settings', 'automator-sample' );

			$this->submit_button( $button_label );
		} else {

			// Otherwise, show a button that will redirect with the disconnect flag in the URL
			$button_label = __( 'Disconnect', 'automator-sample' );
			$link = $this->get_settings_page_url() . '&disconnect=1';

			$this->redirect_button( $button_label, $link );
		}
	}

	public function settings_updated() {

		// Get the setting
		$this->api_key = get_option( 'sample_api_key', false );

		// Run any validation and add alerts
		if ( is_numeric( $this->api_key ) ) {

			// Display a success message
			$this->add_alert(
				array(
					'type'    => 'success',
					'heading' => __( 'Your API key is a number!', 'automator-sample' ),
					'content'  => 'Additional content'
				)
			);
		} else {
			// Delete the invalid APi key
			delete_option( 'sample_api_key' );

			// Display an error
			$this->add_alert(
				array(
					'type'    => 'error',
					'heading' => __( 'Your API key is not a number!', 'automator-sample' ),
					'content' =>  __( 'The API key failed the numeric check', 'automator-sample' ),
				)
			);
		}
	}

	public function disconnect() {

		// Make sure this settings page is the one that is active
		if ( ! $this->is_current_page_settings() ) {
			return;
		}

		// Check that the URL has our custom disconnect flag
		if ( '1' !== automator_filter_input( 'disconnect' ) ) {
			return;
		}

		// Delete the API key
		delete_option( 'sample_api_key' );


		// Redirect back to the settings page
		wp_safe_redirect( $this->get_settings_page_url() );

		exit;
	}
}