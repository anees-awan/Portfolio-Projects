<?php

/**
 * @file
 * Custom table list class.
 *
 * Created by: Anees
 * https://dreamwarrior.com/
 */

// Load base class if it doesn't exist
if(!class_exists('WP_List_Table')) require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');

// Plugin class.
class Dwei_List_Users extends WP_List_Table {
  
  /**
   * Construct function
   * Set default settings.
   */
  function __construct() {
    global $status, $page;
    //Set parent defaults
    parent::__construct(array(
      'ajax' => FALSE,
      'singular' => 'user',
      'plural' => 'users',
    ));
  }
  
  
  /**
   * Define column methods.
   */
  function column_default($item, $column_name) {
    switch ($column_name) {
      case 'name':
      case 'username':
      case 'email':
      case 'subscription':
      case 'type':
      case 'action':
        return $item[$column_name];
      default:
        // Debug.
        // return print_r($item, TRUE);
    }
  }

  /**
   * Define columns.
   */
  function get_columns() {
    return array(
      'name' => 'Full name',
      'username' => 'Username',
      'email' => 'Email',
      'type' => 'Type',
      'action' => 'Action',
    );
  }
  
  /**
   * Sortable settings.
   */
  function get_sortable_columns() {
    return array(
      // Name is sorted by default (second parameter).
      'name' => array('name', TRUE),
      'username' => array('username', FALSE),
      'email' => array('email', FALSE),
    );
  }
  
  
  /**
   * Prepare the data.
   */
  function prepare_items() {
    
    $per_page = 25;
    
    // Column headers.
    $columns = $this->get_columns();
    $hidden = array();
    $sortable = $this->get_sortable_columns();
    $this->_column_headers = array($columns, $hidden, $sortable);
    
    /**
     * Prepare the data.
     */
    global $wpdb;

    /**
     * Sorting.
     */
    function usort_reorder($a,$b) {
      $orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'name';
      $order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'asc';
      $result = strcmp($a[$orderby], $b[$orderby]);
      return ($order === 'asc') ? $result : -$result;
    }
    usort($data, 'usort_reorder');
    
    
    // Pagination.
    $current_page = $this->get_pagenum();
    
    // Total number of items, necessary for pagination.
    $total_items = count($data);
    
    // Get only items for current page.
    $data = array_slice($data,(($current_page - 1) * $per_page),$per_page);
    
    // Done - add it to items.
    $this->items = $data;
    
    // Register pagination options.
    $this->set_pagination_args(array(
      'total_items' => $total_items,
      'per_page' => $per_page,
      'total_pages' => ceil($total_items / $per_page),
    ));
  }
  
}
