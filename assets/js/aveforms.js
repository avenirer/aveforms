document.addEventListener('DOMContentLoaded', function() {

    const form = document.getElementById(aveforms_ajax.form_id);
    if (!form) return;
    form.addEventListener('submit', function(event) {
        // Prevent the default form submission
        event.preventDefault();

        // Check if the form is already being submitted
        if (form.classList.contains('submitting')) {
            return;
        }
        // Add a class to indicate the form is being submitted
        form.classList.add('submitting');

        // Clear any previous status messages
        const statusDiv = document.getElementById('status');
        statusDiv.innerText = 'Sending...';

        // Deactivate the submit button to prevent multiple submissions
        const submitButton = form.querySelector('button[type="submit"]');
        if (submitButton) {
            submitButton.disabled = true;
        }
        var formData = new FormData(this);
        // we will retrieve the url from a variable that we will create in our shortcode.php
        // we will later add other variables, so that this script will be as agnostic as it can related to the forms
        fetch(aveforms_ajax.ajax_url, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                statusDiv.innerText = data.data;
                form.reset();
            } else {
                statusDiv.innerText = data.data || 'Error sending message.';
            }
        })
        .catch(error => {
            statusDiv.innerText = 'Error sending message.';
            console.error('Error:', error);
        })
        .finally(() => {
            // Remove the submitting class after the request is complete
            form.classList.remove('submitting');
            // Re-enable the submit button
            if (submitButton) {
                submitButton.disabled = false;
            }
        });
    });
});