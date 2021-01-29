document.addEventListener("DOMContentLoaded", function() {
    // Public / Private Form on profile page

    var profileTypeForm = document.querySelector("#profile-type-form");
    var profileTypeCheckbox = document.querySelector('#profile-type-checkbox');

    profileTypeCheckbox.addEventListener('change', () => {
         profileTypeForm.submit();
    });

    // Notification API

});


