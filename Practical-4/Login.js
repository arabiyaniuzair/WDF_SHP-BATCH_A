let loginForm = document.getElementById("loginForm");

loginForm.addEventListener("submit", function (event) {

    event.preventDefault();

    let studentId = document.getElementById("studentId").value;
    let password = document.getElementById("password").value;

    let idPattern = /^[0-9]{2}[A-Za-z]{2,4}[0-9]{3}$/;

    if (studentId == "") {
        alert("Please enter Student ID");
    }
    else if (!idPattern.test(studentId)) {
        alert("Invalid Student ID");
    }
    else if (password == "") {
        alert("Please enter Password");
    }
    else {
        sessionStorage.setItem("studentId", studentId);

        alert("Successfully Login!");

        window.location.href = "Dashboard.html";
    }

});
let showPassword = document.getElementById("showPassword");
let password = document.getElementById("password");

showPassword.addEventListener("click", function () {

    if (password.type == "password") {
        password.type = "text";
    }
    else {
        password.type = "password";
    }

});
let LoginLogout = document.getElementById("LoginLogout");

if (sessionStorage.getItem("studentId")) {
    LoginLogout.innerText = "Logout";
    LoginLogout.href = "Login.html";

LoginLogout.addEventListener("click", function (event) {
    event.preventDefault();
    sessionStorage.removeItem("studentId");
    alert("Succesfully Logout!");
    window.location.href = "Login.html";
});
}