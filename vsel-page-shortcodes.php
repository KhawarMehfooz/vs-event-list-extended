<?php
// disable direct access
if (! defined('ABSPATH')) {
	exit;
}
// ----------------
// helpers to display the events in timeline
function vsel_process_recurring_events($post_id, $start_date, $end_date, $check_date, $recurrence_end_date) {
    $events = array();
    
    // Get time-related meta
    $all_day = get_post_meta($post_id, 'event-all-day', true);
    $hide_end_time = get_post_meta($post_id, 'event-hide-end-time', true);
    
    // Extract original time components
    $start_hour = wp_date('H', $start_date);
    $start_minute = wp_date('i', $start_date);
    $end_hour = wp_date('H', $end_date);
    $end_minute = wp_date('i', $end_date);
    
    if (!empty($recurrence_end_date)) {
        $current_date = new DateTime(date('Y-m-d', $start_date));
        $end_recurring = new DateTime($recurrence_end_date);
        
        while ($current_date <= $end_recurring) {
            $date_only = $current_date->format('Y-m-d');
            $instance_start = strtotime("$date_only $start_hour:$start_minute:00");
            $instance_end = strtotime("$date_only $end_hour:$end_minute:00");
            
            if ($instance_end >= $check_date) {
                // Format time string
                if ($all_day === 'yes') {
                    $time_string = 'All-day event';
                } else {
                    if ($hide_end_time === 'yes') {
                        $time_string = $start_hour . ':' . $start_minute;
                    } else {
                        $time_string = $start_hour . ':' . $start_minute . ' - ' . $end_hour . ':' . $end_minute;
                    }
                }

                $events[] = array(
                    'post_id' => $post_id,
                    'start_date' => $instance_start,
                    'end_date' => $instance_end,
                    'title' => get_the_title($post_id),
                    'location' => get_post_meta($post_id, 'event-location', true),
                    'time' => $time_string,
                    'recurring' => true,
                    'all_day' => $all_day === 'yes'
                );
            }
            
            $current_date->modify('+1 week');
        }
    }
    
    return $events;
}

function vsel_timeline_display($all_events) {
    $output = '';
    
    // Group events by month
    $grouped_events = array();
    foreach ($all_events as $event) {
        $month_year = date('F Y', $event['start_date']);
        if (!isset($grouped_events[$month_year])) {
            $grouped_events[$month_year] = array();
        }
        $grouped_events[$month_year][] = $event;
    }

    // Display events list / timeline
    foreach ($grouped_events as $month_year => $month_events) {
        $output .= '<h4 class="vsel-month-header">' . esc_html($month_year) . '</h4>';
        
        foreach ($month_events as $event) {
            $output .= '<div class="vsel-event-entry">';
            
            // Date section
            $output .= '<div class="vsel-event-date">';
            $output .= '<span class="vsel-event-day">' . date('d', $event['start_date']) . '</span>';
            $output .= '<span class="vsel-event-month">' . date('M', $event['start_date']) . '</span>';
            $output .= '</div>';
            
            // Info section
            $output .= '<div class="vsel-event-info">';
            $output .= '<h3 class="vsel-event-title">' . esc_html($event['title']) . '</h3>';
            
            // Time and location section
            $output .= '<div class="vsel-meta-info">';
            
            // Time display (always show time if available)
            if (!empty($event['time'])) {
                if ($event['all_day']) {
                    $output .= '<span class="vsel-event-time">All-day event</span>';
                } else {
                    $output .= '<span class="vsel-event-time">' . esc_html($event['time']) . '</span>';
                }
            }
            
            // Location display
            if (!empty($event['location'])) {
                if (!empty($event['time'])) {
                    $output .= ' <span class="vsel-meta-separator">|</span> ';
                }
                $output .= '<span class="vsel-event-location">' . esc_html($event['location']) . '</span>';
            }
            
            $output .= '</div>'; // Close vsel-meta-info
            $output .= '</div>'; // Close vsel-event-info
            $output .= '</div>'; // Close vsel-event-entry
        }
    }
    
    return $output;
}
//----------------

// upcoming events shortcode
function vsel_upcoming_events_shortcode($vsel_atts)
{
    $page_pagination = get_option('vsel-setting-98', '');
    $page_pagination_hide = get_option('vsel-setting-42', '');
    
    // shortcode attributes
    $vsel_atts = shortcode_atts(array(
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
        'pagination' => '',
        'no_events_text' => __('There are no upcoming events.', 'very-simple-event-list')
    ), $vsel_atts);

    // initialize output
    $output = '';
    $custom_class = empty($vsel_atts['class']) ? '' : ' ' . $vsel_atts['class'];
    $output .= '<div id="vsel" class="vsel-shortcode vsel-shortcode-upcoming-events' . esc_attr($custom_class) . '">';

    // query setup
    global $paged;
    $paged = get_query_var('paged') ?: get_query_var('page') ?: 1;
    $today = vsel_timestamp_today();

    // Modified meta query to include recurring events
    $vsel_meta_query = array(
        'relation' => 'OR',
        array(
            'key' => 'event-date',
            'value' => $today,
            'compare' => '>=',
            'type' => 'NUMERIC'
        ),
        array(
            'relation' => 'AND',
            array(
                'key' => 'vsel_is_recurring',
                'value' => 'yes',
                'compare' => '='
            ),
            array(
                'key' => 'vsel_recurrence_end_date',
                'value' => date('Y-m-d', $today),
                'compare' => '>=',
                'type' => 'DATE'
            )
        )
    );

    $vsel_query_args = array(
        'post_type' => 'event',
        'event_cat' => $vsel_atts['event_cat'],
        'post_status' => 'publish',
        'ignore_sticky_posts' => true,
        'meta_key' => 'event-start-date',
        'orderby' => 'meta_value_num menu_order',
        'order' => $vsel_atts['order'],
        'posts_per_page' => $vsel_atts['posts_per_page'],
        'offset' => $vsel_atts['offset'],
        'paged' => $paged,
        'meta_query' => $vsel_meta_query
    );

    $vsel_upcoming_query = new WP_Query($vsel_query_args);

    if ($vsel_upcoming_query->have_posts()) {
        $all_events = array();

        while ($vsel_upcoming_query->have_posts()) {
            $vsel_upcoming_query->the_post();
            $post_id = get_the_ID();

            // Get event details
            $is_recurring = get_post_meta($post_id, 'vsel_is_recurring', true);
            $start_date = get_post_meta($post_id, 'event-start-date', true);
            $end_date = get_post_meta($post_id, 'event-date', true);

            if ($is_recurring === 'yes') {
                $recurrence_end_date = get_post_meta($post_id, 'vsel_recurrence_end_date', true);
                $recurring_events = vsel_process_recurring_events($post_id, $start_date, $end_date, $today, $recurrence_end_date);
                $all_events = array_merge($all_events, $recurring_events);
            } else {
                if ($end_date >= $today) {
                    // Get time-related meta
                    $all_day = get_post_meta($post_id, 'event-all-day', true);
                    $hide_end_time = get_post_meta($post_id, 'event-hide-end-time', true);
                    $time_format = get_option('time_format');
                    
                    // Format time string
                    if ($all_day === 'yes') {
                        $time_string = 'All-day event';
                    } else {
                        $start_time = wp_date($time_format, $start_date);
                        $end_time = wp_date($time_format, $end_date);
                        
                        if ($hide_end_time === 'yes') {
                            $time_string = $start_time;
                        } else {
                            $time_string = $start_time . ' - ' . $end_time;
                        }
                    }

                    $all_events[] = array(
                        'post_id' => $post_id,
                        'start_date' => $start_date,
                        'end_date' => $end_date,
                        'title' => get_the_title(),
                        'location' => get_post_meta($post_id, 'event-location', true),
                        'time' => $time_string,
                        'all_day' => $all_day === 'yes',
                        'recurring' => false
                    );
                }
            }
        }

        // Sort events by start date
        usort($all_events, function ($a, $b) {
            return $a['start_date'] - $b['start_date'];
        });

        // Use helper function for display
        $output .= vsel_timeline_display($all_events);

        // Pagination
        if (empty($vsel_atts['offset']) && $vsel_atts['pagination'] !== 'false') {
            if ($page_pagination_hide !== 'yes') {
                if ($page_pagination == 'numeric') {
                    $output .= '<div class="vsel-nav-numeric">';
                    $output .= paginate_links(array(
                        'total' => $vsel_upcoming_query->max_num_pages,
                        'next_text' => __('Next &raquo;', 'very-simple-event-list'),
                        'prev_text' => __('&laquo; Previous', 'very-simple-event-list')
                    ));
                    $output .= '</div>';
                } else {
                    $output .= '<div class="vsel-nav">';
                    $output .= get_next_posts_link(__('Next &raquo;', 'very-simple-event-list'), $vsel_upcoming_query->max_num_pages);
                    $output .= get_previous_posts_link(__('&laquo; Previous', 'very-simple-event-list'));
                    $output .= '</div>';
                }
            }
        }

        wp_reset_postdata();
    }

    $output .= '</div>';
    return $output;
}
add_shortcode('vsel', 'vsel_upcoming_events_shortcode');

function vsel_future_events_shortcode($vsel_atts) {
    $page_pagination = get_option('vsel-setting-98', '');
    $page_pagination_hide = get_option('vsel-setting-42', '');
    
    // shortcode attributes
    $vsel_atts = shortcode_atts(array(
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
        'pagination' => '',
        'no_events_text' => __('There are no future events.', 'very-simple-event-list')
    ), $vsel_atts);

    // initialize output
    $output = '';
    $custom_class = empty($vsel_atts['class']) ? '' : ' ' . $vsel_atts['class'];
    $output .= '<div id="vsel" class="vsel-shortcode vsel-shortcode-future-events' . esc_attr($custom_class) . '">';

    // query setup
    global $paged;
    $paged = get_query_var('paged') ?: get_query_var('page') ?: 1;
    $tomorrow = vsel_timestamp_tomorrow();

    // Modified meta query to include recurring events
    $vsel_meta_query = array(
        'relation' => 'OR',
        array(
            'key' => 'event-start-date',
            'value' => $tomorrow,
            'compare' => '>=',
            'type' => 'NUMERIC'
        ),
        array(
            'relation' => 'AND',
            array(
                'key' => 'vsel_is_recurring',
                'value' => 'yes',
                'compare' => '='
            ),
            array(
                'key' => 'vsel_recurrence_end_date',
                'value' => date('Y-m-d', $tomorrow),
                'compare' => '>=',
                'type' => 'DATE'
            )
        )
    );

    $vsel_query_args = array(
        'post_type' => 'event',
        'event_cat' => $vsel_atts['event_cat'],
        'post_status' => 'publish',
        'ignore_sticky_posts' => true,
        'meta_key' => 'event-start-date',
        'orderby' => 'meta_value_num menu_order',
        'order' => $vsel_atts['order'],
        'posts_per_page' => $vsel_atts['posts_per_page'],
        'offset' => $vsel_atts['offset'],
        'paged' => $paged,
        'meta_query' => $vsel_meta_query
    );

    $vsel_future_query = new WP_Query($vsel_query_args);

    if ($vsel_future_query->have_posts()) {
        $all_events = array();

        while ($vsel_future_query->have_posts()) {
            $vsel_future_query->the_post();
            $post_id = get_the_ID();

            // Get event details
            $is_recurring = get_post_meta($post_id, 'vsel_is_recurring', true);
            $start_date = get_post_meta($post_id, 'event-start-date', true);
            $end_date = get_post_meta($post_id, 'event-date', true);
            $all_day = get_post_meta($post_id, 'event-all-day', true);
            $hide_end_time = get_post_meta($post_id, 'event-hide-end-time', true);

            if ($is_recurring === 'yes') {
                $recurrence_end_date = get_post_meta($post_id, 'vsel_recurrence_end_date', true);
                $recurring_events = vsel_process_recurring_events($post_id, $start_date, $end_date, $tomorrow, $recurrence_end_date);
                $all_events = array_merge($all_events, $recurring_events);
            } else {
                if ($start_date >= $tomorrow) {
                    // Format time string for non-recurring events
                    if ($all_day === 'yes') {
                        $time_string = 'All-day event';
                    } else {
                        $start_hour = wp_date('H', $start_date);
                        $start_minute = wp_date('i', $start_date);
                        $end_hour = wp_date('H', $end_date);
                        $end_minute = wp_date('i', $end_date);

                        if ($hide_end_time === 'yes') {
                            $time_string = $start_hour . ':' . $start_minute;
                        } else {
                            $time_string = $start_hour . ':' . $start_minute . ' - ' . $end_hour . ':' . $end_minute;
                        }
                    }

                    $all_events[] = array(
                        'post_id' => $post_id,
                        'start_date' => $start_date,
                        'end_date' => $end_date,
                        'title' => get_the_title(),
                        'location' => get_post_meta($post_id, 'event-location', true),
                        'time' => $time_string,
                        'all_day' => $all_day === 'yes',
                        'recurring' => false
                    );
                }
            }
        }
		usort($all_events, function ($a, $b) {
            return $a['start_date'] - $b['start_date'];
        });

        // Use the common display function
        $output .= vsel_timeline_display($all_events);
	}

    $output .= '</div>';
    return $output;
}
add_shortcode('vsel-future-events', 'vsel_future_events_shortcode');

// current events shortcode
function vsel_current_events_shortcode($vsel_atts) {
    $page_pagination = get_option('vsel-setting-98', '');
    $page_pagination_hide = get_option('vsel-setting-42', '');
    
    // shortcode attributes
    $vsel_atts = shortcode_atts(array(
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
        'pagination' => '',
        'no_events_text' => __('There are no current events.', 'very-simple-event-list')
    ), $vsel_atts);

    // initialize output
    $output = '';
    $custom_class = empty($vsel_atts['class']) ? '' : ' ' . $vsel_atts['class'];
    $output .= '<div id="vsel" class="vsel-shortcode vsel-shortcode-current-events' . esc_attr($custom_class) . '">';

    // query setup
    global $paged;
    $paged = get_query_var('paged') ?: get_query_var('page') ?: 1;
    $today = vsel_timestamp_today();
    $tomorrow = vsel_timestamp_tomorrow();

    // Modified meta query to include recurring events
    $vsel_meta_query = array(
        'relation' => 'OR',
        // Regular events that are current
        array(
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
        ),
        // Recurring events
        array(
            'relation' => 'AND',
            array(
                'key' => 'vsel_is_recurring',
                'value' => 'yes',
                'compare' => '='
            ),
            array(
                'key' => 'vsel_recurrence_end_date',
                'value' => date('Y-m-d', $today),
                'compare' => '>=',
                'type' => 'DATE'
            )
        )
    );

    $vsel_query_args = array(
        'post_type' => 'event',
        'event_cat' => $vsel_atts['event_cat'],
        'post_status' => 'publish',
        'ignore_sticky_posts' => true,
        'meta_key' => 'event-start-date',
        'orderby' => 'meta_value_num menu_order',
        'order' => $vsel_atts['order'],
        'posts_per_page' => $vsel_atts['posts_per_page'],
        'offset' => $vsel_atts['offset'],
        'paged' => $paged,
        'meta_query' => $vsel_meta_query
    );

    $vsel_current_query = new WP_Query($vsel_query_args);

    if ($vsel_current_query->have_posts()) {
        $all_events = array();

        while ($vsel_current_query->have_posts()) {
            $vsel_current_query->the_post();
            $post_id = get_the_ID();

            // Get event details
            $is_recurring = get_post_meta($post_id, 'vsel_is_recurring', true);
            $start_date = get_post_meta($post_id, 'event-start-date', true);
            $end_date = get_post_meta($post_id, 'event-date', true);

            if ($is_recurring === 'yes') {
                $recurrence_end_date = get_post_meta($post_id, 'vsel_recurrence_end_date', true);
                $recurring_events = vsel_process_recurring_events($post_id, $start_date, $end_date, $today, $recurrence_end_date);
                
                // Filter out non-current events
                $current_recurring_events = array_filter($recurring_events, function($event) use ($today, $tomorrow) {
                    return $event['start_date'] < $tomorrow && $event['end_date'] >= $today;
                });
                
                $all_events = array_merge($all_events, $current_recurring_events);
            } else {
                if ($start_date < $tomorrow && $end_date >= $today) {
                    // Get time-related meta
                    $all_day = get_post_meta($post_id, 'event-all-day', true);
                    $hide_end_time = get_post_meta($post_id, 'event-hide-end-time', true);
                    $time_format = get_option('time_format');
                    
                    // Format time string
                    if ($all_day === 'yes') {
                        $time_string = 'All-day event';
                    } else {
                        $start_time = wp_date($time_format, $start_date);
                        $end_time = wp_date($time_format, $end_date);
                        
                        if ($hide_end_time === 'yes') {
                            $time_string = $start_time;
                        } else {
                            $time_string = $start_time . ' - ' . $end_time;
                        }
                    }

                    $all_events[] = array(
                        'post_id' => $post_id,
                        'start_date' => $start_date,
                        'end_date' => $end_date,
                        'title' => get_the_title(),
                        'location' => get_post_meta($post_id, 'event-location', true),
                        'time' => $time_string,
                        'all_day' => $all_day === 'yes',
                        'recurring' => false
                    );
                }
            }
        }

        // Sort events by start date
        usort($all_events, function ($a, $b) {
            return $a['start_date'] - $b['start_date'];
        });

        // Use helper function for display
        $output .= vsel_timeline_display($all_events);

        // Pagination
        if (empty($vsel_atts['offset']) && $vsel_atts['pagination'] !== 'false') {
            if ($page_pagination_hide !== 'yes') {
                if ($page_pagination == 'numeric') {
                    $output .= '<div class="vsel-nav-numeric">';
                    $output .= paginate_links(array(
                        'total' => $vsel_current_query->max_num_pages,
                        'next_text' => __('Next &raquo;', 'very-simple-event-list'),
                        'prev_text' => __('&laquo; Previous', 'very-simple-event-list')
                    ));
                    $output .= '</div>';
                } else {
                    $output .= '<div class="vsel-nav">';
                    $output .= get_next_posts_link(__('Next &raquo;', 'very-simple-event-list'), $vsel_current_query->max_num_pages);
                    $output .= get_previous_posts_link(__('&laquo; Previous', 'very-simple-event-list'));
                    $output .= '</div>';
                }
            }
        }

        wp_reset_postdata();
    } else {
        $output .= '<p class="vsel-no-events">' . esc_attr($vsel_atts['no_events_text']) . '</p>';
    }

    $output .= '</div>';
    return $output;
}
add_shortcode('vsel-current-events', 'vsel_current_events_shortcode');

// past events shortcode
function vsel_past_events_shortcode($vsel_atts) {
    $page_pagination = get_option('vsel-setting-98', '');
    $page_pagination_hide = get_option('vsel-setting-42', '');
    
    // shortcode attributes
    $vsel_atts = shortcode_atts(array(
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
        'pagination' => '',
        'no_events_text' => __('There are no past events.', 'very-simple-event-list')
    ), $vsel_atts);

    // initialize output
    $output = '';
    $custom_class = empty($vsel_atts['class']) ? '' : ' ' . $vsel_atts['class'];
    $output .= '<div id="vsel" class="vsel-shortcode vsel-shortcode-past-events' . esc_attr($custom_class) . '">';

    // query setup
    global $paged;
    $paged = get_query_var('paged') ?: get_query_var('page') ?: 1;
    $today = vsel_timestamp_today();

    // Modified meta query to include past recurring events
    $vsel_meta_query = array(
        'relation' => 'OR',
        array(
            'key' => 'event-date',
            'value' => $today,
            'compare' => '<',
            'type' => 'NUMERIC'
        ),
        array(
            'relation' => 'AND',
            array(
                'key' => 'vsel_is_recurring',
                'value' => 'yes',
                'compare' => '='
            ),
            array(
                'key' => 'event-start-date',
                'value' => 0,
                'compare' => '>',
                'type' => 'NUMERIC'
            )
        )
    );

    $vsel_query_args = array(
        'post_type' => 'event',
        'event_cat' => $vsel_atts['event_cat'],
        'post_status' => 'publish',
        'ignore_sticky_posts' => true,
        'meta_key' => 'event-start-date',
        'orderby' => 'meta_value_num menu_order',
        'order' => $vsel_atts['order'],
        'posts_per_page' => $vsel_atts['posts_per_page'],
        'offset' => $vsel_atts['offset'],
        'paged' => $paged,
        'meta_query' => $vsel_meta_query
    );

    $vsel_past_query = new WP_Query($vsel_query_args);

    if ($vsel_past_query->have_posts()) {
        $all_events = array();

        while ($vsel_past_query->have_posts()) {
            $vsel_past_query->the_post();
            $post_id = get_the_ID();

            // Get event details
            $is_recurring = get_post_meta($post_id, 'vsel_is_recurring', true);
            $start_date = get_post_meta($post_id, 'event-start-date', true);
            $end_date = get_post_meta($post_id, 'event-date', true);

            if ($is_recurring === 'yes') {
                $recurrence_end_date = get_post_meta($post_id, 'vsel_recurrence_end_date', true);
                $recurring_events = vsel_process_recurring_events($post_id, $start_date, $end_date, 0, $recurrence_end_date);
                
                // Filter out non-past events
                $past_recurring_events = array_filter($recurring_events, function($event) use ($today) {
                    return $event['end_date'] < $today;
                });
                
                $all_events = array_merge($all_events, $past_recurring_events);
            } else {
                if ($end_date < $today) {
                    // Get time-related meta
                    $all_day = get_post_meta($post_id, 'event-all-day', true);
                    $hide_end_time = get_post_meta($post_id, 'event-hide-end-time', true);
                    $time_format = get_option('time_format');
                    
                    // Format time string
                    if ($all_day === 'yes') {
                        $time_string = 'All-day event';
                    } else {
                        $start_time = wp_date($time_format, $start_date);
                        $end_time = wp_date($time_format, $end_date);
                        
                        if ($hide_end_time === 'yes') {
                            $time_string = $start_time;
                        } else {
                            $time_string = $start_time . ' - ' . $end_time;
                        }
                    }

                    $all_events[] = array(
                        'post_id' => $post_id,
                        'start_date' => $start_date,
                        'end_date' => $end_date,
                        'title' => get_the_title(),
                        'location' => get_post_meta($post_id, 'event-location', true),
                        'time' => $time_string,
                        'all_day' => $all_day === 'yes',
                        'recurring' => false
                    );
                }
            }
        }

        // Sort events by start date (descending for past events)
        usort($all_events, function ($a, $b) {
            return $b['start_date'] - $a['start_date']; // Note the reversed comparison
        });

        // Use helper function for display
        $output .= vsel_timeline_display($all_events);

        // Pagination
        if (empty($vsel_atts['offset']) && $vsel_atts['pagination'] !== 'false') {
            if ($page_pagination_hide !== 'yes') {
                if ($page_pagination == 'numeric') {
                    $output .= '<div class="vsel-nav-numeric">';
                    $output .= paginate_links(array(
                        'total' => $vsel_past_query->max_num_pages,
                        'next_text' => __('Next &raquo;', 'very-simple-event-list'),
                        'prev_text' => __('&laquo; Previous', 'very-simple-event-list')
                    ));
                    $output .= '</div>';
                } else {
                    $output .= '<div class="vsel-nav">';
                    $output .= get_next_posts_link(__('Next &raquo;', 'very-simple-event-list'), $vsel_past_query->max_num_pages);
                    $output .= get_previous_posts_link(__('&laquo; Previous', 'very-simple-event-list'));
                    $output .= '</div>';
                }
            }
        }

        wp_reset_postdata();
    } else {
        $output .= '<p class="vsel-no-events">' . esc_attr($vsel_atts['no_events_text']) . '</p>';
    }

    $output .= '</div>';
    return $output;
}
add_shortcode('vsel-past-events', 'vsel_past_events_shortcode');

// all events shortcode
function vsel_all_events_shortcode($vsel_atts) {
    $page_pagination = get_option('vsel-setting-98', '');
    $page_pagination_hide = get_option('vsel-setting-42', '');
    
    // shortcode attributes
    $vsel_atts = shortcode_atts(array(
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
        'pagination' => '',
        'no_events_text' => __('There are no events.', 'very-simple-event-list')
    ), $vsel_atts);

    // initialize output
    $output = '';
    $custom_class = empty($vsel_atts['class']) ? '' : ' ' . $vsel_atts['class'];
    $output .= '<div id="vsel" class="vsel-shortcode vsel-shortcode-all-events' . esc_attr($custom_class) . '">';

    // query setup
    global $paged;
    $paged = get_query_var('paged') ?: get_query_var('page') ?: 1;

    // For recurring events, we need to include them in the query
    $vsel_meta_query = array(
        'relation' => 'OR',
        array(
            'key' => 'event-start-date',
            'compare' => 'EXISTS',
            'type' => 'NUMERIC'
        ),
        array(
            'key' => 'vsel_is_recurring',
            'value' => 'yes',
            'compare' => '='
        )
    );

    $vsel_query_args = array(
        'post_type' => 'event',
        'event_cat' => $vsel_atts['event_cat'],
        'post_status' => 'publish',
        'ignore_sticky_posts' => true,
        'meta_key' => 'event-start-date',
        'orderby' => 'meta_value_num menu_order',
        'order' => $vsel_atts['order'],
        'posts_per_page' => $vsel_atts['posts_per_page'],
        'offset' => $vsel_atts['offset'],
        'paged' => $paged,
        'meta_query' => $vsel_meta_query
    );

    $vsel_all_query = new WP_Query($vsel_query_args);

    if ($vsel_all_query->have_posts()) {
        $all_events = array();

        while ($vsel_all_query->have_posts()) {
            $vsel_all_query->the_post();
            $post_id = get_the_ID();

            // Get event details
            $is_recurring = get_post_meta($post_id, 'vsel_is_recurring', true);
            $start_date = get_post_meta($post_id, 'event-start-date', true);
            $end_date = get_post_meta($post_id, 'event-date', true);

            if ($is_recurring === 'yes') {
                $recurrence_end_date = get_post_meta($post_id, 'vsel_recurrence_end_date', true);
                $recurring_events = vsel_process_recurring_events($post_id, $start_date, $end_date, 0, $recurrence_end_date);
                $all_events = array_merge($all_events, $recurring_events);
            } else {
                // Get time-related meta
                $all_day = get_post_meta($post_id, 'event-all-day', true);
                $hide_end_time = get_post_meta($post_id, 'event-hide-end-time', true);
                $time_format = get_option('time_format');
                
                // Format time string
                if ($all_day === 'yes') {
                    $time_string = 'All-day event';
                } else {
                    $start_time = wp_date($time_format, $start_date);
                    $end_time = wp_date($time_format, $end_date);
                    
                    if ($hide_end_time === 'yes') {
                        $time_string = $start_time;
                    } else {
                        $time_string = $start_time . ' - ' . $end_time;
                    }
                }

                $all_events[] = array(
                    'post_id' => $post_id,
                    'start_date' => $start_date,
                    'end_date' => $end_date,
                    'title' => get_the_title(),
                    'location' => get_post_meta($post_id, 'event-location', true),
                    'time' => $time_string,
                    'all_day' => $all_day === 'yes',
                    'recurring' => false
                );
            }
        }

        // Sort events by start date
        usort($all_events, function ($a, $b) use ($vsel_atts) {
            if ($vsel_atts['order'] === 'ASC') {
                return $a['start_date'] - $b['start_date'];
            }
            return $b['start_date'] - $a['start_date'];
        });

        // Use helper function for display
        $output .= vsel_timeline_display($all_events);

        // Pagination
        if (empty($vsel_atts['offset']) && $vsel_atts['pagination'] !== 'false') {
            if ($page_pagination_hide !== 'yes') {
                if ($page_pagination == 'numeric') {
                    $output .= '<div class="vsel-nav-numeric">';
                    $output .= paginate_links(array(
                        'total' => $vsel_all_query->max_num_pages,
                        'next_text' => __('Next &raquo;', 'very-simple-event-list'),
                        'prev_text' => __('&laquo; Previous', 'very-simple-event-list')
                    ));
                    $output .= '</div>';
                } else {
                    $output .= '<div class="vsel-nav">';
                    $output .= get_next_posts_link(__('Next &raquo;', 'very-simple-event-list'), $vsel_all_query->max_num_pages);
                    $output .= get_previous_posts_link(__('&laquo; Previous', 'very-simple-event-list'));
                    $output .= '</div>';
                }
            }
        }

        wp_reset_postdata();
    } else {
        $output .= '<p class="vsel-no-events">' . esc_attr($vsel_atts['no_events_text']) . '</p>';
    }

    $output .= '</div>';
    return $output;
}
add_shortcode('vsel-all-events', 'vsel_all_events_shortcode');