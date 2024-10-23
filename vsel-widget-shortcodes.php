<?php
// disable direct access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// upcoming events shortcode
function vsel_widget_upcoming_events_shortcode( $vsel_widget_atts ) {
	// shortcode attributes
	$vsel_widget_atts = shortcode_atts(array(
		'class' => '',
		'date_format' => '',
		'event_cat' => '',
		'posts_per_page' => '',
		'offset' => '',
		'order' => 'ASC',
		'title_link' => '',
		'featured_image' => '',
		'featured_image_link' => '',
		'featured_image_caption' => '',
		'event_info' => '',
		'read_more' => '',
		'no_events_text' => __('There are no upcoming events.', 'very-simple-event-list')
	), $vsel_widget_atts );

	// initialize output
	$output = '';
	// main container
	if ( empty($vsel_widget_atts['class']) ) {
		$custom_class = '';
	} else {
		$custom_class = ' '.$vsel_widget_atts['class'];
	}
	$output .= '<div id="vsel" class="vsel-widget vsel-widget-upcoming-events'.esc_attr($custom_class).'">';
		// query
		$today = vsel_timestamp_today();
		$vsel_meta_query = array(
			'relation' => 'AND',
			array(
				'key' => 'event-date',
				'value' => $today,
				'compare' => '>=',
				'type' => 'NUMERIC'
			)
		);
		$vsel_query_args = array(
			'post_type' => 'event',
			'event_cat' => $vsel_widget_atts['event_cat'],
			'post_status' => 'publish',
			'ignore_sticky_posts' => true,
			'meta_key' => 'event-start-date',
			'orderby' => 'meta_value_num menu_order',
			'order' => $vsel_widget_atts['order'],
			'posts_per_page' => $vsel_widget_atts['posts_per_page'],
			'offset' => $vsel_widget_atts['offset'],
			'meta_query' => $vsel_meta_query
		);
		$vsel_widget_upcoming_query = new WP_Query( $vsel_query_args );

		if ( $vsel_widget_upcoming_query->have_posts() ) :
			while( $vsel_widget_upcoming_query->have_posts() ): $vsel_widget_upcoming_query->the_post();
				// include event variables
				include 'vsel-variables.php';

				// include event template
				include 'vsel-widget-template.php';
			endwhile;
			// reset post data
			wp_reset_postdata();
		else:
			// if no events
			$output .= '<p class="vsel-no-events">';
			$output .= esc_attr($vsel_widget_atts['no_events_text']);
			$output .= '</p>';
		endif;
	$output .= '</div>';

	// return output
	return $output;
}
add_shortcode('vsel-widget', 'vsel_widget_upcoming_events_shortcode');

// future events shortcode
function vsel_widget_future_events_shortcode( $vsel_widget_atts ) {
	// shortcode attributes
	$vsel_widget_atts = shortcode_atts(array(
		'class' => '',
		'date_format' => '',
		'event_cat' => '',
		'posts_per_page' => '',
		'offset' => '',
		'order' => 'ASC',
		'title_link' => '',
		'featured_image' => '',
		'featured_image_link' => '',
		'featured_image_caption' => '',
		'pagination' => '',
		'event_info' => '',
		'read_more' => '',
		'no_events_text' => __('There are no future events.', 'very-simple-event-list')
	), $vsel_widget_atts );

	// initialize output
	$output = '';
	// main container
	if ( empty($vsel_widget_atts['class']) ) {
		$custom_class = '';
	} else {
		$custom_class = ' '.$vsel_widget_atts['class'];
	}
	$output .= '<div id="vsel" class="vsel-widget vsel-widget-future-events'.esc_attr($custom_class).'">';
		// query
		$tomorrow = vsel_timestamp_tomorrow();
		$vsel_meta_query = array(
			'relation' => 'AND',
			array(
				'key' => 'event-start-date',
				'value' => $tomorrow,
				'compare' => '>=',
				'type' => 'NUMERIC'
			)
		);
		$vsel_query_args = array(
			'post_type' => 'event',
			'event_cat' => $vsel_widget_atts['event_cat'],
			'post_status' => 'publish',
			'ignore_sticky_posts' => true,
			'meta_key' => 'event-start-date',
			'orderby' => 'meta_value_num menu_order',
			'order' => $vsel_widget_atts['order'],
			'posts_per_page' => $vsel_widget_atts['posts_per_page'],
			'offset' => $vsel_widget_atts['offset'],
			'meta_query' => $vsel_meta_query
		);
		$vsel_widget_future_query = new WP_Query( $vsel_query_args );

		if ( $vsel_widget_future_query->have_posts() ) :
			while( $vsel_widget_future_query->have_posts() ): $vsel_widget_future_query->the_post();
				// include event variables
				include 'vsel-variables.php';

				// include event template
				include 'vsel-widget-template.php';
			endwhile;
			// reset post data
			wp_reset_postdata();
		else:
			// if no events
			$output .= '<p class="vsel-no-events">';
			$output .= esc_attr($vsel_widget_atts['no_events_text']);
			$output .= '</p>';
		endif;
	$output .= '</div>';

	// return output
	return $output;
}
add_shortcode('vsel-widget-future-events', 'vsel_widget_future_events_shortcode');

// current events shortcode
function vsel_widget_current_events_shortcode( $vsel_widget_atts ) {
	// shortcode attributes
	$vsel_widget_atts = shortcode_atts(array(
		'class' => '',
		'date_format' => '',
		'event_cat' => '',
		'posts_per_page' => '',
		'offset' => '',
		'order' => 'ASC',
		'title_link' => '',
		'featured_image' => '',
		'featured_image_link' => '',
		'featured_image_caption' => '',
		'event_info' => '',
		'read_more' => '',
		'no_events_text' => __('There are no current events.', 'very-simple-event-list')
	), $vsel_widget_atts );

	// initialize output
	$output = '';
	// main container
	if ( empty($vsel_widget_atts['class']) ) {
		$custom_class = '';
	} else {
		$custom_class = ' '.$vsel_widget_atts['class'];
	}
	$output .= '<div id="vsel" class="vsel-widget vsel-widget-current-events'.esc_attr($custom_class).'">';
		// query
		$today = vsel_timestamp_today();
		$tomorrow = vsel_timestamp_tomorrow();
		$vsel_meta_query = array(
			'relation' => 'AND',
				array(
					'key' => 'event-start-date',
					'value' => $tomorrow,
					'compare' => '<',
					'type' => 'NUMERIC'
				),
				array(
					'key' => 'event-date',
					'value' => $today,
					'compare' => '>=',
					'type' => 'NUMERIC'
			)
		);
		$vsel_query_args = array(
			'post_type' => 'event',
			'event_cat' => $vsel_widget_atts['event_cat'],
			'post_status' => 'publish',
			'ignore_sticky_posts' => true,
			'meta_key' => 'event-start-date',
			'orderby' => 'meta_value_num menu_order',
			'order' => $vsel_widget_atts['order'],
			'posts_per_page' => $vsel_widget_atts['posts_per_page'],
			'offset' => $vsel_widget_atts['offset'],
			'meta_query' => $vsel_meta_query
		);
		$vsel_widget_current_query = new WP_Query( $vsel_query_args );

		if ( $vsel_widget_current_query->have_posts() ) :
			while( $vsel_widget_current_query->have_posts() ): $vsel_widget_current_query->the_post();
				// include event variables
				include 'vsel-variables.php';

				// include event template
				include 'vsel-widget-template.php';
			endwhile;
			// reset post data
			wp_reset_postdata();
		else:
			// if no events
			$output .= '<p class="vsel-no-events">';
			$output .= esc_attr($vsel_widget_atts['no_events_text']);
			$output .= '</p>';
		endif;
	$output .= '</div>';

	// return output
	return $output;
}
add_shortcode('vsel-widget-current-events', 'vsel_widget_current_events_shortcode');

// past events shortcode
function vsel_widget_past_events_shortcode( $vsel_widget_atts ) {
	// shortcode attributes
	$vsel_widget_atts = shortcode_atts(array(
		'class' => '',
		'date_format' => '',
		'event_cat' => '',
		'posts_per_page' => '',
		'offset' => '',
		'order' => 'DESC',
		'title_link' => '',
		'featured_image' => '',
		'featured_image_link' => '',
		'featured_image_caption' => '',
		'event_info' => '',
		'read_more' => '',
		'no_events_text' => __('There are no past events.', 'very-simple-event-list')
	), $vsel_widget_atts );

	// initialize output
	$output = '';
	// main container
	if ( empty($vsel_widget_atts['class']) ) {
		$custom_class = '';
	} else {
		$custom_class = ' '.$vsel_widget_atts['class'];
	}
	$output .= '<div id="vsel" class="vsel-widget vsel-widget-past-events'.esc_attr($custom_class).'">';
		// query
		$today = vsel_timestamp_today();
		$vsel_meta_query = array(
			'relation' => 'AND',
			array(
				'key' => 'event-date',
				'value' => $today,
				'compare' => '<',
				'type' => 'NUMERIC'
			)
		);
		$vsel_query_args = array(
			'post_type' => 'event',
			'event_cat' => $vsel_widget_atts['event_cat'],
			'post_status' => 'publish',
			'ignore_sticky_posts' => true,
			'meta_key' => 'event-start-date',
			'orderby' => 'meta_value_num menu_order',
			'order' => $vsel_widget_atts['order'],
			'posts_per_page' => $vsel_widget_atts['posts_per_page'],
			'offset' => $vsel_widget_atts['offset'],
			'meta_query' => $vsel_meta_query
		);
		$vsel_widget_past_query = new WP_Query( $vsel_query_args );

		if ( $vsel_widget_past_query->have_posts() ) :
			while( $vsel_widget_past_query->have_posts() ): $vsel_widget_past_query->the_post();
				// include event variables
				include 'vsel-variables.php';

				// include event template
				include 'vsel-widget-template.php';
			endwhile;
			// reset post data
			wp_reset_postdata();
		else:
			// if no events
			$output .= '<p class="vsel-no-events">';
			$output .= esc_attr($vsel_widget_atts['no_events_text']);
			$output .= '</p>';
		endif;
	$output .= '</div>';

	// return output
	return $output;
}
add_shortcode('vsel-widget-past-events', 'vsel_widget_past_events_shortcode');

// all events shortcode
function vsel_widget_all_events_shortcode( $vsel_widget_atts ) {
	// shortcode attributes
	$vsel_widget_atts = shortcode_atts(array(
		'class' => '',
		'date_format' => '',
		'event_cat' => '',
		'posts_per_page' => '',
		'offset' => '',
		'order' => 'DESC',
		'title_link' => '',
		'featured_image' => '',
		'featured_image_link' => '',
		'featured_image_caption' => '',
		'event_info' => '',
		'read_more' => '',
		'no_events_text' => __('There are no events.', 'very-simple-event-list')
	), $vsel_widget_atts );

	// initialize output
	$output = '';
	// main container
	if ( empty($vsel_widget_atts['class']) ) {
		$custom_class = '';
	} else {
		$custom_class = ' '.$vsel_widget_atts['class'];
	}
	$output .= '<div id="vsel" class="vsel-widget vsel-widget-all-events'.esc_attr($custom_class).'">';
		// query
		$vsel_query_args = array(
			'post_type' => 'event',
			'event_cat' => $vsel_widget_atts['event_cat'],
			'post_status' => 'publish',
			'ignore_sticky_posts' => true,
			'meta_key' => 'event-start-date',
			'orderby' => 'meta_value_num menu_order',
			'order' => $vsel_widget_atts['order'],
			'posts_per_page' => $vsel_widget_atts['posts_per_page'],
			'offset' => $vsel_widget_atts['offset']
		);
		$vsel_widget_all_query = new WP_Query( $vsel_query_args );

		if ( $vsel_widget_all_query->have_posts() ) :
			while( $vsel_widget_all_query->have_posts() ): $vsel_widget_all_query->the_post();
				// include event variables
				include 'vsel-variables.php';
				
				// include event template
				include 'vsel-widget-template.php';
			endwhile;
			// reset post data
			wp_reset_postdata();
		else:
			// if no events
			$output .= '<p class="vsel-no-events">';
			$output .= esc_attr($vsel_widget_atts['no_events_text']);
			$output .= '</p>';
		endif;
	$output .= '</div>';

	// return output
	return $output;
}
add_shortcode('vsel-widget-all-events', 'vsel_widget_all_events_shortcode');
