<?php

/**
 * @file
 * General options page.
 *
 * Created by: Anees
 * https://dreamwarrior.com/
 */

function import_events(){

    
?>
<div class="wrap" id="wp-media-grid" data-search="">
  <h1 class="wp-heading-inline"><?php echo DWEI_TITLE;?></h1>
</div>  
    
<div class="wrap">
  <div class="row">
    <div class="col-md-4">
    <form method="POST" action="/wp-admin/admin.php?page=add_update_events">
      <h1>
        <input type="hidden" name="import" value="1">
        <input type="submit" class="btn btn-primary" name="submit" value="Update Calendar" onclick="return confirm('Are you sure you want to Import Events?');">
      </h1>
    </form> 
    </div>    
  </div>
<?php 
global $wpdb;
$current_date = date('Y-m-d');

$table = $wpdb->prefix . 'temp_events';
$new_events = "SELECT COUNT(id) AS count 
FROM {$table}";
$archive_events_rows = $wpdb->get_results($new_events);
//Pagination 
$limit = 25;
$page_id = 1;
$total_pages = 0;

if(!isset($_GET['page_id'])){
  $page_id = 1;
}else{
  $page_id = $_GET['page_id'];
}


if(isset($archive_events_rows)){
  $total_pages = ceil(($archive_events_rows[0]->count / $limit));
}
// Calculate the offset for the query
$offset = ($page_id - 1)  * $limit;

// Some information to display to the user
$start = $offset + 1;
$end = ($offset + $limit);

//get current events 
$rs_events = "SELECT * 
  FROM {$table} order by TimeEventStart limit $start,$end";
$rs_events_rows = $wpdb->get_results($rs_events);


?>

<div class="wrap" id="wp-media-grid" data-search="">
<p><span>New & Updated Events</span></p>
</div>  
  
<div class="wrap">
<table class="wp-list-table widefat fixed striped table-view-list posts">
  <thead>
  <tr>
    <th width="60" scope="col" id="id" class="manage-column column-primary">
    <span>ID</span>
    </td>
    <th width="200"  scope="col" id="title" class="manage-column column-primary">
    <span>Title</span>
    </th>
    <th width="180" scope="col" id="location" class="manage-column column-primary">
    <span>Location</span>
    </th>
    <th width="150"  scope="col" id="Event Type" class="manage-column column-primary">
    <span>Event Type</span>
    </th>
    <th width="125"  scope="col" id="Start Date" class="manage-column column-primary">
    <span>Start Date</span>
    </th>
    <th width="125"   scope="col" id="End Date" class="manage-column column-primary">
    <span>End Date</span>
    </th>  
    <th width="0"   scope="col" id="Status" class="manage-column column-primary">
    <span>Status</span>
    </th>  
    <th width="140"   scope="col" id="Updated_Field" class="manage-column column-primary">
    <span>Updated Field</span>
    </th>                       
    <th width="80" scope="col" id="Actions" class="manage-column column-primary">
    <span>Actions</span>
    </th>           
  </tr>
  </thead>

  <tbody id="the-list">
  <?php 
  if(isset($rs_events_rows) && !empty($rs_events_rows)){
    foreach ( $rs_events_rows as $rs_events_row )   { 
  ?>
      <tr id="post-1" class="iedit">
        <td>
        <?php echo $rs_events_row->EventID; ?>
        </td>
        <td>
        <?php echo $rs_events_row->Title; ?> 
        </td>
        <td>
        <?php echo $rs_events_row->Location ; ?>
        </td>
        <td>
        <?php echo $rs_events_row->EventTypeName ; ?>
        </td>
        <td>
        <?php echo date("Y-m-d H:i", strtotime($rs_events_row->TimeEventStart)); ?>
        </td>
        <td>
        <?php echo date("Y-m-d H:i", strtotime($rs_events_row->TimeEventEnd)); ?>
        </td>
        <td>
        <?php echo ($rs_events_row->Canceled=='false') ? 'Active' : 'Canceled' ; ?>
        </td>
        <td> <?php echo $rs_events_row->field_updated ; ?> </td>
        <td>
        <?php  
        if($rs_events_row->status != 'added'){ ?> 
        <a href="<?php echo get_admin_url().'admin.php?page=edit_event&action=edit_event&event_id='.$rs_events_row->EventID; ?> ">Edit</a> 
        <?php  } ?> 
        </td>
        
      </tr>  
  <?php 
    }
  }else{
  ?>
  <tr>
    <th colspan="9" class="manage-column column-cb check-column">
      No records found!
    </th>	
  </tr>
  <?php   
  }
  ?>
    </tbody>

  <tfoot>
  <tr>
    <th colspan="9" class="manage-column column-cb check-column">
      <center>
      <?php echo generate_pagination($total_pages, $page_id,'/wp-admin/admin.php?page=upcoming_events', $limit);?>
      </center>
    </th>	
  </tr>
  </tfoot>

</table>
</div>  
</div>
<?php 
}

