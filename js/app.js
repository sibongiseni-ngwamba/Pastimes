document.addEventListener('DOMContentLoaded', function () {
    // Let the user toggle password visibility on login and registration forms.
    document.querySelectorAll('[data-toggle-password]').forEach(function (toggleButton) {
        toggleButton.addEventListener('click', function () {
            var input = toggleButton.parentElement.querySelector('input');

            if (!input) {
                return;
            }

            input.type = input.type === 'password' ? 'text' : 'password';
        });
    });

});
