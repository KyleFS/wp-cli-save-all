<?php
/***
 * Plugin Name: WP-CLI Save All
 * Description: WP-CLI tool to trigger a save on all instances of a given post type.
 * Author: Kyle Spiller
 * Version: 0
 ***/


class SALL {

	/**
	 * Default constructor to integrate with WP CLI.
	 *
	 * @throws Exception - Inherited from WP CLI.
	 */
	public function __construct() {
		WP_CLI::add_command( 'save-all', array( $this, 'save_all' ) );
	}

	/**
	 * Prints a greeting.
	 *
	 * ## OPTIONS
	 *
	 * <post_type>
	 * : The post type you wish to trigger saves for.
	 *
	 * [--wpml=<language>]
	 * : Force set a language in WPML if required.
	 * ---
	 *
	 * ## EXAMPLES
	 *
	 *     wp save-all page --wpml=en
	 *
	 * @when after_wp_load
	 */
	public function save_all( $args, $assoc_args ) {
		if ( ! empty( $args[0] ) ) {
			$post_type = strtolower( $args[0] );
		} else {
			$post_type = 'post';
		}

		$initial_output = 'Post type to trigger: ' . $post_type;

		if ( ! empty( $assoc_args['wpml'] ) && function_exists( 'icl_object_id' ) ) {
			$wpml = strtolower( $assoc_args['wpml'] );

			global $sitepress;

			$sitepress->switch_lang( $wpml );

			$initial_output = $initial_output . ' (WPML language set to ' . $wpml . ')';
		}


		$query_params = array(
			'post_type'      => $post_type,
			'posts_per_page' => -1,
			'post_status'    => 'publish',
			'order'          => 'ASC',
		);

		$results = new WP_Query( $query_params );

		WP_CLI::log( $initial_output );
		$count = 0;

		foreach ( $results->posts as $post ) {
			WP_CLI::log( 'Updating ' . $post_type . ' ID #' . $post->ID . '. (' . $count++ . '/' . $results->post_count . ')' );
			wp_update_post( $post );
		}

		WP_CLI::success( 'Saving complete.' );


	}
}

if ( defined( 'WP_CLI' ) && WP_CLI ) {
	$sall = new SALL();
}
