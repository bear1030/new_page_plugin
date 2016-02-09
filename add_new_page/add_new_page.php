<?php

/*
Plugin Name: Add New Page
Plugin URI: https://github.com/elliotcondon/acf-field-type-template
Description: This is adding new page plugin.
Version: 1.0.0
Author: Hong
Author URI: AUTHOR_URL
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

/* Runs when plugin is activated */
register_activation_hook(__FILE__,'my_plugin_install'); 

/* Runs on plugin deactivation*/
register_deactivation_hook( __FILE__, 'my_plugin_remove' );	
if (is_admin()){		
	/* Call the html code */
	add_action('admin_menu', 'hello_world_admin_menu');
	function hello_world_admin_menu() {
		add_menu_page('Add New Page', 'Add New Page', 'manage_options', 'add_new_page', 'new_page_html_page');
	}
}


function my_plugin_install() {

}

function my_plugin_remove() {

}




function new_page_html_page() {
echo '<div>';
echo '<h2>New Page Options</h2>';

echo '<form method="post" action="admin.php?page=add_new_page">';
wp_nonce_field('update-options');

echo '<table width="510">';
echo '<tr valign="top">';
echo '<th width="92" scope="row">Enter URL</th>';
echo '<td width="406">';
echo '<input name="trial_page_url" type="text" id="trial_page_url"
value="" />';
echo '</td>';

echo '<th width="92" scope="row">Enter Title</th>';
echo '<td width="406">';
echo '<input name="trial_page_title" type="text" id="trial_page_title"
value="" />';
echo '</td>';

echo '<th width="92" scope="row">Enter Description</th>';
echo '<td width="406">';
echo '<input name="trial_page_desc" type="text" id="trial_page_desc"
value="" />';
echo '</td>';

echo '</tr></table>';

echo '<input type="hidden" name="action" value="update" />';
echo '<input type="hidden" name="page_options" value="trial_page_url" />';

echo '<p>';
echo '<input type="submit" name="add_page" value="Save Changes" />';
echo '</p>';

echo '</form>';
echo '</div>';
}

if (isset($_REQUEST['add_page'])){
	add_new_page();
}

function add_new_page(){
	global $wpdb;
    $the_page_title = $_REQUEST['trial_page_title'];
    $the_page_desc = $_REQUEST['trial_page_desc'];
    $the_page_url = $_REQUEST['trial_page_url'];
    $the_page_name = 'trial_page';

    $the_page = get_page_by_title( $the_page_title );

    if ( ! $the_page ) {

        // Create post object
        $_p = array();
        $_p['post_title'] = $the_page_title;
        $_p['post_content'] = $the_page_desc;
        $_p['post_status'] = 'publish';
        $_p['post_type'] = 'page';
        $_p['post_name'] = $the_page_url;
        $_p['comment_status'] = 'closed';
        $_p['ping_status'] = 'closed';
        $_p['post_category'] = array(1); // the default 'Uncatrgorised'

        // Insert the post into the database
        $the_page_id = wp_insert_post( $_p );

    }
    else {
        // the plugin may have been previously active and the page may just be trashed...

        $the_page_id = $the_page->ID;

        //make sure the page is not trashed...
        $the_page->post_status = 'publish';
        $the_page_id = wp_update_post( $the_page );

    }
	
}
	
function my_plugin_query_parser( $q ) {

$the_page_name = get_option( "my_plugin_page_name" );
$the_page_id = get_option( 'my_plugin_page_id' );

$qv = $q->query_vars;

// have we NOT used permalinks...?
if( !$q->did_permalink AND ( isset( $q->query_vars['page_id'] ) ) AND ( intval($q->query_vars['page_id']) == $the_page_id ) ) {

$q->set('my_plugin_page_is_called', TRUE );
return $q;

}
elseif( isset( $q->query_vars['pagename'] ) AND ( ($q->query_vars['pagename'] == $the_page_name) OR ($_pos_found = strpos($q->query_vars['pagename'],$the_page_name.'/') === 0) ) ) {

$q->set('my_plugin_page_is_called', TRUE );
return $q;

}
else {

$q->set('my_plugin_page_is_called', FALSE );
return $q;

}

}
add_filter( 'parse_query', 'my_plugin_query_parser' );
?>