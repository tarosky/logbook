<?php

class LogBook_Plugin_Test extends \WP_UnitTestCase
{
	public function test_log()
	{
		$obj = new LogBook\Logger\Activated_Extensions();

		$GLOBALS['wp_current_filter'] = array( 'activated_plugin' ); // Force `test` hook.

		$log = new LogBook\Log();
		$obj->set_log( $log );
		$obj->log( array( 'hello.php' ) );

		$res = $log->get_log();

		$this->assertSame( 'Plugin "hello.php" was activated.', $res->title );

		$content = json_decode( urldecode( $res->content ), true );
		$this->assertSame( 'Plugin Data', $content[0]['title'] );
		$this->assertTrue( strpos( $content[0]['content'], 'Hello Dolly' ) > 0 );
	}
}
