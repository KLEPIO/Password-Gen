// Add custom user role for business users
function add_business_user_role() {
    add_role('business_user', 'Business User', array(
        'read' => true,
        'edit_posts' => true,
        'delete_posts' => true,
        'publish_posts' => true,
        'upload_files' => true,
        'manage_options' => true, // Add any other capabilities you need
    ));
}
add_action('init', 'add_business_user_role');
