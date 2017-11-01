<?php

class Talog_Plugin_Test extends \WP_UnitTestCase
{
	public function test_log()
	{
		$obj = new Talog\Logger\Activated_Plugin();

		$GLOBALS['wp_current_filter'] = array( 'activated_plugin' ); // Force `test` hook.

		$log = new Talog\Log();
		$obj->log( $log, array( 'hello.php' ) );

		$res = $log->get_log();
		//var_dump( $res );
		// TODO: need test
	}

	public function test_admin()
	{
		$obj = new Talog\Logger\Activated_Plugin();
		$post = $this->factory()->post->create_and_get( array(
			'post_content' => '',
		) );

		$this->assertFalse( !! $post->post_content );

		// TODO: need test
//		$obj->admin( $post, array( 'error' => $error, 'error-file' => 'file' ) );
//		$this->assertTrue( !! $post->post_content );
	}
}
