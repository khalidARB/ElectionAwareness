<?php
/**
 * Newsletter Section Template Part
 * 
 * Displays the full-width newsletter subscription section.
 */
?>

<section class="newsletter-full-section reveal-on-scroll">
    <div class="container">
        <div class="newsletter-grid">
            <div class="newsletter-info">
                <?php
                $heading = get_option('election_theme_newsletter_heading', 'Join our Newsletter');
                $subheading = get_option('election_theme_newsletter_subheading', 'Get the latest election updates and deep dives directly in your inbox.');
                ?>
                <h2><?php echo esc_html($heading); ?></h2>
                <p><?php echo esc_html($subheading); ?></p>
            </div>
            <div class="newsletter-action">
                <?php
                $btn_text = get_option('election_theme_newsletter_btn_text', 'Subscribe Now');
                $placeholder = get_option('election_theme_newsletter_placeholder', 'Your email address');
                ?>
                <form class="newsletter-form-large" id="newsletter-subscription-form">
                    <div class="form-group-glow">
                        <input type="email" name="email" placeholder="<?php echo esc_attr($placeholder); ?>" required>
                        <button type="submit" class="btn btn-primary">
                            <span><?php echo esc_html($btn_text); ?></span>
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                                <polyline points="12 5 19 12 12 19"></polyline>
                            </svg>
                        </button>
                    </div>
                    <div class="form-response"></div>
                </form>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('newsletter-subscription-form');
    if (!form) return;

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        const email = this.querySelector('input[name="email"]').value;
        const responseDiv = this.querySelector('.form-response');
        const submitBtn = this.querySelector('button');

        submitBtn.disabled = true;
        submitBtn.style.opacity = '0.7';
        responseDiv.innerHTML = '<span style="color: #FACC15;">Processing...</span>';

        fetch('<?php echo esc_url(rest_url('election-awareness/v1/subscribe')); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ email: email })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                responseDiv.innerHTML = '<span style="color: #10B981;">' + data.message + '</span>';
                form.reset();
            } else {
                responseDiv.innerHTML = '<span style="color: #EF4444;">' + (data.message || 'An error occurred.') + '</span>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            responseDiv.innerHTML = '<span style="color: #EF4444;">Could not connect to the server.</span>';
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitBtn.style.opacity = '1';
        });
    });
});
</script>
