
let faqData = [];

let currentPage = 1;

let itemsPerPage = 3;

let searchInput = document.getElementById("searchInput");

let categoryFilter = document.getElementById("categoryFilter");

let sortSelect = document.getElementById("sortSelect");

let faqContainer = document.getElementById("faqContainer");

let pagination = document.getElementById("pagination");


fetch("faqs.json")

    .then(response => response.json())

    .then(data => {

        faqData = data;

        loadCategories();

        displayFAQs();

    })

    .catch(error => {

        faqContainer.innerHTML = "<p>Error loading FAQ data.</p>";

        console.log(error);

    });


function loadCategories() {

    let categories = [];

    for (let i = 0; i < faqData.length; i++) {

        if (!categories.includes(faqData[i].category)) {

            categories.push(faqData[i].category);

        }

    }

    for (let i = 0; i < categories.length; i++) {

        let option = document.createElement("option");

        option.value = categories[i];

        option.textContent = categories[i];

        categoryFilter.appendChild(option);

    }

}


function displayFAQs() {

    let searchText = searchInput.value.toLowerCase();

    let selectedCategory = categoryFilter.value;


    let filteredData = faqData.filter(function(faq) {

        let matchesSearch =
            faq.question.toLowerCase().includes(searchText) ||
            faq.answer.toLowerCase().includes(searchText);

        let matchesCategory =
            selectedCategory === "all" ||
            faq.category === selectedCategory;

        return matchesSearch && matchesCategory;

    });


    if (sortSelect.value === "az") {

        filteredData.sort(function(a, b) {

            return a.question.localeCompare(b.question);

        });

    } else {

        filteredData.sort(function(a, b) {

            return b.question.localeCompare(a.question);

        });

    }


    let totalPages = Math.ceil(filteredData.length / itemsPerPage);


    if (currentPage > totalPages) {

        currentPage = 1;

    }


    let start = (currentPage - 1) * itemsPerPage;

    let end = start + itemsPerPage;

    let pageData = filteredData.slice(start, end);


    faqContainer.innerHTML = "";


    if (pageData.length === 0) {

        faqContainer.innerHTML = "<p>No FAQs found.</p>";

    }


    for (let i = 0; i < pageData.length; i++) {

        let faqItem = document.createElement("div");

        faqItem.className = "faqItem";


        let questionButton = document.createElement("button");

        questionButton.className = "faqQuestion";

        questionButton.textContent = pageData[i].question;


        let answerParagraph = document.createElement("p");

        answerParagraph.className = "faqAnswer";

        answerParagraph.textContent = pageData[i].answer;


        let categoryText = document.createElement("small");

        categoryText.textContent =
            "Category: " + pageData[i].category;


        faqItem.appendChild(questionButton);

        faqItem.appendChild(answerParagraph);

        faqItem.appendChild(categoryText);

        faqContainer.appendChild(faqItem);

    }


    addAccordion();

    createPagination(totalPages);

}


function addAccordion() {

    let questions =
        document.querySelectorAll(".faqQuestion");


    for (let i = 0; i < questions.length; i++) {

        questions[i].addEventListener("click", function() {

            let answer = this.nextElementSibling;


            if (answer.style.display === "block") {

                answer.style.display = "none";

            } else {

                answer.style.display = "block";

            }

        });

    }

}


function createPagination(totalPages) {

    pagination.innerHTML = "";


    for (let i = 1; i <= totalPages; i++) {

        let button = document.createElement("button");

        button.textContent = i;


        button.addEventListener("click", function() {

            currentPage = i;

            displayFAQs();

        });


        pagination.appendChild(button);

    }

}


searchInput.addEventListener("input", function() {

    currentPage = 1;

    displayFAQs();

});


categoryFilter.addEventListener("change", function() {

    currentPage = 1;

    displayFAQs();

});


sortSelect.addEventListener("change", function() {

    currentPage = 1;

    displayFAQs();

});
