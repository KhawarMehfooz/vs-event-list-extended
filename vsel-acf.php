<?php
// disable direct access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// get acf fields
$fields = get_field_objects();
if ( $fields ) {
	// set field order
	if ( is_array($fields) || is_object($fields) ) {
		$order = array();
		foreach( $fields as $field_name => $field ) {
			$order[$field_name] = $field['menu_order'];
		}
		array_multisort( $order, SORT_ASC, $fields );
	}
	foreach( $fields as $field_name => $field ) {
		// get field value
		$value = $field['value'];
		// if field has value
		if ( $value && !empty($value) ) {
			// supported field types
			$supported_field_types = array('text', 'textarea', 'number', 'range', 'select', 'checkbox', 'radio', 'email', 'url', 'link', 'file', 'image', 'date_picker', 'time_picker', 'date_time_picker');
			if ( !in_array($field['type'], $supported_field_types) ) {
				$value = '';
			}
			// if field value is array
			if ( is_array($value) ) {
				// link field type
				if ( $field['type'] == 'link' ) {
					$acf_value_url = $value['url'];
					$acf_value_title = $value['title'];
					$acf_value_target = $value['target'] ? $value['target'] : '_self';
				// file field type
				} elseif ( $field['type'] == 'file' ) {
					$acf_value_url = $value['url'];
					$acf_value_title = $value['title'];
				// image field type
				} elseif ( $field['type'] == 'image' ) {
					$acf_value_url = $value['url'];
					if ( $value['alt'] ) {
						$acf_value_alt = $value['alt'];
					} else {
						$acf_value_alt = $value['name'];
					}
				// other field type
				} else {
					$acf_value = implode(" | ", $value);
				}
			// if field value is no array
			} else {
				// link field type
				if ( $field['type'] == 'link' ) {
					$acf_value_url = $value;
					$acf_value_title = $value;
					$acf_value_target = '_blank';
				// file field type
				} elseif ( $field['type'] == 'file' ) {
					$acf_value_url = $value;
					$acf_value_title = basename($value);
				// image field type
				} elseif ( $field['type'] == 'image' ) {
					$acf_value_url = $value;
					$acf_value_alt = basename($value);
				// other field type
				} else {
					$acf_value = $value;
				}
			}
			// list all fields and values
			$acf_label = $field['label'].': %s';
			$output .= '<div class="vsel-acf-'.$field['name'].'">';
				if ( $field['type'] == 'textarea' ) {
					$output .= '<span class="acf-field-name">'.sprintf(esc_attr($acf_label), '</span><span class="acf-field-value">'.wp_kses_post($acf_value).'</span>' );
				} elseif ( $field['type'] == 'email' ) {
					$output .= '<span class="acf-field-name">'.sprintf(esc_attr($acf_label), '</span><span class="acf-field-value"><a href="mailto:'.esc_attr($acf_value).'">'.esc_attr($acf_value).'</a></span>' );
				} elseif ( $field['type'] == 'url' ) {
					$output .= '<span class="acf-field-name">'.sprintf(esc_attr($acf_label), '</span><span class="acf-field-value"><a href="'.esc_url($acf_value).'" rel="noopener noreferrer" target="_blank">'.esc_attr($acf_value).'</a></span>' );
				} elseif ( $field['type'] == 'link' ) {
					$output .= '<span class="acf-field-name">'.sprintf(esc_attr($acf_label), '</span><span class="acf-field-value"><a href="'.esc_url($acf_value_url).'" target="'.esc_attr($acf_value_target).'">'.esc_attr($acf_value_title).'</a></span>' );
				} elseif ( $field['type'] == 'file' ) {
					$output .= '<span class="acf-field-name">'.sprintf(esc_attr($acf_label), '</span><span class="acf-field-value"><a href="'.esc_url($acf_value_url).'" target="_blank">'.esc_attr($acf_value_title).'</a></span>' );				
				} elseif ( $field['type'] == 'image' ) {
					$output .= '<img src="'.esc_url($acf_value_url).'" alt="'.esc_attr($acf_value_alt).'" />';
				} elseif ( ($field['type'] == 'text') || ($field['type'] == 'number') || ($field['type'] == 'range') || ($field['type'] == 'select') || ($field['type'] == 'checkbox') || ($field['type'] == 'radio') || ($field['type'] == 'date_picker') || ($field['type'] == 'time_picker') || ($field['type'] == 'date_time_picker') ) {
					$output .= '<span class="acf-field-name">'.sprintf(esc_attr($acf_label), '</span><span class="acf-field-value">'.esc_attr($acf_value).'</span>' );
				} else {
					$output .= '<span class="acf-field-name acf-field-error">'.esc_attr__( 'Field type not supported.', 'very-simple-event-list' ).'</span>';
				}
			$output .= '</div>';
		}
	}
}
