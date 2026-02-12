/**
 * Car Rental System - Client-Side Validation
 */

/**
 * Validate registration form (customer or agency).
 */
function validateRegistrationForm(form, isAgency) {
    clearErrors(form);
    var isValid = true;

    // Company Name (agency only)
    if (isAgency) {
        var companyName = form.querySelector('[name="company_name"]');
        if (companyName && companyName.value.trim() === '') {
            showError(companyName, 'Company name is required.');
            isValid = false;
        }
    }

    // Full Name
    var fullName = form.querySelector('[name="full_name"]');
    if (fullName && fullName.value.trim() === '') {
        showError(fullName, 'Full name is required.');
        isValid = false;
    }

    // Email
    var email = form.querySelector('[name="email"]');
    if (email) {
        if (email.value.trim() === '') {
            showError(email, 'Email is required.');
            isValid = false;
        } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value)) {
            showError(email, 'Please enter a valid email address.');
            isValid = false;
        }
    }

    // Phone
    var phone = form.querySelector('[name="phone_number"]');
    if (phone && phone.value.trim() === '') {
        showError(phone, 'Phone number is required.');
        isValid = false;
    }

    // Password
    var password = form.querySelector('[name="password"]');
    if (password) {
        if (password.value === '') {
            showError(password, 'Password is required.');
            isValid = false;
        } else if (password.value.length < 8) {
            showError(password, 'Password must be at least 8 characters.');
            isValid = false;
        }
    }

    // Confirm Password
    var confirmPassword = form.querySelector('[name="confirm_password"]');
    if (confirmPassword) {
        if (confirmPassword.value === '') {
            showError(confirmPassword, 'Please confirm your password.');
            isValid = false;
        } else if (password && confirmPassword.value !== password.value) {
            showError(confirmPassword, 'Passwords do not match.');
            isValid = false;
        }
    }

    return isValid;
}

/**
 * Validate login form.
 */
function validateLoginForm(form) {
    clearErrors(form);
    var isValid = true;

    var email = form.querySelector('[name="email"]');
    if (email && email.value.trim() === '') {
        showError(email, 'Email is required.');
        isValid = false;
    }

    var password = form.querySelector('[name="password"]');
    if (password && password.value === '') {
        showError(password, 'Password is required.');
        isValid = false;
    }

    return isValid;
}

/**
 * Validate add/edit car form.
 */
function validateCarForm(form) {
    clearErrors(form);
    var isValid = true;

    var vehicleModel = form.querySelector('[name="vehicle_model"]');
    if (vehicleModel && vehicleModel.value.trim() === '') {
        showError(vehicleModel, 'Vehicle model is required.');
        isValid = false;
    }

    var vehicleNumber = form.querySelector('[name="vehicle_number"]');
    if (vehicleNumber && vehicleNumber.value.trim() === '') {
        showError(vehicleNumber, 'Vehicle number is required.');
        isValid = false;
    }

    var seatingCapacity = form.querySelector('[name="seating_capacity"]');
    if (seatingCapacity) {
        var seatVal = parseInt(seatingCapacity.value, 10);
        if (isNaN(seatVal) || seatVal < 1) {
            showError(seatingCapacity, 'Seating capacity must be at least 1.');
            isValid = false;
        }
    }

    var rentPerDay = form.querySelector('[name="rent_per_day"]');
    if (rentPerDay) {
        var rentVal = parseFloat(rentPerDay.value);
        if (isNaN(rentVal) || rentVal <= 0) {
            showError(rentPerDay, 'Rent per day must be greater than 0.');
            isValid = false;
        }
    }

    return isValid;
}

/**
 * Validate rent car form.
 */
function validateRentForm(form) {
    clearErrors(form);
    var isValid = true;

    var startDate = form.querySelector('[name="start_date"]');
    if (startDate) {
        if (startDate.value === '') {
            showError(startDate, 'Start date is required.');
            isValid = false;
        } else {
            var today = new Date();
            today.setHours(0, 0, 0, 0);
            var selected = new Date(startDate.value);
            if (selected < today) {
                showError(startDate, 'Start date cannot be in the past.');
                isValid = false;
            }
        }
    }

    var numberOfDays = form.querySelector('[name="number_of_days"]');
    if (numberOfDays && (numberOfDays.value === '' || numberOfDays.value === '0')) {
        showError(numberOfDays, 'Please select the number of days.');
        isValid = false;
    }

    return isValid;
}

/* --- Helper functions --- */

function showError(input, message) {
    input.classList.add('is-invalid');
    var div = document.createElement('div');
    div.className = 'invalid-feedback';
    div.textContent = message;
    input.parentNode.appendChild(div);
}

function clearErrors(form) {
    var invalids = form.querySelectorAll('.is-invalid');
    for (var i = 0; i < invalids.length; i++) {
        invalids[i].classList.remove('is-invalid');
    }
    var feedbacks = form.querySelectorAll('.invalid-feedback');
    for (var j = 0; j < feedbacks.length; j++) {
        feedbacks[j].remove();
    }
}

/* --- Set min date on date inputs to today --- */
document.addEventListener('DOMContentLoaded', function () {
    var today = new Date().toISOString().split('T')[0];
    var dateInputs = document.querySelectorAll('input[type="date"]');
    for (var i = 0; i < dateInputs.length; i++) {
        if (!dateInputs[i].min) {
            dateInputs[i].min = today;
        }
    }
});
