<?php
/*
 * Plugin Name: VS Event List Extended
 * Description: With this lightweight plugin you can create an event list.
 * Version: 1.0
 * Author: Khawar Mehfooz
 * Author URI: https://www.khawarmehfooz.com
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Requires PHP: 7.1
 * Requires at least: 5.3
 * Text Domain: very-simple-event-list
 */

// disable direct access
if (! defined('ABSPATH')) {
	exit;
}

// enqueue css script
function vsel_css_script()
{
	wp_enqueue_style('vsel-style', plugins_url('/css/vsel-style.min.css', __FILE__));
}
add_action('wp_enqueue_scripts', 'vsel_css_script');

// the sidebar widget
function vsel_register_widget()
{
	register_widget('vsel_widget');
}
add_action('widgets_init', 'vsel_register_widget');

// create rss feed
function vsel_add_rss_feed()
{
	$feed_setting = get_option('vsel-setting-99');
	if ($feed_setting == 'yes') {
		add_feed('vsel-rss-feed', 'vsel_rss_feed');
	}
}
add_action('init', 'vsel_add_rss_feed');

// create ical feed
function vsel_add_ical_feed()
{
	$feed_setting = get_option('vsel-setting-49');
	if ($feed_setting == 'yes') {
		add_feed('vsel-ical-feed', 'vsel_ical_feed');
	}
}
add_action('init', 'vsel_add_ical_feed');

// set timestamp for today
function vsel_timestamp_today()
{
	$current_date = current_datetime();
	$var = $current_date->setTime(0, 0, 0, 0);
	$today = $var->getTimestamp() + $var->getOffset();
	return $today;
}

// set timestamp for tomorrow
function vsel_timestamp_tomorrow()
{
	$current_date = current_datetime();
	$var = $current_date->setTime(0, 0, 0, 0);
	$tomorrow = $var->getTimestamp() + $var->getOffset() + 86400;
	return $tomorrow;
}

// set utc timezone
function vsel_utc_timezone()
{
	$time_zone = new DateTimeZone('UTC');
	return $time_zone;
}

// set date format for date input fields
function vsel_input_dateformat()
{
	$dateformat_input = get_option('date_format');
	if ($dateformat_input == 'j F Y' || $dateformat_input == 'd/m/Y' || $dateformat_input == 'd-m-Y') {
		$dateformat_input = 'd-m-Y';
	} else {
		$dateformat_input = 'Y-m-d';
	}
	return $dateformat_input;
}

// set date format for datepicker
function vsel_datepicker_dateformat()
{
	$dateformat = get_option('date_format');
	if ($dateformat == 'j F Y' || $dateformat == 'd/m/Y' || $dateformat == 'd-m-Y') {
		$dateformat = 'dd-mm-yy';
	} else {
		$dateformat = 'yy-mm-dd';
	}
	return $dateformat;
}

// enqueue datepicker script
function vsel_enqueue_datepicker()
{
	if (get_post_type() != 'event') {
		return;
	}
	wp_enqueue_script('vsel-datepicker-script', plugins_url('/js/vsel-datepicker.js', __FILE__), array('jquery', 'jquery-ui-datepicker'));
	wp_enqueue_style('vsel-datepicker-style', plugins_url('/css/vsel-datepicker.min.css', __FILE__));
	// datepicker args
	$dataL10n = array(
		'dateFormat' => vsel_datepicker_dateformat()
	);
	// localize script with data for datepicker
	wp_localize_script('vsel-datepicker-script', 'vsel_datepicker_l10n', $dataL10n);
}
add_action('admin_enqueue_scripts', 'vsel_enqueue_datepicker');

// create event post type
function vsel_custom_post_type()
{
	$disable_public = get_option('vsel-setting-60');
	if ($disable_public == 'yes') {
		$public_event = false;
	} else {
		$public_event = true;
	}
	$disable_archive = get_option('vsel-setting-48');
	if ($disable_archive == 'yes') {
		$has_archive = false;
	} else {
		$has_archive = true;
	}
	$disable_menu = get_option('vsel-setting-50');
	if ($disable_menu == 'yes') {
		$show_in_menu = false;
	} else {
		$show_in_menu = true;
	}
	$custom_slug = get_option('vsel-setting-46');
	if (!empty($custom_slug)) {
		$event_slug = $custom_slug;
	} else {
		$event_slug = 'event';
	}
	$vsel_labels = array(
		'name' => __('Events', 'very-simple-event-list'),
		'singular_name' => __('Event', 'very-simple-event-list'),
		'all_items' => __('All events', 'very-simple-event-list'),
		'add_new_item' => __('Add new event', 'very-simple-event-list'),
		'add_new' => __('New event', 'very-simple-event-list'),
		'new_item' => __('New event', 'very-simple-event-list'),
		'edit_item' => __('Edit event', 'very-simple-event-list'),
		'view_item' => __('View event', 'very-simple-event-list'),
		'search_items' => __('Search events', 'very-simple-event-list'),
		'not_found' => __('No events found', 'very-simple-event-list'),
		'not_found_in_trash' => __('No events found in trash', 'very-simple-event-list')
	);
	$vsel_args = array(
		'labels' => $vsel_labels,
		'menu_icon' => 'dashicons-calendar-alt',
		'public' => $public_event,
		'can_export' => true,
		'show_in_nav_menus' => $show_in_menu,
		'has_archive' => $has_archive,
		'show_ui' => true,
		'show_in_rest' => true,
		'capability_type' => 'post',
		'taxonomies' => array('event_cat'),
		'rewrite' => array('slug' => sanitize_key($event_slug)),
		'supports' => array('title', 'thumbnail', 'page-attributes', 'custom-fields', 'editor', 'author')
	);
	register_post_type('event', $vsel_args);
}
add_action('init', 'vsel_custom_post_type');

// create event categories
function vsel_taxonomy()
{
	$disable_menu = get_option('vsel-setting-50');
	if ($disable_menu == 'yes') {
		$show_in_menu = false;
	} else {
		$show_in_menu = true;
	}
	$custom_slug = get_option('vsel-setting-47');
	if (!empty($custom_slug)) {
		$cat_slug = $custom_slug;
	} else {
		$cat_slug = 'event_cat';
	}
	$disable_cats_column = get_option('vsel-setting-55');
	if ($disable_cats_column == 'yes') {
		$cats_column = false;
	} else {
		$cats_column = true;
	}
	$vsel_cat_args = array(
		'label' => __('Event categories', 'very-simple-event-list'),
		'hierarchical' => true,
		'show_in_nav_menus' => $show_in_menu,
		'show_admin_column' => $cats_column,
		'show_in_rest' => true,
		'rewrite' => array('slug' => sanitize_key($cat_slug))
	);
	register_taxonomy('event_cat', 'event', $vsel_cat_args);
}
add_action('init', 'vsel_taxonomy');

// flush rewrite rules on plugin activation
function vsel_activation_hook()
{
	vsel_custom_post_type();
	vsel_taxonomy();
	flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'vsel_activation_hook');

// create metabox
function vsel_metabox()
{
	add_meta_box(
		'vsel-event-metabox',
		__('Event details', 'very-simple-event-list'),
		'vsel_metabox_callback',
		'event',
		'normal',
		'high'
	);
}
add_action('add_meta_boxes', 'vsel_metabox');

function vsel_metabox_callback($post)
{
	// generate a nonce field
	wp_nonce_field('vsel_nonce_action', 'vsel_nonce');

	// get setting for one date instead of start date and end date
	$one_date = get_option('vsel-setting-58');

	// get setting for one time field instead of start time and end time
	$one_time = get_option('vsel-setting-87');

	// get previously saved meta values (if any)
	$start_date_timestamp = get_post_meta($post->ID, 'event-start-date', true);
	$end_date_timestamp = get_post_meta($post->ID, 'event-date', true);
	$time = get_post_meta($post->ID, 'event-time', true);
	$hide_end_time = get_post_meta($post->ID, 'event-hide-end-time', true);
	$all_day = get_post_meta($post->ID, 'event-all-day', true);
	$location = get_post_meta($post->ID, 'event-location', true);
	$link = get_post_meta($post->ID, 'event-link', true);
	$link_label = get_post_meta($post->ID, 'event-link-label', true);
	$link_target = get_post_meta($post->ID, 'event-link-target', true);
	$link_title = get_post_meta($post->ID, 'event-link-title', true);
	$link_image = get_post_meta($post->ID, 'event-link-image', true);
	$summary = get_post_meta($post->ID, 'event-summary', true);
	$is_recurring = get_post_meta($post->ID, 'vsel_is_recurring', true);
	$recurrence_type = get_post_meta($post->ID, 'vsel_recurrence_type', true);
	$recurrence_end_date = get_post_meta($post->ID, 'vsel_recurrence_end_date', true);

	// get date format
	$date_format = vsel_input_dateformat();

	// get utc timezone
	$utc_timezone = vsel_utc_timezone();

	// get timestamp today 
	$today = vsel_timestamp_today();

	// get date if saved, else set it to today
	$start_date_timestamp = !empty($start_date_timestamp) ? $start_date_timestamp : $today;
	$end_date_timestamp = !empty($end_date_timestamp) ? $end_date_timestamp : $today;

	// set time values
	$hour_values = array('00', '01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20', '21', '22', '23');
	$minute_values = array('00', '05', '10', '15', '20', '25', '30', '35', '40', '45', '50', '55');

	// get start date and end date for comparing dates
	$start_date = gmdate('Ymd', intval($start_date_timestamp));
	$end_date = gmdate('Ymd', intval($end_date_timestamp));

	// get start time and end time for comparing times
	$start_time = gmdate('Hi', intval($start_date_timestamp));
	$end_time = gmdate('Hi', intval($end_date_timestamp));

	// error notice if start date is greater than end date
	if ($start_date > $end_date) {
		$notice_date = sprintf(__('Error: %1$s must be equal to or greater than %2$s.', 'very-simple-event-list'), __('End date', 'very-simple-event-list'), __('Start date', 'very-simple-event-list'));
	}

	// error notice if start time is greater than end time
	if (($start_date == $end_date) && ($start_time > $end_time)) {
		$notice_time = sprintf(__('Error: %1$s must be equal to or greater than %2$s.', 'very-simple-event-list'), __('End time', 'very-simple-event-list'), __('Start time', 'very-simple-event-list'));
	}

	// metabox fields
	if ($one_date == 'yes') { ?>
		<p><label for="event-end-date"><?php esc_attr_e('Date', 'very-simple-event-list'); ?></label>
			<input class="widefat" id="event-end-date" type="text" name="event-end-date" required maxlength="10" placeholder="<?php esc_attr_e('Use datepicker', 'very-simple-event-list'); ?>" value="<?php echo wp_date($date_format, esc_attr($end_date_timestamp), $utc_timezone); ?>" />
		</p>
	<?php } else { ?>
		<p><label for="event-start-date"><?php esc_attr_e('Start date', 'very-simple-event-list'); ?></label>
			<input class="widefat" id="event-start-date" type="text" name="event-start-date" required maxlength="10" placeholder="<?php esc_attr_e('Use datepicker', 'very-simple-event-list'); ?>" value="<?php echo wp_date($date_format, esc_attr($start_date_timestamp), $utc_timezone); ?>" />
		</p>
		<p><label for="event-end-date"><?php esc_attr_e('End date', 'very-simple-event-list'); ?>
				<input class="widefat" id="event-end-date" type="text" name="event-end-date" required maxlength="10" placeholder="<?php esc_attr_e('Use datepicker', 'very-simple-event-list'); ?>" value="<?php echo wp_date($date_format, esc_attr($end_date_timestamp), $utc_timezone); ?>" /><br>
				<?php echo (isset($notice_date) ? '<span style="color:red;">' . esc_attr($notice_date) . '</span>' : ''); ?></p>
	<?php }
	if ($one_time != 'yes') { ?>
		<fieldset>
			<p>
				<legend><?php esc_attr_e('Start time', 'very-simple-event-list'); ?></legend>
				<select id="event-start-time-hour" name="event-start-time-hour" style="width:100px;">
					<?php foreach ($hour_values as $hour_value_start) { ?>
						<option value='<?php echo esc_attr($hour_value_start); ?>' <?php echo ((wp_date('H', esc_attr($start_date_timestamp), $utc_timezone)) == $hour_value_start) ? ' selected' : ''; ?>><?php echo esc_attr($hour_value_start); ?></option>
					<?php } ?>
				</select>
				<select id="event-start-time-minute" name="event-start-time-minute" style="width:100px;">
					<?php foreach ($minute_values as $minute_value_start) { ?>
						<option value='<?php echo esc_attr($minute_value_start); ?>' <?php echo ((wp_date('i', esc_attr($start_date_timestamp), $utc_timezone)) == $minute_value_start) ? ' selected' : ''; ?>><?php echo esc_attr($minute_value_start); ?></option>
					<?php } ?>
				</select>
			</p>
		</fieldset>
		<fieldset>
			<p>
				<legend><?php esc_attr_e('End time', 'very-simple-event-list'); ?></legend>
				<select id="event-end-time-hour" name="event-end-time-hour" style="width:100px;">
					<?php foreach ($hour_values as $hour_value_end) { ?>
						<option value='<?php echo esc_attr($hour_value_end); ?>' <?php echo ((wp_date('H', esc_attr($end_date_timestamp), $utc_timezone)) == $hour_value_end) ? ' selected' : ''; ?>><?php echo esc_attr($hour_value_end); ?></option>
					<?php } ?>
				</select>
				<select id="event-end-time-minute" name="event-end-time-minute" style="width:100px;">
					<?php foreach ($minute_values as $minute_value_end) { ?>
						<option value='<?php echo esc_attr($minute_value_end); ?>' <?php echo ((wp_date('i', esc_attr($end_date_timestamp), $utc_timezone)) == $minute_value_end) ? ' selected' : ''; ?>><?php echo esc_attr($minute_value_end); ?></option>
					<?php } ?>
				</select>
				<input class="checkbox" id="event-hide-end-time" type="checkbox" name="event-hide-end-time" value="yes" <?php checked(esc_attr($hide_end_time), 'yes'); ?><label for="event-hide-end-time"><?php esc_attr_e('Hide end time', 'very-simple-event-list'); ?></label><br>
				<?php echo (isset($notice_time) ? '<span style="color:red;">' . esc_attr($notice_time) . '</span>' : ''); ?>
			</p>
		</fieldset>
		<fieldset>
			<p>
				<input class="checkbox" id="vsel_is_recurring" type="checkbox" name="vsel_is_recurring" value="yes" <?php checked($is_recurring, 'yes'); ?> />
				<label for="vsel_is_recurring"><?php esc_html_e('This is a recurring event', 'very-simple-event-list'); ?></label>
			</p>
			<div id="vsel_recurring_options" style="display: <?php echo ($is_recurring === 'yes') ? 'block' : 'none'; ?>;">
				<p>
					<label for="vsel_recurrence_type"><?php esc_html_e('Recurrence Type:', 'very-simple-event-list'); ?></label>
					<select id="vsel_recurrence_type" name="vsel_recurrence_type">
						<option value="weekly" <?php selected($recurrence_type, 'weekly'); ?>><?php esc_html_e('Weekly', 'very-simple-event-list'); ?></option>
					</select>
				</p>
				<p>
					<label for="vsel_recurrence_end_date"><?php esc_html_e('Recurrence End Date:', 'very-simple-event-list'); ?></label>
					<input class="widefat vsel-datepicker" id="vsel_recurrence_end_date" type="text" name="vsel_recurrence_end_date" value="<?php echo esc_attr($recurrence_end_date); ?>" />
				</p>
			</div>
		</fieldset>
		<p><input class="checkbox" id="event-all-day" type="checkbox" name="event-all-day" value="yes" <?php checked(esc_attr($all_day), 'yes'); ?> />
			<label for="event-all-day"><?php esc_attr_e('All-day event', 'very-simple-event-list'); ?></label>
		</p>
	<?php } else { ?>
		<p><label for="event-time"><?php esc_attr_e('Time', 'very-simple-event-list'); ?></label>
			<input class="widefat" id="event-time" type="text" name="event-time" placeholder="<?php esc_attr_e('Example', 'very-simple-event-list'); ?>: <?php esc_attr_e('16:00 - 18:00', 'very-simple-event-list'); ?>" value="<?php echo esc_attr($time); ?>" />
		</p>
	<?php } ?>
	<p><label for="event-location"><?php esc_attr_e('Location', 'very-simple-event-list'); ?></label>
		<input class="widefat" id="event-location" type="text" name="event-location" placeholder="<?php esc_attr_e('Example', 'very-simple-event-list'); ?>: <?php esc_attr_e('Times Square', 'very-simple-event-list'); ?>" value="<?php echo esc_attr($location); ?>" />
	</p>
	<p><label for="event-link"><?php esc_attr_e('More info link', 'very-simple-event-list'); ?></label>
		<input class="widefat" id="event-link" type="text" name="event-link" placeholder="<?php esc_attr_e('Example', 'very-simple-event-list'); ?>: <?php esc_attr_e('www.example.com/more-info', 'very-simple-event-list'); ?>" value="<?php echo esc_url($link); ?>" />
	</p>
	<p><label for="event-link-label"><?php esc_attr_e('Link label', 'very-simple-event-list'); ?></label>
		<input class="widefat" id="event-link-label" type="text" name="event-link-label" placeholder="<?php esc_attr_e('Example', 'very-simple-event-list'); ?>: <?php esc_attr_e('More info', 'very-simple-event-list'); ?>" value="<?php echo esc_attr($link_label); ?>" />
	</p>
	<p><input class="checkbox" id="event-link-target" type="checkbox" name="event-link-target" value="yes" <?php checked(esc_attr($link_target), 'yes'); ?> />
		<label for="event-link-target"><?php esc_attr_e('Open link in new window', 'very-simple-event-list'); ?></label>
	</p>
	<p><input class="checkbox" id="event-link-title" type="checkbox" name="event-link-title" value="yes" <?php checked(esc_attr($link_title), 'yes'); ?> />
		<label for="event-link-title"><?php esc_attr_e('Redirect event title to the more info link', 'very-simple-event-list'); ?></label><br>
		<input class="checkbox" id="event-link-image" type="checkbox" name="event-link-image" value="yes" <?php checked(esc_attr($link_image), 'yes'); ?> />
		<label for="event-link-image"><?php esc_attr_e('Redirect featured image to the more info link', 'very-simple-event-list'); ?></label><br>
		<?php esc_attr_e('The more info link will be hidden in frontend.', 'very-simple-event-list'); ?>
	</p>
	<p><label for="event-summary"><?php esc_attr_e('Custom summary', 'very-simple-event-list'); ?></label>
		<textarea id="event-summary" name="event-summary" class="large-text" rows="6" placeholder="<?php esc_attr_e('This will replace the default summary', 'very-simple-event-list'); ?>"><?php echo wp_kses_post($summary); ?></textarea>
	</p>
<?php
}

// save event
function vsel_save_event_info($post_id)
{
	// get current timezone
	$current_zone = date_default_timezone_get();
	// set utc timezone for strtotime
	date_default_timezone_set('UTC');
	// get setting for one date instead of start date and end date
	$one_date = get_option('vsel-setting-58');
	// check if nonce is set
	if (! isset($_POST['vsel_nonce'])) {
		return;
	}
	// verify that nonce is valid
	if (! wp_verify_nonce($_POST['vsel_nonce'], 'vsel_nonce_action')) {
		return;
	}
	// if this is an autosave, our form has not been submitted, so do nothing
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
		return;
	}
	// check user permission
	if ((get_post_type() != 'event') || ! current_user_can('edit_post', $post_id)) {
		return;
	}

	if (isset($_POST['vsel_is_recurring'])) {
		update_post_meta($post_id, 'vsel_is_recurring', 'yes');
	} else {
		update_post_meta($post_id, 'vsel_is_recurring', 'no');
	}
	if (isset($_POST['vsel_recurrence_type'])) {
		update_post_meta($post_id, 'vsel_recurrence_type', sanitize_text_field($_POST['vsel_recurrence_type']));
	}
	if (isset($_POST['vsel_recurrence_end_date'])) {
		update_post_meta($post_id, 'vsel_recurrence_end_date', sanitize_text_field($_POST['vsel_recurrence_end_date']));
	}

	// set start time
	if (isset($_POST['event-start-time-hour'])) {
		$start_hour = $_POST['event-start-time-hour'];
	} else {
		$start_hour = '00';
	}
	if (isset($_POST['event-start-time-minute'])) {
		$start_minute = $_POST['event-start-time-minute'];
	} else {
		$start_minute = '00';
	}
	// set end time
	if (isset($_POST['event-end-time-hour'])) {
		$end_hour = $_POST['event-end-time-hour'];
	} else {
		$end_hour = '00';
	}
	if (isset($_POST['event-end-time-minute'])) {
		$end_minute = $_POST['event-end-time-minute'];
	} else {
		$end_minute = '00';
	}
	// checking values and save fields
	if ($one_date == 'yes') {
		if (isset($_POST['event-end-date'])) {
			$start_date = $_POST['event-end-date'];
			$start_date_time = $start_date . $start_hour . $start_minute;
			update_post_meta($post_id, 'event-start-date', sanitize_text_field(strtotime($start_date_time)));
		}
	} else {
		if (isset($_POST['event-start-date'])) {
			$start_date = $_POST['event-start-date'];
			$start_date_time = $start_date . $start_hour . $start_minute;
			update_post_meta($post_id, 'event-start-date', sanitize_text_field(strtotime($start_date_time)));
		}
	}
	if (isset($_POST['event-end-date'])) {
		$end_date = $_POST['event-end-date'];
		$end_date_time = $end_date . $end_hour . $end_minute;
		update_post_meta($post_id, 'event-date', sanitize_text_field(strtotime($end_date_time)));
	}
	if (isset($_POST['event-time'])) {
		update_post_meta($post_id, 'event-time', sanitize_text_field($_POST['event-time']));
	}
	if (isset($_POST['event-hide-end-time'])) {
		update_post_meta($post_id, 'event-hide-end-time', 'yes');
	} else {
		update_post_meta($post_id, 'event-hide-end-time', 'no');
	}
	if (isset($_POST['event-all-day'])) {
		update_post_meta($post_id, 'event-all-day', 'yes');
	} else {
		update_post_meta($post_id, 'event-all-day', 'no');
	}
	if (isset($_POST['event-location'])) {
		update_post_meta($post_id, 'event-location', sanitize_text_field($_POST['event-location']));
	}
	if (isset($_POST['event-link'])) {
		update_post_meta($post_id, 'event-link', esc_url_raw($_POST['event-link']));
	}
	if (isset($_POST['event-link-label'])) {
		update_post_meta($post_id, 'event-link-label', sanitize_text_field($_POST['event-link-label']));
	}
	if (isset($_POST['event-link-target'])) {
		update_post_meta($post_id, 'event-link-target', 'yes');
	} else {
		update_post_meta($post_id, 'event-link-target', 'no');
	}
	if (isset($_POST['event-link-title'])) {
		update_post_meta($post_id, 'event-link-title', 'yes');
	} else {
		update_post_meta($post_id, 'event-link-title', 'no');
	}
	if (isset($_POST['event-link-image'])) {
		update_post_meta($post_id, 'event-link-image', 'yes');
	} else {
		update_post_meta($post_id, 'event-link-image', 'no');
	}
	if (isset($_POST['event-summary'])) {
		update_post_meta($post_id, 'event-summary', wp_kses_post($_POST['event-summary']));
	}
	// set current timezone again
	date_default_timezone_set($current_zone);
}
add_action('save_post', 'vsel_save_event_info');

function vsel_include_recurring_events($query)
{
	if (!is_admin() && $query->is_main_query() && (is_post_type_archive('event') || is_tax('event_cat'))) {
		$meta_query = $query->get('meta_query', array());

		$today = vsel_timestamp_today();

		$meta_query[] = array(
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
					'value' => $today,
					'compare' => '>=',
					'type' => 'DATE'
				)
			)
		);

		$query->set('meta_query', $meta_query);
	}
}
add_action('pre_get_posts', 'vsel_include_recurring_events');

function vsel_generate_recurring_dates($start_date, $end_date, $recurrence_type)
{
	$dates = array();
	$current_date = new DateTime($start_date);
	$end_date = new DateTime($end_date);

	while ($current_date <= $end_date) {
		$dates[] = $current_date->format('Y-m-d');
		$current_date->modify('+1 week');
	}

	return $dates;
}

function vsel_display_recurring_events($content)
{
	if (is_singular('event')) {
		$post_id = get_the_ID();
		$is_recurring = get_post_meta($post_id, 'vsel_is_recurring', true);

		if ($is_recurring == 'yes') {
			$start_date = get_post_meta($post_id, 'event-start-date', true);
			$end_date = get_post_meta($post_id, 'vsel_recurrence_end_date', true);
			$recurrence_type = 'weekly';

			$recurring_dates = vsel_generate_recurring_dates(date('Y-m-d', $start_date), $end_date, $recurrence_type);

			$content .= '<h3>' . esc_html__('Recurring Dates:', 'very-simple-event-list') . '</h3><ul>';
			foreach ($recurring_dates as $date) {
				$content .= '<li>' . esc_html(date_i18n('l, F j, Y', strtotime($date))) . '</li>';
			}
			$content .= '</ul>';
		}
	}

	return $content;
}
add_filter('the_content', 'vsel_display_recurring_events');

// remove default custom fields metabox from the editor
function vsel_remove_default_metabox()
{
	remove_meta_box('postcustom', 'event', 'normal');
}
add_action('admin_menu', 'vsel_remove_default_metabox');

// dashboard event columns
function vsel_custom_columns($defaults)
{
	// get settings to disable time and location column
	$disable_time_column = get_option('vsel-setting-56');
	$disable_loc_column = get_option('vsel-setting-57');
	$disable_menu_order_column = get_option('vsel-setting-61');

	unset($defaults['date']);
	$defaults['recurring_column'] = __('Recurring', 'very-simple-event-list');
	$defaults['start_date_column'] = __('Start date', 'very-simple-event-list');
	$defaults['end_date_column'] = __('End date', 'very-simple-event-list');
	if ($disable_time_column != 'yes') {
		$defaults['time_column'] = __('Time', 'very-simple-event-list');
	}
	if ($disable_loc_column != 'yes') {
		$defaults['location_column'] = __('Location', 'very-simple-event-list');
	}
	if ($disable_menu_order_column != 'yes') {
		$defaults['menu_order_column'] = __('Order', 'very-simple-event-list');
	}
	return $defaults;
}
add_filter('manage_event_posts_columns', 'vsel_custom_columns', 10);

function vsel_custom_columns_content($column_name, $post_id)
{
	// get utc timezone
	$utc_timezone = vsel_utc_timezone();
	// get timestamps
	$start_date_timestamp = get_post_meta($post_id, 'event-start-date', true);
	$end_date_timestamp = get_post_meta($post_id, 'event-date', true);
	// get time
	$time = get_post_meta($post_id, 'event-time', true);
	// get setting for one time field instead of start time and end time
	$one_time = get_option('vsel-setting-87');

	// start date column

	if ('recurring_column' == $column_name) {
		$is_recurring = get_post_meta($post_id, 'vsel_is_recurring', true);
		if ($is_recurring == 'yes') {
			echo esc_html__('Yes', 'very-simple-event-list');
		} else {
			echo esc_html__('No', 'very-simple-event-list');
		}
	}

	if ('start_date_column' == $column_name) {
		if (!empty($start_date_timestamp)) {
			echo wp_date(get_option('date_format'), esc_attr($start_date_timestamp), $utc_timezone);
		} else {
			echo '<span aria-hidden="true">&mdash;</span>';
		}
	}
	// end date column
	if ('end_date_column' == $column_name) {
		if (!empty($end_date_timestamp)) {
			echo wp_date(get_option('date_format'), esc_attr($end_date_timestamp), $utc_timezone);
		} else {
			echo '<span aria-hidden="true">&mdash;</span>';
		}
	}
	// time column
	if ('time_column' == $column_name) {
		if ($one_time != 'yes') {
			if (!empty($start_date_timestamp) && !empty($end_date_timestamp)) {
				echo wp_date(get_option('time_format'), esc_attr($start_date_timestamp), $utc_timezone) . ' - ' . wp_date(get_option('time_format'), esc_attr($end_date_timestamp), $utc_timezone);
			} else {
				echo '<span aria-hidden="true">&mdash;</span>';
			}
		} else {
			if (!empty($time)) {
				echo esc_attr($time);
			} else {
				echo '<span aria-hidden="true">&mdash;</span>';
			}
		}
	}
	// location column
	if ('location_column' == $column_name) {
		$location = get_post_meta($post_id, 'event-location', true);
		if (!empty($location)) {
			echo esc_attr($location);
		} else {
			echo '<span aria-hidden="true">&mdash;</span>';
		}
	}
	// order column
	if ('menu_order_column' == $column_name) {
		$order = get_post($post_id)->menu_order;
		if (!empty($order)) {
			echo esc_attr($order);
		} else {
			echo '<span aria-hidden="true">0</span>';
		}
	}
}
add_action('manage_event_posts_custom_column', 'vsel_custom_columns_content', 10, 2);

// make event date column sortable
function vsel_make_columns_sortable($columns)
{
	$columns['start_date_column'] = 'event-start-date';
	$columns['end_date_column'] = 'event-date';
	return $columns;
}
add_filter('manage_edit-event_sortable_columns', 'vsel_make_columns_sortable');

function vsel_start_date_column_sortable($vars)
{
	if (is_admin()) {
		if (isset($vars['orderby']) && 'event-start-date' == $vars['orderby']) {
			$vars = array_merge($vars, array(
				'meta_key' => 'event-start-date',
				'orderby' => 'meta_value_num'
			));
		}
	}
	return $vars;
}
add_filter('request', 'vsel_start_date_column_sortable');

function vsel_end_date_column_sortable($vars)
{
	if (is_admin()) {
		if (isset($vars['orderby']) && 'event-date' == $vars['orderby']) {
			$vars = array_merge($vars, array(
				'meta_key' => 'event-date',
				'orderby' => 'meta_value_num'
			));
		}
	}
	return $vars;
}
add_filter('request', 'vsel_end_date_column_sortable');

// add categories to event css class
function vsel_event_cats()
{
	// set global
	global $post;

	$terms = get_the_terms($post->ID, 'event_cat');
	if ($terms && ! is_wp_error($terms)) {
		$cats = array();
		foreach ($terms as $term) {
			$cats[] = $term->slug;
		}
		$vsel_cats = implode(" ", $cats);
		return ' ' . $vsel_cats;
	} else {
		return '';
	}
}

// add status to event css class
function vsel_event_status()
{
	// set global
	global $post;
	// get timestamps
	$start_date_timestamp = get_post_meta($post->ID, 'event-start-date', false);
	$end_date_timestamp = get_post_meta($post->ID, 'event-date', false);
	$today = vsel_timestamp_today();
	$tomorrow = vsel_timestamp_tomorrow();

	$start_date = array();
	$end_date = array();
	foreach ($start_date_timestamp as $term) {
		$start_date = $term;
	}
	foreach ($end_date_timestamp as $term) {
		$end_date = $term;
	}
	if (($start_date < $tomorrow) && ($end_date >= $today)) {
		return ' vsel-upcoming vsel-current';
	} elseif ($end_date >= $tomorrow) {
		return ' vsel-upcoming vsel-future';
	} elseif ($end_date < $today) {
		return ' vsel-past';
	} else {
		return '';
	}
}

// add categories to single event body class
function vsel_single_event_body_class_cats($classes)
{
	if (is_singular('event')) {
		// set global
		global $post;

		$terms = get_the_terms($post->ID, 'event_cat');
		if ($terms && ! is_wp_error($terms)) {
			foreach ($terms as $term) {
				$classes[] = 'single-' . $term->slug;
			}
		}
	}
	return $classes;
}
add_filter('body_class', 'vsel_single_event_body_class_cats');

// add status to single event body class
function vsel_single_event_body_class_status($classes)
{
	if (is_singular('event')) {
		$status_function = substr(vsel_event_status(), 1);
		if (!empty($status_function)) {
			$statuses = explode(' ', $status_function);
			foreach ($statuses as $status) {
				$classes[] = 'single-' . $status;
			}
		}
	}
	return $classes;
}
add_filter('body_class', 'vsel_single_event_body_class_status');

// add class to pagination
function vsel_prev_posts()
{
	return 'class="vsel-prev"';
}
add_filter('previous_posts_link_attributes', 'vsel_prev_posts', 10);

function vsel_next_posts()
{
	return 'class="vsel-next"';
}
add_filter('next_posts_link_attributes', 'vsel_next_posts', 10);

// add settings link
function vsel_action_links($links)
{
	$settingslink = array('<a href="' . admin_url('options-general.php?page=vsel') . '">' . __('Settings', 'very-simple-event-list') . '</a>');
	return array_merge($links, $settingslink);
}
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'vsel_action_links');

function vsel_enqueue_admin_scripts( $hook ) {
    global $post;
    if ( $hook == 'post-new.php' || $hook == 'post.php' ) {
        if ( 'event' === $post->post_type ) {
            wp_enqueue_script( 'vsel-admin-script', plugins_url( 'js/vsel-admin.js', __FILE__ ), array( 'jquery' ), '1.0.0', true );
        }
    }
}
add_action( 'admin_enqueue_scripts', 'vsel_enqueue_admin_scripts' );


// include files
include 'vsel-options.php';
include 'vsel-block.php';
include 'vsel-widget.php';
include 'vsel-page-shortcodes.php';
include 'vsel-widget-shortcodes.php';
include 'vsel-template-support.php';
include 'vsel-feed.php';
