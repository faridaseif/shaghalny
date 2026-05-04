// ======== HELPER FUNCTIONS ========

// Add error style to empty or invalid field
function markError(field) {
  field.classList.add('error');
}

// Remove all error styles within a container
function clearErrors(container) {
  container.querySelectorAll('.error').forEach(el => el.classList.remove('error'));
}

// Populate select with array of options
function populateSelect(select, options) {
  options.forEach(opt => {
    const option = document.createElement('option');
    option.value = opt;
    option.text = opt;
    select.add(option);
  });
}

// Populate year range
function populateYear(select, start, end) {
  for (let y = start; y <= end; y++) {
    const option = document.createElement('option');
    option.value = y;
    option.text = y;
    select.add(option);
  }
}

// ======== PERSONAL INFO DROPDOWNS ========

// Main Initialization (Wrapped to ensure DOM exists)
document.addEventListener("DOMContentLoaded", () => {
  // ======== PERSONAL INFO DROPDOWNS ========
  
  // Birthdate
  const daySelect = document.getElementById('day');
  const monthSelect = document.getElementById('month');
  const yearSelect = document.getElementById('year');
  if (daySelect && monthSelect && yearSelect) {
    populateSelect(daySelect, Array.from({ length: 31 }, (_, i) => i + 1));
    populateSelect(monthSelect, ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"]);
    populateYear(yearSelect, 1950, 2025);
  }
  
  // Nationalities
  const nationalitySelect = document.getElementById('nationality');
  if (nationalitySelect) {
    populateSelect(nationalitySelect, ["Egypt", "USA", "UK", "Canada"]);
  }
  
  // Location data
  const locationData = {
    "Egypt": { "Cairo": ["New Heliopolis", "Maadi", "Zamalek"], "Alexandria": ["Montaza", "Smouha", "Roushdy"], "Giza": ["Dokki", "Mohandessin", "6th of October"] },
    "USA": { "New York": ["Manhattan", "Brooklyn", "Queens"], "Los Angeles": ["Hollywood", "Beverly Hills", "Santa Monica"] },
    "UK": { "London": ["Chelsea", "Greenwich", "Camden"], "Manchester": ["Didsbury", "Salford", "Ancoats"] },
    "Canada": { "Toronto": ["Scarborough", "Etobicoke", "North York"], "Vancouver": ["Kitsilano", "Gastown", "Yaletown"] }
  };
  
  const countrySelect = document.getElementById('country');
  const citySelect = document.getElementById('city');
  const areaSelect = document.getElementById('area');
  if (countrySelect && citySelect && areaSelect) {
    populateSelect(countrySelect, Object.keys(locationData));
  
    countrySelect.addEventListener('change', function () {
      citySelect.innerHTML = '<option value="">Select City</option>';
      areaSelect.innerHTML = '<option value="">Select Area</option>';
      if (this.value) {
        populateSelect(citySelect, Object.keys(locationData[this.value]));
      }
    });
  
    citySelect.addEventListener('change', function () {
      areaSelect.innerHTML = '<option value="">Select Area</option>';
      const country = countrySelect.value;
      if (country && this.value) {
        populateSelect(areaSelect, locationData[country][this.value]);
      }
    });
  }
  
  // ======== EDUCATION DROPDOWNS ========
  
  const eduLevel = document.getElementById('edu-level');
  const degreeDetails = document.getElementById('degree-details');
  const highschoolDetails = document.getElementById('highschool-details');
  
  // Degree years
  const degreeYear = document.getElementById('degree-year');
  const gradYear = document.getElementById('grad-year');
  if (degreeYear) populateYear(degreeYear, 1950, 2025);
  if (gradYear) populateYear(gradYear, 1950, 2025);
  
  // Hide both initially
  if (degreeDetails) degreeDetails.style.display = 'none';
  if (highschoolDetails) highschoolDetails.style.display = 'none';
  
  // Show/hide education fields dynamically
  if (eduLevel) {
    eduLevel.addEventListener('change', function () {
      const level = this.value;
  
      // Hide both
      degreeDetails.style.display = 'none';
      highschoolDetails.style.display = 'none';
  
      if (level === 'Highschool') {
        highschoolDetails.style.display = 'block';
      } else if (level) {
        degreeDetails.style.display = 'block';
      }
    });
  
    // Trigger display on page load if a value exists
    if (eduLevel.value) {
      const event = new Event('change');
      eduLevel.dispatchEvent(event);
    }
  }
  
  // ======== FORM VALIDATION ========
  
  function validateForm(formId, fields, radioGroups = []) {
    const form = document.getElementById(formId);
    clearErrors(form);
    let isValid = true;
  
    // Required fields
    fields.forEach(id => {
      const field = document.getElementById(id);
      if (field && !field.value) {
        markError(field);
        isValid = false;
      }
    });
  
    // Radio groups
    radioGroups.forEach(groupName => {
      const checked = document.querySelector(`input[name="${groupName}"]:checked`);
      if (!checked) {
        const container = document.querySelector(`input[name="${groupName}"]`).closest('.radio-group');
        markError(container);
        isValid = false;
      }
    });
  
    // Mobile validation
    const mobileField = document.getElementById('mobile');
    if (mobileField && mobileField.value && !/^\d{10,15}$/.test(mobileField.value.trim())) {
      markError(mobileField);
      isValid = false;
    }
  
    return isValid;
  }
  
  // ======== PERSONAL FORM SUBMIT ========
  const personalForm = document.getElementById('personalForm');
  if (personalForm) {
    personalForm.addEventListener('submit', function (e) {
      const requiredFields = ['first-name', 'last-name', 'day', 'month', 'year', 'nationality', 'country', 'city', 'area', 'mobile'];
      const radioGroups = ['gender'];
      if (!validateForm('personalForm', requiredFields, radioGroups)) {
        e.preventDefault();
      }
    });
  }
  
  // ======== EDUCATION FORM SUBMIT ========
  const educationForm = document.getElementById('educationForm');
  if (educationForm) {
    educationForm.addEventListener('submit', function (e) {
      let requiredFields = ['edu-level'];
  
      if (eduLevel.value === 'Highschool') {
        requiredFields = requiredFields.concat(['school-name', 'certificate', 'language', 'grad-year', 'school-grade']);
      } else if (eduLevel.value) {
        requiredFields = requiredFields.concat(['field-study', 'university', 'degree-year', 'degree-grade']);
      }
  
      if (!validateForm('educationForm', requiredFields)) {
        e.preventDefault();
      }
    });
  }
  // ======== EXPERIENCE DROPDOWNS / FIELDS ========
  const yearsExperience = document.getElementById('years-experience');
  const experienceDetails = document.getElementById('experience-details');
  const experienceList = document.getElementById('experience-list');
  
  const jobTitle = document.getElementById('job-title');
  const company = document.getElementById('company');
  const jobCategory = document.getElementById('job-category');
  const experienceType = document.getElementById('experience-type');
  const startDate = document.getElementById('start-date');
  const endDate = document.getElementById('end-date');
  
  let experiences = [];
  
  if (experienceDetails) experienceDetails.style.display = 'none';
  
  if (yearsExperience) {
    yearsExperience.addEventListener('change', function () {
      if (this.value === 'No' || this.value === '') {
        experienceDetails.style.display = 'none';
      } else {
        experienceDetails.style.display = 'block';
      }
    });
  }
  
  // Helper: Validate Experience
  function validateExperience() {
    let valid = true;
    [jobTitle, company, jobCategory, experienceType, startDate, endDate].forEach(field => {
      if (field) {
        field.classList.remove('error');
        if (!field.value) {
          field.classList.add('error');
          valid = false;
        }
      }
    });
    if (startDate.value && endDate.value && startDate.value > endDate.value) {
      startDate.classList.add('error');
      endDate.classList.add('error');
      console.log('Start date cannot be after end date.');
      valid = false;
    }
    return valid;
  }
  
  // Add experience to list
  function addExperienceToList(exp) {
    const div = document.createElement('div');
    div.style.border = '1px solid #ccc';
    div.style.padding = '10px';
    div.style.marginBottom = '10px';
    div.style.borderRadius = '8px';
    div.innerHTML = `
      <strong>${exp.jobTitle}</strong> at <em>${exp.company}</em><br>
      ${exp.jobCategory} | ${exp.experienceType} | ${exp.startDate} → ${exp.endDate}
      <button type="button" class="delete-exp-btn" style="color:red;border:none;background:none;float:right;cursor:pointer;">X</button>
    `;
    div.querySelector('.delete-exp-btn').onclick = () => div.remove();
    if (experienceList) experienceList.appendChild(div);
  }
  
  // Save / Add More Buttons
  const saveBtn = document.getElementById('save-experience');
  const addBtn = document.getElementById('add-experience');
  
  if (saveBtn) {
    saveBtn.addEventListener('click', function () {
      if (!validateExperience()) return;
      const exp = {
        jobTitle: jobTitle.value,
        company: company.value,
        jobCategory: jobCategory.value,
        experienceType: experienceType.value,
        startDate: startDate.value,
        endDate: endDate.value
      };
      experiences.push(exp);
      addExperienceToList(exp);
    });
  }
  
  if (addBtn) {
    addBtn.addEventListener('click', function () {
      if (!validateExperience()) return;
      const exp = {
        jobTitle: jobTitle.value,
        company: company.value,
        jobCategory: jobCategory.value,
        experienceType: experienceType.value,
        startDate: startDate.value,
        endDate: endDate.value
      };
      experiences.push(exp);
      addExperienceToList(exp);
  
      // Clear fields
      [jobTitle, company, jobCategory, experienceType, startDate, endDate].forEach(f => f.value = '');
    });
  }
  
}); // END DOMContentLoaded Wrapper

// =====================
//      EXPERTISE JS
// =====================

document.addEventListener("DOMContentLoaded", () => {

  // ---------- ADD LANGUAGE ----------
  const languageList = document.getElementById("languageList");
  const addLanguageBtn = document.getElementById("addLanguageBtn");

  if (addLanguageBtn) {
    addLanguageBtn.addEventListener("click", () => {
      const lang = document.getElementById("languageDD").value;
      const prof = document.getElementById("proficiencyDD").value;

      if (!lang || !prof) {
        // alert("Please select both language and proficiency.");
        return;
      }

      const tag = document.createElement("div");
      tag.className = "tag";
      tag.innerHTML = `
             <span>${lang} - ${prof}</span>
    <button type="button" style="color:red; border:none; background:none; cursor:pointer;">Delete</button>
    <input type="hidden" name="languages[]" value="${lang}">
    <input type="hidden" name="proficiency[]" value="${prof}">

          `;

      tag.querySelector("button").onclick = () => tag.remove();
      languageList.appendChild(tag);

      document.getElementById("languageDD").value = "";
      document.getElementById("proficiencyDD").value = "";
    });
  }

  // ---------- ADD SKILL ----------
  const skillList = document.getElementById("skillList");
  const addSkillBtn = document.getElementById("addSkillBtn");

  if (addSkillBtn) {
    addSkillBtn.addEventListener("click", () => {
      if (skillList.children.length >= 10) {
        // alert("Maximum 10 skills allowed.");
        return;
      }

      let selectedSkill = document.getElementById("skillsDD").value;
      let customSkill = document.getElementById("customSkill").value.trim();

      if (!selectedSkill && !customSkill) {
        // alert("Select or type a skill.");
        return;
      }

      let skill = selectedSkill || customSkill;

      const tag = document.createElement("div");
      tag.className = "tag";
      tag.innerHTML = `
              <span>${skill}</span>
               <button type="button" style="color:red; border:none; background:none; cursor:pointer;">Delete</button>
              <input type="hidden" name="skills[]" value="${skill}">
          `;



      tag.querySelector("button").onclick = () => tag.remove();
      skillList.appendChild(tag);

      document.getElementById("skillsDD").value = "";
      document.getElementById("customSkill").value = "";
    });
  }

  // ---------- CV UPLOAD ----------
  const dropArea = document.getElementById("cvDropArea");
  const cvFileInput = document.getElementById("cvFile");
  const browseCV = document.getElementById("browseCV");
  const fileNameDiv = document.getElementById("cvFileName");

  if (browseCV) browseCV.onclick = () => cvFileInput.click();

  if (dropArea) {
    dropArea.addEventListener("dragover", e => {
      e.preventDefault();
      dropArea.style.borderColor = "var(--blue-dark)";
    });

    dropArea.addEventListener("dragleave", () => {
      dropArea.style.borderColor = "var(--blue-main)";
    });

    dropArea.addEventListener("drop", e => {
      e.preventDefault();
      cvFileInput.files = e.dataTransfer.files;
      displayCV();
    });
  }

  if (cvFileInput) {
    cvFileInput.addEventListener("change", displayCV);
  }

  function displayCV() {
    const file = cvFileInput.files[0];
    if (!file) return;

    if (file.size > 5 * 1024 * 1024) {
      console.log("File must be ≤ 5MB");
      cvFileInput.value = "";
      return;
    }

    fileNameDiv.innerHTML = `<p>Uploaded: ${file.name}</p>`;
  }

});
document.addEventListener("DOMContentLoaded", () => {

  // ---------- JOB CATEGORIES ----------
  const categoryList = document.getElementById("categoryList");
  const addCategoryBtn = document.getElementById("addCategoryBtn");

  if (addCategoryBtn) {
    addCategoryBtn.addEventListener("click", () => {
      const selectedCategory = document.getElementById("jobCategoryDD").value;
      const customCategory = document.getElementById("customCategory").value.trim();

      if (!selectedCategory && !customCategory) {
        // alert("Select or type a job category.");
        return;
      }

      const category = selectedCategory || customCategory;

      const tag = document.createElement("div");
      tag.className = "tag";
      tag.innerHTML = `
        <span>${category}</span>
        <button type="button" style="color:red; border:none; background:none; cursor:pointer;">Delete</button>
        <input type="hidden" name="job_categories[]" value="${category}">
      `;

      tag.querySelector("button").onclick = () => tag.remove();
      categoryList.appendChild(tag);

      document.getElementById("jobCategoryDD").value = "";
      document.getElementById("customCategory").value = "";
    });
  }

  // ---------- TERMS & CONDITIONS MODAL ----------
  const termsLabel = document.getElementById("termsLabel");
  const termsModal = document.getElementById("termsModal");
  const closeTerms = document.getElementById("closeTerms");

  termsLabel.addEventListener("click", () => termsModal.style.display = "flex");
  closeTerms.addEventListener("click", () => termsModal.style.display = "none");

  // ---------- POPULATE DROPDOWNS (Added Fix) ----------
  
  // Job Categories
  const jobCategories = [
    "Software Development", "Graphic Design", "Content Writing", "Digital Marketing", 
    "Data Entry", "Sales", "Customer Support", "Video Editing", "Translation", "Tutoring"
  ];
  const jobCategoryDD = document.getElementById("jobCategoryDD");
  if (jobCategoryDD) {
    populateSelect(jobCategoryDD, jobCategories);
  }

  // Skills
  const commonSkills = [
    "Communication", "Time Management", "Teamwork", "Problem Solving", 
    "Python", "JavaScript", "HTML/CSS", "Photoshop", "Excel", "Public Speaking"
  ];
  const skillsDD = document.getElementById("skillsDD");
  if (skillsDD) {
    populateSelect(skillsDD, commonSkills);
  }

  // Languages
  const languages = ["English", "Arabic", "French", "German", "Spanish", "Italian"];
  const languageDD = document.getElementById("languageDD");
  if (languageDD) {
    populateSelect(languageDD, languages);
  }
  
  const proficiency = ["Basic", "Conversational", "Fluent", "Native"];
  const proficiencyDD = document.getElementById("proficiencyDD");
  if (proficiencyDD) {
    populateSelect(proficiencyDD, proficiency);
  }

  // ---------- FORM SUBMISSION ----------


});

