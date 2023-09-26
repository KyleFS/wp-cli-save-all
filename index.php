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
	 * Executes the actual saving of all posts regardless of post type.
	 *
	 * @param $args       - Arguments stored by position.
	 * @param $assoc_args - Arguments stored in an associative array.
	 */
	public function save_all( $args, $assoc_args ) {
		if ( ! empty( $assoc_args['post-type'] ) ) {
			$post_type = strtolower( $assoc_args['post-type'] );
		} else {
			$post_type = 'post';
		}

		WP_CLI::success( 'Post type to trigger: ' . $post_type );

		$query_params = array(
			'post_type'      => $post_type,
			'posts_per_page' => -1,
			'order'          => 'ASC',
		);

		$results = new WP_Query( $query_params );

		foreach ( $results->posts as $post ) {
			WP_CLI::success( 'Updating ' . $post_type . ' ID # ' . $post->ID );
			wp_update_post( $post );
		}

	}

}

if ( defined( 'WP_CLI' ) && WP_CLI ) {
	$sall = new SALL();
}
