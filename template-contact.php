<?php
/**
 * Template Name: Contact Page
 */

$response_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['election_contact_submit'])) {
    if (isset($_POST['election_contact_nonce']) && wp_verify_nonce($_POST['election_contact_nonce'], 'election_contact_action')) {
        $name = sanitize_text_field($_POST['contact_name']);
        $phone = sanitize_text_field($_POST['contact_phone']);
        $address = sanitize_text_field($_POST['contact_address']);
        $message = sanitize_textarea_field($_POST['contact_message']);

        // Basic Validation
        if (empty($name) || empty($phone) || empty($message)) {
            $response_message = '<div class="alert alert-error">Please fill in all required fields.</div>';
        } else {
            // Store in Database (Custom Post Type)
            $post_data = array(
                'post_title' => $name . ' - ' . date('Y-m-d H:i:s'),
                'post_content' => "Phone: $phone\nAddress: $address\n\nMessage:\n$message",
                'post_status' => 'publish',
                'post_type' => 'inquiry',
            );

            $post_id = wp_insert_post($post_data);

            if ($post_id && !is_wp_error($post_id)) {
                // Save meta data
                update_post_meta($post_id, '_contact_phone', $phone);
                update_post_meta($post_id, '_contact_address', $address);
                update_post_meta($post_id, '_contact_email', $name); // Storing name as email placeholder if needed

                // Send Email (Optional, but good for notification)
                $to = get_option('admin_email');
                $subject = 'New Contact Inquiry from ' . $name;
                $body = "Name: $name\nPhone: $phone\nAddress: $address\n\nMessage:\n$message";
                $headers = array('Content-Type: text/plain; charset=UTF-8');
                wp_mail($to, $subject, $body, $headers); // We don't check result here to avoid showing error if mail server is down

                $response_message = '<div class="alert alert-success">Thank you! Your message has been received and stored securely.</div>';
            } else {
                $response_message = '<div class="alert alert-error">Sorry, could not save your message. Please try again.</div>';
            }
        }
    } else {
        $response_message = '<div class="alert alert-error">Security check failed. Please refresh and try again.</div>';
    }
}

get_header(); ?>

<main id="primary" class="contact-page">

    <!-- Contact Header -->
    <section class="contact-header container section-spacing-top">
        <div class="centered-content">
            <h1 class="page-title">Get in Touch</h1>
            <p class="section-desc">Have a question or a scoop? Reach out to our electoral transparency team.</p>
        </div>
    </section>

    <!-- Contact Form Section -->
    <section class="contact-form-section container">
        <div class="contact-form-wrapper">
            <?php if (!empty($response_message))
                echo $response_message; ?>

            <form action="" method="post" class="modern-contact-form">
                <?php wp_nonce_field('election_contact_action', 'election_contact_nonce'); ?>

                <div class="form-grid">
                    <div class="form-group floating-group">
                        <input type="text" id="name" name="contact_name" class="form-control" placeholder=" " required
                            value="<?php echo isset($_POST['contact_name']) ? esc_attr($_POST['contact_name']) : ''; ?>">
                        <label for="name">Your Name</label>
                    </div>

                    <div class="form-group floating-group">
                        <input type="tel" id="phone" name="contact_phone" class="form-control" placeholder=" " required
                            value="<?php echo isset($_POST['contact_phone']) ? esc_attr($_POST['contact_phone']) : ''; ?>">
                        <label for="phone">Phone number</label>
                    </div>
                </div>

                <div class="form-group floating-group">
                    <input type="text" id="address" name="contact_address" class="form-control" placeholder=" "
                        value="<?php echo isset($_POST['contact_address']) ? esc_attr($_POST['contact_address']) : ''; ?>">
                    <label for="address">Your full Address</label>
                </div>

                <div class="form-group floating-group">
                    <textarea id="message" name="contact_message" class="form-control" placeholder=" " rows="5"
                        required><?php echo isset($_POST['contact_message']) ? esc_textarea($_POST['contact_message']) : ''; ?></textarea>
                    <label for="message">Your Message</label>
                </div>

                <div class="form-submit">
                    <button type="submit" name="election_contact_submit" class="btn btn-primary btn-large w-100">Send
                        Message</button>
                </div>
            </form>
        </div>
    </section>


</main>

<?php get_footer(); ?>