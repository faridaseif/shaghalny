// Tabs: switch between "Jobs I Posted", "Jobs I Applied For", "Work History"
(function () {
  var tabs = document.querySelectorAll('.sec');
  var sections = document.querySelectorAll('.jobs-section');

  function showTab(name) {
    sections.forEach(function (sec) {
      var match = sec.getAttribute('data-section') === name;
      sec.style.display = match ? 'block' : 'none';
    });

    tabs.forEach(function (btn) {
      if (btn.getAttribute('data-tab') === name) {
        btn.classList.add('sec-active');
      } else {
        btn.classList.remove('sec-active');
      }
    });
  }

  tabs.forEach(function (btn) {
    btn.addEventListener('click', function () {
      var name = btn.getAttribute('data-tab');
      if (name) {
        showTab(name);
      }
    });
  });

  // Initial state: show "Jobs I Posted"
  showTab('posted');
})();


// Simple applications modal logic – backend can still control data later
(function () {
  var modal = document.getElementById('applications-modal');
  if (!modal) return;

  var backdrop = modal.querySelector('.applications-backdrop');
  var closeButtons = modal.querySelectorAll('[data-close-modal]');
  var openButtons = document.querySelectorAll('.view-link:not(.contact-link)');
  var jobTitleEl = modal.querySelector('.applications-job-title');
  var editButtons = document.querySelectorAll('.edit-job');


  function openModal(jobTitle) {
    if (jobTitleEl && jobTitle) {
      jobTitleEl.textContent = jobTitle;
    }
    modal.classList.add('is-open');
    modal.setAttribute('aria-hidden', 'false');
  }

  function closeModal() {
    modal.classList.remove('is-open');
    modal.setAttribute('aria-hidden', 'true');
  }

  // Ensure we don't accidentally intercept the actual Accept/Decline links
  // The links are standard <a> tags with hrefs.
  // This blocked needs to ensure no 'click' listeners on '.btn' generic classes block them.
  // Current code mostly listens on specific classes like .view-link or .contact-link.
  // Just to be safe, we do nothing here.
  
  var contactButtons = document.querySelectorAll('.contact-link');

  contactButtons.forEach(function (btn) {
    btn.addEventListener('click', function (event) {
      event.preventDefault();
      // logic for contact modal if needed, or just let it be handled by the other block
    });
  });

  // PREVIOUSLY:
  /*
  openButtons.forEach(function (btn) {
    btn.addEventListener('click', function (event) {
      event.preventDefault();
      var card = btn.closest('.job-card');
      var title = card ? card.querySelector('.job-title') : null;
      openModal(title ? title.textContent.trim() : '');
    });
  });
  */

  closeButtons.forEach(function (btn) {
    btn.addEventListener('click', closeModal);
  });

  if (backdrop) {
    backdrop.addEventListener('click', closeModal);
  }

  document.addEventListener('keydown', function (event) {
    if (event.key === 'Escape') {
      closeModal();
    }
  });
})();

// Contact modal logic
(function () {
  var modal = document.getElementById('contact-modal');
  if (!modal) return;

  var backdrop = modal.querySelector('.applications-backdrop');
  var closeButtons = modal.querySelectorAll('[data-close-contact]');
  var openButtons = document.querySelectorAll('.contact-link');
  var workerNameEl = modal.querySelector('.contact-worker-name');
  var jobTitleEl = modal.querySelector('.contact-job-title');

  var emailEl = modal.querySelector('.contact-value-email'); // Needs ID in PHP
  // ... but elements don't have IDs yet. Let's use selectors for now since structure is simple.
  // Actually, I should update PHP to add IDs, but I can use nth-child or class structure.
  // The structure is .contact-row > .contact-value. First row is Phone, Second is Email.
  var contactValues = modal.querySelectorAll('.contact-value');

  function openModal(workerName, jobTitle, phone, email) {
    if (workerNameEl && workerName) {
      workerNameEl.textContent = workerName;
    }
    if (jobTitleEl && jobTitle) {
      jobTitleEl.textContent = jobTitle;
    }
    
    // Update Phone (First contact-value)
    if (contactValues[0] && phone) {
        contactValues[0].textContent = phone;
    }
    // Update Email (Second contact-value)
    if (contactValues[1] && email) {
        contactValues[1].textContent = email;
    }

    modal.classList.add('is-open');
    modal.setAttribute('aria-hidden', 'false');
  }

  function closeModal() {
    modal.classList.remove('is-open');
    modal.setAttribute('aria-hidden', 'true');
  }

  // Auto-Open Check
  if (window.autoOpenContact) {
      openModal(
          window.autoOpenContact.workerName, 
          window.autoOpenContact.jobTitle,
          window.autoOpenContact.phone,
          window.autoOpenContact.email
      );
  }

  openButtons.forEach(function (btn) {
    btn.addEventListener('click', function (event) {
      event.preventDefault();
      
      // Try reading data attributes first (New robust method)
      var workerName = btn.getAttribute('data-worker-name');
      var jobTitle = btn.getAttribute('data-job-title');
      var phone = btn.getAttribute('data-phone'); // Future proofing
      var email = btn.getAttribute('data-email'); // Future proofing

      // Fallback to old DOM traversal method if data attributes missing
      if (!workerName) {
          var card = btn.closest('.job-card');
          var workerEl = card ? card.querySelector('.job-extra-value') : null;
          workerName = workerEl ? workerEl.textContent.trim() : '';
      }
      if (!jobTitle) {
           var card = btn.closest('.job-card');
           var jobTitleElCard = card ? card.querySelector('.job-title') : null;
           jobTitle = jobTitleElCard ? jobTitleElCard.textContent.trim() : '';
      }

      openModal(workerName, jobTitle, phone, email);
    });
  });

  closeButtons.forEach(function (btn) {
    btn.addEventListener('click', closeModal);
  });

  if (backdrop) {
    backdrop.addEventListener('click', closeModal);
  }

  document.addEventListener('keydown', function (event) {
    if (event.key === 'Escape') {
      closeModal();
    }
  });
})();

// Edit Job modal logic
(function () {
  var modal = document.getElementById('edit-job-modal');
  if (!modal) return;

  var backdrop = modal.querySelector('.applications-backdrop');
  var closeButtons = modal.querySelectorAll('[data-close-edit]');
  var editButtons = document.querySelectorAll('.edit-job'); // CHANGED FROM '.edit' TO '.edit-job'
  var jobNameEl = modal.querySelector('.edit-job-name');
  var jobIdInput = document.getElementById('edit-job-id');
  var paymentInput = document.getElementById('edit-payment');
  var timeInput = document.getElementById('edit-time');

  function openModal(jobId, jobTitle, payment, time) {
    if (jobNameEl && jobTitle) {
      jobNameEl.textContent = jobTitle;
    }
    if (jobIdInput) {
      jobIdInput.value = jobId;
    }
    if (paymentInput && payment) {
      paymentInput.value = payment;
    }
    if (timeInput && time) {
      timeInput.value = time;
    }
    modal.classList.add('is-open');
    modal.setAttribute('aria-hidden', 'false');
  }

  function closeModal() {
    modal.classList.remove('is-open');
    modal.setAttribute('aria-hidden', 'true');
  }

  editButtons.forEach(function (btn) {
    btn.addEventListener('click', function (event) {
      event.preventDefault();
      var card = btn.closest('.job-card');
      if (!card) return;

      var jobId = card.getAttribute('data-job-id');
      var payment = card.getAttribute('data-payment');
      var time = card.getAttribute('data-time');
      var titleEl = card.querySelector('.job-title');

      openModal(
        jobId,
        titleEl ? titleEl.textContent.trim() : '',
        payment,
        time
      );
    });
  });

  closeButtons.forEach(function (btn) {
    btn.addEventListener('click', closeModal);
  });

  if (backdrop) {
    backdrop.addEventListener('click', closeModal);
  }

  document.addEventListener('keydown', function (event) {
    if (event.key === 'Escape') {
      closeModal();
    }
  });

  // Handle form submission (you'll implement the backend)
  // Normal form submission is handled by the browser now.
  // We removed the fetch() block so the page will reload/redirect properly.
})();

// Rate Applicant Modal Logic
(function () {
  var modal = document.getElementById('rate-modal');
  if (!modal) return;

  var backdrop = modal.querySelector('.applications-backdrop');
  var closeButtons = modal.querySelectorAll('[data-close-rate]');
  var applicantNameEl = modal.querySelector('.rate-applicant-name');
  var jobTitleEl = modal.querySelector('.rate-job-title');
  var jobIdInput = document.getElementById('rate-job-id');
  var applicantIdInput = document.getElementById('rate-applicant-id');

  function openModal(job) {
    if (applicantNameEl) applicantNameEl.textContent = job.first_name + ' ' + job.last_name;
    if (jobTitleEl) jobTitleEl.textContent = job.title;
    if (jobIdInput) jobIdInput.value = job.job_id;
    if (applicantIdInput) applicantIdInput.value = job.applicant_id;

    modal.classList.add('is-open');
    modal.setAttribute('aria-hidden', 'false');
  }

  function closeModal() {
    modal.classList.remove('is-open');
    modal.setAttribute('aria-hidden', 'true');
  }

  closeButtons.forEach(function (btn) {
    btn.addEventListener('click', closeModal);
  });

  if (backdrop) {
    backdrop.addEventListener('click', closeModal);
  }

  // Auto-open if jobs exist
  if (window.jobsToRate && window.jobsToRate.length > 0) {
    // Show the first one for now
    openModal(window.jobsToRate[0]);
  }
})();

// Auto-refresh page every 60 seconds to check for expired jobs
setInterval(function () {
  if (!document.hidden) { // Only reload if tab is active to save resources/annoyance
    window.location.reload();
  }
}, 60000);

