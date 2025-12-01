<?php
// This component should be included in the student dashboard
// It checks if the student has approved registrations and shows download options
?>

<div id="downloadFormSection" class="card shadow mb-4" style="display: none;">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-download"></i> Download Course Registration Form
        </h6>
    </div>
    <div class="card-body">
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> Your course registration has been approved! You can now download your registration form.
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <label class="form-label"><strong>Select Format:</strong></label>
                <select class="form-select" id="downloadFormat">
                    <option value="pdf">PDF Document (.pdf)</option>
                    <option value="doc">Word Document (.doc)</option>
                    <option value="docx">Word Document (.docx)</option>
                    <option value="xls">Excel Spreadsheet (.xls)</option>
                    <option value="xlsx">Excel Spreadsheet (.xlsx)</option>
                    <option value="txt">Text File (.txt)</option>
                    <option value="csv">CSV File (.csv)</option>
                    <option value="html">HTML Page (.html)</option>
                </select>
            </div>
            <div class="col-md-6 d-flex align-items-end">
                <button class="btn btn-primary" id="downloadFormBtn">
                    <i class="fas fa-download"></i> Download Form
                </button>
            </div>
        </div>
        
        <div class="mt-3">
            <small class="text-muted">
                <i class="fas fa-info-circle"></i> 
                The downloaded form contains all your approved course registrations for the current academic session.
            </small>
        </div>
    </div>
</div>

<script>
// Check if student has approved registrations
function checkApprovedRegistrations() {
    fetch('../PHP/check_approved_registration.php')
    .then(response => response.json())
    .then(data => {
        if (data.success && data.has_approved) {
            document.getElementById('downloadFormSection').style.display = 'block';
        }
    })
    .catch(error => {
        console.error('Error checking approved registrations:', error);
    });
}

// Handle download button click
document.getElementById('downloadFormBtn').addEventListener('click', function() {
    const format = document.getElementById('downloadFormat').value;
    const downloadUrl = `../PHP/download_course_form.php?format=${format}`;
    
    // Open download in new window/tab
    window.open(downloadUrl, '_blank');
});

// Check on page load
document.addEventListener('DOMContentLoaded', function() {
    checkApprovedRegistrations();
});
</script>