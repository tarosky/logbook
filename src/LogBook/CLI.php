<?php

namespace LogBook;

use \WP_CLI\CommandWithDBObject;
use \WP_Query;

/**
 * Manages logs, content, and meta.
 *
 * @package wp-cli
 */
class CLI extends CommandWithDBObject
{
	protected $obj_type = 'logbook';
	protected $obj_fields = array(
		'date',
		'log',
		'level',
		'ip',
		'login',
	);

	/**
	 * Get a list of logs.
	 *
	 * ## OPTIONS
	 *
	 * [--<field>=<value>]
	 * : One or more args to pass to WP_Query.
	 *
	 * [--field=<field>]
	 * : Prints the value of a single field for each post.
	 *
	 * [--fields=<fields>]
	 * : Limit the output to specific object fields.
	 *
	 * [--format=<format>]
	 * : Render output in a particular format.
	 * ---
	 * default: table
	 * options:
	 *   - table
	 *   - csv
	 *   - ids
	 *   - json
	 *   - count
	 *   - yaml
	 * ---
	 *
	 * ## AVAILABLE FIELDS
	 *
	 * These fields will be displayed by default for each post:
	 *
	 * * ID
	 * * date
	 * * title
	 * * level
	 * * ip
	 * * label
	 * * login
	 *
	 * ## EXAMPLES
	 *
	 *     # List log
	 *     $ wp log list
	 *
	 *     # Query log
	 *     $ wp log list --level=error
	 *
	 * @subcommand list
	 *
	 * @param array $_
	 * @param array $assoc_args
	 */
	public function list_( $_, $assoc_args )
	{
		$formatter = $this->get_formatter( $assoc_args );

		if ( ! empty( $assoc_args['login'] ) ) {
			$u = get_user_by( 'login', $assoc_args['login'] );
			$assoc_args['author'] = $u->ID;
			unset( $assoc_args['login'] );
		}

		if ( ! empty( $assoc_args['level'] ) ) {
			$assoc_args['meta_query'][] = array(
				'key'   => '_logbook_log_level',
				'value' => $assoc_args['level'],
			);
			unset( $assoc_args['level'] );
		}

		if ( ! empty( $assoc_args['label'] ) ) {
			$assoc_args['meta_query'][] = array(
				'key'   => '_logbook_label',
				'value' => $assoc_args['label'],
			);
			unset( $assoc_args['label'] );
		}

		if ( ! empty( $assoc_args['ip'] ) ) {
			$assoc_args['meta_query'][] = array(
				'key'   => '_logbook_ip',
				'value' => $assoc_args['ip'],
			);
			unset( $assoc_args['ip'] );
		}

		$defaults = array(
			'post_type' => 'logbook',
			'posts_per_page' => -1,
			'post_status'    => 'any',
		);
		$query_args = array_merge( $defaults, $assoc_args );
		$query_args = self::process_csv_arguments_to_arrays( $query_args );

		if ( 'ids' == $formatter->format ) {
			$query_args['fields'] = 'ids';
			$query = new WP_Query( $query_args );
			echo implode( ' ', $query->posts );
		} else if ( 'count' === $formatter->format ) {
			$query_args['fields'] = 'ids';
			$query = new WP_Query( $query_args );
			$formatter->display_items( $query->posts );
		} else {
			$query = new WP_Query( $query_args );
			$posts = array_map( function( $post ) {
				$log = Log::get_the_log( $post );
				if ( empty( $log['meta']['ip'] ) ) {
					$log['ip'] = '';
				} else {
					$log['ip'] = $log['meta']['ip'];
				}
				$log['login'] = $log['user'];
				return $log;
			}, $query->posts );
			$formatter->display_items( $posts );
		}
	}

	/**
	 * Delete all logs.
	 *
	 * @subcommand delete-all
	 *
	 * @param array $_
	 * @param array $assoc_args
	 */
	public function delete_all( $_, $assoc_args )
	{
		define( 'SKIP_LOGGING', true );

		$posts = get_posts( array(
			'post_type' => 'logbook',
			'posts_per_page' => -1,
		) );

		/**
		 * @var $log \WP_Post
		 */
		foreach( $posts as $log ) {
			wp_delete_post( $log->ID, true );
		}

		\WP_CLI::success( 'All logs are deleted.' );
	}
}
