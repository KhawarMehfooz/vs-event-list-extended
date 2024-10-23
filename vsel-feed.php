<?php
// disable direct access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// file name for ical feed
function vsel_ical_feed_file_name() {
	$today = vsel_timestamp_today();
	$utc_timezone = vsel_utc_timezone();
	$filename = urlencode( get_bloginfo('name').'-ical-'.wp_date( 'Y-m-d', esc_attr($today), $utc_timezone ).'.ics' );
	return $filename;
}

// create ical feed
function vsel_ical_feed() {
header('Content-Description:File Transfer');
header('Content-Disposition:attachment; filename="'.vsel_ical_feed_file_name().'"');
header('Content-type:text/calendar; charset=UTF-8');
	$eol = "\r\n";
$output = '';
$output .= 'BEGIN:VCALENDAR'.$eol.'';
$output .= 'VERSION:2.0'.$eol.'';
$output .= 'PRODID:-//'.get_bloginfo('name').'//NONSGML Events//EN'.$eol.'';
	$items = get_option('vsel-setting-93');
	if ( is_numeric($items) && !empty($items) ) {
		$number_of_events = $items;
	} else {
		$number_of_events  = '10';
	}
	$vsel_ical_query = new WP_Query(array(
		'post_type' => 'event',
		'meta_key' => 'event-date',
		'orderby' => 'meta_value_num',
		'order' => 'DESC',
		'posts_per_page' => $number_of_events
	));
	if ( $vsel_ical_query->have_posts() ) :
	while($vsel_ical_query->have_posts()) : $vsel_ical_query->the_post();
		$event = get_post( get_the_ID() );
		$title = $event->post_title;
		$start_date = gmdate("Ymd\THis", get_post_meta( get_the_ID(), 'event-start-date', true ));
		$end_date = gmdate("Ymd\THis", get_post_meta( get_the_ID(), 'event-date', true ));
		$modified_date = get_the_modified_date("Ymd\THis", get_the_ID());
		$location = get_post_meta( get_the_ID(), 'event-location', true );
		$url = get_the_permalink();
		$summary = get_post_meta( get_the_ID(), 'event-summary', true );
		if ( empty($summary) ) {
			$summary = wp_trim_words( $event->post_content, 15, '...' );
		}
		$content = preg_replace( "/\r\n/", "\\n", $summary);
		$image = get_the_post_thumbnail_url( get_the_ID(), 'large' );
$output .= 'BEGIN:VEVENT'.$eol.'';
$output .= 'UID:'.esc_attr(get_the_ID()).$eol.'';
$output .= 'DTSTAMP:'.esc_attr($modified_date).$eol.'';
$output .= 'DTSTART:'.esc_attr($start_date).$eol.'';
$output .= 'DTEND:'.esc_attr($end_date).$eol.'';
$output .= 'LOCATION:'.wp_strip_all_tags($location).$eol.'';
$output .= 'DESCRIPTION:'.wp_strip_all_tags($content).$eol.'';
$output .= 'SUMMARY:'.wp_strip_all_tags($title).$eol.'';
$output .= 'ATTACH;FMTTYPE=image/jpeg:'.esc_url($image).$eol.'';
$output .= 'URL;VALUE=URI:'.esc_url($url).$eol.'';
$output .= 'END:VEVENT'.$eol.'';
	endwhile;
	endif;
$output .= 'END:VCALENDAR';
	echo $output;
}

// create rss feed
function vsel_rss_feed() {
header('Content-type:application/rss+xml; charset=UTF-8');
	$eol = "\r\n";
	$feed_url = get_self_link();
$output = '';
$output .= '<?xml version="1.0" encoding="UTF-8"?>'.$eol.'';
$output .= '<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom" xmlns:sy="http://purl.org/rss/1.0/modules/syndication/">'.$eol.'';
$output .= '<channel>'.$eol.'';
$output .= '<title>'.get_bloginfo_rss('name').'</title>'.$eol.'';
$output .= '<atom:link href="'.esc_url($feed_url).'" rel="self" type="application/rss+xml" />'.$eol.'';
$output .= '<link>'.get_bloginfo_rss('url').'</link>'.$eol.'';
$output .= '<description>'.get_bloginfo_rss('description').'</description>'.$eol.'';
$output .= '<sy:updatePeriod>'.apply_filters('rss_update_period', 'hourly').'</sy:updatePeriod>'.$eol.'';
$output .= '<sy:updateFrequency>'.apply_filters('rss_update_frequency', '1').'</sy:updateFrequency>'.$eol.'';
	$items = get_option('posts_per_rss');
	if ( is_numeric($items) && !empty($items) ) {
		$number_of_events = $items;
	} else {
		$number_of_events  = '10';
	}
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
		'meta_key' => 'event-start-date',
		'orderby' => 'meta_value_num',
		'order' => 'ASC',
		'posts_per_page' => $number_of_events,
		'meta_query' => $vsel_meta_query
	);
	$vsel_rss_query = new WP_Query( $vsel_query_args );
	if ( $vsel_rss_query->have_posts() ) :
	while($vsel_rss_query->have_posts()) : $vsel_rss_query->the_post();
		$event = get_post( get_the_ID() );
		$title = $event->post_title;
		$start_date = gmdate("D, d M Y H:i:s", get_post_meta( get_the_ID(), 'event-start-date', true ));
		$url = get_the_permalink();
		$shortlink = wp_get_shortlink();
		$offset = '+0000';
		if (get_option('rss_use_excerpt') == true) {
			$summary = get_post_meta( get_the_ID(), 'event-summary', true );
			if ( empty($summary) ) {
				$summary = wp_trim_words( $event->post_content, 15, '...' );
			}
			$content = preg_replace( "/\r\n/", "\\n", $summary);
		} else {
			$content = wpautop( wp_kses_post( $event->post_content ) );
		}
$output .= '<item>'.$eol.'';
$output .= '<title>'.wp_strip_all_tags($title).'</title>'.$eol.'';
$output .= '<link>'.esc_url($url).'</link>'.$eol.'';
$output .= '<pubDate>'.esc_attr($start_date).' '.$offset.'</pubDate>'.$eol.'';
$output .= '<description>'.wp_strip_all_tags($content).'</description>'.$eol.'';
$output .= '<guid>'.esc_url($shortlink).'</guid>'.$eol.'';
$output .= '</item>'.$eol.'';
	endwhile;
	endif;
$output .= '</channel>'.$eol.'';
$output .= '</rss>';
	echo $output;
}
