<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register VS Event List block in the backend.
 *
 * @since 16.7
 */
function vsel_register_block() {
	$attributes = array(
		'listType' => array(
			'type' => 'string'
		),
		'shortcodeSettings' => array(
			'type' => 'string'
		),
		'noNewChanges' => array(
			'type' => 'boolean'
		),
		'executed' => array(
			'type' => 'boolean'
		)
	);
	register_block_type(
		'vsel/vsel-block',
		array(
			'attributes' => $attributes,
			'render_callback' => 'vsel_get_event_list'
		)
	);
}
add_action( 'init', 'vsel_register_block' );

/**
 * Load VS Event List block scripts.
 *
 * @since 16.7
 */
function vsel_enqueue_block_editor_assets() {
	wp_enqueue_style(
		'vsel-style',
		plugins_url('/css/vsel-style.min.css',__FILE__ )
	);
	wp_enqueue_script(
		'vsel-block-script',
		plugins_url( '/js/vsel-block.js' , __FILE__ ),
		array( 'wp-blocks', 'wp-i18n', 'wp-element' ),
		false,
		true
	);
	$dataL10n = array(
		'title' => esc_html__( 'VS Event List', 'very-simple-event-list' ),
		'addSettings' => esc_html__( 'Settings', 'very-simple-event-list' ),
		'listTypeLabel' => esc_html__( 'Display', 'very-simple-event-list' ),
		'listTypes' => array(
			array(
				'id' => 'vsel',
				'label' => esc_html__( 'Upcoming events (today included)', 'very-simple-event-list' )
			),
			array(
				'id' => 'vsel-future-events',
				'label' => esc_html__( 'Future events (today not included)', 'very-simple-event-list' )
			),
			array(
				'id' => 'vsel-current-events',
				'label' => esc_html__( 'Current events', 'very-simple-event-list' )
			),
			array(
				'id' => 'vsel-past-events',
				'label' => esc_html__( 'Past events (before today)', 'very-simple-event-list' )
			),
			array(
				'id' => 'vsel-all-events',
				'label' => esc_html__( 'All events', 'very-simple-event-list' )
			)
		),
		'shortcodeSettingsLabel' => esc_html__( 'Attributes', 'very-simple-event-list' ),
		'example' => esc_html__( 'Example', 'very-simple-event-list' ),
		'previewButton' => esc_html__( 'Apply changes', 'very-simple-event-list' ),
		'linkText' => esc_html__( 'For info and available attributes', 'very-simple-event-list' ),
		'linkLabel' => esc_html__( 'click here', 'very-simple-event-list' )
	);
	wp_localize_script(
		'vsel-block-script',
		'vsel_block_l10n',
		$dataL10n
	);
}
add_action( 'enqueue_block_editor_assets', 'vsel_enqueue_block_editor_assets' );

/**
 * Get shortcode with attributes to display in a VS Event List block.
 *
 * @since 16.7
 */
function vsel_get_event_list( $attr ) {
	$return = '';
	$list_type = isset( $attr['listType'] ) ? sanitize_text_field( wp_unslash( $attr['listType'] ) ) : 'vsel';
	$shortcode_settings = isset( $attr['shortcodeSettings'] ) ? $attr['shortcodeSettings'] : '';
	$shortcode_settings = str_replace( array( '[', ']' ), '', $shortcode_settings );
	$return .= do_shortcode( '[' . $list_type . ' ' . $shortcode_settings . ']' );
	return $return;
}
