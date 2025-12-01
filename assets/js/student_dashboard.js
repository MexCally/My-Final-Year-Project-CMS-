 // Navigation functionality
 document.addEventListener('DOMContentLoaded', function() {
    const navLinks = document.querySelectorAll('.nav-link[data-section]');
    const contentSections = document.querySelectorAll('.content-section');

    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Remove active class from all nav links
            navLinks.forEach(nl => nl.classList.remove('active'));
            
            // Add active class to clicked link
            this.classList.add('active');
            
            // Hide all content sections
            contentSections.forEach(section => {
                section.style.display = 'none';
            });
            
            // Show selected section
            const targetSection = this.getAttribute('data-section');
            document.getElementById(targetSection).style.display = 'block';
        });
    });

    // Course selection functionality
    const courseCheckboxes = document.querySelectorAll('input[type="checkbox"]');
    courseCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            // Add logic here for dynamic course selection updates
            console.log('Course selection changed:', this.id, this.checked);
        });
    });

    // Smooth animations for cards
    const cards = document.querySelectorAll('.dashboard-card');
    cards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });

    // Materials functionality
    const viewMaterialsBtns = document.querySelectorAll('.view-materials');
    viewMaterialsBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const courseCode = this.getAttribute('data-course-code');
            loadMaterials(courseCode);
        });
    });

    // Assignments functionality
    const viewAssignmentsBtns = document.querySelectorAll('.view-assignments');
    viewAssignmentsBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const courseCode = this.getAttribute('data-course-code');
            loadAssignments(courseCode);
        });
    });
});

// Load materials function
function loadMaterials(courseCode) {
    const modal = new bootstrap.Modal(document.getElementById('materialsModal'));
    const modalTitle = document.querySelector('#materialsModal .modal-title');
    const content = document.getElementById('materialsContent');
    
    modalTitle.textContent = `Course Materials - ${courseCode}`;
    content.innerHTML = '<div class="text-center"><div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div></div>';
    
    modal.show();
    
    fetch('../PHP/get_student_materials.php')
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                content.innerHTML = `<div class="alert alert-danger">${data.error}</div>`;
                return;
            }
            
            const materials = data.materials.filter(material => material.course_code === courseCode);
            
            if (materials.length === 0) {
                content.innerHTML = '<div class="text-center text-muted py-4"><i class="fas fa-folder-open fa-3x mb-3"></i><p>No materials available for this course yet.</p></div>';
                return;
            }
            
            let html = '<div class="list-group">';
            materials.forEach(material => {
                const fileIcon = getFileIcon(material.file_type);
                const createdDate = new Date(material.created_at).toLocaleDateString();
                
                html += `
                    <div class="list-group-item">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <h6 class="mb-1"><i class="${fileIcon} me-2"></i>${material.title}</h6>
                                <p class="mb-1 text-muted">${material.description || 'No description available'}</p>
                                <small class="text-muted">Uploaded by ${material.lecturer_name} on ${createdDate}</small>
                            </div>
                            <div class="ms-3">
                                <a href="../PHP/download_material.php?material_id=${material.material_id}" class="btn btn-primary btn-sm" target="_blank">
                                    <i class="fas fa-download me-1"></i>Download
                                </a>
                            </div>
                        </div>
                    </div>
                `;
            });
            html += '</div>';
            
            content.innerHTML = html;
        })
        .catch(error => {
            console.error('Error loading materials:', error);
            content.innerHTML = '<div class="alert alert-danger">Error loading materials. Please try again.</div>';
        });
}

// Load assignments function
function loadAssignments(courseCode) {
    const modal = new bootstrap.Modal(document.getElementById('assignmentsModal'));
    const modalTitle = document.querySelector('#assignmentsModal .modal-title');
    const content = document.getElementById('assignmentsContent');
    
    modalTitle.textContent = `Course Assignments - ${courseCode}`;
    content.innerHTML = '<div class="text-center"><div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div></div>';
    
    modal.show();
    
    fetch('../PHP/get_student_assignments.php')
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                content.innerHTML = `<div class="alert alert-danger">${data.error}</div>`;
                return;
            }
            
            const assignments = data.assignments.filter(assignment => assignment.course_code === courseCode);
            
            if (assignments.length === 0) {
                content.innerHTML = '<div class="text-center text-muted py-4"><i class="fas fa-tasks fa-3x mb-3"></i><p>No assignments available for this course yet.</p></div>';
                return;
            }
            
            let html = '<div class="list-group">';
            assignments.forEach(assignment => {
                const dueDate = new Date(assignment.due_date);
                const now = new Date();
                const isOverdue = dueDate < now;
                const isSubmitted = assignment.sub_id !== null;
                
                const statusBadge = isSubmitted ? 
                    '<span class="badge bg-success">Submitted</span>' : 
                    (isOverdue ? '<span class="badge bg-danger">Overdue</span>' : '<span class="badge bg-warning">Pending</span>');
                
                html += `
                    <div class="list-group-item">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="mb-0">${assignment.title}</h6>
                                    ${statusBadge}
                                </div>
                                <p class="mb-1 text-muted">${assignment.description || 'No description available'}</p>
                                <div class="row">
                                    <div class="col-md-6">
                                        <small class="text-muted"><i class="fas fa-calendar me-1"></i>Due: ${dueDate.toLocaleDateString()} ${dueDate.toLocaleTimeString()}</small>
                                    </div>
                                    <div class="col-md-6">
                                        <small class="text-muted"><i class="fas fa-star me-1"></i>Max Score: ${assignment.max_score}</small>
                                    </div>
                                </div>
                                ${isSubmitted ? `<small class="text-success"><i class="fas fa-check me-1"></i>Submitted on ${new Date(assignment.submitted_at).toLocaleDateString()}</small>` : ''}
                            </div>
                            <div class="ms-3">
                                ${!isSubmitted && !isOverdue ? 
                                    `<button class="btn btn-primary btn-sm" onclick="submitAssignment(${assignment.assignment_id})">
                                        <i class="fas fa-upload me-1"></i>Submit
                                    </button>` : 
                                    `<button class="btn btn-outline-secondary btn-sm" disabled>
                                        <i class="fas fa-eye me-1"></i>View
                                    </button>`
                                }
                            </div>
                        </div>
                    </div>
                `;
            });
            html += '</div>';
            
            content.innerHTML = html;
        })
        .catch(error => {
            console.error('Error loading assignments:', error);
            content.innerHTML = '<div class="alert alert-danger">Error loading assignments. Please try again.</div>';
        });
}

// Get file icon based on file type
function getFileIcon(fileType) {
    switch(fileType) {
        case 'lecture':
            return 'fas fa-chalkboard-teacher text-primary';
        case 'reference':
            return 'fas fa-book text-success';
        case 'video':
            return 'fas fa-video text-danger';
        case 'assignment':
            return 'fas fa-tasks text-warning';
        default:
            return 'fas fa-file text-secondary';
    }
}

// Submit assignment function (placeholder)
function submitAssignment(assignmentId) {
    alert('Assignment submission functionality will be implemented here. Assignment ID: ' + assignmentId);
}