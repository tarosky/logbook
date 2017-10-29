<?php

namespace Talog;

class Log_Level
{
	const TRACE = 'trace';
	const DEBUG = 'debug';
	const INFO = 'info';
	const WARN = 'warn';
	const ERROR = 'error';
	const FATAL = 'fatal';

	const DEFAULT_LEVEL = self::INFO;

	public static function get_level( $level = null )
	{
		if ( $level && in_array( $level, self::get_all_levels() ) ) {
			return $level;
		} else {
			return self::get_default_level();
		}
	}

	public static function get_default_level()
	{
		return self::DEFAULT_LEVEL;
	}

	public static function get_all_levels()
	{
		return array(
			self::TRACE,
			self::DEBUG,
			self::INFO,
			self::WARN,
			self::ERROR,
			self::FATAL,
		);
	}
}
