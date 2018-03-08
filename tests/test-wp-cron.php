<?php

class LogBook_Cron_Test extends \WP_UnitTestCase
{
	public function test_cron() {
		$events = array_values( _get_cron_array() );
		$this->assertSame( 1, count( $events[0]['logbook_scheduled_event'] ) );
	}

	public function test_cron_option() {
		$this->assertSame( "1", get_option( 'logbook-scheduled-event' ) );
	}

	public function test_cron_delete() {
		// Add logs.
		$this->factory()->post->create_many( 10, array(
			'post_type' => 'logbook'
		) );

		// Add old logs which are deleted by cron.
		$this->factory()->post->create_many( 10, array(
			'post_type' => 'logbook',
			'post_date' => date( "Y-m-d H:i:s", strtotime( '1 year ago' ))
		) );

		// Get all logs.
		$logs = get_posts( array( 'post_type' => 'logbook', 'posts_per_page' => -1 ) );
		$this->assertSame( 20, count( array_keys( $logs ) ) );

		// Fires the cron and get all logs.
		\LogBook\scheduled_event();
		$logs = get_posts( array( 'post_type' => 'logbook', 'posts_per_page' => -1 ) );
		$this->assertSame( 10, count( array_keys( $logs ) ) );
	}
}
