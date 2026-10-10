
let loginForm = document.getElementById("loginForm");
let identity = document.getElementById("identity");
let password = document.getElementById("password");
let showPassword = document.getElementById("showPassword");
let LoginLogout = document.getElementById("LoginLogout");

loginForm.addEventListener("submit", function (event) {
    let value = identity.value.trim();

    if (value === "") {
        event.preventDefault();
        alert("Please enter Username or Email");
        return;
    }

    if (value.includes("@") && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) {
        event.preventDefault();
        alert("Please enter a valid email address");
        return;
    }

    if (password.value === "") {
        event.preventDefault();
        alert("Please enter Password");
    }
});

showPassword.addEventListener("click", function () {
    if (password.type === "password") {
        password.type = "text";
    } else {
        password.type = "password";
    }
});

if (LoginLogout) {
    LoginLogout.addEventListener("click", function (event) {
        if (LoginLogout.innerText.trim() === "Logout") {
            event.preventDefault();
            window.location.href = "logout.php";
        }
    });
}