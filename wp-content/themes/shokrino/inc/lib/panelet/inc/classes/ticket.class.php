<?php
defined('ABSPATH') || exit;
class Panelet_TICKET {
    private $main_table_name;
    private $messages_table_name;
    public function __construct() {
        register_activation_hook(PNLT_PATH.'/panelet.php', array($this, 'create_table'));
        if (!$this->tables_exist()) {
            $this->create_table();
        }
        add_action('admin_menu', array($this, 'add_tickets_menu_item'));
        add_shortcode('ticket_system', array($this, 'display_tickets_page_shortcode'));
        create_my_account_endpoint_pnlt_func('تیکت ها', 'p-tickets', [$this, 'endpoint_content']);
        create_my_account_only_endpoint_pnlt_func('p-new-ticket', [$this, 'new_tickets_form_page']);
        add_action('wp_ajax_nopriv_new_ticket_panelet', [$this, 'new_ticket']);
        add_action('wp_ajax_new_ticket_panelet', [$this, 'new_ticket']);
        add_action('wp_ajax_nopriv_add_ticket_panelet', [$this, 'add_ticket']);
        add_action('wp_ajax_add_ticket_panelet', [$this, 'add_ticket']);
        add_action('wp_ajax_nopriv_admin_ticket_panelet', [$this, 'add_ticket_admin']);
        add_action('wp_ajax_admin_ticket_panelet', [$this, 'add_ticket_admin']);
        add_action('admin_bar_menu', [$this, 'tickets_admin_bar_count_pnlt'], 70);
    }

    public function endpoint_content() {
        echo do_shortcode('[ticket_system]');
    }
    public function tickets_admin_bar_count_pnlt() {
        $ticket_system = new Panelet_TICKET;
        $open_tickets_count = $ticket_system->count_open_tickets();
        global $wp_admin_bar;
        $wp_admin_bar->add_menu(
            array(
                'id' => 'tickets',
                'title' => '<span class="ab-icon dashicons dashicons-tickets"></span><span class="ab-label awaiting-mod pending-count count-' . $open_tickets_count . '">' . $open_tickets_count . '</span>',
                'href' => admin_url('?page=tickets'),
                'parent' => false,
                'meta' => array(
                    'class' => 'menupop with-count',
                    'title' => $open_tickets_count . ' تیکت در حالت انتظار',
                ),
            )
        );
    }
    private function tables_exist() {
        global $wpdb;
        $main_table_name = $wpdb->prefix . 'ticket_pnlt';
        $messages_table_name = $wpdb->prefix . 'ticket_pnlt_messages';
        return $wpdb->get_var("SHOW TABLES LIKE '$main_table_name'") == $main_table_name &&
               $wpdb->get_var("SHOW TABLES LIKE '$messages_table_name'") == $messages_table_name;
    }
    public function create_table() {
        global $wpdb;
        $main_table = $wpdb->prefix . 'ticket_pnlt';
        $messages_table = $wpdb->prefix . 'ticket_pnlt_messages';
        $charset_collate = $wpdb->get_charset_collate();
        $sql_main = "CREATE TABLE $main_table (
            ticket_id INT PRIMARY KEY AUTO_INCREMENT,
            user_id INT NULL,
            admin_id INT NULL,
            title VARCHAR(255) NOT NULL,
            status VARCHAR(20) DEFAULT 'open',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) $charset_collate;";
        $sql_messages = "CREATE TABLE $messages_table (
            message_id INT PRIMARY KEY AUTO_INCREMENT,
            ticket_id INT NOT NULL,
            sender_id INT NULL,
            role VARCHAR(20) DEFAULT 'user',
            description TEXT,
            datetime TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (ticket_id) REFERENCES $main_table(ticket_id) ON DELETE CASCADE
        ) $charset_collate;";
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql_main);
        dbDelta($sql_messages);
    }
    public function add_ticket_admin() {
        $ticket_id = sanitize_text_field($_POST['ticket_id_panelet']);
        $user_id = sanitize_text_field($_POST['user_id_ticket_panelet']);
        $ticket_data = $this->get_ticket_data($ticket_id);
        $ticket_main = $ticket_data['main_data'];
        if (is_admin()) {
            global $wpdb;
            $messages_table = $wpdb->prefix . 'ticket_pnlt_messages';
            $text = sanitize_text_field($_POST['ticket_text']);
            $wpdb->insert(
                $messages_table,
                array(
                    'ticket_id' => $ticket_id,
                    'description' => $text,
                    'sender_id' => $user_id,
                    'role' => 'admin',
                ),
                array('%d', '%s', '%d', '%s', '%s')
            );
            $main_table = $wpdb->prefix . 'ticket_pnlt';
            $wpdb->update(
                $main_table,
                array(
                    'status' => 'closed',
                    'admin_id' => $user_id,
                ),
                array('ticket_id' => $ticket_id),
                array('%s', '%d'),
                array('%d')
            );
        }
        $response = [
            'success' => true,
            'message' => "تیکت با موفقیت ارسال شد..."
        ];
        header('Content-Type: application/json');
        if (function_exists('error_get_last')) {
            $last_error = error_get_last();
            if ($last_error && $last_error['type'] === E_ERROR) {
                echo json_encode(['error' => 'PHP Error: ' . $last_error['message']]);
            }
        }
        echo json_encode($response);
    }
    public function add_ticket() {
        $ticket_id = sanitize_text_field($_POST['ticket_id_panelet']);
        $current_user_id = get_current_user_id();
        $user_id = $current_user_id == sanitize_text_field($_POST['user_id_ticket_panelet']) ? sanitize_text_field($_POST['user_id_ticket_panelet']) : 0;
        $ticket_data = $this->get_ticket_data($ticket_id);
        $ticket_main = $ticket_data['main_data'];
        $valid_user_id = $ticket_main['user_id'];
        if ($user_id == $valid_user_id) {
            global $wpdb;
            $messages_table = $wpdb->prefix . 'ticket_pnlt_messages';
            $text = sanitize_text_field($_POST['ticket_text']);
            $wpdb->insert(
                $messages_table,
                array(
                    'ticket_id' => $ticket_id,
                    'description' => $text,
                    'sender_id' => $user_id,
                    'role' => 'user',
                ),
                array('%d', '%s', '%d', '%s')
            );
            $main_table = $wpdb->prefix . 'ticket_pnlt';
            $wpdb->update(
                $main_table,
                array(
                    'status' => 'open',
                ),
                array('ticket_id' => $ticket_id),
                array('%s'),
                array('%d')
            );
        }
        $response = [
            'success' => true,
            'message' => "تیکت با موفقیت ارسال شد...",
        ];
        header('Content-Type: application/json');
        if (function_exists('error_get_last')) {
            $last_error = error_get_last();
            if ($last_error && $last_error['type'] === E_ERROR) {
                $response['error'] = 'PHP Error: ' . $last_error['message'];
            }
        }
        echo json_encode($response);
    }

    public function new_ticket() {
        global $wpdb;
        $main_table = $wpdb->prefix . 'ticket_pnlt';
        $messages_table = $wpdb->prefix . 'ticket_pnlt_messages';
        if ($wpdb->get_var("SHOW TABLES LIKE '$main_table'") !== $main_table or $wpdb->get_var("SHOW TABLES LIKE '$messages_table'") !== $messages_table) {
            $charset_collate = $wpdb->get_charset_collate();
            $sql_main = "CREATE TABLE $main_table (
            ticket_id INT PRIMARY KEY AUTO_INCREMENT,
            user_id INT NULL,
            admin_id INT NULL,
            title VARCHAR(255) NOT NULL,
            status VARCHAR(20) DEFAULT 'open',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) $charset_collate;";
            $sql_messages = "CREATE TABLE $messages_table (
            message_id INT PRIMARY KEY AUTO_INCREMENT,
            ticket_id INT NOT NULL,
            sender_id INT NULL,
            role VARCHAR(20) DEFAULT 'user',
            description TEXT,
            datetime TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (ticket_id) REFERENCES $main_table(ticket_id) ON DELETE CASCADE
        ) $charset_collate;";
            require_once ABSPATH . 'wp-admin/includes/upgrade.php';
            dbDelta($sql_main);
            dbDelta($sql_messages);
        }
        $title = sanitize_text_field($_POST['ticket_title']);
        $text = sanitize_text_field($_POST['ticket_text']);
        $current_user_id = get_current_user_id();
        $current_user_role = "user";
        $wpdb->insert(
            $main_table,
            array(
                'title' => $title,
                'user_id' => $current_user_id,
            ),
            array('%s', '%d')
        );
        $ticket_id = $wpdb->insert_id;
        $wpdb->insert(
            $messages_table,
            array(
                'ticket_id' => $ticket_id,
                'description' => $text,
                'sender_id' => $current_user_id,
                'role' => $current_user_role,
            ),
            array('%d', '%s', '%d', '%s')
        );
        $response = [
            'success' => true,
            'message' => "تیکت با موفقیت ارسال شد...",
            'data' => array(
                'ticket_id' => $ticket_id,
                'ticket_url' => wc_get_account_endpoint_url('p-tickets') . "?view-ticket=" . $ticket_id,
            )
        ];
        wp_send_json($response);
    }
    public function get_tickets($user_id = '') {
        global $wpdb;
        $main_table = $wpdb->prefix . 'ticket_pnlt';
        $messages_table = $wpdb->prefix . 'ticket_pnlt_messages';
        if (empty($user_id)) {
            $query = "SELECT main.*, GROUP_CONCAT(messages.description ORDER BY messages.datetime ASC SEPARATOR '|') AS messages
              FROM $main_table AS main
              LEFT JOIN $messages_table AS messages ON main.ticket_id = messages.ticket_id
              GROUP BY main.ticket_id";
        } else {
            $query = "SELECT main.*, GROUP_CONCAT(messages.description ORDER BY messages.datetime ASC SEPARATOR '|') AS messages
              FROM $main_table AS main
              LEFT JOIN $messages_table AS messages ON main.ticket_id = messages.ticket_id
              WHERE main.user_id = $user_id
              GROUP BY main.ticket_id";
        }
        $tickets_data = $wpdb->get_results($query, ARRAY_A);
        if (!$tickets_data) {
            return false;
        }
        return $tickets_data;
    }
    public function get_ticket_data($ticket_id) {
        global $wpdb;
        $main_table = $wpdb->prefix . 'ticket_pnlt';
        $messages_table = $wpdb->prefix . 'ticket_pnlt_messages';
        $main_query = $wpdb->prepare(
            "SELECT * FROM $main_table WHERE ticket_id = %d",
            $ticket_id
        );
        $main_ticket_data = $wpdb->get_row($main_query, ARRAY_A);
        if (!$main_ticket_data) {
            return false;
        }
        $valid_user_id = $main_ticket_data['user_id'];
        $current_user_id = get_current_user_id();
        if ($valid_user_id != $current_user_id && !is_admin()) {
            return false;
        }
        $messages_query = $wpdb->prepare(
            "SELECT * FROM $messages_table WHERE ticket_id = %d",
            $ticket_id
        );
        $messages_data = $wpdb->get_results($messages_query, ARRAY_A);
        $ticket_data = array(
            'main_data' => $main_ticket_data,
            'messages_data' => $messages_data,
        );
        return $ticket_data;
    }
    public function add_tickets_menu_item() {
        add_menu_page(
            'تیکت ها',
            'تیکت ها',
            'manage_options',
            'tickets',
            array($this, 'admin_tickets_page'),
            'data:image/svg+xml;base64,PHN2ZyBpZD0iTGF5ZXJfMSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgeD0iMHB4IiB5PSIwcHgiIHZpZXdCb3g9IjAgMCAxOTE3LjQ0IDE5MTcuNDQiIHN0eWxlPSJlbmFibGUtYmFja2dyb3VuZDpuZXcgMCAwIDE5MTcuNDQgMTkxNy40NDsiIHhtbDpzcGFjZT0icHJlc2VydmUiIHdpZHRoPSI1MCIgaGVpZ2h0PSI1MCIgZmlsbD0iI2ZmZiI+DQogICAgICAgIDxnPg0KICAgICAgICAgICAgPHBhdGggY2xhc3M9InN0MCIgZD0iTTkyOS4yMSwxNzUxYy01Mi4yLDAtMTAzLjEyLTEwLjEzLTE1MS4zNS0zMC4xYy00OC4yMy0xOS45OC05MS40LTQ4LjgyLTEyOC4zMS04NS43M2wtMTE2LjU0LTExNi41NCAgIGMtOTIuODYtOTIuODYtOTIuODYtMjQzLjQxLDAtMzM2LjI3bDAsMGwyOTkuMTEsMjk5LjExYzMuOTcsMy45Nyw4LjUsNy4zMiwxMy40NSw5Ljk3YzY5LjkxLDM3LjQ5LDE0NS45NCwyNC44MSwxOTUuMTYtMjQuNDEgICBsMy44OC0zLjg4YzYxLjUtNjEuNSw2MS41LTE2MS41NiwwLTIyMy4wNWwtOTAuMTgtOTAuMThsMTY4LjEzLTE2OC4xM2w5MC4xOCw5MC4xOGMzNy43MiwzNy43Miw2Ni45NSw4MS43Niw4Ni44NywxMzAuODkgICBjMTkuMjIsNDcuNCwyOC45Nyw5Ny40NSwyOC45NywxNDguNzdjMCw1MS4zMS05Ljc1LDEwMS4zNi0yOC45NywxNDguNzdjLTE5LjkzLDQ5LjE0LTQ5LjE1LDkzLjE4LTg2Ljg3LDEzMC44OWwtMTEuNDQsMTEuNDQgICBjLTExLjcxLDExLjcxLTI0LjMsMjIuNTItMzcuODEsMzIuMUMxMDkyLjQ4LDE3MjUuMjMsMTAxMi40NywxNzUxLDkyOS4yMSwxNzUxeiIvPg0KICAgICAgICA8L2c+DQogICAgPGc+DQogICAgICAgICAgICA8cGF0aCBjbGFzcz0ic3QwIiBkPSJNNzM1Ljg2LDkzNi4yN2wtOTAuMTgtOTAuMThjLTM3LjcyLTM3LjcyLTY2Ljk0LTgxLjc2LTg2Ljg3LTEzMC44OWMtMTkuMjItNDcuNC0yOC45Ny05Ny40NS0yOC45Ny0xNDguNzcgICBzOS43NS0xMDEuMzYsMjguOTctMTQ4Ljc2YzE5LjkzLTQ5LjE0LDQ5LjE1LTkzLjE4LDg2Ljg3LTEzMC44OWwzLjg4LTMuODhjMzYuOTEtMzYuOTEsODAuMDgtNjUuNzYsMTI4LjMxLTg1LjczICAgYzQ4LjIzLTE5Ljk4LDk5LjE1LTMwLjExLDE1MS4zNS0zMC4xMWM1Mi4yLDAsMTAzLjEyLDEwLjEzLDE1MS4zNSwzMC4xMWM0OC4yMywxOS45OCw5MS40LDQ4LjgyLDEyOC4zMSw4NS43M2wxMDkuODEsMTA5LjgxICAgYzkyLjg2LDkyLjg2LDkyLjg2LDI0My40MSwwLDMzNi4yN2wwLDBsLTI3Ny45NC0yNzcuOTRjLTI5Ljc5LTI5Ljc5LTY5LjQtNDYuMi0xMTEuNTItNDYuMTljLTQyLjEzLDAtODEuNzQsMTYuNDEtMTExLjUyLDQ2LjIgICBsLTMuODgsMy44OGMtNjEuNSw2MS40OS02MS41LDE2MS41NSwwLDIyMy4wNWw5MC4xOCw5MC4xOEw3MzUuODYsOTM2LjI3eiIvPg0KICAgICAgICA8L2c+DQogICAgPHBhdGggY2xhc3M9InN0MCIgZD0iTTE0MDcuNjYsODc2Ljc0TDEwMTEuNyw0ODAuNzljLTQ2LjQzLTQ2LjQzLTEyMS43LTQ2LjQzLTE2OC4xMywwdjBjLTQ2LjQzLDQ2LjQzLTQ2LjQzLDEyMS43LDAsMTY4LjEzICBsMTEzLjM0LDExMy4zNEw3MzEuNTUsOTg3LjYyTDYxOS4zMiw4NzUuMzljLTQ2LjQzLTQ2LjQzLTEyMS43LTQ2LjQzLTE2OC4xMywwdjBjLTQ2LjQzLDQ2LjQzLTQ2LjQzLDEyMS43LDAsMTY4LjEzbDM5NS45NSwzOTUuOTUgIGM0Ni40Myw0Ni40MywxMjEuNyw0Ni40MywxNjguMTMsMGwwLDBjNDYuNDMtNDYuNDMsNDYuNDMtMTIxLjcsMC0xNjguMTNsLTExNS41OC0xMTUuNThsMjI1LjM2LTIyNS4zNmwxMTQuNDgsMTE0LjQ4ICBjNDYuNDMsNDYuNDMsMTIxLjcsNDYuNDMsMTY4LjEzLDBsMCwwQzE0NTQuMDksOTk4LjQ0LDE0NTQuMDksOTIzLjE3LDE0MDcuNjYsODc2Ljc0eiIvPg0KICAgIDwvc3ZnPg==',
            6,
        );
    }
    public function count_open_tickets() {
        global $wpdb;
        $main_table = $wpdb->prefix . 'ticket_pnlt';
        $open_tickets_count = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM $main_table WHERE status = %s",
                'open'
            )
        );
        return $open_tickets_count;
    }

    public function admin_tickets_page() {
        wp_enqueue_style('panel-style-panelet', PNLT_ASSETS.'/css/panel-style.css', array(), PNLT_VERSION, 'all', false);
        wp_enqueue_style('font-style-panelet', PNLT_ASSETS.'/css/font-yekan-bakh.css', array(), PNLT_VERSION, 'all', false);
        wp_enqueue_script('panel-script-panelet', PNLT_ASSETS.'/js/script-panel.js', array(), PNLT_VERSION, true);
        wp_localize_script('panel-script-panelet', 'login_script_data_panelet', array(
            'smstime' => 60,
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'nonce' => wp_create_nonce('pnlt-ajax-nonce')
        ));
        global $wpdb;
        $main_table = $wpdb->prefix . 'ticket_pnlt';
        $messages_table = $wpdb->prefix . 'ticket_pnlt_messages';
        $query = "SELECT main.*, GROUP_CONCAT(messages.description ORDER BY messages.datetime ASC SEPARATOR '|') AS messages
          FROM $main_table AS main
          LEFT JOIN $messages_table AS messages ON main.ticket_id = messages.ticket_id
          GROUP BY main.ticket_id";
        $tickets = $wpdb->get_results($query, ARRAY_A);
        if (isset($_GET['view-ticket']) && $tickets != false) {
            $ticket_id = $_GET['view-ticket'];
            $ticket_data = $this->get_ticket_data($ticket_id);
            $ticket_main = $ticket_data['main_data'];
            $ticket_messages = $ticket_data['messages_data'];
            $ticket_date = strtotime($ticket_messages['updated_at']);
            $formatter = new IntlDateFormatter(
                'fa_IR@calendar=persian',
                IntlDateFormatter::LONG,
                IntlDateFormatter::SHORT,
                'Asia/Tehran',
                IntlDateFormatter::TRADITIONAL
            );
            $ticket_date = $formatter->format($ticket_date);
            if ($ticket_main['status'] == "open") {
                $ticket_status = "در انتظار پاسخ";
                $ticket_status_class = "yellow-wait-pnlt";
            } else {
                $ticket_status = "پاسخ داده شده";
                $ticket_status_class = "green-answered-pnlt";
            }
            $admin_tickets = add_query_arg(
                array(
                    'page' => 'tickets',
                ),
                admin_url('admin.php')
            );
            ?>
            <div class="wrap tickets-pnlt">
                <h1 class="wp-heading-inline">تیکت ها</h1>
                <div class="pnlt-loading-popup">
                    <svg version="1.1" id="L9" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                                     viewBox="0 0 100 100" enable-background="new 0 0 0 0" xml:space="preserve">
                        <path fill="currentColor" d="M73,50c0-12.7-10.3-23-23-23S27,37.3,27,50 M30.9,50c0-10.5,8.5-19.1,19.1-19.1S69.1,39.5,69.1,50">
                            <animateTransform
                                    attributeName="transform"
                                    attributeType="XML"
                                    type="rotate"
                                    dur="1s"
                                    from="0 50 50"
                                    to="360 50 50"
                                    repeatCount="indefinite" />
                        </path>
                    </svg>
                </div>
                <div class="panelet-settings-inner-wrap tickets-pnlt-single-box">
                    <div class="head-ticket-single-pnlt">
                        <div class="titles-ticket-pnlt">
                            <span class="id-ticket">
                                <?php echo "شماره تیکت: ".$ticket_id; ?>
                            </span>
                            <h2>
                                <?php echo $ticket_main['title']; ?>
                            </h2>
                            <span class="date-ticket">
                                <?php echo $ticket_date; ?>
                            </span>
                        </div>
                        <span id="chatStatusPnlt">
                            <span class="status-ticket-pnlt <?php echo $ticket_status_class; ?>">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-chart-pie-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="1.5" stroke="#2c3e50" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                  <path d="M12 3v9h9" />
                                  <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                                </svg>
                                <?php echo $ticket_status; ?>
                            </span>
                        </span>
                        <div class="btns-ticket-pnlt">
                            <a href="<?php echo $admin_tickets; ?>" class="go-to-tickets-pnlt btn-tickets">
                                بازگشت به تیکت ها
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-arrow-left" width="24" height="24" viewBox="0 0 24 24" stroke-width="1.5" stroke="#2c3e50" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M5 12l14 0" />
                                    <path d="M5 12l6 6" />
                                    <path d="M5 12l6 -6" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
                <div id="chatBoxPnlt" class="tickets-pnlt-chat-box">
                    <?php $this->messages_page_builder($ticket_id); ?>
                </div>
                <div class="panelet-settings-inner-wrap tickets-pnlt-form-add-box">
                    <form id="admin_ticket_panelet" class="box-inner-content form-edit-detail" action="" method="post">
                        <div class="fields-box-pnlt flex">
                            <div class="field-detail flex">
                                <textarea class="input-text input-fixed-size-textarea" name="ticket_text" id="ticket_text" placeholder="" rows="5"/></textarea>
                                <span><?php echo "پاسخ شما"; ?></span>
                            </div>
                        </div>
                        <?php
                        $current_user_id = get_current_user_id();
                        wp_nonce_field( 'add-ticket-panelet', 'add-ticket-panelet-nonce' ); ?>
                        <input type="hidden" name="action" value="add_ticket_panelet" />
                        <input type="hidden" name="user_id_ticket_panelet" value="<?php echo $current_user_id; ?>" />
                        <input type="hidden" name="ticket_status" value="closed" />
                        <input type="hidden" name="ticket_id_panelet" value="<?php echo $ticket_id; ?>" />
                        <input class="btn-panel" type="submit" name="submit" value="<?php echo "ارسال"; ?>">
                    </form>
                </div>
            </div>
            <?php
        } else {
        ?>
            <div class="wrap tickets-pnlt">
                <h1 class="wp-heading-inline">Tickets</h1>
                <div class="tickets-pnlt-list-box">
                    <?php
                    if (is_array($tickets)) {
                        foreach ($tickets as $message) {
                            $ticket_id = $message['ticket_id'];
                            $ticket_date = strtotime($message['updated_at']);
                            $formatter = new IntlDateFormatter(
                                'fa_IR@calendar=persian',
                                IntlDateFormatter::LONG,
                                IntlDateFormatter::SHORT,
                                'Asia/Tehran',
                                IntlDateFormatter::TRADITIONAL
                            );
                            $ticket_date = $formatter->format($ticket_date);
                            if ($message['status'] == "open") {
                                $ticket_status = "در انتظار پاسخ";
                                $ticket_status_class = "yellow-wait-pnlt";
                            } else {
                                $ticket_status = "پاسخ داده شده";
                                $ticket_status_class = "green-answered-pnlt";
                            }
                            $admin_url = add_query_arg(
                                array(
                                    'page' => 'tickets',
                                    'view-ticket' => $ticket_id,
                                ),
                                admin_url('admin.php')
                            );
                            ?>
                            <a href="<?php echo esc_url($admin_url); ?>" class="box-order-item">
                                <div class="order-item-pnlt name-id-order-pnlt">
                                <span class="order-product-name-pnlt">
                                    <?php echo $message['title']; ?>
                                </span>
                                    <span class="order-item-title-pnlt">
                                    شماره تیکت:
                                    <strong>
                                        <?php echo $ticket_id; ?>
                                    </strong>
                                </span>
                                </div>
                                <div class="order-item-pnlt datetime-order-pnlt">
                                    <time><?php echo $ticket_date; ?></time>
                                </div>
                                <div class="order-item-pnlt status-order-pnlt">
                                <span class="status-ticket-pnlt <?php echo $ticket_status_class; ?>">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-chart-pie-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="1.5" stroke="#2c3e50" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                      <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                      <path d="M12 3v9h9" />
                                      <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                                    </svg>
                                    <?php echo $ticket_status; ?>
                                </span>
                                </div>
                                <div class="order-item-pnlt view-order-pnlt">
                                <span class="order-item-view-pnlt">
                                    <?php echo "مشاهده تیکت"; ?>
                                </span>
                                </div>
                            </a>
                            <?php
                        }
                    } else {
                        ?>
                        <a class="box-order-item">هیچ تیکتی تاکنون ارسال نشده است</a>
                        <?php
                    }
                    ?>
                </div>
            </div>
        <?php
        }
    }
    public function display_tickets_page() {
        $ticket_id = isset($_GET['view-ticket']) ? $_GET['view-ticket'] : "";
        $current_user_id = get_current_user_id();
        $tickets = $this->get_tickets($current_user_id);
        $ticket_data = $this->get_ticket_data($ticket_id);
        $ticket_main = is_array($ticket_data) ? $ticket_data['main_data'] : "";
        $ticket_messages = is_array($ticket_data) ? $ticket_data['messages_data'] : "";
        if (isset($_GET['view-ticket']) && $ticket_data != false) {
            $ticket_date = strtotime($ticket_main['created_at']);
            $formatter = new IntlDateFormatter(
                'fa_IR@calendar=persian',
                IntlDateFormatter::LONG,
                IntlDateFormatter::SHORT,
                'Asia/Tehran',
                IntlDateFormatter::TRADITIONAL
            );
            $ticket_date = $formatter->format($ticket_date);
            if ($ticket_main['status'] == "open") {
                $ticket_status = "در انتظار پاسخ";
                $ticket_status_class = "yellow-wait-pnlt";
            } else {
                $ticket_status = "پاسخ داده شده";
                $ticket_status_class = "green-answered-pnlt";
            }
        ?>
        <div class="panelet-settings-inner-wrap tickets-pnlt-single-box">
            <div class="head-ticket-single-pnlt">
                <div class="titles-ticket-pnlt">
                    <span class="id-ticket">
                        <?php echo "شماره تیکت: ".$ticket_id; ?>
                    </span>
                    <h2>
                        <?php echo $ticket_main['title']; ?>
                    </h2>
                    <span class="date-ticket">
                        <?php echo $ticket_date; ?>
                    </span>
                </div>
                <span id="chatStatusPnlt">
                    <span class="status-ticket-pnlt <?php echo $ticket_status_class; ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-chart-pie-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="1.5" stroke="#2c3e50" fill="none" stroke-linecap="round" stroke-linejoin="round">
                          <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                          <path d="M12 3v9h9" />
                          <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                        </svg>
                        <?php echo $ticket_status; ?>
                    </span>
                </span>
                <div class="btns-ticket-pnlt">
                    <a href="<?php echo wc_get_account_endpoint_url('p-tickets'); ?>" class="go-to-tickets-pnlt btn-tickets">
                        بازگشت به تیکت ها
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-arrow-left" width="24" height="24" viewBox="0 0 24 24" stroke-width="1.5" stroke="#2c3e50" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M5 12l14 0" />
                            <path d="M5 12l6 6" />
                            <path d="M5 12l6 -6" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>
        <div id="chatBoxPnlt" class="tickets-pnlt-chat-box">
            <?php $this->messages_page_builder($ticket_id); ?>
        </div>
        <div class="panelet-settings-inner-wrap tickets-pnlt-form-add-box">
            <form id="add_ticket_panelet" class="box-inner-content form-edit-detail" action="" method="post">
                <div class="fields-box-pnlt flex">
                    <div class="field-detail flex">
                        <textarea class="input-text input-fixed-size-textarea" name="ticket_text" id="ticket_text" placeholder="" rows="5"/></textarea>
                        <span><?php echo "پاسخ شما"; ?></span>
                    </div>
                </div>
                <?php
                $current_user_id = get_current_user_id();
                wp_nonce_field( 'add-ticket-panelet', 'add-ticket-panelet-nonce' ); ?>
                <input type="hidden" name="action" value="add_ticket_panelet" />
                <input type="hidden" name="user_id_ticket_panelet" value="<?php echo $current_user_id; ?>" />
                <input type="hidden" name="ticket_id_panelet" value="<?php echo $ticket_id; ?>" />
                <input class="btn-panel" type="submit" name="submit" value="<?php echo "ارسال"; ?>">
            </form>
        </div>
        <?php
        } else { ?>
            <div class="panelet-settings-inner-wrap tickets-pnlt-head-box">
                <h2>تیکت ها</h2>
                <p>در این بخش میتوانید با مدیر سایت ارتباط برقرار کنید. یکی از تیکت های قبلی را انتخاب کرده یا یک تیکت با موضوع جدید بفرستید</p>
                <a href="<?php echo wc_get_account_endpoint_url('p-new-ticket'); ?>" class="btn-tickets" onclick="openTicketFormPnlt(event)">ارسال تیکت جدید</a>
            </div>
            <div class="tickets-pnlt-list-box">
                <?php
                if (is_array($tickets)) {
                    foreach ($tickets as $message) {
                        $ticket_id = $message['ticket_id'];
                        $ticket_date = strtotime($message['updated_at']);
                        $formatter = new IntlDateFormatter(
                            'fa_IR@calendar=persian',
                            IntlDateFormatter::LONG,
                            IntlDateFormatter::SHORT,
                            'Asia/Tehran',
                            IntlDateFormatter::TRADITIONAL
                        );
                        $ticket_date = $formatter->format($ticket_date);
                        if ($message['status'] == "open") {
                            $ticket_status = "در انتظار پاسخ";
                            $ticket_status_class = "yellow-wait-pnlt";
                        } else {
                            $ticket_status = "پاسخ داده شده";
                            $ticket_status_class = "green-answered-pnlt";
                        }
                        ?>
                        <a href="<?php echo wc_get_account_endpoint_url('p-tickets') . "?view-ticket=" . $ticket_id; ?>" class="box-order-item">
                            <div class="order-item-pnlt name-id-order-pnlt">
                                <span class="order-product-name-pnlt">
                                    <?php echo $message['title']; ?>
                                </span>
                                    <span class="order-item-title-pnlt">
                                    شماره تیکت:
                                    <strong>
                                        <?php echo $ticket_id; ?>
                                    </strong>
                                </span>
                            </div>
                            <div class="order-item-pnlt datetime-order-pnlt">
                                <time><?php echo $ticket_date; ?></time>
                            </div>
                            <div class="order-item-pnlt status-order-pnlt">
                                <span class="status-ticket-pnlt <?php echo $ticket_status_class; ?>">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-chart-pie-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="1.5" stroke="#2c3e50" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                      <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                      <path d="M12 3v9h9" />
                                      <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                                    </svg>
                                    <?php echo $ticket_status; ?>
                                </span>
                            </div>
                            <div class="order-item-pnlt view-order-pnlt">
                                <span class="order-item-view-pnlt">
                                    <?php echo "مشاهده تیکت"; ?>
                                </span>
                            </div>
                        </a>
                        <?php
                    }
                } else {
                    ?>
                        <a class="box-order-item">هیچ تیکتی تاکنون ارسال نشده است</a>
                    <?php
                }
                ?>
            </div>
        <?php }
    }
    public function new_tickets_form_page() {
        ?>
        <div class="panelet-settings-inner-wrap tickets-pnlt-head-box">
            <h2>ثبت تیکت جدید</h2>
            <p>لطفا عنوان و محتوای پیام خود را در فرم زیر وارد کنید</p>
            <form id="new_ticket_panelet" class="box-inner-content form-edit-detail" action="" method="post">
                <div class="error-text"></div>
                <div class="success-text"></div>
                <div class="fields-box-pnlt flex">
                    <div class="field-detail flex">
                        <input type="text" class="input-text" name="ticket_title" id="ticket_title" value="" placeholder=""/>
                        <span><?php echo "عنوان تیکت"; ?></span>
                    </div>
                    <div class="field-detail flex">
                        <textarea class="input-text input-fixed-size-textarea" name="ticket_text" id="ticket_text" placeholder="" rows="10"/></textarea>
                        <span><?php echo "محتوای تیکت"; ?></span>
                    </div>
                </div>
                <?php wp_nonce_field( 'new-ticket-panelet', 'new-ticket-panelet-nonce' ); ?>
                <input type="hidden" name="action" value="new_ticket_panelet" />
                <input class="btn-panel" type="submit" name="submit" value="<?php echo "ارسال تیکت"; ?>">
            </form>
        </div>
        <?php
    }
    public function messages_page_builder($ticket_id) {
        $ticket_data = $this->get_ticket_data($ticket_id);
        $ticket_main = $ticket_data['main_data'];
        $ticket_messages = $ticket_data['messages_data'];
        foreach ($ticket_messages as $message) {
            ?>
            <div class="message-ticket-pnlt <?php echo 'message-pnlt-'.$message['role']; ?>">
                <div class="avatar-user-message">
                    <?php echo get_avatar($message['sender_id'], '50px'); ?>
                </div>
                <div class="main-user-message">
                    <p class="message-text-pnlt">
                        <?php
                        echo $message['description'];
                        ?>
                    </p>
                    <span class="name-date-pnlt">
                        <?php
                        $ticket_date = strtotime($message['datetime']);
                        $formatter = new IntlDateFormatter(
                            'fa_IR@calendar=persian',
                            IntlDateFormatter::LONG,
                            IntlDateFormatter::SHORT,
                            'Asia/Tehran',
                            IntlDateFormatter::TRADITIONAL
                        );
                        $ticket_date = $formatter->format($ticket_date);
                        ?>
                        <time><?php echo $ticket_date; ?></time>
                        <span class="display-name-user-message-pnlt">
                            <?php
                            $user_data = get_userdata($message['sender_id']);
                            $display_name = $user_data->display_name;
                            echo $display_name;
                            if ($message['role'] == "admin") { ?>
                                <span class="admin-tag-message-pnlt">مدیر سایت</span>
                            <?php } ?>
                        </span>
                    </span>
                </div>
            </div>
            <?php
        }
    }
    public function display_tickets_page_shortcode() {
        ob_start();
        $this->display_tickets_page();
        return ob_get_clean();
    }
}
new Panelet_TICKET;