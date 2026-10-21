
const menuButton = document.getElementById("menuButton");
const navbar = document.getElementById("navbar");
const themeButton = document.getElementById("themeButton");
const notification = document.getElementById("notification");
const modal = document.getElementById("modal");

menuButton.addEventListener("click", function () {
    navbar.classList.toggle("show");
    menuButton.setAttribute(
        "aria-expanded",
        navbar.classList.contains("show")
    );
});

document.getElementById("closeNotification").addEventListener("click", function () {
    notification.style.display = "none";
});

function openModal() {
    modal.hidden = false;
}

function closeModal() {
    modal.hidden = true;
}

document.getElementById("registerButton").addEventListener("click", function () {
    window.location.href = "Register.html";
});

document.getElementById("loginButton").addEventListener("click", openModal);

document.getElementById("modalRegisterButton").addEventListener("click", function () {
    window.location.href = "Register.html";
});

document.getElementById("modalLoginButton").addEventListener("click", function () {
    window.location.href = "Login.html";
});

document.getElementById("closeModal").addEventListener("click", closeModal);

modal.addEventListener("click", function (event) {
    if (event.target === modal) {
        closeModal();
    }
});

function applyTheme() {
    const savedTheme = localStorage.getItem("studenthub-theme");

    if (savedTheme === "dark") {
        document.body.classList.add("dark");
        themeButton.textContent = "Light Mode";
    } else {
        document.body.classList.remove("dark");
        themeButton.textContent = "Dark Mode";
    }
}

themeButton.addEventListener("click", function () {
    document.body.classList.toggle("dark");

    if (document.body.classList.contains("dark")) {
        localStorage.setItem("studenthub-theme", "dark");
        themeButton.textContent = "Light Mode";
    } else {
        localStorage.setItem("studenthub-theme", "light");
        themeButton.textContent = "Dark Mode";
    }
});

const activities = [
    {
        title: "Tech Fest 2026",
        description: "Join the university Tech Fest and participate in technical competitions."
    },
    {
        title: "Sports Week",
        description: "Participate in sports activities and represent your department."
    },
    {
        title: "Coding Competition",
        description: "Test your programming skills and solve exciting coding challenges."
    }
];

let currentSlide = 0;

function showSlide() {
    const slideContent = document.getElementById("slideContent");

    slideContent.innerHTML = `
        <h3>${activities[currentSlide].title}</h3>
        <p>${activities[currentSlide].description}</p>
    `;
}

document.getElementById("previousButton").addEventListener("click", function () {
    currentSlide = (currentSlide - 1 + activities.length) % activities.length;
    showSlide();
});

document.getElementById("nextButton").addEventListener("click", function () {
    currentSlide = (currentSlide + 1) % activities.length;
    showSlide();
});

applyTheme();
showSlide();
