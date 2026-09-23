let questions = document.querySelectorAll(".faqQuestion");

for(let i = 0; i < questions.length; i++){

    questions[i].addEventListener("click", function(){

        let answer = this.nextElementSibling;

        if(answer.style.display == "block"){
            answer.style.display = "none";
        }
        else{
            answer.style.display = "block";
        }

    });

}