<?php
// exit if uninstall is not called
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}

$keep = get_option( 'vsel-setting-100' );
if ( $keep != 'yes' ) {
	// set global
	global $wpdb;

	// delete custom post meta
	$wpdb->query( "DELETE FROM {$wpdb->postmeta} WHERE meta_key LIKE 'event%'" );

	// delete options
	$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE 'vsel-setting%'" );
	$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name = 'widget_vsel_widget'" );

	// delete terms
	$wpdb->query( "
		DELETE FROM
		{$wpdb->terms}
		WHERE term_id IN
		( SELECT * FROM (
			SELECT {$wpdb->terms}.term_id
			FROM {$wpdb->terms}
			JOIN {$wpdb->term_taxonomy}
			ON {$wpdb->term_taxonomy}.term_id = {$wpdb->terms}.term_id
			WHERE taxonomy = 'event_cat'
		) as T );
 	" );

	// delete taxonomy
	$wpdb->query( "DELETE FROM {$wpdb->term_taxonomy} WHERE taxonomy = 'event_cat'" );

	// delete events
	$wpdb->query( "DELETE FROM {$wpdb->posts} WHERE post_type = 'event'" );
}
