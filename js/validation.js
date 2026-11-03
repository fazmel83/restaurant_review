document.addEventListener("DOMContentLoaded", function() {
    var form = document.getElementById("registerForm");

    if (form) {
        form.addEventListener("submit", function(e) {
            var pw = document.getElementById("password").value;
            var pw2 = document.getElementById("password2").value;

            if (pw !== pw2) {
                alert("Passwords do not match!");
                e.preventDefault();
            }
        });
    }
});
