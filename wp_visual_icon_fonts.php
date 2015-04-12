<?php
/*
Plugin Name: WordPress Visual Icon Fonts
Plugin URI: http://wordpress.org/plugins/
Description: Easily and quickly add an extended 'Font Awesome' icon font icons to your content in the visual editor, visual icon management, search and filter all at your fingertips with this handy plugin.
Version: 0.5.7
Author:  Paul van Zyl
Author URI: http://profiles.wordpress.org/pushplaybang/
*/

/**
 * Copyright (c) 2013 Paul van Zyl. All rights reserved.
 *
 * Released under the GPLv2 license
 * http://www.gnu.org/licenses/gpl-2.0.html
 *
 * This is an add-on for WordPress
 * http://wordpress.org/
 *
 * **********************************************************************
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * **********************************************************************
 *
 */


// Include Options
include 'wpvi-options.php';

function wpvi_return_font() {
	$opfont = get_option( 'font_select' );
	if ( isset($opfont) && !empty($opfont) ) {
		return $opfont;
	} else {
		return 'fa4';
	}
}

/* Load Icon CSS
- - - - - - - - - - - - - - - - - - - - - - - - - - */
function wpvi_set_css() {
	return plugins_url('/css/wpvi-'.wpvi_return_font().'.css', __FILE__ );
}

/* Load Icon List
- - - - - - - - - - - - - - - - - - - - - - - - - - */
function wpvi_include_icon_list() {
	include 'iconlists/wpvi-'.wpvi_return_font().'.php';
}

/* Register and Enqueue Admin Scripts and Styles
- - - - - - - - - - - - - - - - - - - - - - - - - - */
function wpvi_editor_scripts() {
	// admin scripts
	wp_register_script('chosen', plugins_url('/js/chosen.js',__FILE__) );
	wp_register_script('wpvi-admin-js', plugins_url('/js/'.wpvi_return_font().'-admin.js',__FILE__) );
	wp_enqueue_script('chosen');
	wp_enqueue_script('wpvi-admin-js');

	// admin style
	wp_register_style('wpvi-admin-css', plugins_url('/css/wpvi-admin-style.css', __FILE__ ) );
	wp_enqueue_style('wpvi-admin-css');

	// Font CSS
	wp_register_style('wpvi-font-css', wpvi_set_css() );
	wp_enqueue_style('wpvi-font-css');
}

/* Add Icon CSS to the Editor
- - - - - - - - - - - - - - - - - - - - - - - - - - */
function wpvi_plugin_mce_css( $mce_css ) {
	if ( ! empty( $mce_css ) ) {
		$mce_css .= ',';
	}

	$mce_css .= wpvi_set_css();
	return $mce_css;
}

/* Add Icon Select Drop Down above editor
- - - - - - - - - - - - - - - - - - - - - - - - - - */
function wpvi_add_icon_select() {
	$icons = wpvi_icon_list();
    echo '<a id="ico-trig" class="button">Icons</a><span class="ico-wrap"><select id="icon_select"><option>Icons</option>';
    	foreach($icons as $icon) {
    		echo '<option>'.$icon.'</option>';
    	}
    echo '</select><a id="ico-close" class="button">X</a></span>';
}

/* Additional editor buttons
  - - - - - - - - - - - - - - - - - - - - - - - - - */
function wpvi_add_more_buttons($buttons) {
	$buttons[] = 'fontsizeselect';
	$buttons[] = 'forecolorpicker';
	return $buttons;
}

/* Add a custom selection
  - - - - - - - - - - - - - - - - - - - - - - - - - */
function wpvi_text_sizes($initArray){
	$initArray['theme_advanced_font_sizes'] = "10px,12px,14px,16px,18px,20px,22px,24px,30px,36px,48px,54px,61px,72px,84px,96px";
	return $initArray;
}

/* Add Actions for plugin backend
  - - - - - - - - - - - - - - - - - - - - - - - - - */
function wpvi_admin() {
	add_action('edit_form_after_title', 'wpvi_icon_list', 10 );
	add_action('edit_form_after_title', 'wpvi_editor_scripts', 11);
	add_action('media_buttons','wpvi_add_icon_select',12);
	add_filter( 'mce_css', 'wpvi_plugin_mce_css' );
	add_filter("mce_buttons_3", "wpvi_add_more_buttons");
	add_filter('tiny_mce_before_init', 'wpvi_text_sizes');
}


/* Register and Enqueue Icons on the front End
  - - - - - - - - - - - - - - - - - - - - - - - - - */
function wp_v_icon_frontend_styles() {
	wp_register_style('wp-v-icons-css', wpvi_set_css() );
	wp_enqueue_style('wp-v-icons-css');
}

/* Run Actions
  - - - - - - - - - - - - - - - - - - - - - - - - - */
add_action('admin_head', 'wpvi_include_icon_list');
add_action('admin_head', 'wpvi_admin');
add_action( 'wp_enqueue_scripts', 'wp_v_icon_frontend_styles' );


/* add a settings link to the plugin management page
  - - - - - - - - - - - - - - - - - - - - - - - - - */
function wpvi_plugin_add_settings_link( $links ) {
    $settings_link = '<a href="options-general.php?page=wpvi_plugin_options">Settings</a>';
    array_push( $links, $settings_link );
    return $links;
}

$plugin = plugin_basename( __FILE__ );

add_filter( "plugin_action_links_$plugin", 'wpvi_plugin_add_settings_link' );
