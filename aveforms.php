<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://avenir.ro
 * @since             1.0.0
 * @package           Aveforms
 *
 * @wordpress-plugin
 * Plugin Name:       AveForms
 * Plugin URI:        https://avenir.ro
 * Description:       This plugin will hold our contact form(s).
 * Version:           1.0.0
 * Author:            Adrian Voicu
 * Author URI:        https://avenir.ro/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       aveforms
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

require plugin_dir_path( __FILE__ ) . 'includes/shortcodes.php';
