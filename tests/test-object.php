<?php

class Object_Test extends \WP_UnitTestCase
{
	function test_php()
	{
		$obj = new stdClass;
		$obj->flag = false;
		$obj_copy = $obj;

		$func = function( $obj ) {
			$obj->flag = true;
		};

		$this->assertSame( false, $obj->flag );
		$this->assertSame( false, $obj_copy->flag );

		call_user_func_array( $func, array( $obj ) );

		$this->assertSame( true, $obj->flag );
		$this->assertSame( true, $obj_copy->flag );
	}
}
