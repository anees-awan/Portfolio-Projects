<?php

/**
 * @file
 * General options page.
 *
 * Created by: Anees
 * https://dreamwarrior.com/
 */

function events_shortcodes(){

  global $wpdb;

  $table = $wpdb->prefix . 'all_events';
  //current events 
  $event_types = "SELECT distinct EventTypeName 
      FROM {$table}";
  $event_type_rows = $wpdb->get_results($event_types);

?>

<div class="wrap" id="wp-media-grid" data-search="">
  <h1 class="wp-heading-inline"><?php echo DWEI_TITLE;?></h1>
  <p><span>Shortcode Manager</span></p>
</div>  
    
<div class="wrap border">
  <form>
    <div class="row  pt-5">
      <div class="col-md-2">
      </div>  
      <div class="col-md-4">
        <div class="form-group">
          <label for="event_type">Event Type:</label>
          <select name="event_type" class="form-control" id="event_type">
          <?php   
          if(isset($event_type_rows) && !empty($event_type_rows)){
            foreach ( $event_type_rows as $event_type_row )   { 
          ?>
              <option value="<?php echo $event_type_row->EventTypeName;?>"><?php echo $event_type_row->EventTypeName;?></option>
          <?php 
            }
          }else{
          ?>
            <option value="">None</option>
          <?php 
          }    
          ?>
            </select>
        </div>
      </div>  
      <div class="col-md-4">
        <div class="form-group">
          <label for="display_type">Display Type:</label>
          <select class="form-control" id="display_type">
          <option value="card">Card</option>
          <option value="list">List</option> 
          </select>
        </div>  
      </div>
      <div class="col-md-2">
      </div>  
    </div>
    <div class="row">
      <div class="col-md-2">
      </div>  
      <div class="col-md-4">
        <div class="form-group">
          <label for="rows">Select Total Records:</label>
          <select class="form-control" id="rows"> 
          <option value="2">2</option>
          <option value="5">5</option>  
          <option value="10">10</option>
          <option value="0">50</option>
          <option value="100">100</option>
          <option value="">All</option>
          </select>
        </div>
      </div>  
      <div class="col-md-4">
       <!-- 
       <div class="form-group">
          <label for="rows_per_page">Rows Per Page:</label> 
          <select class="form-control" id="rows_per_page">
          <option value="2">2</option>
          <option value="3">3</option>
          <option value="5">5</option>
          <option value="10">10</option>
          <option value="20">20</option>
          <option value="50">50</option>
          </select>
        </div>
        -->  
      </div>
        
      <div class="col-md-2">
      </div>  
    </div>
    <!--
    <div class="row">
      <div class="col-md-2">
      </div>  
      <div class="col-md-4">
        <div class="form-group">
          <label for="pagination">Pagination:</label>
          <select class="form-control" id="pagination">
          <option value="true">True</option>
          <option value="false">False</option>
          </select>
        </div>
      </div>  
      <div class="col-md-4">
        
      </div>
      <div class="col-md-2">
      </div>  
    </div>
    -->
    <div class="row">
      <div class="col-md-12 text-center mb-2 mt-2">
        <button type="button" class="btn btn-primary" onClick="generate_shortcode();">Generate Shortcode</button>
      </div>  
    </div>  

    <div class="row">
      <div class="col-md-1">
      </div>  
      <div class="col-md-10 text-center">
        <textarea class="form-control" id="shortcode_value" rows="3"></textarea>
      </div>  
      <div class="col-md-1">
      </div>  
    </div>  
  </form>
</div>
<?php 
}
?>
<script>
function generate_shortcode(){
  
  var event_type = document.getElementById("event_type").value;
  var display_type = document.getElementById("display_type").value;
  var rows = document.getElementById("rows").value;
  //var rows_per_page = document.getElementById("rows_per_page").value;
  //var pagination = document.getElementById("pagination").value;
  
  var shortcode = "[event-code event_type= '"+ event_type + "' display_type='"+ display_type + "' rows= '" + rows + "' target= '_blank']";
  //var shortcode = "[event-code event_type= '"+ event_type + "' display_type='"+ display_type + "' rows= '" + rows + "' rows_per_page= '" + rows_per_page + "' pagination= '" + pagination + "' target= '_blank']";

  document.getElementById("shortcode_value").value = shortcode;
}
</script>