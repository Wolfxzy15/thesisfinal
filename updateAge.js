function calculateAge(dob) {
    const today = new Date();
    const birthDate = new Date(dob);
    let age = today.getFullYear() - birthDate.getFullYear();
    const m = today.getMonth() - birthDate.getMonth();

    if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
        age--;
    }
    return age;
}

// Function to update the age field when date of birth changes
function updateAge() {
    const dobInput = document.getElementById('dateOfBirth');
    const ageInput = document.getElementById('age');
    const dob = dobInput.value;

    if (dob) {
        const age = calculateAge(dob);
        ageInput.value = age;
    } else {
        ageInput.value = ''; // Clear age if no DOB is selected
    }
}

