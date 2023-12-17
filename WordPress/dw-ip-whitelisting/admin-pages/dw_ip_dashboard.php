<?php
/**
 * @file
 * General options page.
 *
 * Created by: Anees
 * https://10labz.com/
 */

function dw_ip_dashboard()
{


    global $wpdb;
    $table_name = $wpdb->prefix . 'ip_whitelisting';

    // Pagination variables
    $items_per_page = 10;
    $current_page = isset($_GET['page_num']) ? absint($_GET['page_num']) : 1;
    $offset = ($current_page - 1) * $items_per_page;

    $query = "SELECT * FROM $table_name ORDER BY id DESC LIMIT $offset, $items_per_page";
    $results = $wpdb->get_results($query);
    $total_items = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
    $total_pages = ceil($total_items / $items_per_page);

    ?>
    <div class="wrap" id="wp-media-grid" data-search="">
        <h1 class="wp-heading-inline"><?php echo DWIPWL_TITLE; ?></h1>
        <p><span>List of all whitelisted IP's</span></p>
    </div>

    <div class="wrap">
        <div class="row">
            <div class="col-md-12">
                <?php
                if (!empty($results)) {
                    echo '<table class="custom-admin-table">';

                    if (isset($_GET['msg'])) {
                        echo '<tr>
                        <th colspan="7" style="color:green">' . $_GET['msg'] . '</th>
                        </tr>';
                    }

                    echo '<tr>
                    <th>ID</th><th>IP Address</th>
                    <th>Email</th>
                    <th>Email Status</th>
                    <th>Status</th>
                    <th>Date Time</th>
                    <th></th>
                    </tr>';
                    $page_no = isset($_GET['page_num']) ? $_GET['page_num'] : '1';

                    //echo $usFormat;

                    foreach ($results as $row) {

                        $dateTime = new DateTime($row->created_at);
                        $usFormat = $dateTime->format('m/d/Y h:i:s A');

                        echo '<tr>';
                        echo '<td>' . ++$offset . '</td>';
                        echo '<td>' . $row->ip_address . '</td>';
                        echo '<td>' . $row->email . '</td>';
                        echo '<td>' . ($row->email_status ? 'Sent' : 'Not Sent') . '</td>';
                        echo '<td>' . ($row->status ? 'Active' : 'In-active') . '</td>';
                        echo '<td>' . $usFormat . '</td>';
                        echo '<td><a href="?page=dw_ip_dashboard&action=delete&id=' . $row->id .'&page_num=' . $page_no . '" onclick="return confirm(\'Are you sure you want to delete this record?\')">Delete</a></td>';
                        echo '</tr>';
                    }

                    echo '</table>';

                    // Pagination links
                    if ($total_pages > 1) {
                        echo '<div class="pagination">';

                        if ($current_page > 1) {
                            echo '<a class="page-numbers" href="?page=dw_ip_dashboard&page_num=' . ($current_page - 1) . '">Previous</a>';
                        }

                        // Show 5 page numbers
                        $start_page = max(1, $current_page - 2);
                        $end_page = min($start_page + 4, $total_pages);

                        for ($i = $start_page; $i <= $end_page; $i++) {
                            if ($i === $current_page) {
                                echo '<span class="page-numbers current-page">' . $i . '</span>';
                            } else {
                                echo '<a class="page-numbers" href="?page=dw_ip_dashboard&page_num=' . $i . '">' . $i . '</a>';
                            }
                        }

                        if ($current_page < $total_pages) {
                            echo '<a class="page-numbers" href="?page=dw_ip_dashboard&page_num=' . ($current_page + 1) . '">Next</a>';
                        }

                        echo '</div>';
                    }
                } else {
                    echo 'No records found.';
                }
                ?>
            </div>
        </div>
    </div>
<?php
}
