<?php
/**
 * Template Name: Account Page
 */

if (!is_user_logged_in()) {
    wp_redirect(home_url());
    exit;
}

$current_user = wp_get_current_user();
$user_id = $current_user->ID;
$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_account_nonce']) && wp_verify_nonce($_POST['update_account_nonce'], 'update_account')) {
    
    $name = sanitize_text_field($_POST['account_name']);
    $email = sanitize_email($_POST['account_email']);
    $phone = sanitize_text_field($_POST['account_phone']);
    $password = $_POST['account_password'];

    $userdata = array(
        'ID' => $user_id,
    );

    if (!empty($name)) {
        $userdata['display_name'] = $name;
        $userdata['first_name'] = $name;
    }
    
    // Email update requires care to avoid collisions
    if (!empty($email) && $email !== $current_user->user_email) {
        if (!email_exists($email)) {
            $userdata['user_email'] = $email;
        } else {
            $message = 'Email address is already in use by another account.';
            $message_type = 'error';
        }
    } else if (empty($email)) {
        // Handle optional email logic by generating a placeholder if it was emptied
        if (strpos($current_user->user_email, '@dummy.com') === false) {
            $userdata['user_email'] = $phone . '@dummy.com';
        }
    }

    if (!empty($password)) {
        $userdata['user_pass'] = $password;
    }

    if (empty($message_type)) {
        $update_id = wp_update_user($userdata);
        if (is_wp_error($update_id)) {
            $message = $update_id->get_error_message();
            $message_type = 'error';
        } else {
            if (!empty($phone)) {
                update_user_meta($user_id, 'billing_phone', $phone);
                update_user_meta($user_id, 'user_phone', $phone);
            }

            if (!empty($_FILES['account_avatar']) && $_FILES['account_avatar']['error'] === UPLOAD_ERR_OK) {
                require_once(ABSPATH . 'wp-admin/includes/file.php');
                $upload_overrides = array('test_form' => false);
                $uploaded_file = wp_handle_upload($_FILES['account_avatar'], $upload_overrides);
                if (isset($uploaded_file['file'])) {
                    update_user_meta($user_id, 'custom_avatar_url', $uploaded_file['url']);
                } else {
                    $message = $uploaded_file['error'];
                    $message_type = 'error';
                }
            }

            if (empty($message_type)) {
                $message = 'Account updated successfully.';
                $message_type = 'success';
            }
            
            // Refresh current user data
            $current_user = wp_get_current_user();
        }
    }
}

get_header();

$current_phone = get_user_meta($user_id, 'billing_phone', true);
if (empty($current_phone)) {
    $current_phone = get_user_meta($user_id, 'user_phone', true);
}
?>

<div class="account-page-container container" style="padding: 80px 24px; min-height: 70vh;">
    <div class="account-card card" style="max-width: 600px; margin: 0 auto; border: 1px solid var(--color-steel-blue);">
        <h1 class="account-title" style="margin-bottom: 30px; text-align: center;">My Account</h1>
        
        <?php if (!empty($message)): ?>
            <div class="account-message <?php echo esc_attr($message_type); ?>" style="padding: 15px; margin-bottom: 20px; border-radius: 8px; text-align: center; background-color: rgba(255,255,255,0.05); color: <?php echo $message_type === 'success' ? '#10b981' : '#ef4444'; ?>;">
                <?php echo esc_html($message); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="" class="account-form" enctype="multipart/form-data">
            <?php wp_nonce_field('update_account', 'update_account_nonce'); ?>
            
            <?php 
            $avatar_url = get_user_meta($user_id, 'custom_avatar_url', true); 
            ?>
            <div class="form-group" style="margin-bottom: 30px; text-align: center;">
                <div style="width: 100px; height: 100px; border-radius: 50%; background-color: var(--color-deep-void); border: 2px solid var(--color-steel-blue); margin: 0 auto 15px auto; overflow: hidden; display: flex; align-items: center; justify-content: center;">
                    <?php if ($avatar_url): ?>
                        <img src="<?php echo esc_url($avatar_url); ?>" alt="Profile Picture" style="width: 100%; height: 100%; object-fit: cover;">
                    <?php else: ?>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="var(--color-steel-blue)" style="width: 50px; height: 50px;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                        </svg>
                    <?php endif; ?>
                </div>
                <label for="account_avatar" style="display: block; margin-bottom: 8px; color: var(--color-text-white); cursor: pointer; text-decoration: underline;">Change Profile Picture</label>
                <input type="file" id="account_avatar" name="account_avatar" accept="image/*" style="display: none;">
                <script>
                    document.getElementById('account_avatar').addEventListener('change', function(e) {
                        if (this.files && this.files[0]) {
                            const reader = new FileReader();
                            reader.onload = function(e) {
                                const imgContainer = document.querySelector('.form-group > div');
                                imgContainer.innerHTML = '<img src="' + e.target.result + '" style="width: 100%; height: 100%; object-fit: cover;">';
                            }
                            reader.readAsDataURL(this.files[0]);
                        }
                    });
                </script>
            </div>
            
            <div class="form-group" style="margin-bottom: 20px;">
                <label for="account_name" style="display: block; margin-bottom: 8px; color: var(--color-text-grey);">Full Name *</label>
                <input type="text" id="account_name" name="account_name" value="<?php echo esc_attr($current_user->display_name); ?>" required style="width: 100%; padding: 12px; background-color: var(--color-deep-void); border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; color: var(--color-text-white);">
            </div>
            
            <div class="form-group" style="margin-bottom: 20px;">
                <label for="account_phone" style="display: block; margin-bottom: 8px; color: var(--color-text-grey);">Phone Number *</label>
                <input type="tel" id="account_phone" name="account_phone" value="<?php echo esc_attr($current_phone); ?>" required style="width: 100%; padding: 12px; background-color: var(--color-deep-void); border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; color: var(--color-text-white);">
            </div>
            
            <div class="form-group" style="margin-bottom: 20px;">
                <label for="account_email" style="display: block; margin-bottom: 8px; color: var(--color-text-grey);">Email (Optional)</label>
                <input type="email" id="account_email" name="account_email" value="<?php echo esc_attr(strpos($current_user->user_email, '@dummy.com') === false ? $current_user->user_email : ''); ?>" style="width: 100%; padding: 12px; background-color: var(--color-deep-void); border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; color: var(--color-text-white);">
                <small class="form-help" style="display: block; margin-top: 5px; color: var(--color-text-muted); font-size: 13px;">Leave blank if you prefer not to provide an email.</small>
            </div>
            
            <div class="form-group" style="margin-bottom: 30px;">
                <label for="account_password" style="display: block; margin-bottom: 8px; color: var(--color-text-grey);">New Password</label>
                <input type="password" id="account_password" name="account_password" placeholder="Leave blank to keep current password" style="width: 100%; padding: 12px; background-color: var(--color-deep-void); border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; color: var(--color-text-white);">
            </div>
            
            <button type="submit" class="btn btn-primary account-submit-btn" style="width: 100%; padding: 15px; font-size: 16px;">Update Profile</button>
        </form>
    </div>
</div>

<?php get_footer(); ?>
