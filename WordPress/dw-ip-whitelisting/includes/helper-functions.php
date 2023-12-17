<?php

/**
 * @file
 * All helper functions used in the plugin.
 *
 * Created by: Anees
 * https://10labz.com/
 */


/**
 * 
 *
 * Accepts a title and will display a box.
 *
 * @param array  $atts    Shortcode attributes. Default empty.
 * @param string $content Shortcode content. Default null.
 * @param string $tag     Shortcode tag (name). Default empty.
 * @return string Shortcode output.
 * [event-cards event_type= 'temple' display_type='list' rows = '10' rows_per_page = '5' pagination = 'false' target= '_blank']
 */

 /**
 * Create custom table.
 */

// Check if the delete action is triggered
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];

    // Perform the deletion logic
    global $wpdb;
    $table_name = $wpdb->prefix . 'ip_whitelisting';

    // You can customize the deletion query based on your table structure
    $wpdb->delete($table_name, ['id' => $id]);
    // Redirect back to the admin page after deletion
    header('Location: ' . admin_url('admin.php?page=dw_ip_dashboard&page_num=' . $_GET['page_num'] . '&msg=Record deleted successfully!'));
    exit; 
}

function create_ip_whitelisting_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'ip_whitelisting'; // Add a prefix to the table name

    $query = "DROP TABLE IF EXISTS {$table_name}";
    $wpdb->query($query);

    // Define the character set and collation
    $charset_collate = $wpdb->get_charset_collate();

    // SQL query to create the table
    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id INT(11) NOT NULL AUTO_INCREMENT,
        ip_address VARCHAR(255) NOT NULL,
        status TINYINT(1) NOT NULL DEFAULT 0,
        email_status TINYINT(1) NOT NULL DEFAULT 0,
        email VARCHAR(255) NOT NULL,
        user_name VARCHAR(255) NOT NULL,
        verification_code INT(4) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql); // Execute the SQL query to create the table
}

function dwipwl_uninstall() {
    // Delete tables and options here.
    global $wpdb;
    $table_name = $wpdb->prefix . 'ip_whitelisting'; // Add a prefix to the table name

    $query = "DROP TABLE IF EXISTS {$table_name}";
    $wpdb->query($query);

}


// Function to create a page and assign template if it doesn't exist
function dw_create_page( $page_title, $content, $slug ) {
    // Check if the page already exists
    $existing_page = get_page_by_path( $slug );

    // Create the page only if it doesn't exist
    if ( !$existing_page ) {
        // Create the page post object
        $page = array(
            'post_title'     => $page_title,
            'post_content'   => $content,
            'post_status'    => 'publish',
            'post_type'      => 'page',
            'post_name'      => $slug, // Set the slug (URL-friendly name) for the page
        );

        // Insert the page into the database
        $page_id = wp_insert_post( $page );
    }
}

// Create email verification page
function create_email_verification_form() {
    $page_title = 'Email Verification';
    $shortcode = '[dw_email_verification_form]';
    $slug = 'dw-email-verification'; // Specify the desired slug for the page
    dw_create_page( $page_title, $shortcode, $slug );
}

// Create code verification page
function create_code_verification_form() {
    $page_title = 'Code Verification';
    $shortcode = '[dw_code_verification_form]';
    $slug = 'dw-code-verification'; // Specify the desired slug for the page
    dw_create_page( $page_title, $shortcode, $slug );
}

//delete pages on deactivation 

// Delete plugin pages
function delete_plugin_pages() {
    // Page titles to be deleted
    $page_titles = array( 'Email Verification', 'Code Verification' );

    // Loop through the page titles
    foreach ( $page_titles as $page_title ) {
        $page = get_page_by_title( $page_title );

        // Delete the page if it exists
        if ( $page ) {
            wp_delete_post( $page->ID, true );
        }
    }
}

// Function to check if IP exists in the allowed list in the custom table
function is_ip_allowed($ip_address) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'ip_whitelisting'; 

    $query = $wpdb->prepare("SELECT id FROM $table_name WHERE ip_address = %s AND status = %d", $ip_address, 1);
    $result = $wpdb->get_var($query);

    return $result ? true : false;
}




// Function to save IP and email status to the custom table
function save_ip_email_status($ip_address, $email, $user_name, $code) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'ip_whitelisting';

    // Check if the IP address already exists in the table
    $existing_email = $wpdb->get_row(
        $wpdb->prepare("SELECT id FROM $table_name WHERE email = %s or user_name = %s", $email, $user_name)
    );
    
    if ($existing_email) {
        // IP address already exists, update the row
        $data = array(
            'ip_address' => $ip_address,
            'email_status' => 1,
            'verification_code' => $code,
            'created_at' => current_time('mysql'), // Set the current date and time
        );

        $where = array(
            'id' => $existing_email->id,
        );

        $result = $wpdb->update($table_name, $data, $where, array('%s', '%d', '%d', '%s'), array('%d'));
    } else {
        // IP address does not exist, insert a new row


        $wpdb->insert(
            $table_name,
            array(
                'ip_address' => $ip_address,
                'email' => $email,
                'user_name' => $user_name,
                'email_status' => 1,
                'verification_code' => $code,
            ),
            array('%s', '%s', '%s', '%d', '%d')
        );
    }
}

// Function to save IP and email status to the custom table
function update_ip_whitelist_status($id) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'ip_whitelisting';

    // Check if the email address exists in the table
    $data = array(
        'status' => 1,
    );
    
    $where = array(
        'id' => $id,
    );
    $result = $wpdb->update($table_name, $data, $where, array('%d'), array('%d'));

}

function dw_email_form_shortcode() {
    ob_start();
    // Display the form HTML code here
    ?>

<div class="centered-div">
        <table width="400" align="center">

        <?php if(isset($_SESSION['ip_whitelisting_error'])){ ?>
        <tr>
            <td>
            <div style="text-align:center; color:red">
                <h3><?php echo $_SESSION['ip_whitelisting_error'];?></h3> 
            </div>
            </td>
        </tr>

        <?php 
            unset($_SESSION['ip_whitelisting_error']);
        } ?>
        <tr>
            <td style="text-align:center">    
            Your IP needs to be whitelisted.<br/>
            Please contact your Owner/Admin or enter your username or email below. Your IP is 
            <?php echo $_SERVER['REMOTE_ADDR'];?>
            </span>    
            <br/>
            <br/>
            <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="POST">
                <input type="hidden" name="action" value="dw_email_form_submission">
                <!-- Add your form fields here -->
                <input type="text" name="login_id" placeholder="Email Address or Username" required>
                <button type="submit">Submit</button>
            </form>
            </td>
        </tr>
        </table>    
    </div>

    <?php
    return ob_get_clean();
}
function dw_code_verification_form_shortcode() {
    ob_start();
    // Display the form HTML code here
    global $wpdb;
    $table_name = $wpdb->prefix . 'ip_whitelisting';

    $result = $wpdb->get_row(
        $wpdb->prepare(
            "SELECT * FROM {$table_name} WHERE email = %s AND status = 0",
            $_SESSION['email']
        )
    );
    
    // Check if the query returned a valid row
    if ($result) {
        // Convert the "created_at" value to a DateTime object
        $created_at_datetime = new DateTime($result->created_at);
    
        // Get the current time as a DateTime object
        $current_datetime = new DateTime();
    
        // Get the difference between the two DateTime objects
        $time_difference = $current_datetime->diff($created_at_datetime);
        $minutes_difference = $time_difference->i; // Number of minutes between the two dates
        $seconds_difference = $time_difference->s;

        if($minutes_difference <= 3){
            $display_timer = (3 - $minutes_difference) * 60 - $seconds_difference;
        }else{
            $display_timer = 0;
        }

    }   

    ?>

<div class="centered-div">
    <table width="400"  align="center">
        <?php if (isset($_SESSION['ip_whitelisting_error'])) { ?>
            <tr>
                <td>
                    <div style="text-align:center; color:red">
                        <h3><?php echo $_SESSION['ip_whitelisting_error']; ?></h3>
                    </div>
                </td>
            </tr>
        <?php 
                
        } ?>
        <tr>
            <td  style="text-align:center">
                <span>
                    A code was sent to your email address.
                    Please copy and paste the code in the box below to continue.
                </span>
                <br/>
                <br/>
                <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="POST">
                    <input type="hidden" name="action" value="dw_code_verification_form">
                    <input type="hidden" name="email" value="<?php echo $_SESSION['email']; ?>">
                    <!-- Add your form fields here -->
                    <input type="text" name="verification_code" placeholder="Verification Code" required autocomplete="off">

                    <button type="submit">Submit</button>
                </form>
                <div id="timer"></div>
            </td>
        </tr>
    </table>
</div>

<script>
    // Set the duration of the timer in seconds (3 minutes = 180 seconds)
    var duration = <?php echo $display_timer;?>;
    var timerElement = document.getElementById('timer');

    function startTimer() {
        var minutes, seconds;

        var timerInterval = setInterval(function () {
            minutes = parseInt(duration / 60, 10);
            seconds = parseInt(duration % 60, 10);

            minutes = minutes < 10 ? "0" + minutes : minutes;
            seconds = seconds < 10 ? "0" + seconds : seconds;

            timerElement.textContent = minutes + ":" + seconds;

            if (--duration < 0) {
                clearInterval(timerInterval);
                timerElement.textContent = "Time's up!";
            }
        }, 1000);
    }

    startTimer();
</script>

    <?php
    return ob_get_clean();
}