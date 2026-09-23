let menuButton = document.getElementById("menuButton");
let navbar = document.getElementById("navbar");

menuButton.addEventListener("click", function(){

    navbar.classList.toggle("show");

});
let closeNotification=document.getElementById("closeNotification");
let notification=document.getElementById("notification");

closeNotification.addEventListener("click",function(){
    notification.style.display = "none";
}
);
let registerButton = document.getElementById("registerButton");
let closeModal = document.getElementById("closeModal");
let modal = document.getElementById("modal");

registerButton.addEventListener("click", function(){
     modal.style.display = "block";

}
);


closeModal.addEventListener("click", function(){
              modal.style.display = "none";

}
);
let slides = [
    {
        title: "Tech Fest 2026",
        description: "Join the university Tech Fest and participate in various technical competitions."
    },

    {
        title: "Sports Week 2026",
        description: "Participate in different sports activities and represent your department."
    },

    {
        title: "Coding Competition",
        description: "Test your programming skills by participating in the coding competition."
    }
];

let currentSlide = 0;

let slideContent = document.getElementById("slideContent");

let nextButton = document.getElementById("nextButton");

let previousButton = document.getElementById("previousButton");


function showSlide(){

    slideContent.innerHTML =
        "<h3>" + slides[currentSlide].title + "</h3>" +
        "<p>" + slides[currentSlide].description + "</p>";

}


nextButton.addEventListener("click",function(){

    currentSlide++;

    if(currentSlide == slides.length){
        currentSlide = 0;
    }

    showSlide();

});


previousButton.addEventListener("click",function(){

    currentSlide--;

    if(currentSlide < 0){
        currentSlide = slides.length - 1;
    }

    showSlide();

});
let themeButton = document.getElementById("themeButton");

themeButton.addEventListener("click", function(){

    document.body.classList.toggle("dark");

    if(document.body.classList.contains("dark")){
        themeButton.innerHTML = "Light Mode";
    }
    else{
        themeButton.innerHTML = "Dark Mode";
    }

});
window.addEventListener("load", function(){

    alert("Welcome to StudentHub Portal!");

});
