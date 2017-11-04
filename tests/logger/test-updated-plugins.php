<?php

class Talog_Updated_Plugins_Test extends \WP_UnitTestCase
{
	public function test_log()
	{
		$result = self::getMethod( '_table', array(
			array(
		        array(
		            "language" => "ja",
		            "type" => "plugin",
		            "slug" => "akismet",
		            "version" => "3.3.4"
				),
		        array(
			        "language" => "ja",
		            "type" => "theme",
		            "slug" => "twentyseventeen",
		            "version" => "1.3"
				),
				array(
			        "language" => "ja",
		            "type" => "theme",
		            "slug" => "twentysixteen",
		            "version" => "1.3"
				),
			),
			array(
				'language',
				'slug',
				'version',
			),
		) );

		$table = '<table>';
		$table .= '<tr><th>Language</th><th>Slug</th><th>Version</th></tr>';
		$table .= '<tr><td>ja</td><td>akismet</td><td>3.3.4</td></tr>';
		$table .= '<tr><td>ja</td><td>twentyseventeen</td><td>1.3</td></tr>';
		$table .= '<tr><td>ja</td><td>twentysixteen</td><td>1.3</td></tr>';
		$table .= '</table>';

		$this->assertSame( $table, $result );
	}

	protected static function getMethod( $method_name, $args = array() )
	{
		$class = new \ReflectionClass( '\Talog\Logger\Updated_Plugins' );

		$method = $class->getMethod( $method_name );
		$method->setAccessible( true );

		$obj = new \Talog\Logger\Updated_Plugins();
		return $method->invokeArgs( $obj, $args );
	}
}
