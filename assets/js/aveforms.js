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

        // Remove any previous error messages
        const errorMessages = form.querySelectorAll('.error-message');
        errorMessages.forEach(function(errorMessage) {
            errorMessage.remove();
        });

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
                const responseData = data.data || 'An error occurred.';
                // if the response is not successful and data.data is a string, display it
                if (typeof responseData === 'string') {
                    statusDiv.innerText = responseData;
                }
                // else if responseData is an object, display the error message
                else if (typeof responseData === 'object') {
                    for (const [key, errors] of Object.entries(responseData)) {
                        const input = form.querySelector(`[name="${key}"]`);
                        if (input) {
                            for (const [errorType, errorMessage] of Object.entries(errors)) {
                                const errorDiv = document.createElement('div');
                                errorDiv.className = 'error-message';
                                errorDiv.innerText = errorMessage;
                                input.parentNode.insertBefore(errorDiv, input.nextSibling);
                            };
                            
                        }
                    }
                    statusDiv.innerText = 'Please, fix the errors above.';
                } else {
                    // Fallback message if data.data is not a string or array
                    statusDiv.innerText = 'An error occurred.';
                }
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