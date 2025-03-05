document.addEventListener("DOMContentLoaded", function () {
    function selectOption(option) {

        const options = document.querySelectorAll(".option");
        options.forEach(opt => {
            opt.classList.remove("bg-gray-200", "text-gray-800", "border-gray-400");
            opt.classList.add("bg-white", "text-gray-500", "border-transparent");
        });

   
        option.classList.add("bg-gray-200", "text-gray-800", "border-gray-400");

        const nextButton = document.getElementById("next-button");
        if (nextButton) {
            nextButton.style.display = "inline-block"; 
            nextButton.style.backgroundColor = "#FFA500"; 
        }

     
        const selectedDinners = option.getAttribute("data-dinners");
        const selectedDinnerInput = document.getElementById("selected-dinners");
        if (selectedDinnerInput) {
            updateDiscounts(selectedDinners); 
            selectedDinnerInput.value = selectedDinners; 
        }
          
        const householdValue = option.getAttribute("data-household");
        const selectedHouseholdInput = document.getElementById("selected-household");

        if (selectedHouseholdInput) {
            selectedHouseholdInput.value = householdValue; 
        }
    }

    function updateDiscounts(selectedDinners) {
        const discountMessages = document.querySelectorAll('.discount-message');
        discountMessages.forEach(message => {
            message.style.display = 'none';
        });


        let discountText = '';
        if (selectedDinners === "6") {
            document.getElementById('discount-6').innerHTML = '';
            document.getElementById('discount-5').innerHTML = '';
            document.getElementById('discount-4').innerHTML = '';
            document.getElementById('discount-3').innerHTML = '';
        } else if (selectedDinners === "5") {
            document.getElementById('discount-6').innerHTML = 'SAVE 10% PER MEAL';
            document.getElementById('discount-4').innerHTML = '';
            document.getElementById('discount-3').innerHTML = '';
        } else if (selectedDinners === "4") {
            document.getElementById('discount-6').innerHTML = 'SAVE 22% PER MEAL';
            document.getElementById('discount-5').innerHTML = 'SAVE 13% PER MEAL';
            document.getElementById('discount-3').innerHTML = '';
        } else if (selectedDinners === "3") {
            document.getElementById('discount-6').innerHTML = 'SAVE 33% PER MEAL';
            document.getElementById('discount-5').innerHTML = 'SAVE 26% PER MEAL';
            document.getElementById('discount-4').innerHTML = 'SAVE 15% PER MEAL';
        }


        const discountField = document.getElementById('selected-dinners');
        if (selectedDinners === "6") {
            discountText = ''; 
        } else if (selectedDinners === "5") {
            discountText = 'SAVE 10% PER MEAL';
        } else if (selectedDinners === "4") {
            discountText = 'SAVE 22% PER MEAL';
        } else if (selectedDinners === "3") {
            discountText = 'SAVE 33% PER MEAL';
        }

        discountField.value = discountText;
    }

 
    document.querySelectorAll(".option").forEach(item => {
        item.addEventListener("click", function () {
            selectOption(this);
        });
    });
});
