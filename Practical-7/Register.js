document.getElementById("registerForm").addEventListener("submit", function(event) {


let name = document.getElementById("name").value.trim();
let email = document.getElementById("email").value.trim();
let mobile = document.getElementById("mobile").value.trim();
let password = document.getElementById("password").value;
let confirmPassword = document.getElementById("confirmPassword").value;
let course = document.getElementById("course").value;
let year = document.getElementById("year").value;
let gender = document.querySelector('input[name="gender"]:checked');
let terms = document.getElementById("terms").checked;

let valid = true;

document.querySelectorAll(".error").forEach(function(error) {
    error.textContent = "";
});

if (name.length < 3) {
    document.getElementById("nameError").textContent = "Enter a valid name.";
    valid = false;
}

if (!email.includes("@")) {
    document.getElementById("emailError").textContent = "Enter a valid email.";
    valid = false;
}

if (!/^[0-9]{10}$/.test(mobile)) {
    document.getElementById("mobileError").textContent = "Enter 10 digit mobile number.";
    valid = false;
}

if (password.length < 8) {
    document.getElementById("passwordError").textContent = "Password must contain at least 8 characters.";
    valid = false;
}

if (password !== confirmPassword) {
    document.getElementById("confirmPasswordError").textContent = "Passwords do not match.";
    valid = false;
}

if (course === "") {
    document.getElementById("courseError").textContent = "Select your course.";
    valid = false;
}

if (year === "") {
    document.getElementById("yearError").textContent = "Select your year.";
    valid = false;
}

if (!gender) {
    document.getElementById("genderError").textContent = "Select your gender.";
    valid = false;
}

if (!terms) {
    document.getElementById("termsError").textContent = "Accept the Terms and Conditions.";
    valid = false;
}

if (!valid) {
    event.preventDefault();
}

});

document.getElementById("registerForm").addEventListener("reset", function() {

setTimeout(function() {
    document.querySelectorAll(".error").forEach(function(error) {
        error.textContent = "";
    });
}, 10);


});

