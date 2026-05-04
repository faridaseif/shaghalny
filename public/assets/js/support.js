// Support Center JavaScript

// Report Modal Functions
function openReportModal() {
    if (!CURRENT_USER_ID) {
        ModalUI.requireAuth('You need to sign in to report an issue.');
        return;
    }
    document.getElementById('report-modal').style.display = 'block';
}

function closeReportModal() {
    document.getElementById('report-modal').style.display = 'none';
    document.getElementById('report-form').reset();
}

// Help Modal Functions
function openHelpModal() {
    document.getElementById('help-modal').style.display = 'block';
}

function closeHelpModal() {
    document.getElementById('help-modal').style.display = 'none';
    document.getElementById('help-form').reset();
}

// Report Details Modal
function viewReportDetails(reportId) {
    if (!CURRENT_USER_ID) {
        ModalUI.requireAuth('Please sign in to view report details.');
        return;
    }

    const baseUrl = window.APP_BASE_URL || 'index.php'; // or just empty if relative
    // For legacy router, we post to index.php?action=get_report
    fetch(`index.php?action=get_report&id=${reportId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const report = data.report;
                const modal = document.getElementById('report-details-modal');
                const content = document.getElementById('report-details-content');

                const statusLabels = {
                    'pending': 'Pending',
                    'under_review': 'Under Review',
                    'resolved': 'Resolved',
                    'closed': 'Closed'
                };

                const typeLabels = {
                    'payment_issue': 'Payment Issue',
                    'theft': 'Theft',
                    'harassment': 'Harassment',
                    'safety_concern': 'Safety Concern',
                    'other': 'Other'
                };

                content.innerHTML = `
                    <h2>Report Details</h2>
                    <div class="report-detail-item">
                        <div class="report-detail-label">Title</div>
                        <div class="report-detail-value">${escapeHtml(report.title)}</div>
                    </div>
                    <div class="report-detail-item">
                        <div class="report-detail-label">Type</div>
                        <div class="report-detail-value">${typeLabels[report.report_type] || report.report_type}</div>
                    </div>
                    <div class="report-detail-item">
                        <div class="report-detail-label">Status</div>
                        <div class="report-detail-value">
                            <span class="status-badge status-${report.status}">
                                ${statusLabels[report.status] || report.status}
                            </span>
                        </div>
                    </div>
                    <div class="report-detail-item">
                        <div class="report-detail-label">Description</div>
                        <div class="report-detail-value">${escapeHtml(report.description)}</div>
                    </div>
                    <div class="report-detail-item">
                        <div class="report-detail-label">Priority</div>
                        <div class="report-detail-value">${report.priority.charAt(0).toUpperCase() + report.priority.slice(1)}</div>
                    </div>
                    <div class="report-detail-item">
                        <div class="report-detail-label">Reported</div>
                        <div class="report-detail-value">${new Date(report.created_at).toLocaleString()}</div>
                    </div>
                    ${report.resolved_at ? `
                    <div class="report-detail-item">
                        <div class="report-detail-label">Resolved</div>
                        <div class="report-detail-value">${new Date(report.resolved_at).toLocaleString()}</div>
                    </div>
                    ` : ''}
                    ${report.admin_notes ? `
                    <div class="report-detail-item">
                        <div class="report-detail-label">Admin Notes</div>
                        <div class="report-detail-value">${escapeHtml(report.admin_notes)}</div>
                    </div>
                    ` : ''}
                `;

                modal.style.display = 'block';
            } else {
                ModalUI.alert('Failed to load report details: ' + (data.error || 'Unknown error'), 'Error');
            }
        })
        .catch(error => {
            console.error('Error loading report details:', error);
            ModalUI.alert('An error occurred while loading report details.', 'Error');
        });
}

function closeReportDetailsModal() {
    document.getElementById('report-details-modal').style.display = 'none';
}

// Trust & Ratings Modal
function openRatingsModal() {
    document.getElementById('ratings-modal').style.display = 'block';
}

function closeRatingsModal() {
    document.getElementById('ratings-modal').style.display = 'none';
}

// Emergency Contacts
function openEmergencyContacts() {
    if (!CURRENT_USER_ID) {
        ModalUI.requireAuth('Please sign in to manage emergency contacts.');
        return;
    }
    document.getElementById('emergency-modal').style.display = 'block';
}

function closeEmergencyContactsModal() {
    document.getElementById('emergency-modal').style.display = 'none';
}

// Close modals when clicking outside
window.onclick = function (event) {
    const modals = document.querySelectorAll('.modal');
    modals.forEach(modal => {
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    });
}

// Report Form Submission
document.addEventListener('DOMContentLoaded', function () {
    const reportForm = document.getElementById('report-form');
    if (reportForm) {
        reportForm.addEventListener('submit', function (e) {
            e.preventDefault();

            if (!CURRENT_USER_ID) {
                ModalUI.requireAuth('Please sign in to submit a report.');
                return;
            }

            const formData = {
                report_type: document.getElementById('report-type').value,
                title: document.getElementById('report-title').value,
                description: document.getElementById('report-description').value,
                priority: document.getElementById('report-priority').value
            };

            // Legacy endpoint
            fetch(`index.php?action=report_submit`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(formData)
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        ModalUI.alert('Report submitted successfully! We will review it shortly.', 'Success')
                            .then(() => {
                                closeReportModal();
                                window.location.reload();
                            });
                    } else {
                        ModalUI.alert('Failed to submit report: ' + (data.error || 'Unknown error'), 'Error');
                    }
                })
                .catch(error => {
                    console.error('Error submitting report:', error);
                    ModalUI.alert('An error occurred while submitting the report.', 'Error');
                });
        });
    }

    // Help Form Submission
    const helpForm = document.getElementById('help-form');
    if (helpForm) {
        helpForm.addEventListener('submit', function (e) {
            e.preventDefault();

            const formData = {
                subject: document.getElementById('help-subject').value,
                message: document.getElementById('help-message').value
            };

            const nameInput = document.getElementById('help-name');
            const emailInput = document.getElementById('help-email');

            if (nameInput) formData.guest_name = nameInput.value;
            if (emailInput) formData.guest_email = emailInput.value;

            // Legacy endpoint
            fetch(`index.php?action=contact_submit`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(formData)
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        ModalUI.alert(data.message || 'Your message has been sent. We will respond soon.', 'Message Sent')
                            .then(() => closeHelpModal());
                    } else {
                        ModalUI.alert('Failed to send message: ' + (data.error || 'Unknown error'), 'Error');
                    }
                })
                .catch(error => {
                    console.error('Error sending message:', error);
                    ModalUI.alert('An error occurred while sending your message.', 'Error');
                });
        });
    }
});

// Utility function to escape HTML
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
