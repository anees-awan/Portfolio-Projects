<?php

/**
 * @file
 * General options page.
 *
 * Created by: Anees
 * https://dreamwarrior.com/
 */

function dw_event_dashboard(){

  global $wpdb;
  $current_date = date('Y-m-d');

  $table = $wpdb->prefix . 'all_events';
  //current events 
  $current_events = "SELECT COUNT(id) AS count 
      FROM {$table} WHERE TimeEventStart like '". $current_date."%'";
  $current_events_rows = $wpdb->get_results($current_events);
  //upcoming events 
  $upcoming_events = "SELECT COUNT(id) AS count 
      FROM {$table} WHERE TimeEventStart > '". $current_date."%'";
  $upcoming_events_rows = $wpdb->get_results($upcoming_events);
  //archived events 
  //upcoming events 
  $archive_events = "SELECT COUNT(id) AS count 
      FROM {$table} WHERE TimeEventStart < '". $current_date."%'";
  $archive_events_rows = $wpdb->get_results($archive_events);
  
?>
<div class="wrap" id="wp-media-grid" data-search="">
  <h1 class="wp-heading-inline"><?php echo DWEI_TITLE;?></h1>
  <p><span>Dashboard</span></p>
</div>  
    
<div class="wrap">
  <div class="row">
    <div class="col-md-4">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Today's Events (<?php echo $current_events_rows[0]->count;?>)</h5>
          <p class="card-text">Get the list of today's events.</p>
          <a href="/wp-admin/admin.php?page=todays_events" class="btn btn-primary">Today's Events</a>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Upcoming Events (<?php echo $upcoming_events_rows[0]->count;?>)</h5>
          <p class="card-text">Get the list of upcoming events.</p>
          <a href="/wp-admin/admin.php?page=upcoming_events" class="btn btn-primary">Upcoming Events</a>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Archived Events (<?php echo $archive_events_rows[0]->count;?>)</h5>
          <p class="card-text">Get the list of archived events.</p>
          <a href="/wp-admin/admin.php?page=archived_events" class="btn btn-primary">Archived Events</a>
        </div>
      </div>
    </div>

  </div>

  <div class="row">

  <div class="col-md-4">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Import Events</h5>
          <p class="card-text">Import new events.</p>
          <a href="/wp-admin/admin.php?page=import_events" class="btn btn-primary">Import Events</a>
        </div>
      </div>
  </div>


  <div class="col-md-4">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Generate Shortcodes</h5>
          <p class="card-text">Generate shortcodes</p>
          <a href="/wp-admin/admin.php?page=events_shortcodes" class="btn btn-primary">Shortcode Manager</a>
        </div>
      </div>
    </div>
  </div>
</div>

</div>
<?php 
}