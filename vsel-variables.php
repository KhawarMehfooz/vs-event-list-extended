<?php
// disable direct access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// global variables

// get utc timezone
$utc_timezone = vsel_utc_timezone();

// get default date format
$date_format_default = get_option('date_format');

// get setting for custom date format
$date_format_settings_page = get_option('vsel-setting-38');

// set global date format
if ( !empty($date_format_settings_page) ) {
	$template_date_format = $date_format_settings_page;
} else {
	$template_date_format = $date_format_default;
}

// get default time format
$time_format_default = get_option('time_format');

// get setting for custom time format
$time_format_settings_page = get_option('vsel-setting-92');

// set global time format
if ( !empty($time_format_settings_page) ) {
	$template_time_format = $time_format_settings_page;
} else {
	$template_time_format = $time_format_default;
}

// get setting for one time field instead of start time and end time
$one_time_field = get_option('vsel-setting-87');

// get setting for hiding equal start time and end time
$hide_equal_time = get_option('vsel-setting-94');

// get settings for date, time and cat separator
$date_separator = get_option('vsel-setting-69');
$time_separator = get_option('vsel-setting-88');
$cat_separator = get_option('vsel-setting-70');

// date, time and cat separator
if (empty($date_separator)) {
	$date_separator = '-';
}
if (empty($time_separator)) {
	$time_separator = '-';
}
if (empty($cat_separator)) {
	$cat_separator = '|';
}

// get settings to disable theme template support
$disable_single_template = get_option('vsel-setting-39');
$disable_category_template = get_option('vsel-setting-40');
$disable_post_type_template = get_option('vsel-setting-43');
$disable_search_template = get_option('vsel-setting-41');

// get event details
$start_date_timestamp = get_post_meta( get_the_ID(), 'event-start-date', true );
$end_date_timestamp = get_post_meta( get_the_ID(), 'event-date', true );
$time = get_post_meta( get_the_ID(), 'event-time', true );
$hide_end_time = get_post_meta( get_the_ID(), 'event-hide-end-time', true );
$all_day_event = get_post_meta( get_the_ID(), 'event-all-day', true );
$location = get_post_meta( get_the_ID(), 'event-location', true );
$more_info_link = get_post_meta( get_the_ID(), 'event-link', true );
$more_info_link_label = get_post_meta( get_the_ID(), 'event-link-label', true );
$more_info_link_target = get_post_meta( get_the_ID(), 'event-link-target', true );
$redirect_title_to_more_info = get_post_meta( get_the_ID(), 'event-link-title', true );
$redirect_image_to_more_info = get_post_meta( get_the_ID(), 'event-link-image', true );
$summary = get_post_meta( get_the_ID(), 'event-summary', true );

// get start date and end date for comparing dates
$start_date = gmdate( 'Ymd', intval($start_date_timestamp) );
$end_date = gmdate( 'Ymd', intval($end_date_timestamp) );

// get start time and end time for comparing times
$start_time = gmdate( 'Hi', intval($start_date_timestamp) );
$end_time = gmdate( 'Hi', intval($end_date_timestamp) );

// set more info link label
if (empty($more_info_link_label)) {
	$more_info_link_label = __( 'More info', 'very-simple-event-list' );
}

// set more info link target
if ($more_info_link_target == 'yes') {
	$more_info_link_target = 'rel="noopener noreferrer" target="_blank"';
} else {
	$more_info_link_target = 'rel="noreferrer" target="_self"';
}

// page variables

// set date format
if ( !empty($vsel_atts['date_format']) ) {
	$page_date_format = $vsel_atts['date_format'];
} else {
	$page_date_format = $template_date_format;
}

// get custom labels from settings page
$page_date_label = get_option('vsel-setting-16');
$page_start_label = get_option('vsel-setting-17');
$page_end_label = get_option('vsel-setting-18');
$page_time_label = get_option('vsel-setting-19');
$page_all_day_label = get_option('vsel-setting-89');
$page_location_label = get_option('vsel-setting-20');
$page_read_more_label = get_option('vsel-setting-102');

// get setting for title tag
$page_title_tag  = get_option('vsel-setting-95');

// get setting to show date label or icon
$page_date_type  = get_option('vsel-setting-62');

// get setting to display date icon next to other event details
$page_meta_combine = get_option('vsel-setting-68');

// get setting to combine dates on the same line
$page_date_combine = get_option('vsel-setting-15');

// get setting to show all event info or summary
$page_event_info = get_option('vsel-setting-13');

// get setting for acf fields
$page_acf_fields = get_option('vsel-setting-51');

// get setting to show read more link
$page_read_more = get_option('vsel-setting-101');

// get setting to relocate title
$page_title_location = get_option('vsel-setting-59');

// get settings to link title and featured image to event page
$page_link_title = get_option('vsel-setting-9');
$page_link_image = get_option('vsel-setting-29');

// get setting to link category to category page
$page_link_cat = get_option('vsel-setting-44');

// get settings for event layout
$page_meta_location = get_option('vsel-setting-35');
$page_image_location = get_option('vsel-setting-36');
$page_meta_width = get_option('vsel-setting-66');

// get setting to set featured image size
$page_image_size = get_option('vsel-setting-30');

// get setting to set featured image max width
$page_image_width = get_option('vsel-setting-53');

// get setting for pagination
$page_pagination = get_option('vsel-setting-98');

// get settings to hide elements
$page_title_hide = get_option('vsel-setting-64');
$page_date_hide = get_option('vsel-setting-8');
$page_time_hide = get_option('vsel-setting-11');
$page_location_hide = get_option('vsel-setting-12');
$page_image_hide = get_option('vsel-setting-27');
$page_info_hide = get_option('vsel-setting-28');
$page_link_hide = get_option('vsel-setting-10');
$page_cats_hide = get_option('vsel-setting-33');
$page_pagination_hide = get_option('vsel-setting-42');

// show default label if no custom label is set
if (empty($page_date_label)) {
	$page_date_label = __( 'Date: %s', 'very-simple-event-list' );
}
if (empty($page_start_label)) {
	$page_start_label = __( 'Start date: %s', 'very-simple-event-list' );
}
if (empty($page_end_label)) {
	$page_end_label = __( 'End date: %s', 'very-simple-event-list' );
}
if (empty($page_time_label)) {
	$page_time_label = __( 'Time: %s', 'very-simple-event-list' );
}
if (empty($page_all_day_label)) {
	$page_all_day_label = __( 'All-day event', 'very-simple-event-list' );
}
if (empty($page_location_label)) {
	$page_location_label = __( 'Location: %s', 'very-simple-event-list' );
}
if (empty($page_read_more_label)) {
	$page_read_more_label = __( 'Read more', 'very-simple-event-list' );
}

// set title tag
if ($page_title_tag == 'h2') {
	$page_title_tag_start = '<h2 class="vsel-meta-title">';
	$page_title_tag_end = '</h2>';
} elseif ($page_title_tag == 'h4') {
	$page_title_tag_start = '<h4 class="vsel-meta-title">';
	$page_title_tag_end = '</h4>';
} elseif ($page_title_tag == 'div') {
	$page_title_tag_start = '<div class="vsel-meta-title">';
	$page_title_tag_end = '</div>';
} else {
	$page_title_tag_start = '<h3 class="vsel-meta-title">';
	$page_title_tag_end = '</h3>';
}

// set title
if ( $page_title_hide == 'yes' ) {
	$page_event_title = '';
} else {
	if ( !empty($vsel_atts['title_link']) && ($vsel_atts['title_link'] == 'false') ) {
		$page_event_title = $page_title_tag_start.get_the_title().$page_title_tag_end;
	} else {
		if ( ($redirect_title_to_more_info == 'yes') && !empty($more_info_link) ) {
			$page_event_title = $page_title_tag_start.'<a href="'.esc_url($more_info_link).'" rel="noopener noreferrer" '.$more_info_link_target.' title="'.esc_url($more_info_link).'">'.get_the_title().'</a>'.$page_title_tag_end;
		} elseif ($page_link_title == 'yes') {
			$page_event_title =  $page_title_tag_start.'<a href="'.get_permalink().'" rel="bookmark" title="'.get_the_title().'">'.get_the_title().'</a>'.$page_title_tag_end;
		} else {
			$page_event_title = $page_title_tag_start.get_the_title().$page_title_tag_end;
		}
	}
}

// set size for featured image
if ($page_image_size == 'small') {
	$page_image_source = 'thumbnail';
} elseif ($page_image_size == 'medium') {
	$page_image_source = 'medium';
} elseif ($page_image_size == 'large') {
	$page_image_source = 'large';
} elseif ($page_image_size == 'full') {
	$page_image_source = 'full';
} else {
	$page_image_source = 'post-thumbnail';
}

// set max width for featured image
if (!empty($page_image_width) && is_numeric($page_image_width) && ($page_image_width > 19) && ($page_image_width < 101) ) {
	if ($page_image_width == '100') {
		$page_image_max_width = 'max-width:100%; float:none; margin-left:0; margin-right:0; box-sizing:border-box;';
	} else {
		$page_image_max_width = 'max-width:'.$page_image_width.'%;';
	}
} else {
	$page_image_max_width = 'max-width:40%';
}

// set css class for featured image
if ($page_image_location == 'left') {
	$page_img_class = 'vsel-alignleft';
} else {
	$page_img_class = 'vsel-alignright';
}

// set width for event details and event info block
if ( ($page_image_hide == 'yes') && ($page_info_hide == 'yes') ) {
	$page_meta_width = 'width:100%; box-sizing:border-box;';
	$page_info_block_width = '';
} else {
	if (!empty($page_meta_width) && is_numeric($page_meta_width) && ($page_meta_width > 19) && ($page_meta_width < 61) ) {
		$page_content_width_default = 96;
		$page_content_width = $page_content_width_default - $page_meta_width;
		$page_meta_width = 'width:'.$page_meta_width.'%;';
		$page_info_block_width = 'width:'.$page_content_width.'%;';
	} else {
		$page_meta_width = 'width:36%;';
		$page_info_block_width = 'width:60%;';
	}
}

// set css class for event details and event info block
if ( ($page_image_hide == 'yes') && ($page_info_hide == 'yes') ) {
	$page_meta_class = 'vsel-meta';
	$page_info_block_class = '';
} else {
	if ($page_meta_location == 'right') {
		$page_meta_class = 'vsel-meta vsel-alignright';
		$page_info_block_class = 'vsel-info-block vsel-alignleft';
	} else {
		$page_meta_class = 'vsel-meta vsel-alignleft';
		$page_info_block_class = 'vsel-info-block vsel-alignright';
	}
}

// combine width and css class for event details and event info block
$page_meta_start = '<div class="'.$page_meta_class.'" style="'.$page_meta_width.'">';
$page_meta_end = '</div>';
$page_info_block_start = '<div class="'.$page_info_block_class.'" style="'.$page_info_block_width.'">';
$page_info_block_end = '</div>';

// show date label or icon
$page_start_default = sprintf(esc_attr($page_start_label), '<span>'.wp_date( esc_attr($page_date_format), esc_attr($start_date_timestamp), $utc_timezone ).'</span>' );
$page_end_default = sprintf(esc_attr($page_end_label), '<span>'.wp_date( esc_attr($page_date_format), esc_attr($end_date_timestamp), $utc_timezone ).'</span>' );
$page_same_default = sprintf(esc_attr($page_date_label), '<span>'.wp_date( esc_attr($page_date_format), esc_attr($end_date_timestamp), $utc_timezone ).'</span>' );
$page_start_icon_1 = '<span class="vsel-day vsel-day-top">'.wp_date( 'j', esc_attr($start_date_timestamp), $utc_timezone ).'</span><span class="vsel-month">'.wp_date( 'M', esc_attr($start_date_timestamp), $utc_timezone ).'</span><span class="vsel-year">'.wp_date( 'Y', esc_attr($start_date_timestamp), $utc_timezone ).'</span>';
$page_start_icon_2 = '<span class="vsel-month vsel-month-top">'.wp_date( 'M', esc_attr($start_date_timestamp), $utc_timezone ).'</span><span class="vsel-day">'.wp_date( 'j', esc_attr($start_date_timestamp), $utc_timezone ).'</span><span class="vsel-year">'.wp_date( 'Y', esc_attr($start_date_timestamp), $utc_timezone ).'</span>';
$page_end_icon_1 = '<span class="vsel-day vsel-day-top">'.wp_date( 'j', esc_attr($end_date_timestamp), $utc_timezone ).'</span><span class="vsel-month">'.wp_date( 'M', esc_attr($end_date_timestamp), $utc_timezone ).'</span><span class="vsel-year">'.wp_date( 'Y', esc_attr($end_date_timestamp), $utc_timezone ).'</span>';
$page_end_icon_2 = '<span class="vsel-month vsel-month-top">'.wp_date( 'M', esc_attr($end_date_timestamp), $utc_timezone ).'</span><span class="vsel-day">'.wp_date( 'j', esc_attr($end_date_timestamp), $utc_timezone ).'</span><span class="vsel-year">'.wp_date( 'Y', esc_attr($end_date_timestamp), $utc_timezone ).'</span>';

// widget variables

// set date format
if ( !empty($vsel_widget_atts['date_format']) ) {
	$widget_date_format = $vsel_widget_atts['date_format'];
} else {
	$widget_date_format = $template_date_format;
}

// get custom labels from settings page
$widget_date_label = get_option('vsel-setting-22');
$widget_start_label = get_option('vsel-setting-23');
$widget_end_label = get_option('vsel-setting-24');
$widget_time_label = get_option('vsel-setting-25');
$widget_all_day_label = get_option('vsel-setting-90');
$widget_location_label = get_option('vsel-setting-26');
$widget_read_more_label = get_option('vsel-setting-104');

// get setting for title tag
$widget_title_tag  = get_option('vsel-setting-96');

// get setting to show date label or icon
$widget_date_type  = get_option('vsel-setting-63');

// get setting to display date icon next to other event details
$widget_meta_combine = get_option('vsel-setting-67');

// get setting to combine dates on the same line
$widget_date_combine = get_option('vsel-setting-21');

// get setting to show all event info or summary
$widget_event_info = get_option('vsel-setting-1');

// get setting for acf fields
$widget_acf_fields = get_option('vsel-setting-52');

// get setting to show read more link
$widget_read_more = get_option('vsel-setting-103');

// get settings to link title and featured image to event page
$widget_link_title = get_option('vsel-setting-14');
$widget_link_image = get_option('vsel-setting-31');

// get setting to link category to category page
$widget_link_cat = get_option('vsel-setting-45');

// get setting for event layout
$widget_image_location = get_option('vsel-setting-37');

// get setting to set featured image size
$widget_image_size = get_option('vsel-setting-32');

// get setting to set featured image max width
$widget_image_width = get_option('vsel-setting-54');

// get settings to hide elements
$widget_title_hide = get_option('vsel-setting-65');
$widget_date_hide = get_option('vsel-setting-2');
$widget_time_hide = get_option('vsel-setting-3');
$widget_location_hide = get_option('vsel-setting-4');
$widget_image_hide = get_option('vsel-setting-5');
$widget_info_hide = get_option('vsel-setting-7');
$widget_link_hide = get_option('vsel-setting-6');
$widget_cats_hide = get_option('vsel-setting-34');

// show default label if no custom label is set
if (empty($widget_date_label)) {
	$widget_date_label = __( 'Date: %s', 'very-simple-event-list' );
}
if (empty($widget_start_label)) {
	$widget_start_label = __( 'Start date: %s', 'very-simple-event-list' );
}
if (empty($widget_end_label)) {
	$widget_end_label = __( 'End date: %s', 'very-simple-event-list' );
}
if (empty($widget_time_label)) {
	$widget_time_label = __( 'Time: %s', 'very-simple-event-list' );
}
if (empty($widget_all_day_label)) {
	$widget_all_day_label = __( 'All-day event', 'very-simple-event-list' );
}
if (empty($widget_location_label)) {
	$widget_location_label = __( 'Location: %s', 'very-simple-event-list' );
}
if (empty($widget_read_more_label)) {
	$widget_read_more_label = __( 'Read more', 'very-simple-event-list' );
}

// set title tag
if ($widget_title_tag == 'h2') {
	$widget_title_tag_start = '<h2 class="vsel-meta-title">';
	$widget_title_tag_end = '</h2>';
} elseif ($widget_title_tag == 'h4') {
	$widget_title_tag_start = '<h4 class="vsel-meta-title">';
	$widget_title_tag_end = '</h4>';
} elseif ($widget_title_tag == 'div') {
	$widget_title_tag_start = '<div class="vsel-meta-title">';
	$widget_title_tag_end = '</div>';
} else {
	$widget_title_tag_start = '<h3 class="vsel-meta-title">';
	$widget_title_tag_end = '</h3>';
}

// set title
if ( $widget_title_hide == 'yes' ) {
	$widget_event_title = '';
} else {
	if ( !empty($vsel_widget_atts['title_link']) && ($vsel_widget_atts['title_link'] == 'false') ) {
		$widget_event_title = $widget_title_tag_start.get_the_title().$widget_title_tag_end;
	} else {
		if ( ($redirect_title_to_more_info == 'yes') && !empty($more_info_link) ) {
			$widget_event_title = $widget_title_tag_start.'<a href="'.esc_url($more_info_link).'" rel="noopener noreferrer" '.$more_info_link_target.' title="'.esc_url($more_info_link).'">'.get_the_title().'</a>'.$widget_title_tag_end;
		} elseif ($widget_link_title == 'yes') {
			$widget_event_title =  $widget_title_tag_start.'<a href="'.get_permalink().'" rel="bookmark" title="'.get_the_title().'">'.get_the_title().'</a>'.$widget_title_tag_end;
		} else {
			$widget_event_title = $widget_title_tag_start.get_the_title().$widget_title_tag_end;
		}
	}
}

// set size for featured image
if ($widget_image_size == 'small') {
	$widget_image_source = 'thumbnail';
} elseif ($widget_image_size == 'medium') {
	$widget_image_source = 'medium';
} elseif ($widget_image_size == 'large') {
	$widget_image_source = 'large';
} elseif ($widget_image_size == 'full') {
	$widget_image_source = 'full';
} else {
	$widget_image_source = 'post-thumbnail';
}

// set max width for featured image
if (!empty($widget_image_width) && is_numeric($widget_image_width) && ($widget_image_width > 19) && ($widget_image_width < 101) ) {
	if ($widget_image_width == '100') {
		$widget_image_max_width = 'max-width:100%; float:none; margin-left:0; margin-right:0; box-sizing:border-box;';
	} else {
		$widget_image_max_width = 'max-width:'.$widget_image_width.'%;';
	}
} else {
	$widget_image_max_width = 'max-width:40%';
}

// set css class for featured image
if ($widget_image_location == 'left') {
	$widget_img_class = 'vsel-alignleft';
} else {
	$widget_img_class = 'vsel-alignright';
}

// show date label or icon
$widget_start_default = sprintf(esc_attr($widget_start_label), '<span>'.wp_date( esc_attr($widget_date_format), esc_attr($start_date_timestamp), $utc_timezone ).'</span>' );
$widget_end_default = sprintf(esc_attr($widget_end_label), '<span>'.wp_date( esc_attr($widget_date_format), esc_attr($end_date_timestamp), $utc_timezone ).'</span>' );
$widget_same_default = sprintf(esc_attr($widget_date_label), '<span>'.wp_date( esc_attr($widget_date_format), esc_attr($end_date_timestamp), $utc_timezone ).'</span>' );
$widget_start_icon_1 = '<span class="vsel-day vsel-day-top">'.wp_date( 'j', esc_attr($start_date_timestamp), $utc_timezone ).'</span><span class="vsel-month">'.wp_date( 'M', esc_attr($start_date_timestamp), $utc_timezone ).'</span><span class="vsel-year">'.wp_date( 'Y', esc_attr($start_date_timestamp), $utc_timezone ).'</span>';
$widget_start_icon_2 = '<span class="vsel-month vsel-month-top">'.wp_date( 'M', esc_attr($start_date_timestamp), $utc_timezone ).'</span><span class="vsel-day">'.wp_date( 'j', esc_attr($start_date_timestamp), $utc_timezone ).'</span><span class="vsel-year">'.wp_date( 'Y', esc_attr($start_date_timestamp), $utc_timezone ).'</span>';
$widget_end_icon_1 = '<span class="vsel-day vsel-day-top">'.wp_date( 'j', esc_attr($end_date_timestamp), $utc_timezone ).'</span><span class="vsel-month">'.wp_date( 'M', esc_attr($end_date_timestamp), $utc_timezone ).'</span><span class="vsel-year">'.wp_date( 'Y', esc_attr($end_date_timestamp), $utc_timezone ).'</span>';
$widget_end_icon_2 = '<span class="vsel-month vsel-month-top">'.wp_date( 'M', esc_attr($end_date_timestamp), $utc_timezone ).'</span><span class="vsel-day">'.wp_date( 'j', esc_attr($end_date_timestamp), $utc_timezone ).'</span><span class="vsel-year">'.wp_date( 'Y', esc_attr($end_date_timestamp), $utc_timezone ).'</span>';

// single event variables

// get custom labels from settings page
$single_date_label = get_option('vsel-setting-81');
$single_start_label = get_option('vsel-setting-82');
$single_end_label = get_option('vsel-setting-83');
$single_time_label = get_option('vsel-setting-84');
$single_all_day_label = get_option('vsel-setting-91');
$single_location_label = get_option('vsel-setting-85');

// get setting to show date label or icon
$single_date_type  = get_option('vsel-setting-74');

// get setting to display date icon next to other event details
$single_meta_combine = get_option('vsel-setting-97');

// get setting to combine dates on the same line
$single_date_combine = get_option('vsel-setting-75');

// get setting to link category to category page
$single_link_cat = get_option('vsel-setting-73');

// get setting for acf fields
$single_acf_fields = get_option('vsel-setting-80');

// get settings for event layout
$single_meta_location = get_option('vsel-setting-72');
$single_meta_width = get_option('vsel-setting-71');

// get settings to hide elements
$single_date_hide = get_option('vsel-setting-86');
$single_time_hide = get_option('vsel-setting-76');
$single_location_hide = get_option('vsel-setting-77');
$single_link_hide = get_option('vsel-setting-79');
$single_cats_hide = get_option('vsel-setting-78');

// show default label if no custom label is set
if (empty($single_date_label)) {
	$single_date_label = __( 'Date: %s', 'very-simple-event-list' );
}
if (empty($single_start_label)) {
	$single_start_label = __( 'Start date: %s', 'very-simple-event-list' );
}
if (empty($single_end_label)) {
	$single_end_label = __( 'End date: %s', 'very-simple-event-list' );
}
if (empty($single_time_label)) {
	$single_time_label = __( 'Time: %s', 'very-simple-event-list' );
}
if (empty($single_all_day_label)) {
	$single_all_day_label = __( 'All-day event', 'very-simple-event-list' );
}
if (empty($single_location_label)) {
	$single_location_label = __( 'Location: %s', 'very-simple-event-list' );
}

// set width for event details and event info block
if (!empty($single_meta_width) && is_numeric($single_meta_width) && ($single_meta_width > 19) && ($single_meta_width < 61) ) {
	$single_content_width_default = 96;
	$single_content_width = $single_content_width_default - $single_meta_width;
	$single_meta_width = 'width:'.$single_meta_width.'%;';
	$single_info_block_width = 'width:'.$single_content_width.'%;';
} else {
	$single_meta_width = 'width:36%;';
	$single_info_block_width = 'width:60%;';
}

// set css class for event details and event info block
if ($single_meta_location == 'right') {
	$single_meta_class = 'vsel-meta vsel-alignright';
	$single_info_block_class = 'vsel-info-block vsel-alignleft';
} else {
	$single_meta_class = 'vsel-meta vsel-alignleft';
	$single_info_block_class = 'vsel-info-block vsel-alignright';
}

// combine width and css class for event details and event info block
$single_meta_start = '<div class="'.$single_meta_class.'" style="'.$single_meta_width.'">';
$single_meta_end = '</div>';
$single_info_block_start = '<div class="'.$single_info_block_class.'" style="'.$single_info_block_width.'">';
$single_info_block_end = '</div>';

// show date label or icon
$single_start_default = sprintf(esc_attr($single_start_label), '<span>'.wp_date( esc_attr($template_date_format), esc_attr($start_date_timestamp), $utc_timezone ).'</span>' );
$single_end_default = sprintf(esc_attr($single_end_label), '<span>'.wp_date( esc_attr($template_date_format), esc_attr($end_date_timestamp), $utc_timezone ).'</span>' );
$single_same_default = sprintf(esc_attr($single_date_label), '<span>'.wp_date( esc_attr($template_date_format), esc_attr($end_date_timestamp), $utc_timezone ).'</span>' );
$single_start_icon_1 = '<span class="vsel-day vsel-day-top">'.wp_date( 'j', esc_attr($start_date_timestamp), $utc_timezone ).'</span><span class="vsel-month">'.wp_date( 'M', esc_attr($start_date_timestamp), $utc_timezone ).'</span><span class="vsel-year">'.wp_date( 'Y', esc_attr($start_date_timestamp), $utc_timezone ).'</span>';
$single_start_icon_2 = '<span class="vsel-month vsel-month-top">'.wp_date( 'M', esc_attr($start_date_timestamp), $utc_timezone ).'</span><span class="vsel-day">'.wp_date( 'j', esc_attr($start_date_timestamp), $utc_timezone ).'</span><span class="vsel-year">'.wp_date( 'Y', esc_attr($start_date_timestamp), $utc_timezone ).'</span>';
$single_end_icon_1 = '<span class="vsel-day vsel-day-top">'.wp_date( 'j', esc_attr($end_date_timestamp), $utc_timezone ).'</span><span class="vsel-month">'.wp_date( 'M', esc_attr($end_date_timestamp), $utc_timezone ).'</span><span class="vsel-year">'.wp_date( 'Y', esc_attr($end_date_timestamp), $utc_timezone ).'</span>';
$single_end_icon_2 = '<span class="vsel-month vsel-month-top">'.wp_date( 'M', esc_attr($end_date_timestamp), $utc_timezone ).'</span><span class="vsel-day">'.wp_date( 'j', esc_attr($end_date_timestamp), $utc_timezone ).'</span><span class="vsel-year">'.wp_date( 'Y', esc_attr($end_date_timestamp), $utc_timezone ).'</span>';
