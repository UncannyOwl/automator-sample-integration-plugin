<?php

/**
 * Class Sample_Integration
 */
class Sample_Integration extends \Uncanny_Automator\Integration {
	
	protected function setup() {
		$this->set_integration( 'SAMPLE_INTEGRATION' );
		$this->set_name( 'Automator Sample Integration' );
		$this->set_icon_url( plugin_dir_url( __FILE__ ) . 'img/sample-icon.svg' );
	}
}