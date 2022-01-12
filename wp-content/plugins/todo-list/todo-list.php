<?php 
/*
  Plugin Name: To-do list
  Description: Todo plugin 
  Version: 1.0.0
  Author: Marcin Kusior
  Author URI: https://codeunion.pl
 */

 function mk_todo_setup_post_type() 
 {
     register_post_type('todo', 
     [
        'label' => 'Todo',
        'public' => true
    ]);
 }

 add_action('init', 'mk_todo_setup_post_type');

 function mk_remove_content()
 {
     remove_post_type_support('todo', 'editor');
 }

 add_action('init', 'mk_remove_content');

 function mk_database_creation()
 {
     global $wpdb;
     $todo_elements = $wpdb->prefix.'todo_elements';
     $charset = $wpdb->get_charset_collate;

     $query = "CREATE TABLE ".$todo_elements."(
         id int NOT NULL AUTO_INCREMENT,
         todo_id int NOT NULL,
         name text,
         status int,
         PRIMARY KEY (id)
         ) $charset;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

    dbDelta($query);
 }

 function mk_load_todo_template($template) 
 {
     global $post;

     if( $post->post_type === 'todo' && locate_template(['single-todo.php']) !== $template )
        return plugin_dir_path(__FILE__) . 'templates/single-todo.php';

     return $template;
 }

add_filter('single_template', 'mk_load_todo_template');

function mk_add_theme_scripts()
{
    wp_enqueue_style('todo-style', plugin_dir_url(__FILE__) . 'css/todo-style.css');
    wp_enqueue_script('todo-script', plugin_dir_url(__FILE__) . 'js/todo-script.js');
    wp_localize_script('todo-script', 'todo_script_object',
        [
            'ajaxurl' => admin_url('admin-ajax.php'),
        ]
    );
}

add_action('wp_enqueue_scripts', 'mk_add_theme_scripts');

function mk_select_todos()
{
    global $wpdb;

    $todo_id = $_REQUEST['todo_id'];

    $elements = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}todo_elements WHERE todo_id = {$todo_id}", OBJECT );

    $response = ['status' => 'success', 'elements' => $elements];

    echo json_encode($response);

    die();
}

add_action('wp_ajax_select_todos', 'mk_select_todos');
add_action('wp_ajax_nopriv_select_todos', 'mk_select_todos');

function mk_store_todo()
{
    if(!isset($_REQUEST))
        die();

    global $wpdb;

    $wpdb->insert(
        $wpdb->prefix.'todo_elements',
        [
            'name' => $_REQUEST['name'],
            'todo_id'=> $_REQUEST['todo_id'],
            'status' => 0,
        ],
        [
            '%s',
            '%d',
            '%d',
        ]
    );

    $response = ['status' => 'success', 'response' => ['todo_id' => $wpdb->insert_id]];

    echo json_encode($response);

    die();
}

add_action('wp_ajax_store_todo', 'mk_store_todo');
add_action('wp_ajax_nopriv_store_todo', 'mk_store_todo');

function mk_update_todo_name()
{
    if(!isset($_REQUEST))
        die();

    global $wpdb;

    $wpdb->update(
        $wpdb->prefix.'todo_elements',
        [
            'name' => $_REQUEST['name'],
        ],
        [
            'id' => $_REQUEST['id'],
        ]
    );

    $response = ['status' => 'success', 'response' => ['id' => $_REQUEST['id']]];

    echo json_encode($response);

    die();
}

add_action('wp_ajax_update_todo_name', 'mk_update_todo_name');
add_action('wp_ajax_nopriv_update_todo_name', 'mk_update_todo_name');

function mk_update_todo_status()
{
    if(!isset($_REQUEST))
        die();

    global $wpdb;

    $wpdb->update(
        $wpdb->prefix.'todo_elements',
        [
            'status' => $_REQUEST['status'],
        ],
        [
            'id' => $_REQUEST['id'],
        ]
    );

    $response = ['status' => 'success', 'response' => ['id' => $_REQUEST['id']]];

    echo json_encode($response);

    die();
}

add_action('wp_ajax_update_todo_status', 'mk_update_todo_status');
add_action('wp_ajax_nopriv_update_todo_status', 'mk_update_todo_status');


function mk_destroy_todo()
{
    if(!isset($_REQUEST))
        die();

    global $wpdb;

    $wpdb->delete(
        $wpdb->prefix.'todo_elements',
        [
            'id' => $_REQUEST['id'],
        ],
        [
            '%d',
        ]
    );

    $response = ['status' => 'success', 'response' => ['id' => $_REQUEST['id']]];

    echo json_encode($response);

    die();
}

add_action('wp_ajax_destroy_todo', 'mk_destroy_todo');
add_action('wp_ajax_nopriv_destroy_todo', 'mk_destroy_todo');


function mk_todo_activate() 
{
    mk_todo_setup_post_type();
    mk_database_creation();
    flush_rewrite_rules();
}

register_activation_hook(__FILE__, 'mk_todo_activate');

function mk_todo_deactivate() 
{
    unregister_post_type('todo');
    flush_rewrite_rules();
}

register_deactivation_hook(__FILE__, 'mk_todo_deactivate');  