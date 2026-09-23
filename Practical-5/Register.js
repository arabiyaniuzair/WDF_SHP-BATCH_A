
document.getElementById("registerForm").addEventListener("submit", function(event) {
    event.preventDefault();
    let name = document.getElementById("name").value.trim();
    let email = document.getElementById("email").value.trim();
    let mobile = document.getElementById("mobile").value.trim();
    let password = document.getElementById("password").value;
    let confirmPassword = document.getElementById("confirmPassword").value;
    let course = document.getElementById("course").value;
    let year = document.getElementById("year").value;
    let gender = document.querySelector('input[name="gender"]:checked');
    let terms = document.getElementById("terms").checked;

    let namePattern = /^[A-Za-z ]{3,}$/;
    let emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    let mobilePattern = /^[0-9]{10}$/;
    let passwordPattern = /^(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#$%^&*]).{8,}$/;

    document.querySelectorAll(".error").forEach(function(error) {
        error.textContent = "";
    });
    document.getElementById("successMessage").textContent = "";

    let valid = true;

    if (!namePattern.test(name)) {
        document.getElementById("nameError").textContent =
            "Please enter a valid name (minimum 3 letters).";
        valid = false;
    }

    
    if (!emailPattern.test(email)) {
        document.getElementById("emailError").textContent =
            "Please enter a valid email address.";
        valid = false;
    }

    
    if (!mobilePattern.test(mobile)) {
        document.getElementById("mobileError").textContent =
            "Mobile number must contain exactly 10 digits.";
        valid = false;
    }

   
    if (!passwordPattern.test(password)) {
        document.getElementById("passwordError").textContent =
            "Password must be 8 characters with uppercase, number and special character.";
        valid = false;
    }

   
    if (password !== confirmPassword) {
        document.getElementById("confirmPasswordError").textContent =
            "Passwords do not match.";
        valid = false;
    }

    
    if (course === "") {
        document.getElementById("courseError").textContent =
            "Please select your course.";
        valid = false;
    }

   
    if (year === "") {
        document.getElementById("yearError").textContent =
            "Please select your year.";
        valid = false;
    }

   
    if (!gender) {
        document.getElementById("genderError").textContent =
            "Please select your gender.";
        valid = false;
    }

    if (!terms) {
        document.getElementById("termsError").textContent =
            "You must accept the Terms and Conditions.";
        valid = false;
    }

    if (valid) {
        document.getElementById("successMessage").textContent =
            "Registration successful!";
    }
});



document.getElementById("registerForm").addEventListener("reset", function() {

    setTimeout(function() {
        document.querySelectorAll(".error").forEach(function(error) {
            error.textContent = "";
        });

        document.getElementById("successMessage").textContent = "";
    }, 10);

});

