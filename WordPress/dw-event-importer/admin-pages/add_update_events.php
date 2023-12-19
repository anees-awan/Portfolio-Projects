<?php

/**
 * @file
 * General options page.
 *
 * Created by: Anees
 * https://dreamwarrior.com/
 */

function add_update_events(){

  if(isset($_POST['submit']) && $_POST['import']==1){
    // Initialize data and connection
        $soapUrl = "http://calendar.templeisaiah.com/MCAPI/MCAPIService.asmx?op=GetEvents"; // asmx URL of WSDL
        $soapUser = "api@api.com";  //  username
        $soapPassword = "MCAP1$"; // password
        $xml_post_string = dwei_create_xml_post_string($soapUser,$soapPassword);
        $headers = dwei_create_calendar_headers($xml_post_string);
        $url = $soapUrl;
    
        $response = dwei_get_soap_calendar_response($url,$soapUser,$soapPassword,$xml_post_string,$headers);
        $response = htmlspecialchars_decode($response);
        $response1 = str_replace("<soap:Body>","",$response);
        $response2 = str_replace("</soap:Body>","",$response1);
    
        // converting to XML
        $parser = simplexml_load_string($response2);
        $events = array();
        //echo "<pre>"; print_r($response2); echo "</pre>"; exit;
        if(isset($parser->GetEventsResponse->GetEventsResult->NewDataSet)){
          $calendar_data_set = $parser->GetEventsResponse->GetEventsResult->NewDataSet;
          global $wpdb;

          $table1 = $wpdb->prefix . 'temp_events';
          $table2 = $wpdb->prefix . 'all_events';
          
          $truncate = $wpdb->query("TRUNCATE TABLE {$table}");
      
          $event = array();
          foreach ($calendar_data_set->Data as $eventdata) {
      
                  $event_id = (string)$eventdata->EventID;
                  $event['EventDetailID']          = $eventdata->EventDetailID;
                  $event['EventID']                = $eventdata->EventID;
                  $event['Title']                  = $eventdata->Title;
                  $event['Description']            = $eventdata->Description;
                  $event['Location']               = $eventdata->Location;
                  $event['Canceled']               = $eventdata->Canceled;
                  $event['NoEndTime']              = $eventdata->NoEndTime;
                  $event['Priority']               = $eventdata->Priority;
                  $event['TimeEventStart']         = $eventdata->TimeEventStart;
                  $event['TimeEventEnd']           = $eventdata->TimeEventEnd;
                  $event['IsAllDayEvent']          = $eventdata->IsAllDayEvent;
                  $event['IsTimedEvent']           = $eventdata->IsTimedEvent;
                  $event['EventTypeID']            = $eventdata->EventTypeID;
                  $event['EventTypeName']          = $eventdata->EventTypeName;
                  $event['ContactName']            = $eventdata->ContactName;
                  $event['ContactEmail']           = $eventdata->ContactEmail;
                  $event['ContactPhone']           = $eventdata->ContactPhone;
                  $event['IsReOccurring']          = $eventdata->IsReOccurring;
                  $event['IsOnMultipleCalendars']  = $eventdata->IsOnMultipleCalendars;
                  $event['BookingID']              = $eventdata->BookingID;
                  $event['ReservationID']          = $eventdata->ReservationID;
                  $event['ConnectorID']            = $eventdata->ConnectorID;
                  $event['HideContactName']        = $eventdata->HideContactName;
                  $event['HideContactEmail']       = $eventdata->HideContactEmail;
                  $event['HideContactPhone']       = $eventdata->HideContactPhone;
                  $event['EventUpdatedBy']         = $eventdata->EventUpdatedBy;
                  $event['EventUpdatedDate']       = $eventdata->EventUpdatedDate;
                  $event['EventDetailUpdatedBy']   = $eventdata->EventDetailUpdatedBy;
                  $event['EventDetailUpdatedDate'] = $eventdata->EventDetailUpdatedDate;
                  $event['EventUpdatedDate']       = $eventdata->EventUpdatedDate;
                  $event['EventUpdatedDate']       = $event['EventUpdatedDate'];
                  $event['EventDetailUpdatedDate'] = $eventdata->EventDetailUpdatedDate;
                  $event['EventDetailUpdatedDate'] = $event['EventDetailUpdatedDate'];
                  $eventdate                       = date("Y-m-d H:i:s", strtotime($eventdata->EventDate));
                  $TimeEventEnd                    = date("Y-m-d H:i:s", strtotime($event['TimeEventEnd']));
      
                $event_exst = $wpdb->get_row( "SELECT * FROM {$table2} WHERE EventID=$event_id");
                if(isset($event_exst) && !empty($event_exst)){
      
                  $updated_field = '';
                  if($event_exst->Title != (string)$eventdata->Title){
                    $updated_field = "Title";
                  }
                  if($event_exst->Location != (string)$eventdata->Location){
                    $updated_field = "Location";
                  }
                  if($event_exst->Description != (string)$eventdata->Description){
                    $updated_field = "Description";
                  }
                  if($event_exst->ContactName != (string)$eventdata->ContactName){
                    $updated_field = "ContactName";
                  }
                  if($event_exst->ContactEmail != (string)$eventdata->ContactEmail){
                    $updated_field = "ContactEmail";
                  }
                  if($event_exst->ContactPhone != (string)$eventdata->ContactPhone){
                    $updated_field = "ContactPhone";
                  }
      
                  if(isset($updated_field) && !empty($updated_field)){
                  $temp_events = $wpdb->insert($table1, array(
                          'EventDetailID' => (string)$eventdata->EventDetailID,         
                          'EventID' => (string)$eventdata->EventID,
                          'Title' => (string)$eventdata->Title,
                          'Description' => (string)$eventdata->Description,
                          'Location' => (string)$eventdata->Location,
                          'Canceled' => (string)$eventdata->Canceled,
                          'NoEndTime' => (string)$eventdata->NoEndTime,
                          'Priority' => (string)$eventdata->Priority,
                          'TimeEventStart' => (string)$eventdata->TimeEventStart,
                          'TimeEventEnd' => (string)$eventdata->TimeEventEnd,
                          'IsAllDayEvent' => (string)$eventdata->IsAllDayEvent,
                          'IsTimedEvent' => (string)$eventdata->IsTimedEvent,
                          'EventTypeID' => (string)$eventdata->EventTypeID,
                          'EventTypeName' => (string)$eventdata->EventTypeName,
                          'ContactName' => (string)$eventdata->ContactName,
                          'ContactEmail' => (string)$eventdata->ContactEmail,
                          'ContactPhone' => (string)$eventdata->ContactPhone,
                          'IsReOccurring' => (string)$eventdata->IsReOccurring,
                          'IsOnMultipleCalendars' => (string)$eventdata->IsOnMultipleCalendars,
                          'BookingID' => (string)$eventdata->BookingID,
                          'ReservationID' => (string)$eventdata->ReservationID,
                          'ConnectorID' => (string)$eventdata->ConnectorID,
                          'HideContactName' => (string)$eventdata->HideContactName,
                          'HideContactEmail' => (string)$eventdata->HideContactEmail,
                          'HideContactPhone' => (string)$eventdata->HideContactPhone,
                          'EventUpdatedBy' => (string)$eventdata->EventUpdatedBy,
                          'EventUpdatedDate' => (string)$eventdata->EventUpdatedDate,
                          'EventDetailUpdatedBy' => (string)$eventdata->EventDetailUpdatedBy,
                          'EventDetailUpdatedDate' => (string)$eventdata->EventDetailUpdatedDate,
                          'field_updated' => $updated_field,
                          'status' => 'updated',
                        )); 
                      } 
                }else{
      
                    global $wpdb;
                  $result = $wpdb->insert($table2, array(
                          'EventDetailID' => (string)$eventdata->EventDetailID,         
                          'EventID' => (string)$eventdata->EventID,
                          'Title' => (string)$eventdata->Title,
                          'Description' => (string)$eventdata->Description,
                          'Location' => (string)$eventdata->Location,
                          'Canceled' => (string)$eventdata->Canceled,
                          'NoEndTime' => (string)$eventdata->NoEndTime,
                          'Priority' => (string)$eventdata->Priority,
                          'TimeEventStart' => (string)$eventdata->TimeEventStart,
                          'TimeEventEnd' => (string)$eventdata->TimeEventEnd,
                          'IsAllDayEvent' => (string)$eventdata->IsAllDayEvent,
                          'IsTimedEvent' => (string)$eventdata->IsTimedEvent,
                          'EventTypeID' => (string)$eventdata->EventTypeID,
                          'EventTypeName' => (string)$eventdata->EventTypeName,
                          'ContactName' => (string)$eventdata->ContactName,
                          'ContactEmail' => (string)$eventdata->ContactEmail,
                          'ContactPhone' => (string)$eventdata->ContactPhone,
                          'IsReOccurring' => (string)$eventdata->IsReOccurring,
                          'IsOnMultipleCalendars' => (string)$eventdata->IsOnMultipleCalendars,
                          'BookingID' => (string)$eventdata->BookingID,
                          'ReservationID' => (string)$eventdata->ReservationID,
                          'ConnectorID' => (string)$eventdata->ConnectorID,
                          'HideContactName' => (string)$eventdata->HideContactName,
                          'HideContactEmail' => (string)$eventdata->HideContactEmail,
                          'HideContactPhone' => (string)$eventdata->HideContactPhone,
                          'EventUpdatedBy' => (string)$eventdata->EventUpdatedBy,
                          'EventUpdatedDate' => (string)$eventdata->EventUpdatedDate,
                          'EventDetailUpdatedBy' => (string)$eventdata->EventDetailUpdatedBy,
                          'EventDetailUpdatedDate' => (string)$eventdata->EventDetailUpdatedDate,
                        ));  
      
                    $temp_events = $wpdb->insert($table1, array(
                          'EventDetailID' => (string)$eventdata->EventDetailID,         
                          'EventID' => (string)$eventdata->EventID,
                          'Title' => (string)$eventdata->Title,
                          'Description' => (string)$eventdata->Description,
                          'Location' => (string)$eventdata->Location,
                          'Canceled' => (string)$eventdata->Canceled,
                          'NoEndTime' => (string)$eventdata->NoEndTime,
                          'Priority' => (string)$eventdata->Priority,
                          'TimeEventStart' => (string)$eventdata->TimeEventStart,
                          'TimeEventEnd' => (string)$eventdata->TimeEventEnd,
                          'IsAllDayEvent' => (string)$eventdata->IsAllDayEvent,
                          'IsTimedEvent' => (string)$eventdata->IsTimedEvent,
                          'EventTypeID' => (string)$eventdata->EventTypeID,
                          'EventTypeName' => (string)$eventdata->EventTypeName,
                          'ContactName' => (string)$eventdata->ContactName,
                          'ContactEmail' => (string)$eventdata->ContactEmail,
                          'ContactPhone' => (string)$eventdata->ContactPhone,
                          'IsReOccurring' => (string)$eventdata->IsReOccurring,
                          'IsOnMultipleCalendars' => (string)$eventdata->IsOnMultipleCalendars,
                          'BookingID' => (string)$eventdata->BookingID,
                          'ReservationID' => (string)$eventdata->ReservationID,
                          'ConnectorID' => (string)$eventdata->ConnectorID,
                          'HideContactName' => (string)$eventdata->HideContactName,
                          'HideContactEmail' => (string)$eventdata->HideContactEmail,
                          'HideContactPhone' => (string)$eventdata->HideContactPhone,
                          'EventUpdatedBy' => (string)$eventdata->EventUpdatedBy,
                          'EventUpdatedDate' => (string)$eventdata->EventUpdatedDate,
                          'EventDetailUpdatedBy' => (string)$eventdata->EventDetailUpdatedBy,
                          'EventDetailUpdatedDate' => (string)$eventdata->EventDetailUpdatedDate,
                          'status' => 'added',
                        )); 
      
                        
                        $location = (string)$eventdata->Location;
                        $location_t = trim($location);
                        global $wpdb;
                        $venue_data = $wpdb->get_var($wpdb->prepare("SELECT ID FROM {$wpdb->posts} WHERE post_type='tribe_venue' AND post_title = %s",$location_t));
      
                        if($venue_data){
                            $venue_id = $venue_data;
                          }else{
                              $venue_args = array(
                                  'post_type' => 'tribe_venue',
                                  'post_title' => (string)$eventdata->Location,
                                  'post_status' => 'publish',
                                );
                          $venue_id = wp_insert_post($venue_args);
                        }
      
      
                        $contact_t = (string)$eventdata->ContactName;
                        $org_data = dwei_get_page_by_title(html_entity_decode($contact_t), null, 'tribe_organizer');
                        if(isset($org_data) && !empty($org_data)){
                            $org_id = $org_data->ID;
                          }else{
                              $org_args = array(
                                  'post_type' => 'tribe_organizer',
                                  'post_title' => (string)$eventdata->ContactName,
                                  'post_status' => 'publish',
                                );
                            $org_id = wp_insert_post($org_args);
                        }
      
                          if ($org_id) {
                              add_post_meta($org_id, '_OrganizerPhone', (string)$eventdata->ContactPhone);
                              add_post_meta($org_id, '_OrganizerEmail', (string)$eventdata->ContactEmail);
                          }  
      
      
                      $args = array(
                            'post_type' => $imported_event_slug_val,
                            'post_title' => (string)$eventdata->Title,
                            'post_content' => (string)$eventdata->Description,
                            'post_status' => 'publish',
                            'comment_status' => 'closed',
                      );
      
                        $custom_event_id = wp_insert_post($args);
      
                        if ($custom_event_id) {
                            // insert post meta
                            add_post_meta($custom_event_id, '_EventOrganizerID', $org_id);
                            add_post_meta($custom_event_id, '_EventVenueID', $venue_id);
                            add_post_meta($custom_event_id, 'EventID', (string)$eventdata->EventID);
                            add_post_meta($custom_event_id, '_EventStartDate', (string)$eventdata->TimeEventStart);
                            add_post_meta($custom_event_id, '_EventEndDate', (string)$eventdata->TimeEventEnd);
                        }       
                } 
          }
        }
        echo '<script> window.location.href = "/wp-admin/admin.php?page=import_events"; </script>';
        //header("location: /wp-admin/admin.php?page=import_events");
      
    }
}

function edit_event(){
  global $wpdb;
  $table1 = $wpdb->prefix . 'temp_events';
  $table2 = $wpdb->prefix . 'all_events';

  if($_REQUEST['action'] == 'edit_event' && !empty($_REQUEST['event_id'])){ 
    $event_id = $_REQUEST['event_id'];
    $old_event = $wpdb->get_row("SELECT * FROM {$table2} WHERE EventID=$event_id");
    $event_exst = $wpdb->get_row("SELECT * FROM {$table1} WHERE EventID=$event_id");

?>
<style>
  .all_event_data {
    width: 100%;
    margin-top: 30px;
}
.all_event_data input, .all_event_data textarea {
    display: flex;
}
.new_data_mn , .old_data_mn{
  width: 40%;
  float: left;
}
.old_data_mn.disable {
    opacity: 0.8;
    pointer-events: none;
}
table {
    text-transform: capitalize;
}
.all_event_data {
    width: 100%;
    margin-top: 30px;
    display: inline-flex;
    align-items: flex-start;
    justify-content: space-between;
}


.all_event_data textarea, .all_event_data input {
    width: 100%;
    margin-top: 5px;
    padding: 5px 10px;
}
</style>
  <div class="wrap" id="wp-media-grid" data-search="">
    <h1 class="wp-heading-inline"><?php echo DWEI_TITLE;?></h1>
    <p>Update Event</p>
  </div>  
  <div class="wrap">
    <div class="row border">
      <div class="col-md-12">
        <div class="all_event_data">
          <div class="old_data_mn disable">
                  <h5>Previous Saved Data</h5>
                  <label class="title">Title </label>
                  <input type="text" name="Title" value="<?php echo $old_event->Title; ?>" > <br>

                  <label class="Location">Location </label>
                  <input type="text" name="Location" value="<?php echo $old_event->Location; ?>" ><br>

                  <label class="Description">Description </label>
                  <textarea name="Description"><?php echo $old_event->Description; ?></textarea><br>

                  <label class="ContactName">Contact Name </label>
                  <input type="text" name="ContactName" value="<?php echo $old_event->ContactName; ?>" ><br>

                  <label class="ContactEmail">Contact Email  </label>
                  <input type="text" name="ContactEmail" value="<?php echo $old_event->ContactEmail; ?>" ><br>

                  <label class="ContactPhone">Contact Email  </label>
                  <input type="text" name="ContactPhone" value="<?php echo $old_event->ContactPhone; ?>" ><br>

                  <input type="hidden" name="event_id" value="<?php echo $event_id; ?>">
                
          </div>
          <div class="new_data_mn"> 
              <h5>New Imported Data</h5>
              <form method="POST" name="update_event_form" action="/wp-admin/admin.php?page=update_event" >
                  <label class="title">Title </label>
                  <input type="text" name="Title" value="<?php echo $event_exst->Title; ?>" > <br>

                  <label class="Location">Location </label>
                  <input type="text" name="Location" value="<?php echo $event_exst->Location; ?>" ><br>

                  <label class="Description">Description </label>
                  <textarea name="Description"><?php echo $event_exst->Description; ?></textarea><br>

                  <label class="ContactName">Contact Name </label>
                  <input type="text" name="ContactName" value="<?php echo $event_exst->ContactName; ?>" ><br>

                  <label class="ContactEmail">Contact Email  </label>
                  <input type="text" name="ContactEmail" value="<?php echo $event_exst->ContactEmail; ?>" ><br>

                  <label class="ContactPhone">Contact Email  </label>
                  <input type="text" name="ContactPhone" value="<?php echo $event_exst->ContactPhone; ?>" ><br>

                  <input type="hidden" name="event_id" value="<?php echo $event_id; ?>">

                  <input type="submit" name="update_event" class="button button-primary" value="Update Event">
                
              </form>
          </div>
        <div class="clear" style="clear:both;">
        </div>
      </div>
    </div>  
  </div>
<?php 
  }
}

function update_event(){
  if(isset($_POST['update_event'])){
    $success = 0;
    global $wpdb;
    $event_id = $_POST['event_id'];
    $Title = $_POST['Title'];
    $Description = $_POST['Description'];
    $Location = $_POST['Location'];
    $ContactName = $_POST['ContactName'];
    $ContactEmail = $_POST['ContactEmail'];
    $ContactPhone = $_POST['ContactPhone'];
    $imported_event_slug_val = '';
    $event_args = array(
        'post_type' => $imported_event_slug_val,
        'posts_per_page'   => 1,
        'post_status'      => 'any',
        'meta_query' => array(
            array(
                'key'     => 'EventID',
                'value'   => $event_id,
                'compare' => '=',
            ),
        ),
    );
    $event_posts = get_posts($event_args);
    $db_event_id = '';
    foreach($event_posts as $signle_event){
        $db_event_id = $signle_event->ID;
    }

    if($db_event_id){
        $location_t = trim($Location);
        global $wpdb;
  
        $venue_data = $wpdb->get_var($wpdb->prepare("SELECT ID FROM {$wpdb->posts} WHERE post_type='tribe_venue' AND post_title = %s",$location_t));
        if($venue_data){
            $venue_id = $venue_data;
        }else{
            $venue_args = array(
                'post_type' => 'tribe_venue',
                'post_title' => $Location,
                'post_status' => 'publish',
            );
            $venue_id = wp_insert_post($venue_args);
        }
        exit;
        $contact_t = $ContactName;
        $org_data = dwei_get_page_by_title(html_entity_decode($contact_t), null, 'tribe_organizer');
        if(isset($org_data) && !empty($org_data)){
            $org_id = $org_data->ID;
        }else{
            $org_args = array(
                'post_type' => 'tribe_organizer',
                'post_title' => $ContactName,
                'post_status' => 'publish',
            );
            $org_id = wp_insert_post($org_args);
        }
  
        if ($org_id) {
            update_post_meta($org_id, '_OrganizerPhone', $ContactEmail);
            update_post_meta($org_id, '_OrganizerEmail', $ContactPhone);
        }  
  
        $args = array(
            'ID' => $db_event_id,
            'post_title' => $Title,
            'post_content' => $Description,
            'post_status' => 'publish',
            'comment_status' => 'closed',
        );
        $custom_event_id = wp_update_post($args);
  
        if ($custom_event_id) {
            // insert post meta
            update_post_meta($custom_event_id, '_EventOrganizerID', $org_id);
            update_post_meta($custom_event_id, '_EventVenueID', $venue_id);
            update_post_meta($custom_event_id, 'EventID', $event_id);
        }  
  
        $table1 = $wpdb->prefix . 'temp_events';
        $table2 = $wpdb->prefix . 'all_events';

        $result = $wpdb->update($table2, array(
            'Title' => $Title,
            'Description' => $Description,
            'Location' => $Location,
            'ContactName' => $ContactName,
            'ContactEmail' => $ContactEmail,
            'ContactPhone' => $ContactPhone,
        ), array('EventID'=>$event_id));   
  
        $result = $wpdb->update($table1, array(
            'Title' => $Title,
            'Description' => $Description,
            'Location' => $Location,
            'ContactName' => $ContactName,
            'ContactEmail' => $ContactEmail,
            'ContactPhone' => $ContactPhone,
        ), array('EventID'=>$event_id)); 
  
        $success = 1;
  
    }
  }

  echo '<script> window.location.href = "/wp-admin/admin.php?page=import_events"; </script>';
}