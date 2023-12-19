<?php

/**
 * @file
 * All helper functions used in the plugin.
 *
 * Created by: Anees
 * https://dreamwarrior.com/
 */


/**
 * The [dwei_event_card] shortcode.
 *
 * Accepts a title and will display a box.
 *
 * @param array  $atts    Shortcode attributes. Default empty.
 * @param string $content Shortcode content. Default null.
 * @param string $tag     Shortcode tag (name). Default empty.
 * @return string Shortcode output.
 * [event-cards event_type= 'temple' display_type='list' rows = '10' rows_per_page = '5' pagination = 'false' target= '_blank']
 */
function dwei_event_shortcode( $atts ) {

  global $wpdb;
  global $wp;
  // normalize attribute keys, lowercase
	$atts = array_change_key_case( (array) $atts, CASE_LOWER );

	// override default attributes with user attributes
	$atts = shortcode_atts( array(
    'event_type' => 'community',
    'display_type' => 'card',
    'rows' => '10',
    'rows_per_page' => '2',
    'pagination' => 'false',
    'target'=> '_blank',
  ), $atts);

  $atts['rows_per_page'] = $atts['rows'];

  $table = $wpdb->prefix . 'all_events';
  //events count 
  $events_count = "SELECT COUNT(id) AS count 
    FROM {$table} WHERE EventTypeName like '%". $atts['event_type']."%'";
    $rs_events_count = $wpdb->get_results($events_count);

  //Pagination 
  if(!isset($atts['rows']) or $atts['rows']==""){
    $rows = $rs_events_count[0]->count;
  }else{
    $rows = ($atts['rows'] < $rs_events_count[0]->count) ? $atts['rows'] : $rs_events_count[0]->count;
  }


  $limit = $atts['rows_per_page'];
  $page_id = 1;
  $page_url = home_url( $wp->request ).'/?pagination=true';

  if(!isset($_REQUEST['page_id'])){
    $page_id = 1;
  }else{
    $page_id = $_REQUEST['page_id'];
  }
  $total_pages = 0;

  if(isset($rs_events_count)){
    $total_pages = ceil(($rows / $limit));
  }
  // Calculate the offset for the query
  $offset = ($page_id - 1)  * $limit;

  // Some information to display to the user
  $start = $offset + 1;
  $end = ($offset + $limit);

  //get current events 
  $rs_events = "SELECT * 
  FROM {$table} WHERE EventTypeName like '%". $atts['event_type']."%' limit $start,$end";
  $rs_event_rows = $wpdb->get_results($rs_events);

  if($atts['display_type']=='card'){

      
      // start box
      $o = '<div class="card-wrap">';

      if(isset($rs_event_rows) && !empty($rs_event_rows)){
        foreach ( $rs_event_rows as $rs_event_row )   { 
          
          $date_time_arr = explode('T', $rs_event_row->TimeEventStart);
          //print_r($date_time_arr);
          $o.=    '<section class="elementor-section elementor-inner-section elementor-element elementor-element-86bcbe6 elementor-section-boxed elementor-section-height-default elementor-section-height-default" data-id="86bcbe6" data-element_type="section" data-settings="{&quot;_ha_eqh_enable&quot;:false}">
                      <div class="elementor-container elementor-column-gap-narrow">
                        <div class="elementor-row">
                          <div class="elementor-column elementor-col-50 elementor-inner-column elementor-element elementor-element-5f98f21" data-id="5f98f21" data-element_type="column">
                            <div class="elementor-column-wrap elementor-element-populated">
                              <div class="elementor-widget-wrap">
                                <div class="elementor-element elementor-element-ba1bd4e elementor-widget elementor-widget-image" data-id="ba1bd4e" data-element_type="widget" data-widget_type="image.default">
                                  <div class="elementor-widget-container">
                                    <div class="elementor-image">
                                      <img decoding="async" src="https://templeisaiadev.wpengine.com/wp-content/uploads/2022/12/event-1.jpg" class="attachment-large size-large" alt="" loading="lazy" srcset="https://templeisaiadev.wpengine.com/wp-content/uploads/2022/12/event-1.jpg 140w, https://templeisaiadev.wpengine.com/wp-content/uploads/2022/12/event-1-75x75.jpg 75w, https://templeisaiadev.wpengine.com/wp-content/uploads/2022/12/event-1-90x90.jpg 90w" sizes="(max-width: 140px) 100vw, 140px" width="140" height="140">														</div>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                          <div class="elementor-column elementor-col-50 elementor-inner-column elementor-element elementor-element-3f4623a" data-id="3f4623a" data-element_type="column">
                        <div class="elementor-column-wrap elementor-element-populated">
                          <div class="elementor-widget-wrap">
                            <div class="elementor-element elementor-element-9d3d820 elementor-widget elementor-widget-heading" data-id="9d3d820" data-element_type="widget" data-widget_type="heading.default">
                              <div class="elementor-widget-container">
                                <h5 class="elementor-heading-title elementor-size-default">'.$rs_event_row->Title.'</h5>		</div>
                              </div>
                            <div class="elementor-element elementor-element-9eae6ea elementor-icon-list--layout-inline elementor-align-left elementor-list-item-link-full_width elementor-widget elementor-widget-icon-list" data-id="9eae6ea" data-element_type="widget" data-widget_type="icon-list.default">
                            <div class="elementor-widget-container">
                              <ul class="elementor-icon-list-items elementor-inline-items">
                                <li class="elementor-icon-list-item elementor-inline-item">
                                  <span class="elementor-icon-list-icon">
                                    <i aria-hidden="true" class="far fa-calendar"></i></span>
                                  <span class="elementor-icon-list-text">'.date_format(date_create($date_time_arr[0]),"D, M d").'</span>
                                </li>
                                <li class="elementor-icon-list-item elementor-inline-item">
                                  <span class="elementor-icon-list-icon">
                                    <i aria-hidden="true" class="far fa-clock"></i>						
                                  </span>
                                  <span class="elementor-icon-list-text">'. date("h:i A", strtotime($date_time_arr[1])).'</span>
                                </li>
                              </ul>
                            </div>
                          </div>
                          <div class="elementor-element elementor-element-74cc3d0 elementor-widget elementor-widget-heading" data-id="74cc3d0" data-element_type="widget" data-widget_type="heading.default">
                            <div class="elementor-widget-container">
                              <p class="elementor-heading-title elementor-size-default">As we navigate the return of in-person events at Temple Isaiah, we are delighted to share…</p>		</div>
                            </div>
                            <div class="elementor-element elementor-element-a4f2c30 elementor-align-left fbox-btn elementor-widget elementor-widget-button" data-id="a4f2c30" data-element_type="widget" data-widget_type="button.default">
                              <div class="elementor-widget-container">
                              <div class="elementor-button-wrapper">
                                <a href="#" class="elementor-button-link elementor-button elementor-size-xs" role="button">
                                  <span class="elementor-button-content-wrapper">
                                    <span class="elementor-button-text">learn more</span>
                                  </span>
                                </a>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </section>';
        }
      }
      //pagination 
      //if($total_pages>1){
        //$o.='<div  class="row">';
          //$o.='<div class="col-md-12  text-center">';
            //$o.= generate_pagination($total_pages, $page_id,$page_url, $limit);
          //$o.='</div>';
        //$o.='</div>';
      //}
      // end box
      $o.='</div>';
  }else{

      // start box
      $o = '<div class="list-wrap">';
      if(isset($rs_event_rows) && !empty($rs_event_rows)){
        foreach ( $rs_event_rows as $rs_event_row )   { 
          
          $date_time_arr = explode('T', $rs_event_row->TimeEventStart);
          $o .= '<div class="row">
                  <div class="list-image"> 
                      <img alt="" src="https://templeisaiah.com/ckeditor/userfiles/images/My%20Post%20(62)(1).jpg" style="width: 120px; height: 120px; border-width: 2px; border-style: solid;">
                  </div>
                  <div list-data>
                      <div class="list-heading">
                        <h5>
                          <strong>Shabbat </strong><strong>'.$rs_event_row->Title.'</strong>
                        </h5>
                      </div>
                      <div class="date-time">  
                        <h6>
                          '.date_format(date_create($date_time_arr[0]),"D, M d y").' -
                          '. date("h:i A", strtotime($date_time_arr[1])).'
                        </h6>
                      </div>
                      <div class="list-description">  
                        Attend Shabbat services in-person! We have limited space available for members of Temple Isaiah to join us for prayer, song, and community. Services will also be streamed to our Vimeo channel, website &amp; Facebook page. <strong>Check our calendar and our newsletters for the most up to date information.</strong><br>
                      </div>
                  </div>  
                </div>';
        }
      }

      //if( ($total_pages > 1)){
        //$o.='<div class="row">';
          //$o.='<div class="col-md-12  text-center">';
            //$o.= generate_pagination($total_pages, $page_id,$page_url, $limit);
          //$o.='</div>';
        //$o.='</div>';
      //}
      // end box
      $o.='</div>';
  }
  // return output
  return $o;
}

/**
 * Central location to create all shortcodes.
 */
function dwei_shortcodes_init() {
	add_shortcode( 'event-code', 'dwei_event_shortcode' );
}

add_action( 'init', 'dwei_shortcodes_init' );


 //generate pagination 
 function  generate_pagination($total_pages, $page_id, $url, $limit){

    $pagination = '';
    try {

    
        // Calculate the offset for the query
        $offset = ($page_id - 1)  * $limit;
    
        // Some information to display to the user
        $start = $offset + 1;
        $end = ($offset + $limit);
    
        // The "back" link
        $prevlink = ($page_id > 1) ? '<a href="'.$url.'&page_id=1" title="First page">&laquo;</a> <a href="'.$url.'&page_id=' . ($page_id - 1) . '" title="Previous page">&lsaquo;</a>' : '<span class="disabled">&laquo;</span> <span class="disabled">&lsaquo;</span>';
    
        // The "forward" link
        $nextlink = ($page_id < $total_pages) ? '<a href="'.$url.'&page_id=' . ($page_id + 1) . '" title="Next page">&rsaquo;</a> <a href="'.$url.'&page_id=' . $total_pages . '" title="Last page">&raquo;</a>' : '<span class="disabled">&rsaquo;</span> <span class="disabled">&raquo;</span>';
    
        // Display the paging information
        $pagination .= '<div id="paging"><p>'. $prevlink. ' Page '. $page_id. ' of '. $total_pages. ' pages, displaying '. $start. '-'. $end. ' of '. ($total_pages*$limit). ' results '. $nextlink. ' </p></div>';

    
    } catch (Exception $e) {
        echo '<p>', $e->getMessage(), '</p>';

    }

    echo $pagination;

 }

 function dwei_get_soap_calendar_response($url,$soapUser,$soapPassword,$xml_post_string,$headers) {
    // PHP cURL  for https connection with auth
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERPWD, $soapUser.":".$soapPassword); // username and password - declared at the top of the doc
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_post_string); // the SOAP request
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    // converting
    $response = curl_exec($ch);
    curl_close($ch);

    return $response;
}

//Get Events
function dwei_create_xml_post_string($soapUser,$soapPassword) {
    $today_date = date("Y-m-d");
    return '<?xml version="1.0" encoding="utf-8"?>
        <soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
          <soap:Body>
            <GetEvents xmlns="http://DEA.Web.Service.MasterCalendar.API/">
              <userName>'.$soapUser.'</userName>
              <password>'.$soapPassword.'</password>
              <startDate>'.$today_date.'</startDate>
              <endDate>'.date("Y-m-d", strtotime("+5 years", strtotime($today_date))).'</endDate>
              <calendars>
                <int>1</int>
                <int>2</int>
                <int>4</int>
                <int>5</int>
                <int>3</int>
                <int>6</int>
              </calendars>
              <eventTypes>
                <int>1</int>
                <int>4</int>
                <int>5</int>
                <int>6</int>
                <int>7</int>
                <int>8</int>
                <int>9</int>
                <int>10</int>
                <int>11</int>
                <int>12</int>
                <int>14</int>
                <int>15</int>
                <int>16</int>
                <int>17</int>
                <int>18</int>
                <int>19</int>
                <int>2</int>
                <int>20</int>
                <int>21</int>
                <int>22</int>
                <int>23</int>
                <int>24</int>
                <int>25</int>
                <int>26</int>
                <int>27</int>
                <int>28</int>
                <int>29</int>
                <int>3</int>
                <int>30</int>
                <int>31</int>
                <int>32</int>
                <int>33</int>
                <int>34</int>
                <int>35</int>
              </eventTypes>
            </GetEvents>
          </soap:Body>
        </soap:Envelope>';
}
//Headers
function dwei_create_calendar_headers($xml_post_string) {
    return array(
        "Host: calendar.templeisaiah.com",
        "Content-type: text/xml;charset=utf-8",
        "Content-length: ".strlen($xml_post_string),
        "SOAPAction: http://DEA.Web.Service.MasterCalendar.API/GetEvents", 
    ); //SOAPAction: your op URL        
}
