// script.js for Queen Website

// Contact Form Validation using Bootstrap's built-in validation
document.addEventListener('DOMContentLoaded', function () {
    // Select the form
    const form = document.getElementById('contact-form');
    // Select the message display area
    const formMessage = document.getElementById('form-message');

    if (form) {
        form.addEventListener('submit', function (event) {
            // Prevent default submission
            event.preventDefault();
            event.stopPropagation();

            // Remove previous message states
            formMessage.classList.remove('alert-success', 'alert-danger', 'd-none');
            formMessage.textContent = '';

            // Check validity using Bootstrap's method
            if (!form.checkValidity()) {
                // If invalid, add Bootstrap's was-validated class to show feedback
                form.classList.add('was-validated');

                // Display a generic error message
                formMessage.classList.add('alert', 'alert-danger');
                formMessage.textContent = 'Por favor, corrija os erros no formulário antes de enviar.';
                formMessage.classList.remove('d-none');
            } else {
                // If valid, simulate submission success
                form.classList.remove('was-validated'); // Remove validation classes if previously shown
                form.reset(); // Clear the form fields

                // Display success message
                formMessage.classList.add('alert', 'alert-success');
                formMessage.textContent = 'Mensagem enviada com sucesso! Obrigado pelo seu contato.';
                formMessage.classList.remove('d-none');

               
            }
        }, false);
    }
});



