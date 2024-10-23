<?php
// disable direct access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// content of single event page
function vsel_single_content( $content ) {
	// include event variables
	include 'vsel-variables.php';
	// initialize output
	$output = '';
	// if single event and if template is activated
	if ( is_singular('event') && in_the_loop() && ( $disable_single_template != 'yes' ) ) {
		// start event container
		$output .= '<div class="vsel-content">';
			// start event details
			$output .= $single_meta_start;
				// if date icon is displayed next to other event details
				if ( ($single_date_hide != 'yes') && ($single_date_type == 'icon') && ($single_meta_combine == 'yes') ) {
					$output .= '<div class="vsel-meta-combine">';
				}
				// date
				if ( $single_date_hide != 'yes' ) {
					if ( empty($start_date) || empty($end_date) || ($start_date > $end_date) ) {
						$output .= '<div class="vsel-meta-date vsel-meta-error">';
						$output .= esc_attr__( 'Error: please reset date.', 'very-simple-event-list' );
						$output .= '</div>';
					} elseif ($end_date > $start_date) {
						if ( $single_date_type == 'icon' ) {
							if ($template_date_format == 'j F Y' || $template_date_format == 'd/m/Y' || $template_date_format == 'd-m-Y') {
								$output .= '<div class="vsel-meta-date-icon vsel-meta-combined-date-icon"><div class="vsel-start-icon">';
								$output .= $single_start_icon_1;
								$output .= '</div>';
								$output .= '<div class="vsel-end-icon">';
								$output .= $single_end_icon_1;
								$output .= '</div></div>';
							} else {
								$output .= '<div class="vsel-meta-date-icon vsel-meta-combined-date-icon"><div class="vsel-start-icon">';
								$output .= $single_start_icon_2;
								$output .= '</div>';
								$output .= '<div class="vsel-end-icon">';
								$output .= $single_end_icon_2;
								$output .= '</div></div>';
							}
						} else {
							if ($single_date_combine == 'yes') {
								$output .= '<div class="vsel-meta-date vsel-meta-combined-date">';
								$output .= $single_start_default;
								$output .= ' '.esc_attr($date_separator).' ';
								$output .= $single_end_default;
								$output .= '</div>';
							} else {
								$output .= '<div class="vsel-meta-date vsel-meta-start-date">';
								$output .= $single_start_default;
								$output .= '</div>';
								$output .= '<div class="vsel-meta-date vsel-meta-end-date">';
								$output .= $single_end_default;
								$output .= '</div>';
							}
						}
					} elseif ($end_date == $start_date) {
						if ( $single_date_type == 'icon' ) {
							if ($template_date_format == 'j F Y' || $template_date_format == 'd/m/Y' || $template_date_format == 'd-m-Y') {
								$output .= '<div class="vsel-meta-date-icon vsel-meta-single-date-icon"><div class="vsel-start-icon">';
								$output .= $single_start_icon_1;
								$output .= '</div></div>';
							} else {
								$output .= '<div class="vsel-meta-date-icon vsel-meta-single-date-icon"><div class="vsel-start-icon">';
								$output .= $single_start_icon_2;
								$output .= '</div></div>';
							}
						} else {
							$output .= '<div class="vsel-meta-date vsel-meta-single-date">';
							$output .= $single_same_default;
							$output .= '</div>';
						}
					}
				}
				// time
				if ( $single_time_hide != 'yes' ) {
					if ( $one_time_field != 'yes' ) {
						if ( ($start_date == $end_date) && ($start_time > $end_time) ) {
							$output .= '<div class="vsel-meta-time vsel-meta-error">';
							$output .= esc_attr__( 'Error: please reset time.', 'very-simple-event-list' );
							$output .= '</div>';
						} else {
							if ( $all_day_event == 'yes' ) {
								$output .= '<div class="vsel-meta-time vsel-meta-all-day">';
								$output .= esc_attr($single_all_day_label);
								$output .= '</div>';
							} else {
								if ( ($hide_equal_time == 'yes') && ($start_time == $end_time) ) {
									$output .= '';
								} else {
									if ( $hide_end_time == 'yes' ) {
										$end = '';
									} else {
										$end = ' '.esc_attr($time_separator).' '.wp_date( esc_attr($template_time_format), esc_attr($end_date_timestamp), $utc_timezone );
									}		
									$output .= '<div class="vsel-meta-time">';
									$output .= sprintf(esc_attr($single_time_label), '<span>'.wp_date( esc_attr($template_time_format), esc_attr($start_date_timestamp), $utc_timezone ).$end.'</span>' );
									$output .= '</div>';
								}
							}
						}
					} else {
						if (!empty($time)) {
							$output .= '<div class="vsel-meta-time">';
							$output .= sprintf(esc_attr($single_time_label), '<span>'.esc_attr($time).'</span>' );
							$output .= '</div>';
						}
					}
				}				
				// location
				if ( $single_location_hide != 'yes' ) {
					if (!empty($location)) {
						$output .= '<div class="vsel-meta-location">';
						$output .= sprintf(esc_attr($single_location_label), '<span>'.esc_attr($location).'</span>' );
						$output .= '</div>';
					}
				}
				// include acf fields
				if ( class_exists('acf') && ( empty($single_acf_fields) || ($single_acf_fields == 'no') || ($single_acf_fields == 'details') ) ) {
					include 'vsel-acf.php';
				}
				// more info link
				if ( ($redirect_title_to_more_info != 'yes') && ($redirect_image_to_more_info != 'yes') ) {
					if ( $single_link_hide != 'yes' ) {
						if (!empty($more_info_link)) {
							$output .= '<div class="vsel-meta-link">';
							$output .= '<a href="'.esc_url($more_info_link).'" '.$more_info_link_target.' title="'.esc_url($more_info_link).'">'.esc_attr($more_info_link_label).'</a>';
							$output .= '</div>';
						}
					}
				}
				// categories
				if ( $single_cats_hide != 'yes' ) {
					$cats_raw = wp_strip_all_tags( get_the_term_list( get_the_ID(), 'event_cat', '<span>', ' '.esc_attr($cat_separator).' ', '</span>' ) );
					$cats = get_the_term_list( get_the_ID(), 'event_cat', '<span>', ' '.esc_attr($cat_separator).' ', '</span>' );
					if ( has_term( '', 'event_cat', get_the_ID() ) ) {
						if ($single_link_cat != 'yes') {
							$output .= '<div class="vsel-meta-cats">'.$cats_raw.'</div>';
						} else {
							$output .= '<div class="vsel-meta-cats">'.$cats.'</div>';
						}
					}
				}
				// if date icon is displayed next to other event details
				if ( ($single_date_hide != 'yes') && ($single_date_type == 'icon') && ($single_meta_combine == 'yes') ) {
					$output .= '</div>';
				}
			// end event details
			$output .= $single_meta_end;
			// start event info block
			$output .= $single_info_block_start;
				// event info
				$output .= '<div class="vsel-info">';
				$output .= $content;
				$output .= '</div>';
				// include acf fields
				if ( class_exists('acf') && ($single_acf_fields == 'info') ) {
					$acf_fields = get_fields();
					$has_acf_fields = false;
					if ( $acf_fields ) {
						foreach( $acf_fields as $acf_field => $acf_field_value ) {
							if ( !empty($acf_field_value) ) {
								$has_acf_fields = true;
							}
						}
						if ( $has_acf_fields == true ) {
							$output .= '<div class="vsel-info-acf-fields">';
								include 'vsel-acf.php';
							$output .= '</div>';
						}
					}
				}
			// end event info block
			$output .= $single_info_block_end;
		// end event container
		$output .= '</div>';
	// return default content if template is not activated
  	} else {
		$output .= $content;
	}
	// return output
	return $output;
}
add_filter( 'the_content', 'vsel_single_content' );

// content of category, post type and search results page
function vsel_archive_content( $content ) {
	// include event variables
	include 'vsel-variables.php';
	// initialize output
	$output = '';
	// if post content is no summary and if template is activated
	if ( ( is_tax('event_cat') && in_the_loop() && ( $disable_category_template != 'yes' ) ) || ( is_post_type_archive('event') && ! is_search() && in_the_loop() && ( $disable_post_type_template != 'yes' ) ) || ( ( get_post_type() == 'event' ) && is_search() && in_the_loop() && ( $disable_search_template != 'yes' ) ) ) {
		// get event content
		$vsel_event_data = get_post( get_the_ID() );
		$vsel_event_content = wpautop( wp_kses_post( $vsel_event_data->post_content ) );
		// start event container
		$output .= '<div class="vsel-content">';
			// start event details
			$output .= $page_meta_start;
				// if date icon is displayed next to other event details
				if ( ($page_date_hide != 'yes') && ($page_date_type == 'icon') && ($page_meta_combine == 'yes') ) {
					$output .= '<div class="vsel-meta-combine">';
				}
				// date
				if ( $page_date_hide != 'yes' ) {
					if ( empty($start_date) || empty($end_date) || ($start_date > $end_date) ) {
						$output .= '<div class="vsel-meta-date vsel-meta-error">';
						$output .= esc_attr__( 'Error: please reset date.', 'very-simple-event-list' );
						$output .= '</div>';
					} elseif ($end_date > $start_date) {
						if ( $page_date_type == 'icon' ) {
							if ($template_date_format == 'j F Y' || $template_date_format == 'd/m/Y' || $template_date_format == 'd-m-Y') {
								$output .= '<div class="vsel-meta-date-icon vsel-meta-combined-date-icon"><div class="vsel-start-icon">';
								$output .= $page_start_icon_1;
								$output .= '</div>';
								$output .= '<div class="vsel-end-icon">';
								$output .= $page_end_icon_1;
								$output .= '</div></div>';
							} else {
								$output .= '<div class="vsel-meta-date-icon vsel-meta-combined-date-icon"><div class="vsel-start-icon">';
								$output .= $page_start_icon_2;
								$output .= '</div>';
								$output .= '<div class="vsel-end-icon">';
								$output .= $page_end_icon_2;
								$output .= '</div></div>';
							}
						} else {
							if ($page_date_combine == 'yes') {
								$output .= '<div class="vsel-meta-date vsel-meta-combined-date">';
								$output .= $page_start_default;
								$output .= ' '.esc_attr($date_separator).' ';
								$output .= $page_end_default;
								$output .= '</div>';
							} else {
								$output .= '<div class="vsel-meta-date vsel-meta-start-date">';
								$output .= $page_start_default;
								$output .= '</div>';
								$output .= '<div class="vsel-meta-date vsel-meta-end-date">';
								$output .= $page_end_default;
								$output .= '</div>';
							}
						}
					} elseif ($end_date == $start_date) {
						if ( $page_date_type == 'icon' ) {
							if ($template_date_format == 'j F Y' || $template_date_format == 'd/m/Y' || $template_date_format == 'd-m-Y') {
								$output .= '<div class="vsel-meta-date-icon vsel-meta-single-date-icon"><div class="vsel-start-icon">';
								$output .= $page_start_icon_1;
								$output .= '</div></div>';
							} else {
								$output .= '<div class="vsel-meta-date-icon vsel-meta-single-date-icon"><div class="vsel-start-icon">';
								$output .= $page_start_icon_2;
								$output .= '</div></div>';
							}
						} else {
							$output .= '<div class="vsel-meta-date vsel-meta-single-date">';
							$output .= $page_same_default;
							$output .= '</div>';
						}
					}
				}
				// time
				if ( $page_time_hide != 'yes' ) {
					if ( $one_time_field != 'yes' ) {
						if ( ($start_date == $end_date) && ($start_time > $end_time) ) {
							$output .= '<div class="vsel-meta-time vsel-meta-error">';
							$output .= esc_attr__( 'Error: please reset time.', 'very-simple-event-list' );
							$output .= '</div>';
						} else {
							if ( $all_day_event == 'yes' ) {
								$output .= '<div class="vsel-meta-time vsel-meta-all-day">';
								$output .= esc_attr($page_all_day_label);
								$output .= '</div>';
							} else {
								if ( ($hide_equal_time == 'yes') && ($start_time == $end_time) ) {
									$output .= '';
								} else {
									if ( $hide_end_time == 'yes' ) {
										$end = '';
									} else {
										$end = ' '.esc_attr($time_separator).' '.wp_date( esc_attr($template_time_format), esc_attr($end_date_timestamp), $utc_timezone );
									}		
									$output .= '<div class="vsel-meta-time">';
									$output .= sprintf(esc_attr($page_time_label), '<span>'.wp_date( esc_attr($template_time_format), esc_attr($start_date_timestamp), $utc_timezone ).$end.'</span>' );
									$output .= '</div>';
								}
							}
						}
					} else {
						if (!empty($time)) {
							$output .= '<div class="vsel-meta-time">';
							$output .= sprintf(esc_attr($page_time_label), '<span>'.esc_attr($time).'</span>' );
							$output .= '</div>';
						}
					}
				}
				// location
				if ( $page_location_hide != 'yes' ) {
					if (!empty($location)) {
						$output .= '<div class="vsel-meta-location">';
						$output .= sprintf(esc_attr($page_location_label), '<span>'.esc_attr($location).'</span>' );
						$output .= '</div>';
					}
				}
				// include acf fields
				if ( class_exists('acf') && ( empty($page_acf_fields) || ($page_acf_fields == 'no') || ($page_acf_fields == 'details') ) ) {
					include 'vsel-acf.php';
				}
				// more info link
				if ( ($redirect_title_to_more_info != 'yes') && ($redirect_image_to_more_info != 'yes') ) {
					if ( $page_link_hide != 'yes' ) {
						if (!empty($more_info_link)) {
							$output .= '<div class="vsel-meta-link">';
							$output .= '<a href="'.esc_url($more_info_link).'" '.$more_info_link_target.' title="'.esc_url($more_info_link).'">'.esc_attr($more_info_link_label).'</a>';
							$output .= '</div>';
						}
					}
				}
				// categories
				if ( $page_cats_hide != 'yes' ) {
					$cats_raw = wp_strip_all_tags( get_the_term_list( get_the_ID(), 'event_cat', '<span>', ' '.esc_attr($cat_separator).' ', '</span>' ) );
					$cats = get_the_term_list( get_the_ID(), 'event_cat', '<span>', ' '.esc_attr($cat_separator).' ', '</span>' );
					if ( has_term( '', 'event_cat', get_the_ID() ) ) {
						if ($page_link_cat != 'yes') {
							$output .= '<div class="vsel-meta-cats">'.$cats_raw.'</div>';
						} else {
							$output .= '<div class="vsel-meta-cats">'.$cats.'</div>';
						}
					}
				}
				// if date icon is displayed next to other event details
				if ( ($page_date_hide != 'yes') && ($page_date_type == 'icon') && ($page_meta_combine == 'yes') ) {
					$output .= '</div>';
				}
			// end event details
			$output .= $page_meta_end;
			// start event info block
			$output .= $page_info_block_start;
				// event info
				if ( $page_info_hide != 'yes' ) {
					$output .= '<div class="vsel-info">';
					$output .= $vsel_event_content;
					$output .= '</div>';
				}
				// include acf fields
				if ( class_exists('acf') && ($page_acf_fields == 'info') ) {
					$acf_fields = get_fields();
					$has_acf_fields = false;
					if ( $acf_fields ) {
						foreach( $acf_fields as $acf_field => $acf_field_value ) {
							if ( !empty($acf_field_value) ) {
								$has_acf_fields = true;
							}
						}
						if ( $has_acf_fields == true ) {
							$output .= '<div class="vsel-info-acf-fields">';
								include 'vsel-acf.php';
							$output .= '</div>';
						}
					}
				}
			// end event info block
			$output .= $page_info_block_end;
		// end event container
		$output .= '</div>';
	// return default content if template is not activated
  	} else {
		$output .= $content;
	}
	// return output
	return $output;
}
add_filter( 'the_content', 'vsel_archive_content' );

function vsel_archive_excerpt( $excerpt ) {
	// include event variables
	include 'vsel-variables.php';
	// initialize output
	$output = '';
	// if post content is summary and if template is activated
	if ( ( is_tax('event_cat') && in_the_loop() && ( $disable_category_template != 'yes' ) ) || ( is_post_type_archive('event') && ! is_search() && in_the_loop() && ( $disable_post_type_template != 'yes' ) ) || ( ( get_post_type() == 'event' ) && is_search() && in_the_loop() && ( $disable_search_template != 'yes' ) ) ) {
		// get event content
		$vsel_event_data = get_post( get_the_ID() );
		$vsel_event_content = $vsel_event_data->post_content;
		// create excerpt
		if ( !empty( $summary ) ) {
			$vsel_event_summary = wpautop( wp_kses_post( $summary ) );
		} else {
			$vsel_event_summary = wp_trim_words( $vsel_event_content, 55, ' [&hellip;] ');
		}
		// start event container
		$output .= '<div class="vsel-content">';
			// start event details
			$output .= $page_meta_start;
				// if date icon is displayed next to other event details
				if ( ($page_date_hide != 'yes') && ($page_date_type == 'icon') && ($page_meta_combine == 'yes') ) {
					$output .= '<div class="vsel-meta-combine">';
				}
				// date
				if ( $page_date_hide != 'yes' ) {
					if ( empty($start_date) || empty($end_date) || ($start_date > $end_date) ) {
						$output .= '<div class="vsel-meta-date vsel-meta-error">';
						$output .= esc_attr__( 'Error: please reset date.', 'very-simple-event-list' );
						$output .= '</div>';
					} elseif ($end_date > $start_date) {
						if ( $page_date_type == 'icon' ) {
							if ($template_date_format == 'j F Y' || $template_date_format == 'd/m/Y' || $template_date_format == 'd-m-Y') {
								$output .= '<div class="vsel-meta-date-icon vsel-meta-combined-date-icon"><div class="vsel-start-icon">';
								$output .= $page_start_icon_1;
								$output .= '</div>';
								$output .= '<div class="vsel-end-icon">';
								$output .= $page_end_icon_1;
								$output .= '</div></div>';
							} else {
								$output .= '<div class="vsel-meta-date-icon vsel-meta-combined-date-icon"><div class="vsel-start-icon">';
								$output .= $page_start_icon_2;
								$output .= '</div>';
								$output .= '<div class="vsel-end-icon">';
								$output .= $page_end_icon_2;
								$output .= '</div></div>';
							}
						} else {
							if ($page_date_combine == 'yes') {
								$output .= '<div class="vsel-meta-date vsel-meta-combined-date">';
								$output .= $page_start_default;
								$output .= ' '.esc_attr($date_separator).' ';
								$output .= $page_end_default;
								$output .= '</div>';
							} else {
								$output .= '<div class="vsel-meta-date vsel-meta-start-date">';
								$output .= $page_start_default;
								$output .= '</div>';
								$output .= '<div class="vsel-meta-date vsel-meta-end-date">';
								$output .= $page_end_default;
								$output .= '</div>';
							}
						}
					} elseif ($end_date == $start_date) {
						if ( $page_date_type == 'icon' ) {
							if ($template_date_format == 'j F Y' || $template_date_format == 'd/m/Y' || $template_date_format == 'd-m-Y') {
								$output .= '<div class="vsel-meta-date-icon vsel-meta-single-date-icon"><div class="vsel-start-icon">';
								$output .= $page_start_icon_1;
								$output .= '</div></div>';
							} else {
								$output .= '<div class="vsel-meta-date-icon vsel-meta-single-date-icon"><div class="vsel-start-icon">';
								$output .= $page_start_icon_2;
								$output .= '</div></div>';
							}
						} else {
							$output .= '<div class="vsel-meta-date vsel-meta-single-date">';
							$output .= $page_same_default;
							$output .= '</div>';
						}
					}
				}
				// time
				if ( $page_time_hide != 'yes' ) {
					if ( $one_time_field != 'yes' ) {
						if ( ($start_date == $end_date) && ($start_time > $end_time) ) {
							$output .= '<div class="vsel-meta-time vsel-meta-error">';
							$output .= esc_attr__( 'Error: please reset time.', 'very-simple-event-list' );
							$output .= '</div>';
						} else {
							if ( $all_day_event == 'yes' ) {
								$output .= '<div class="vsel-meta-time vsel-meta-all-day">';
								$output .= esc_attr($page_all_day_label);
								$output .= '</div>';
							} else {
								if ( ($hide_equal_time == 'yes') && ($start_time == $end_time) ) {
									$output .= '';
								} else {
									if ( $hide_end_time == 'yes' ) {
										$end = '';
									} else {
										$end = ' '.esc_attr($time_separator).' '.wp_date( esc_attr($template_time_format), esc_attr($end_date_timestamp), $utc_timezone );
									}		
									$output .= '<div class="vsel-meta-time">';
									$output .= sprintf(esc_attr($page_time_label), '<span>'.wp_date( esc_attr($template_time_format), esc_attr($start_date_timestamp), $utc_timezone ).$end.'</span>' );
									$output .= '</div>';
								}
							}
						}
					} else {
						if (!empty($time)) {
							$output .= '<div class="vsel-meta-time">';
							$output .= sprintf(esc_attr($page_time_label), '<span>'.esc_attr($time).'</span>' );
							$output .= '</div>';
						}
					}
				}
				// location
				if ( $page_location_hide != 'yes' ) {
					if (!empty($location)) {
						$output .= '<div class="vsel-meta-location">';
						$output .= sprintf(esc_attr($page_location_label), '<span>'.esc_attr($location).'</span>' );
						$output .= '</div>';
					}
				}
				// include acf fields
				if ( class_exists('acf') && ( empty($page_acf_fields) || ($page_acf_fields == 'no') || ($page_acf_fields == 'details') ) ) {
					include 'vsel-acf.php';
				}
				// more info link
				if ( ($redirect_title_to_more_info != 'yes') && ($redirect_image_to_more_info != 'yes') ) {
					if ( $page_link_hide != 'yes' ) {
						if (!empty($more_info_link)) {
							$output .= '<div class="vsel-meta-link">';
							$output .= '<a href="'.esc_url($more_info_link).'" '.$more_info_link_target.' title="'.esc_url($more_info_link).'">'.esc_attr($more_info_link_label).'</a>';
							$output .= '</div>';
						}
					}
				}
				// categories
				if ( $page_cats_hide != 'yes' ) {
					$cats_raw = wp_strip_all_tags( get_the_term_list( get_the_ID(), 'event_cat', '<span>', ' '.esc_attr($cat_separator).' ', '</span>' ) );
					$cats = get_the_term_list( get_the_ID(), 'event_cat', '<span>', ' '.esc_attr($cat_separator).' ', '</span>' );
					if ( has_term( '', 'event_cat', get_the_ID() ) ) {
						if ($page_link_cat != 'yes') {
							$output .= '<div class="vsel-meta-cats">'.$cats_raw.'</div>';
						} else {
							$output .= '<div class="vsel-meta-cats">'.$cats.'</div>';
						}
					}
				}
				// if date icon is displayed next to other event details
				if ( ($page_date_hide != 'yes') && ($page_date_type == 'icon') && ($page_meta_combine == 'yes') ) {
					$output .= '</div>';
				}
			// end event details
			$output .= $page_meta_end;
			// start event info block
			$output .= $page_info_block_start;
				// event info
				if ( $page_info_hide != 'yes' ) {
					$output .= '<div class="vsel-info">';
					$output .= $vsel_event_summary;
					$output .= '</div>';
				}
				// include acf fields
				if ( class_exists('acf') && ($page_acf_fields == 'info') ) {
					$acf_fields = get_fields();
					$has_acf_fields = false;
					if ( $acf_fields ) {
						foreach( $acf_fields as $acf_field => $acf_field_value ) {
							if ( !empty($acf_field_value) ) {
								$has_acf_fields = true;
							}
						}
						if ( $has_acf_fields == true ) {
							$output .= '<div class="vsel-info-acf-fields">';
								include 'vsel-acf.php';
							$output .= '</div>';
						}
					}
				}
			// end event info block
			$output .= $page_info_block_end;
		// end event container
		$output .= '</div>';
	// return default excerpt if template is not activated
  	} else {
		$output .= $excerpt;
	}
	// return output
	return $output;
}
add_filter( 'the_excerpt', 'vsel_archive_excerpt' );
