document.addEventListener("DOMContentLoaded", function () {

    // Simulate logged-in user
    const isOwnProfile = true; // backend should replace this

    // Elements
    const editBtn = document.getElementById('editBtn');          // Public profile Edit button
    const profilePic = document.getElementById('profilePic');    // Profile image
    const changePicBtn = document.getElementById('changePicBtn'); // Private profile change pic button
    const inlineEditButtons = document.querySelectorAll('.edit-btn-inline'); // Private profile inline edit buttons

    // ---------- PUBLIC PROFILE LOGIC ----------
    if (editBtn) {
        if (isOwnProfile) {
            editBtn.style.display = 'inline-block';
            if (profilePic) {
                profilePic.classList.add('clickable');
                profilePic.addEventListener('click', () => {
                    window.location.href = 'private_profile.php';
                });
            }
        } else {
            editBtn.style.display = 'none';
        }
    }

    // ---------- PRIVATE PROFILE LOGIC ----------
    if (changePicBtn) {
        // Make profile pic clickable
        if (profilePic) {
            profilePic.classList.add('clickable');
            profilePic.addEventListener('click', () => {
                alert('Open upload/change picture dialog (implement backend)');
            });
        }

        // Change picture button
        changePicBtn.addEventListener('click', (e) => {
            e.preventDefault();
            alert('Open upload/change picture dialog (implement backend)');
        });

        // Inline editable fields
        inlineEditButtons.forEach(button => {
            button.addEventListener('click', () => {
                const fieldContainer = button.closest('.editable-field');
                const valueText = fieldContainer.querySelector('.value-text');
                const input = fieldContainer.querySelector('.value-input');
                const successMsg = fieldContainer.querySelector('.update-success');

                if (button.textContent === 'Edit') {
                    // Enter edit mode
                    input.value = valueText.textContent;
                    valueText.classList.add('d-none');
                    input.classList.remove('d-none');
                    button.textContent = 'Save';
                } else {
                    // Save changes
                    valueText.textContent = input.value;
                    input.classList.add('d-none');
                    valueText.classList.remove('d-none');
                    button.textContent = 'Edit';

                    // Show success message
                    successMsg.classList.remove('d-none');
                    setTimeout(() => {
                        successMsg.classList.add('d-none');
                    }, 1500);
                }
            });
        });
    }
     // ---------- Multi-select dropdown for Skills & Achievements ----------
    const multiSelects = document.querySelectorAll('.multiple-select');

    multiSelects.forEach(select => {
        const saveBtn = select.parentElement.querySelector('.save-btn');
        const successMsg = select.parentElement.querySelector('.update-success');

        // Save button click
        saveBtn.addEventListener('click', () => {
            const selectedOptions = Array.from(select.selectedOptions).map(opt => opt.value);
            console.log("Saved values:", selectedOptions);

            // Optional: if user typed a new value, append it to select options
            selectedOptions.forEach(value => {
                if (![...select.options].some(opt => opt.value === value)) {
                    const newOption = new Option(value, value);
                    select.add(newOption);
                }
            });

            successMsg.classList.remove('d-none');
            setTimeout(() => successMsg.classList.add('d-none'), 1500);
        });
    });

    // ---------- About Me save ----------
    const bioTextarea = document.getElementById('bioText');
    const bioSaveBtn = bioTextarea.parentElement.querySelector('.save-btn');
    const bioSuccess = bioTextarea.parentElement.querySelector('.update-success');

    bioSaveBtn.addEventListener('click', () => {
        console.log("Bio updated:", bioTextarea.value);
        bioSuccess.classList.remove('d-none');
        setTimeout(() => bioSuccess.classList.add('d-none'), 1500);
    });

    // ---------- Email & Phone toggle visibility ----------
    const toggleBtns = document.querySelectorAll('.toggle-visibility-btn');
    toggleBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            const span = btn.previousElementSibling;
            span.style.color = span.style.color === 'transparent' ? '' : 'transparent';
            btn.textContent = span.style.color === 'transparent' ? 'Show' : 'Hide';
        });
    });


});
