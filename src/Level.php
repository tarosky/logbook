<?php
/**
 * The base class for log levels.
 */

namespace Talog;

class Level
{
	protected $level = '';
	protected $class = '';

	public function __construct() {}

	public function get_level()
	{
		return $this->level;
	}

	public function get_class()
	{
		return $this->class;
	}
}
