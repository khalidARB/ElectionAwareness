document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('auth-modal');
    if (!modal) return;

    const authBtns = document.querySelectorAll('[data-auth-action]');
    const closeBtn = modal.querySelector('.auth-modal-close');
    const tabs = modal.querySelectorAll('.auth-tab');
    const forms = modal.querySelectorAll('.auth-form');

    // Open Modal
    authBtns.forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            const action = this.getAttribute('data-auth-action');
            openModal(action);
        });
    });

    // Close Modal
    closeBtn.addEventListener('click', closeModal);
    modal.addEventListener('click', function (e) {
        if (e.target === modal) closeModal();
    });

    // Tab Switching
    tabs.forEach(tab => {
        tab.addEventListener('click', function () {
            const target = this.getAttribute('data-target');
            switchTab(target);
        });
    });

    function openModal(action) {
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
        switchTab(action);
    }

    function closeModal() {
        modal.classList.remove('active');
        document.body.style.overflow = '';
    }

    function switchTab(action) {
        tabs.forEach(t => t.classList.remove('active'));
        forms.forEach(f => f.classList.remove('active'));
        
        modal.querySelector(`.auth-tab[data-target="${action}"]`).classList.add('active');
        modal.querySelector(`#auth-${action}-form`).classList.add('active');
        modal.querySelector('.auth-modal-title').textContent = action === 'login' ? 'Sign In' : 'Sign Up';
    }

    // AJAX Login
    const loginForm = document.getElementById('auth-login-form');
    loginForm.addEventListener('submit', function (e) {
        e.preventDefault();
        const messages = document.getElementById('login-messages');
        const submitBtn = this.querySelector('button[type="submit"]');
        
        messages.textContent = 'Logging in...';
        messages.className = 'auth-form-messages';
        submitBtn.disabled = true;

        const formData = new FormData(this);
        formData.append('action', 'custom_login');

        fetch(electionAuth.ajax_url, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                messages.textContent = 'Login successful! Redirecting...';
                messages.classList.add('success');
                window.location.reload();
            } else {
                messages.textContent = data.data.message || 'Login failed.';
                messages.classList.add('error');
                submitBtn.disabled = false;
            }
        })
        .catch(err => {
            messages.textContent = 'An error occurred. Please try again.';
            messages.classList.add('error');
            submitBtn.disabled = false;
        });
    });

    // AJAX Register
    const registerForm = document.getElementById('auth-register-form');
    registerForm.addEventListener('submit', function (e) {
        e.preventDefault();
        const messages = document.getElementById('register-messages');
        const submitBtn = this.querySelector('button[type="submit"]');
        
        messages.textContent = 'Creating account...';
        messages.className = 'auth-form-messages';
        submitBtn.disabled = true;

        const formData = new FormData(this);
        const phone = formData.get('reg_phone');
        
        // BD Phone Number Validation
        const bdPhoneRegex = /^(?:\+88|88)?(01[3-9]\d{8})$/;
        if (!bdPhoneRegex.test(phone)) {
            messages.textContent = 'Please enter a valid Bangladesh phone number.';
            messages.classList.add('error');
            submitBtn.disabled = false;
            return;
        }

        formData.append('action', 'custom_register');

        fetch(electionAuth.ajax_url, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                messages.textContent = 'Registration successful! Redirecting...';
                messages.classList.add('success');
                window.location.reload();
            } else {
                messages.textContent = data.data.message || 'Registration failed.';
                messages.classList.add('error');
                submitBtn.disabled = false;
            }
        })
        .catch(err => {
            messages.textContent = 'An error occurred. Please try again.';
            messages.classList.add('error');
            submitBtn.disabled = false;
        });
    });
});
