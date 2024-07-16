<?php
/*
Plugin Name: Password Generator
Description: A simple password generator plugin with storage functionality for business users.
Version: 1.2
Authors: Christopher Coipel, Caroline Anderson, Shrey Patel, Mary Sousa, Isha Imran  
*/

// Shortcode to display password generator form
function password_generator_form() 
{
    ob_start();
    ?>
    <form id="password-generator-form">
        <label for="length">Password Length:</label>
        <input type="number" id="length" name="length" min="8" max="50" value="12">
        <br>
        <input type="checkbox" id="require-lowercase" name="require-lowercase" checked>
        <label for="require-lowercase">Require Lowercase</label>
        <br>
        <input type="checkbox" id="require-uppercase" name="require-uppercase" checked>
        <label for="require-uppercase">Require Uppercase</label>
        <br>
        <input type="checkbox" id="require-symbols" name="require-symbols" checked>
        <label for="require-symbols">Require Symbols</label>
        <br>
        <button type="submit">Generate Password</button>
    </form>
    <div id="generated-password"></div>
    <script>
        document.getElementById('password-generator-form').addEventListener('submit', function(e) 
        {
            e.preventDefault();
            var length = document.getElementById('length').value;
            var requireLowercase = document.getElementById('require-lowercase').checked ? 1 : 0;
            var requireUppercase = document.getElementById('require-uppercase').checked ? 1 : 0;
            var requireSymbols = document.getElementById('require-symbols').checked ? 1 : 0;
            fetch('<?php echo admin_url('admin-ajax.php'); ?>?action=generate_password&length=' + length + '&require_lowercase=' + requireLowercase + '&require_uppercase=' + requireUppercase + '&require_symbols=' + requireSymbols)
                .then(response => response.text())
                .then(data => 
                {
                    document.getElementById('generated-password').innerText = data;
                });
        });
    </script>
    <?php
    return ob_get_clean();
}
add_shortcode('password_generator', 'password_generator_form');

// Handles AJAX request to generate a password
function generate_password() 
{
    $length = isset($_GET['length']) ? intval($_GET['length']) : 12;
    $require_lowercase = isset($_GET['require_lowercase']) ? boolval($_GET['require_lowercase']) : true;
    $require_uppercase = isset($_GET['require_uppercase']) ? boolval($_GET['require_uppercase']) : true;
    $require_symbols = isset($_GET['require_symbols']) ? boolval($_GET['require_symbols']) : true;

    $characters = '0123456789';
    if ($require_lowercase) 
    {
        $characters .= 'abcdefghijklmnopqrstuvwxyz';
    }
    if ($require_uppercase) 
    {
        $characters .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    }
    if ($require_symbols) 
    {
        $characters .= '!@#$%^&*()';
    }

    $password = '';
    for ($i = 0; $i < $length; $i++) 
    {
        $password .= $characters[rand(0, strlen($characters) - 1)];
    }

    // Makes sure password meets all requirements
    if ($require_lowercase && !preg_match('/[a-z]/', $password)) 
    {
        $password .= 'a';
    }
    if ($require_uppercase && !preg_match('/[A-Z]/', $password)) 
    {
        $password .= 'A';
    }
    if ($require_symbols && !preg_match('/[!@#$%^&*()]/', $password)) 
    {
        $password .= '@';
    }
    // Shuffle for avoiding predictable patterns
    $password = str_shuffle($password);

    echo $password;
    wp_die();
}
add_action('wp_ajax_generate_password', 'generate_password');
add_action('wp_ajax_nopriv_generate_password', 'generate_password');

// Create custom table for storing passwords
function create_password_table() 
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'passwords';
    $charset_collate = $wpdb->get_charset_collate();
    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        user_id bigint(20) NOT NULL,
        password_name varchar(255) NOT NULL,
        password_value text NOT NULL,
        created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}
register_activation_hook(__FILE__, 'create_password_table');

// Add custom user role for business users
function add_business_user_role() 
{
    add_role('business_user', 'Business User', array(
        'read' => true,
        'edit_posts' => true,
        'delete_posts' => true,
        'publish_posts' => true,
        'upload_files' => true
    ));
}
add_action('init', 'add_business_user_role');

// Display login/register forms and password storage form
function display_login_register_forms() 
{
    if (!is_user_logged_in()) 
    {
        echo '<p>You need to <a href="' . site_url('/index.php/log-reg/#login') . '">log in</a> or <a href="' . site_url('/index.php/log-reg/#register') . '">register</a> to access this area.</p>';
    } 
    else 
    {
        $user_id = get_current_user_id();
        if (user_can($user_id, 'business_user')) 
        {
            ?>
            <h2>Store a New Password</h2>
            <form id="password-storage-form">
                <input type="text" id="password-name" placeholder="Password Name" required><br>
                <input type="text" id="password-value" placeholder="Password Value" required><br>
                <button type="submit">Store Password</button>
            </form>
            <div id="storage-result"></div>
            <script>
                document.getElementById('password-storage-form').addEventListener('submit', function(e) 
                {
                    e.preventDefault();
                    var passwordName = document.getElementById('password-name').value;
                    var passwordValue = document.getElementById('password-value').value;
                    fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: 'action=store_password&password_name=' + encodeURIComponent(passwordName) + '&password_value=' + encodeURIComponent(passwordValue)
                    })
                    .then(response => response.json())
                    .then(data => 
                    {
                        if (data.success) 
                        {
                            document.getElementById('storage-result').innerText = data.data;
                        } 
                        else 
                        {
                            document.getElementById('storage-result').innerText = 'Error: ' + data.data;
                        }
                    });
                });
            </script>
            <?php
        } 
        else 
        {
            echo '<p>You need to be a business user to store passwords.</p>';
        }
    }
}
add_shortcode('display_login_register_forms', 'display_login_register_forms');

// Ajax handler for storing passwords
function handle_password_storage() 
{
    $user_id = get_current_user_id();
    if ($user_id === 0) 
    {
        wp_send_json_error('User not logged in');
    } 
    else 
    {
        $password_name = sanitize_text_field($_POST['password_name']);
        $password_value = sanitize_text_field($_POST['password_value']);
        global $wpdb;
        $table_name = $wpdb->prefix . 'passwords';
        $result = $wpdb->insert(
            $table_name,
            array(
                'user_id' => $user_id,
                'password_name' => $password_name,
                'password_value' => $password_value
            )
        );
        if ($result) 
        {
            wp_send_json_success('Password stored successfully');
        } 
        else 
        {
            wp_send_json_error('Failed to store password');
        }
    }
}
add_action('wp_ajax_store_password', 'handle_password_storage');

// Display stored passwords with delete option
function display_stored_passwords() 
{
    if (!is_user_logged_in()) 
    {
        echo '<p>You need to <a href="' . site_url('/index.php/log-reg/#login') . '">log in</a> or <a href="' . site_url('/index.php/log-reg/#register') . '">register</a> to access this area.</p>';
    } 
    else 
    {
        $user_id = get_current_user_id();
        if (user_can($user_id, 'business_user')) 
        {
            global $wpdb;
            $table_name = $wpdb->prefix . 'passwords';
            $passwords = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name WHERE user_id = %d", $user_id));

            if ($passwords) 
            {
                echo '<h2>Your Stored Passwords</h2>';
                echo '<table>';
                echo '<tr><th>Password Name</th><th>Password Value</th><th>Actions</th></tr>';
                foreach ($passwords as $password) 
                {
                    echo '<tr>';
                    echo '<td>' . esc_html($password->password_name) . '</td>';
                    echo '<td>' . esc_html($password->password_value) . '</td>';
                    echo '<td><button class="delete-password-button" data-id="' . esc_attr($password->id) . '">Delete</button></td>';
                    echo '</tr>';
                }
                echo '</table>';
            } 
            else 
            {
                echo '<p>You have no stored passwords.</p>';
            }
        } 
        else 
        {
            echo '<p>You need to be a business user to view stored passwords.</p>';
        }
    }
}
add_shortcode('stored_passwords', 'display_stored_passwords');

// Ajax handler for deleting passwords
function handle_password_deletion() 
{
    $user_id = get_current_user_id();
    if ($user_id === 0) 
    {
        wp_send_json_error('User not logged in');
    } 
    else 
    {
        $password_id = intval($_POST['password_id']);
        global $wpdb;
        $table_name = $wpdb->prefix . 'passwords';
        $deleted = $wpdb->delete($table_name, array('id' => $password_id, 'user_id' => $user_id));
        if ($deleted) 
        {
            wp_send_json_success('Password deleted successfully');
        } 
        else 
        {
            wp_send_json_error('Failed to delete password');
        }
    }
}
add_action('wp_ajax_delete_password', 'handle_password_deletion');

// Enqueue additional script for deleting passwords
function enqueue_password_deletion_script() 
{
    wp_enqueue_script('password-deletion-ajax', plugin_dir_url(__FILE__) . 'password-deletion-ajax.js', array('jquery'), null, true);
    wp_localize_script('password-deletion-ajax', 'ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));
}
add_action('wp_enqueue_scripts', 'enqueue_password_deletion_script');

// Shortcode to display a form for assigning the Business User role
function assign_business_user_form() 
{
    ob_start();
    ?>
    <form id="assign-business-user-form">
        <label for="user-email">User Email:</label>
        <input type="email" id="user-email" name="user-email" required>
        <button type="submit">Assign Business User Role</button>
    </form>
    <div id="assign-result"></div>
    <script>
        document.getElementById('assign-business-user-form').addEventListener('submit', function(e) 
        {
            e.preventDefault();
            var userEmail = document.getElementById('user-email').value;
            fetch('<?php echo admin_url('admin-ajax.php'); ?>', 
            {
                method: 'POST',
                headers: 
                {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: 'action=assign_business_user&user_email=' + encodeURIComponent(userEmail)
            })
            .then(response => response.json())
            .then(data => 
            {
                if (data.success) 
                {
                    document.getElementById('assign-result').innerText = data.data;
                } 
                else 
                {
                    document.getElementById('assign-result').innerText = 'Error: ' + data.data;
                }
            });
        });
    </script>
    <?php
    return ob_get_clean();
}
add_shortcode('assign_business_user_form', 'assign_business_user_form');

// Handle AJAX request to assign the Business User role
function handle_assign_business_user() 
{
    $user_email = sanitize_email($_POST['user_email']);
    $user = get_user_by('email', $user_email);
    if ($user) 
    {
        $user->set_role('business_user');
        wp_send_json_success('Business User role assigned successfully.');
    } 
    else 
    {
        wp_send_json_error('User not found.');
    }
}
add_action('wp_ajax_assign_business_user', 'handle_assign_business_user');

// Shortcode to display admin user management interface
function admin_user_management() 
{
    if (current_user_can('administrator')) 
    {
        $users = get_users();
        ob_start();
        echo '<h2>User Management</h2>';
        echo '<table>';
        echo '<tr><th>Username</th><th>Email</th><th>Role</th><th>Actions</th></tr>';
        foreach ($users as $user) 
        {
            echo '<tr>';
            echo '<td>' . esc_html($user->user_login) . '</td>';
            echo '<td>' . esc_html($user->user_email) . '</td>';
            echo '<td>' . esc_html(implode(', ', $user->roles)) . '</td>';
            echo '<td>
                <button class="delete-user-button" data-id="' . esc_attr($user->ID) . '">Delete</button>
                <button class="edit-user-button" data-id="' . esc_attr($user->ID) . '" data-email="' . esc_attr($user->user_email) . '" data-role="' . esc_attr(implode(', ', $user->roles)) . '">Edit</button>
            </td>';
            echo '</tr>';
        }
        echo '</table>';
        ?>
        <div id="edit-user-form-container" style="display:none;">
            <h2>Edit User</h2>
            <form id="edit-user-form">
                <input type="hidden" id="edit-user-id" name="user-id">
                <label for="edit-user-email">User Email:</label>
                <input type="email" id="edit-user-email" name="user-email" required>
                <label for="edit-user-role">User Role:</label>
                <select id="edit-user-role" name="user-role">
                    <option value="administrator">Administrator</option>
                    <option value="business_user">Business User</option>
                    <option value="subscriber">Subscriber</option>
                </select>
                <button type="submit">Save Changes</button>
            </form>
            <div id="edit-result"></div>
        </div>
        <script>
            document.querySelectorAll('.delete-user-button').forEach(button => 
            {
                button.addEventListener('click', function() 
                {
                    var userId = this.getAttribute('data-id');
                    fetch('<?php echo admin_url('admin-ajax.php'); ?>', 
                    {
                        method: 'POST',
                        headers: 
                        {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: 'action=delete_user&user_id=' + userId
                    })
                    .then(response => response.json())
                    .then(data => 
                    {
                        if (data.success) 
                        {
                            location.reload();
                        } 
                        else 
                        {
                            alert('Failed to delete user. Please try again.');
                        }
                    });
                });
            });

            document.querySelectorAll('.edit-user-button').forEach(button => 
            {
                button.addEventListener('click', function() 
                {
                    var userId = this.getAttribute('data-id');
                    var userEmail = this.getAttribute('data-email');
                    var userRole = this.getAttribute('data-role');
                    document.getElementById('edit-user-id').value = userId;
                    document.getElementById('edit-user-email').value = userEmail;
                    document.getElementById('edit-user-role').value = userRole;
                    document.getElementById('edit-user-form-container').style.display = 'block';
                });
            });

            document.getElementById('edit-user-form').addEventListener('submit', function(e) 
            {
                e.preventDefault();
                var userId = document.getElementById('edit-user-id').value;
                var userEmail = document.getElementById('edit-user-email').value;
                var userRole = document.getElementById('edit-user-role').value;
                fetch('<?php echo admin_url('admin-ajax.php'); ?>', 
                {
                    method: 'POST',
                    headers: 
                    {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: 'action=edit_user&user_id=' + userId + '&user_email=' + encodeURIComponent(userEmail) + '&user_role=' + userRole
                })
                .then(response => response.json())
                .then(data => 
                {
                    if (data.success) 
                    {
                        document.getElementById('edit-result').innerText = data.data;
                        location.reload();
                    } 
                    else 
                    {
                        document.getElementById('edit-result').innerText = 'Error: ' + data.data;
                    }
                });
            });
        </script>
        <?php
        return ob_get_clean();
    } 
    else 
    {
        return '<p>You do not have permission to access this page.</p>';
    }
}
add_shortcode('admin_user_management', 'admin_user_management');

// Handle AJAX request to delete a user
function handle_delete_user() 
{
    if (!current_user_can('administrator')) 
    {
        wp_send_json_error('You do not have permission to perform this action.');
    }

    $user_id = intval($_POST['user_id']);
    require_once(ABSPATH . 'wp-admin/includes/user.php');
    if (wp_delete_user($user_id)) 
    {
        wp_send_json_success('User deleted successfully.');
    } 
    else 
    {
        wp_send_json_error('Failed to delete user.');
    }
}
add_action('wp_ajax_delete_user', 'handle_delete_user');

// Handle AJAX request to edit a user
function handle_edit_user() 
{
    if (!current_user_can('administrator')) 
    {
        wp_send_json_error('You do not have permission to perform this action.');
    }

    $user_id = intval($_POST['user_id']);
    $user_email = sanitize_email($_POST['user_email']);
    $user_role = sanitize_text_field($_POST['user_role']);
    
    $user = get_user_by('id', $user_id);
    if ($user) 
    {
        $user->user_email = $user_email;
        $user->set_role($user_role);
        wp_update_user($user);
        wp_send_json_success('User updated successfully.');
    } 
    else 
    {
        wp_send_json_error('User not found.');
    }
}
add_action('wp_ajax_edit_user', 'handle_edit_user');
?>
