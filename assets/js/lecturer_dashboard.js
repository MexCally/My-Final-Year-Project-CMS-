document.addEventListener("DOMContentLoaded", () => {
  // Navigation functionality
  const navLinks = document.querySelectorAll(".sidebar .nav-link")
  const contentSections = document.querySelectorAll(".content-section")
  const bootstrap = window.bootstrap // Declare the bootstrap variable

  // Set initial active section
  document.getElementById("dashboard").style.display = "block"
  document.getElementById("dashboard").classList.add("active")
  
  // Load recent activities on dashboard load
  loadRecentActivities()
  
  // Refresh dashboard statistics
  refreshDashboardStats()

  navLinks.forEach((link) => {
    link.addEventListener("click", function (e) {
      e.preventDefault()

      // Remove active class from all nav links
      navLinks.forEach((nl) => nl.classList.remove("active"))

      // Add active class to clicked link
      this.classList.add("active")

      // Hide all content sections
      contentSections.forEach((section) => {
        section.style.display = "none"
        section.classList.remove("active")
      })

      // Show selected section
      const targetSection = this.getAttribute("data-section")
      const section = document.getElementById(targetSection)
      if (section) {
        section.style.display = "block"
        setTimeout(() => {
          section.classList.add("active")
        }, 10)
        
        // Load materials when materials section is shown
        if (targetSection === 'materials') {
          loadCourseMaterials()
        }
      }
    })
  })

  // Grade calculation functionality using Excel formulas
  const gradeInputs = document.querySelectorAll('#grading input[type="number"]')

  // Function to calculate grade letter (Excel formula)
  function calculateGradeLetter(totalScore) {
    if (totalScore < 40) return 'F'
    if (totalScore < 45) return 'E'
    if (totalScore < 50) return 'D'
    if (totalScore < 60) return 'C'
    if (totalScore < 70) return 'B'
    return 'A'
  }

  // Function to get grade display class
  function getGradeClass(gradeLetter) {
    switch (gradeLetter) {
      case 'A': return 'bg-success'
      case 'B': return 'bg-warning'
      case 'C': return 'bg-info'
      case 'D': return 'bg-secondary'
      case 'E': return 'bg-danger'
      case 'F': return 'bg-danger'
      default: return 'bg-secondary'
    }
  }

  gradeInputs.forEach((input) => {
    input.addEventListener("input", function () {
      const row = this.closest("tr")
      const caScore = Number.parseFloat(row.querySelector("td:nth-child(3) input").value) || 0
      const testScore = Number.parseFloat(row.querySelector("td:nth-child(4) input").value) || 0
      const examScore = Number.parseFloat(row.querySelector("td:nth-child(5) input").value) || 0

      // Calculate total score (CA: 30, Test: 20, Exam: 50)
      const totalScore = caScore + testScore + examScore

      // Update total score
      row.querySelector("td:nth-child(6) strong").textContent = totalScore.toFixed(1)

      // Calculate and update grade using Excel formula
      const gradeLetter = calculateGradeLetter(totalScore)
      const gradeClass = getGradeClass(gradeLetter)
      const gradeElement = row.querySelector("td:nth-child(7) .badge")
      
      gradeElement.className = `badge ${gradeClass}`
      gradeElement.textContent = gradeLetter
    })
  })

  // Save grade functionality
  const saveButtons = document.querySelectorAll(".save-grade-btn")
  saveButtons.forEach((button) => {
    button.addEventListener("click", function () {
      const row = this.closest("tr")
      const studentId = this.getAttribute("data-student-id")
      const courseId = this.getAttribute("data-course-id")
      const evalId = this.getAttribute("data-eval-id")
      const creditUnits = this.getAttribute("data-credit-units")
      
      const caScore = Number.parseFloat(row.querySelector("td:nth-child(3) input").value) || 0
      const testScore = Number.parseFloat(row.querySelector("td:nth-child(4) input").value) || 0
      const examScore = Number.parseFloat(row.querySelector("td:nth-child(5) input").value) || 0
      
      // Validate scores
      if (caScore < 0 || caScore > 30) {
        showNotification('CA score must be between 0 and 30', 'danger')
        return
      }
      if (testScore < 0 || testScore > 20) {
        showNotification('Test score must be between 0 and 20', 'danger')
        return
      }
      if (examScore < 0 || examScore > 50) {
        showNotification('Exam score must be between 0 and 50', 'danger')
        return
      }
      
      const originalHtml = this.innerHTML
      this.innerHTML = '<i class="fas fa-spinner fa-spin"></i>'
      this.disabled = true

      // Send data to server
      fetch('../lecturer_modules/save_grades.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          student_id: studentId,
          course_id: courseId,
          eval_id: evalId,
          ca_score: caScore,
          test_score: testScore,
          exam_score: examScore,
          credit_units: creditUnits
        })
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          // Update the display with calculated values
          row.querySelector("td:nth-child(6) strong").textContent = data.data.total_score.toFixed(1)
          const gradeElement = row.querySelector("td:nth-child(7) .badge")
          const gradeClass = getGradeClass(data.data.grade_letter)
          gradeElement.className = `badge ${gradeClass}`
          gradeElement.textContent = data.data.grade_letter
          
          // Update eval_id for future saves
          if (!evalId) {
            this.setAttribute('data-eval-id', 'updated')
          }
          
          showNotification(data.message, 'success')
          
          // Show success state
          this.innerHTML = '<i class="fas fa-check"></i>'
          this.classList.remove('btn-primary')
          this.classList.add('btn-success')
          
          setTimeout(() => {
            this.innerHTML = originalHtml
            this.classList.remove('btn-success')
            this.classList.add('btn-primary')
            this.disabled = false
          }, 2000)
        } else {
          showNotification(data.message || 'Failed to save grade', 'danger')
          this.innerHTML = originalHtml
          this.disabled = false
        }
      })
      .catch(error => {
        console.error('Error:', error)
        showNotification('An error occurred while saving the grade', 'danger')
        this.innerHTML = originalHtml
        this.disabled = false
      })
    })
  })

  // Assignment creation functionality
  const createAssignmentBtn = document.getElementById("createAssignmentBtn")
  if (createAssignmentBtn) {
    createAssignmentBtn.addEventListener("click", function () {
      const form = document.getElementById("createAssignmentForm")
      const formData = new FormData(form)

      // Validate form
      if (!form.checkValidity()) {
        form.reportValidity()
        return
      }

      // Show loading state
      this.textContent = "Creating..."
      this.disabled = true

      // Submit form to server
      fetch('../PHP/create_assignment.php', {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          // Close modal
          const modal = bootstrap.Modal.getInstance(document.getElementById("createAssignmentModal"))
          modal.hide()

          // Reset form
          form.reset()

          // Show success message
          showNotification(data.message, 'success')
          
          // Refresh assignments section if visible
          const assignmentsSection = document.getElementById('assignments')
          if (assignmentsSection.style.display !== 'none') {
            location.reload() // Simple refresh for now
          }
        } else {
          // Show error message
          if (data.errors && Array.isArray(data.errors)) {
            showNotification(data.errors.join('<br>'), 'danger')
          } else {
            showNotification(data.message || 'Failed to create assignment', 'danger')
          }
        }
      })
      .catch(error => {
        console.error('Error:', error)
        showNotification('An error occurred while creating the assignment', 'danger')
      })
      .finally(() => {
        // Reset button
        this.textContent = "Create Assignment"
        this.disabled = false
      })
    })
  }

  // Material upload functionality
  const uploadMaterialBtn = document.getElementById("uploadMaterialBtn")
  if (uploadMaterialBtn) {
    uploadMaterialBtn.addEventListener("click", function () {
      const form = document.getElementById("uploadMaterialForm")
      const formData = new FormData(form)

      // Validate form
      if (!form.checkValidity()) {
        form.reportValidity()
        return
      }

      // Show loading state
      this.textContent = "Uploading..."
      this.disabled = true

      // Submit form to server
      fetch('../PHP/upload_material.php', {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          // Close modal
          const modal = bootstrap.Modal.getInstance(document.getElementById("uploadMaterialModal"))
          modal.hide()

          // Reset form
          form.reset()

          // Show success message
          showNotification(data.message, 'success')
          
          // Refresh materials if visible
          const materialsSection = document.getElementById('materials')
          if (materialsSection.style.display !== 'none') {
            loadCourseMaterials()
          }
        } else {
          // Show error message
          if (data.errors && Array.isArray(data.errors)) {
            showNotification(data.errors.join('<br>'), 'danger')
          } else {
            showNotification(data.message || 'Failed to upload material', 'danger')
          }
        }
      })
      .catch(error => {
        console.error('Error:', error)
        showNotification('An error occurred while uploading the material', 'danger')
      })
      .finally(() => {
        // Reset button
        this.textContent = "Upload Material"
        this.disabled = false
      })
    })
  }

  // Quick action buttons functionality
  const quickActionButtons = document.querySelectorAll(".card-body .btn")
  quickActionButtons.forEach((button) => {
    if (button.textContent.includes("Create Assignment")) {
      button.addEventListener("click", () => {
        // Switch to assignments section
        document.querySelector('[data-section="assignments"]').click()
      })
    } else if (button.textContent.includes("Upload Materials")) {
      button.addEventListener("click", () => {
        // Switch to materials section
        document.querySelector('[data-section="materials"]').click()
      })
    } else if (button.textContent.includes("Grade Students")) {
      button.addEventListener("click", () => {
        // Switch to grading section
        document.querySelector('[data-section="grading"]').click()
      })
    } else if (button.textContent.includes("View Reports")) {
      button.addEventListener("click", () => {
        // Switch to records section
        document.querySelector('[data-section="records"]').click()
      })
    }
  })

  // Course search functionality
  const courseSearchInput = document.getElementById('courseSearch')
  const searchButton = courseSearchInput?.nextElementSibling?.querySelector('button')
  
  function performCourseSearch() {
    const searchTerm = courseSearchInput.value.toLowerCase().trim()
    const table = document.getElementById('coursesTable')
    
    if (table) {
      const rows = table.querySelectorAll('tbody tr')
      let visibleCount = 0
      
      rows.forEach(row => {
        const cells = row.querySelectorAll('td')
        let found = false
        
        // Skip the S/N column (first column) and Actions column (last column)
        for (let i = 1; i < cells.length - 1; i++) {
          const cellText = cells[i].textContent.toLowerCase()
          if (cellText.includes(searchTerm)) {
            found = true
            break
          }
        }
        
        if (found || searchTerm === '') {
          row.style.display = ''
          visibleCount++
        } else {
          row.style.display = 'none'
        }
      })
      
      // Show/hide no results message
      let noResultsMsg = table.querySelector('.no-results-message')
      if (visibleCount === 0 && searchTerm !== '') {
        if (!noResultsMsg) {
          noResultsMsg = document.createElement('tr')
          noResultsMsg.className = 'no-results-message'
          noResultsMsg.innerHTML = '<td colspan="9" class="text-center py-3 text-muted"><i class="fas fa-search me-2"></i>No courses found matching your search.</td>'
          table.querySelector('tbody').appendChild(noResultsMsg)
        }
        noResultsMsg.style.display = ''
      } else if (noResultsMsg) {
        noResultsMsg.style.display = 'none'
      }
    }
  }
  
  if (courseSearchInput) {
    // Search on input (real-time)
    courseSearchInput.addEventListener('input', performCourseSearch)
    
    // Search on Enter key
    courseSearchInput.addEventListener('keypress', function(e) {
      if (e.key === 'Enter') {
        e.preventDefault()
        performCourseSearch()
      }
    })
    
    // Search button click
    if (searchButton) {
      searchButton.addEventListener('click', function(e) {
        e.preventDefault()
        performCourseSearch()
      })
    }
  }

  // Assignment search functionality
  const assignmentSearchInput = document.getElementById('assignmentSearch')
  const assignmentSearchButton = assignmentSearchInput?.nextElementSibling?.querySelector('button')
  
  function performAssignmentSearch() {
    const searchTerm = assignmentSearchInput.value.toLowerCase().trim()
    const assignmentCards = document.querySelectorAll('#assignmentsContainer .card')
    let visibleCount = 0
    
    assignmentCards.forEach(card => {
      const cardText = card.textContent.toLowerCase()
      
      if (cardText.includes(searchTerm) || searchTerm === '') {
        card.style.display = ''
        visibleCount++
      } else {
        card.style.display = 'none'
      }
    })
    
    // Show/hide no results message for assignments
    const container = document.getElementById('assignmentsContainer')
    let noResultsMsg = container.querySelector('.no-results-message')
    
    if (visibleCount === 0 && searchTerm !== '' && assignmentCards.length > 0) {
      if (!noResultsMsg) {
        noResultsMsg = document.createElement('div')
        noResultsMsg.className = 'no-results-message text-center py-4'
        noResultsMsg.innerHTML = '<i class="fas fa-search fa-2x text-muted mb-3"></i><p class="text-muted">No assignments found matching your search.</p>'
        container.appendChild(noResultsMsg)
      }
      noResultsMsg.style.display = ''
    } else if (noResultsMsg) {
      noResultsMsg.style.display = 'none'
    }
  }
  
  if (assignmentSearchInput) {
    // Search on input (real-time)
    assignmentSearchInput.addEventListener('input', performAssignmentSearch)
    
    // Search on Enter key
    assignmentSearchInput.addEventListener('keypress', function(e) {
      if (e.key === 'Enter') {
        e.preventDefault()
        performAssignmentSearch()
      }
    })
    
    // Search button click
    if (assignmentSearchButton) {
      assignmentSearchButton.addEventListener('click', function(e) {
        e.preventDefault()
        performAssignmentSearch()
      })
    }
  }

  // General search functionality for other tables
  const searchInputs = document.querySelectorAll('input[type="search"]')
  searchInputs.forEach((input) => {
    input.addEventListener("input", function () {
      const searchTerm = this.value.toLowerCase()
      const table = this.closest(".card").querySelector("table tbody")

      if (table) {
        const rows = table.querySelectorAll("tr")
        rows.forEach((row) => {
          const text = row.textContent.toLowerCase()
          row.style.display = text.includes(searchTerm) ? "" : "none"
        })
      }
    })
  })

  // Course details modal functionality
  const viewDetailsButtons = document.querySelectorAll('.view-details-btn')
  viewDetailsButtons.forEach(button => {
    button.addEventListener('click', function() {
      // Get course data from button attributes
      const courseCode = this.getAttribute('data-course-code')
      const courseTitle = this.getAttribute('data-course-title')
      const courseDescription = this.getAttribute('data-course-description')
      const courseUnit = this.getAttribute('data-course-unit')
      const department = this.getAttribute('data-department')
      const level = this.getAttribute('data-level')
      const semester = this.getAttribute('data-semester')
      const enrolledStudents = this.getAttribute('data-enrolled-students')
      
      // Populate modal with course data
      document.getElementById('courseDetailsTitle').textContent = `${courseCode} - Course Details`
      document.getElementById('modalCourseCode').textContent = courseCode
      document.getElementById('modalCourseTitle').textContent = courseTitle
      document.getElementById('modalDepartment').textContent = department
      document.getElementById('modalLevel').textContent = level
      document.getElementById('modalUnits').textContent = courseUnit
      document.getElementById('modalSemester').textContent = semester
      document.getElementById('modalEnrolledStudents').textContent = `${enrolledStudents} students`
      document.getElementById('modalCourseDescription').textContent = courseDescription || 'No description available'
    })
  })

  // Manage course modal functionality
  const manageCourseButtons = document.querySelectorAll('.manage-course-btn')
  manageCourseButtons.forEach(button => {
    button.addEventListener('click', function() {
      const courseCode = this.getAttribute('data-course-code')
      const courseTitle = this.getAttribute('data-course-title')
      
      // Update modal title
      document.getElementById('manageCourseTitle').textContent = `${courseCode} - Course Management`
    })
  })

  // Course management action buttons
  document.getElementById('createAssignmentForCourse')?.addEventListener('click', function() {
    bootstrap.Modal.getInstance(document.getElementById('manageCourseModal')).hide()
    document.querySelector('[data-section="assignments"]').click()
  })

  document.getElementById('uploadMaterialForCourse')?.addEventListener('click', function() {
    bootstrap.Modal.getInstance(document.getElementById('manageCourseModal')).hide()
    document.querySelector('[data-section="materials"]').click()
  })

  document.getElementById('viewCourseAnalytics')?.addEventListener('click', function() {
    bootstrap.Modal.getInstance(document.getElementById('manageCourseModal')).hide()
    document.querySelector('[data-section="records"]').click()
  })

  document.getElementById('gradeStudentsForCourse')?.addEventListener('click', function() {
    bootstrap.Modal.getInstance(document.getElementById('manageCourseModal')).hide()
    document.querySelector('[data-section="grading"]').click()
  })

  // View students modal functionality
  const viewStudentsButtons = document.querySelectorAll('.view-students-btn')
  viewStudentsButtons.forEach(button => {
    button.addEventListener('click', function() {
      const courseId = this.getAttribute('data-course-id')
      const courseCode = this.getAttribute('data-course-code')
      const courseTitle = this.getAttribute('data-course-title')
      
      document.getElementById('viewStudentsTitle').textContent = `${courseCode} - Enrolled Students`
      
      // Fetch students for this course
      fetchCourseStudents(courseCode)
    })
  })

  // Assignment details modal functionality
  const viewAssignmentButtons = document.querySelectorAll('.view-assignment-btn')
  viewAssignmentButtons.forEach(button => {
    button.addEventListener('click', function() {
      const title = this.getAttribute('data-title')
      const description = this.getAttribute('data-description')
      const courseCode = this.getAttribute('data-course-code')
      const courseTitle = this.getAttribute('data-course-title')
      const maxScore = this.getAttribute('data-max-score')
      const dueDate = this.getAttribute('data-due-date')
      const academicYear = this.getAttribute('data-academic-year')
      const semester = this.getAttribute('data-semester')
      const isActive = this.getAttribute('data-is-active')
      const createdAt = this.getAttribute('data-created-at')
      
      // Populate modal with assignment data
      document.getElementById('assignmentDetailsTitle').textContent = `${title} - Details`
      document.getElementById('modalAssignmentTitle').textContent = title
      document.getElementById('modalAssignmentCourse').textContent = `${courseCode} - ${courseTitle}`
      document.getElementById('modalAssignmentMaxScore').textContent = `${maxScore} points`
      document.getElementById('modalAssignmentDueDate').textContent = new Date(dueDate).toLocaleString()
      document.getElementById('modalAssignmentYear').textContent = academicYear
      document.getElementById('modalAssignmentSemester').textContent = semester
      document.getElementById('modalAssignmentStatus').innerHTML = `<span class="badge ${isActive == '1' ? 'bg-success' : 'bg-secondary'}">${isActive == '1' ? 'Active' : 'Inactive'}</span>`
      document.getElementById('modalAssignmentCreated').textContent = new Date(createdAt).toLocaleString()
      document.getElementById('modalAssignmentDescription').textContent = description
    })
  })

  // Edit assignment modal functionality
  const editAssignmentButtons = document.querySelectorAll('.edit-assignment-btn')
  editAssignmentButtons.forEach(button => {
    button.addEventListener('click', function() {
      const assignmentId = this.getAttribute('data-assignment-id')
      const title = this.getAttribute('data-title')
      const description = this.getAttribute('data-description')
      const maxScore = this.getAttribute('data-max-score')
      const dueDate = this.getAttribute('data-due-date')
      const academicYear = this.getAttribute('data-academic-year')
      const semester = this.getAttribute('data-semester')
      
      // Populate edit form
      document.getElementById('editAssignmentId').value = assignmentId
      document.getElementById('editAssignmentTitle').value = title
      document.getElementById('editAssignmentDescription').value = description
      document.getElementById('editAssignmentPoints').value = maxScore
      document.getElementById('editAssignmentDueDate').value = dueDate
      document.getElementById('editAcademicYear').value = academicYear
      document.getElementById('editAssignmentSemester').value = semester
    })
  })

  // Update assignment functionality
  const updateAssignmentBtn = document.getElementById('updateAssignmentBtn')
  if (updateAssignmentBtn) {
    updateAssignmentBtn.addEventListener('click', function() {
      const form = document.getElementById('editAssignmentForm')
      const formData = new FormData(form)
      
      if (!form.checkValidity()) {
        form.reportValidity()
        return
      }
      
      this.textContent = 'Updating...'
      this.disabled = true
      
      fetch('../PHP/update_assignment.php', {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          const modal = bootstrap.Modal.getInstance(document.getElementById('editAssignmentModal'))
          modal.hide()
          showNotification(data.message, 'success')
          location.reload()
        } else {
          showNotification(data.message || 'Failed to update assignment', 'danger')
        }
      })
      .catch(error => {
        console.error('Error:', error)
        showNotification('An error occurred while updating the assignment', 'danger')
      })
      .finally(() => {
        this.textContent = 'Update Assignment'
        this.disabled = false
      })
    })
  }

  // Delete assignment functionality
  const deleteAssignmentButtons = document.querySelectorAll('.delete-assignment-btn')
  deleteAssignmentButtons.forEach(button => {
    button.addEventListener('click', function() {
      const assignmentId = this.getAttribute('data-assignment-id')
      const title = this.getAttribute('data-title')
      
      if (confirm(`Are you sure you want to delete the assignment "${title}"? This action cannot be undone.`)) {
        const originalText = this.innerHTML
        this.innerHTML = '<i class="fas fa-spinner fa-spin"></i>'
        this.disabled = true
        
        fetch('../PHP/delete_assignment.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({ assignment_id: assignmentId })
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            showNotification(data.message, 'success')
            location.reload()
          } else {
            showNotification(data.message || 'Failed to delete assignment', 'danger')
          }
        })
        .catch(error => {
          console.error('Error:', error)
          showNotification('An error occurred while deleting the assignment', 'danger')
        })
        .finally(() => {
          this.innerHTML = originalText
          this.disabled = false
        })
      }
    })
  })



  // View submissions modal functionality
  const viewSubmissionsButtons = document.querySelectorAll('.view-submissions-btn')
  viewSubmissionsButtons.forEach(button => {
    button.addEventListener('click', function() {
      const assignmentId = this.getAttribute('data-assignment-id')
      const title = this.getAttribute('data-title')
      const course = this.getAttribute('data-course')
      
      document.getElementById('submissionAssignmentTitle').textContent = title
      document.getElementById('submissionCourse').textContent = course
      
      // Fetch submissions for this assignment
      fetchAssignmentSubmissions(assignmentId)
    })
  })

  // Submission search functionality
  const submissionSearchInput = document.getElementById('submissionSearch')
  if (submissionSearchInput) {
    submissionSearchInput.addEventListener('input', function() {
      const searchTerm = this.value.toLowerCase()
      const rows = document.querySelectorAll('#submissionsTable tbody tr')
      
      rows.forEach(row => {
        const text = row.textContent.toLowerCase()
        row.style.display = text.includes(searchTerm) ? '' : 'none'
      })
    })
  }

  // Student search functionality
  const studentSearchInput = document.getElementById('studentSearch')
  if (studentSearchInput) {
    studentSearchInput.addEventListener('input', function() {
      const searchTerm = this.value.toLowerCase()
      const rows = document.querySelectorAll('#studentsTable tbody tr')
      
      rows.forEach(row => {
        const text = row.textContent.toLowerCase()
        row.style.display = text.includes(searchTerm) ? '' : 'none'
      })
    })
  }

  // Save all grades functionality
  const saveAllButtons = document.querySelectorAll('.save-all-grades-btn')
  saveAllButtons.forEach(button => {
    button.addEventListener('click', function() {
      const courseId = this.getAttribute('data-course-id')
      const courseCard = this.closest('.course-grading-card')
      const rows = courseCard.querySelectorAll('tbody tr')
      
      let savedCount = 0
      let totalRows = rows.length
      
      this.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Saving All...'
      this.disabled = true
      
      rows.forEach(row => {
        const saveBtn = row.querySelector('.save-grade-btn')
        if (saveBtn) {
          saveBtn.click()
          savedCount++
        }
      })
      
      setTimeout(() => {
        this.innerHTML = '<i class="fas fa-save me-2"></i>Save All Grades'
        this.disabled = false
        showNotification(`Processed ${savedCount} student grades`, 'info')
      }, 3000)
    })
  })

  // Initialize tooltips (if Bootstrap tooltips are needed)
  const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
  const tooltipList = tooltipTriggerList.map((tooltipTriggerEl) => new bootstrap.Tooltip(tooltipTriggerEl))

  // Function to fetch assignment submissions
  function fetchAssignmentSubmissions(assignmentId) {
    const container = document.getElementById('submissionsTableContainer')
    
    // Show loading state
    container.innerHTML = `
      <div class="text-center py-4">
        <div class="spinner-border" role="status">
          <span class="visually-hidden">Loading...</span>
        </div>
        <p class="mt-2">Loading submissions...</p>
      </div>
    `
    
    // Fetch submissions from server
    fetch(`../PHP/get_assignment_submissions.php?assignment_id=${assignmentId}`)
      .then(response => response.json())
      .then(data => {
        if (data.error) {
          container.innerHTML = `
            <div class="alert alert-danger" role="alert">
              <i class="fas fa-exclamation-triangle me-2"></i>
              ${data.error}
            </div>
          `
          return
        }
        
        const submissions = data.submissions
        document.getElementById('totalSubmissions').textContent = data.total_submissions
        
        if (submissions.length === 0) {
          container.innerHTML = `
            <div class="text-center py-4">
              <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
              <p class="text-muted">No submissions yet for this assignment.</p>
            </div>
          `
        } else {
          container.innerHTML = `
            <div class="table-responsive">
              <table class="table table-striped" id="submissionsTable">
                <thead>
                  <tr>
                    <th>S/N</th>
                    <th>Student Name</th>
                    <th>Matric No</th>
                    <th>Email</th>
                    <th>File Name</th>
                    <th>Submitted At</th>
                    <th>Score</th>
                    <th>Status</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  ${submissions.map((submission, index) => `
                    <tr>
                      <td>${index + 1}</td>
                      <td><strong>${submission.student_name}</strong></td>
                      <td>${submission.matric_no}</td>
                      <td>${submission.student_email}</td>
                      <td>
                        <i class="fas fa-file me-2"></i>
                        ${submission.file_name}
                      </td>
                      <td>${submission.submitted_at_formatted}</td>
                      <td>
                        <div class="input-group input-group-sm" style="width: 120px;">
                          <input type="number" class="form-control" data-sub-id="${submission.sub_id}"
                                 placeholder="0-100" min="0" max="100" step="0.1"
                                 value="${submission.score_received || ''}">
                          <button class="btn btn-outline-success btn-sm"
                                  onclick="saveAssignmentGrade('${submission.sub_id}', this)"
                                  title="Save Grade">
                            <i class="fas fa-save"></i>
                          </button>
                        </div>
                      </td>
                      <td>
                        ${submission.score_received !== null ?
                          '<small class="text-success ms-2"><i class="fas fa-check-circle"></i> Graded</small>' :
                          '<small class="text-warning ms-2"><i class="fas fa-clock"></i> Pending</small>'}
                      </td>
                      <td>
                        <button class="btn btn-sm btn-outline-primary me-1"
                                onclick="downloadSubmission('${submission.sub_id}', '${submission.file_name}')"
                                title="Download">
                          <i class="fas fa-download"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-info"
                                onclick="viewSubmission('${submission.sub_id}')"
                                title="View">
                          <i class="fas fa-eye"></i>
                        </button>
                      </td>
                    </tr>
                  `).join('')}
                </tbody>
              </table>
            </div>
          `
        }
      })
      .catch(error => {
        console.error('Error fetching submissions:', error)
        container.innerHTML = `
          <div class="alert alert-danger" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            Error loading submissions. Please try again.
          </div>
        `
      })
  }

  // Function to load recent activities
  function loadRecentActivities() {
    const container = document.getElementById('recentActivitiesContainer')
    
    fetch('../PHP/get_lecturer_recent_activities.php')
      .then(response => response.json())
      .then(data => {
        if (data.error) {
          container.innerHTML = `
            <div class="alert alert-warning" role="alert">
              <i class="fas fa-exclamation-triangle me-2"></i>
              ${data.error}
            </div>
          `
          return
        }
        
        if (data.length === 0) {
          container.innerHTML = `
            <div class="text-center py-4">
              <i class="fas fa-history fa-3x text-muted mb-3"></i>
              <p class="text-muted">No recent activities to display.</p>
            </div>
          `
        } else {
          container.innerHTML = `
            <div class="timeline">
              ${data.map(activity => `
                <div class="timeline-item">
                  <div class="timeline-marker">
                    <i class="${activity.icon}"></i>
                  </div>
                  <div class="timeline-content">
                    <p class="timeline-text">${activity.description}</p>
                    <small class="text-muted">${formatActivityTime(activity.timestamp)}</small>
                  </div>
                </div>
              `).join('')}
            </div>
          `
        }
      })
      .catch(error => {
        console.error('Error loading recent activities:', error)
        container.innerHTML = `
          <div class="alert alert-danger" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            Error loading recent activities. Please try again.
          </div>
        `
      })
  }

  // Function to fetch course students
  function fetchCourseStudents(courseCode) {
    const container = document.getElementById('studentsTableContainer')
    
    // Show loading state
    container.innerHTML = `
      <div class="text-center py-4">
        <div class="spinner-border" role="status">
          <span class="visually-hidden">Loading...</span>
        </div>
        <p class="mt-2">Loading students...</p>
      </div>
    `
    
    // Fetch students from server
    fetch(`../PHP/get_course_students.php?course_code=${courseCode}`)
      .then(response => response.json())
      .then(data => {
        if (data.error) {
          container.innerHTML = `
            <div class="alert alert-danger" role="alert">
              <i class="fas fa-exclamation-triangle me-2"></i>
              ${data.error}
            </div>
          `
          return
        }
        
        const students = data.students || data
        
        if (students.length === 0) {
          container.innerHTML = `
            <div class="text-center py-4">
              <i class="fas fa-users fa-3x text-muted mb-3"></i>
              <p class="text-muted">No students enrolled in this course yet.</p>
            </div>
          `
        } else {
          container.innerHTML = `
            <div class="table-responsive">
              <table class="table table-striped" id="studentsTable">
                <thead>
                  <tr>
                    <th>S/N</th>
                    <th>Matric No</th>
                    <th>Student Name</th>
                    <th>Department</th>
                    <th>Level</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Enrollment Date</th>
                  </tr>
                </thead>
                <tbody>
                  ${students.map((student, index) => `
                    <tr>
                      <td>${index + 1}</td>
                      <td><strong>${student.Matric_No}</strong></td>
                      <td>${student.first_name} ${student.last_name}</td>
                      <td>${student.Department}</td>
                      <td>${student.Level}</td>
                      <td>${student.email}</td>
                      <td>${student.Phone_Num || 'N/A'}</td>
                      <td>${new Date(student.enrollment_date).toLocaleDateString()}</td>
                    </tr>
                  `).join('')}
                </tbody>
              </table>
            </div>
          `
        }
      })
      .catch(error => {
        console.error('Error fetching students:', error)
        container.innerHTML = `
          <div class="alert alert-danger" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            Error loading students. Please try again.
          </div>
        `
      })
  }

  // Performance chart placeholder (you can integrate Chart.js here)
  const performanceChart = document.getElementById("performanceChart")
  if (performanceChart) {
    const ctx = performanceChart.getContext("2d")

    // Simple placeholder chart
    ctx.fillStyle = "#e3e6f0"
    ctx.fillRect(0, 0, performanceChart.width, performanceChart.height)

    ctx.fillStyle = "#4e73df"
    ctx.font = "16px Arial"
    ctx.textAlign = "center"
    ctx.fillText("Performance Chart Placeholder", performanceChart.width / 2, performanceChart.height / 2)
    ctx.fillText("(Integrate Chart.js for actual charts)", performanceChart.width / 2, performanceChart.height / 2 + 25)
  }
})

// Utility functions
function showNotification(message, type = "success") {
  // Simple notification system (you can enhance this with a proper toast library)
  const notification = document.createElement("div")
  notification.className = `alert alert-${type} alert-dismissible fade show position-fixed`
  notification.style.top = "20px"
  notification.style.right = "20px"
  notification.style.zIndex = "9999"
  notification.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `

  document.body.appendChild(notification)

  // Auto remove after 5 seconds
  setTimeout(() => {
    if (notification.parentNode) {
      notification.parentNode.removeChild(notification)
    }
  }, 5000)
}

function formatDate(date) {
  return new Intl.DateTimeFormat("en-US", {
    year: "numeric",
    month: "short",
    day: "numeric",
    hour: "2-digit",
    minute: "2-digit",
  }).format(date)
}

function calculateGPA(grades) {
  const gradePoints = {
    "A+": 4.0,
    A: 4.0,
    "A-": 3.7,
    "B+": 3.3,
    B: 3.0,
    "B-": 2.7,
    "C+": 2.3,
    C: 2.0,
    "C-": 1.7,
    "D+": 1.3,
    D: 1.0,
    F: 0.0,
  }

  const totalPoints = grades.reduce((sum, grade) => sum + (gradePoints[grade] || 0), 0)
  return (totalPoints / grades.length).toFixed(2)
}

// Function to download submission file
function downloadSubmission(filePath, fileName) {
  // Create a temporary link element
  const link = document.createElement('a')
  link.href = filePath
  link.download = fileName
  link.target = '_blank'
  
  // Trigger download
  document.body.appendChild(link)
  link.click()
  document.body.removeChild(link)
}

// Function to view submission file
function viewSubmission(filePath) {
  // Open file in new tab
  window.open(filePath, '_blank')
}

// Function to save assignment grade
function saveAssignmentGrade(subId, buttonElement) {
  const row = buttonElement.closest('tr')
  const scoreInput = row.querySelector(`input[data-sub-id="${subId}"]`)
  const score = scoreInput.value.trim()

  if (!score) {
    showNotification('Please enter a score', 'warning')
    return
  }

  const scoreValue = parseFloat(score)
  if (scoreValue < 0 || scoreValue > 100) {
    showNotification('Score must be between 0 and 100', 'warning')
    return
  }

  // Show loading state
  const originalHtml = buttonElement.innerHTML
  buttonElement.innerHTML = '<i class="fas fa-spinner fa-spin"></i>'
  buttonElement.disabled = true

  // Prepare form data
  const formData = new FormData()
  formData.append('sub_id', subId)
  formData.append('score', scoreValue)

  fetch('../PHP/save_assignment_grade.php', {
    method: 'POST',
    body: formData
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      showNotification(data.message, 'success')
      // Update the status text
      const statusElement = row.querySelector('small.ms-2')
      statusElement.className = 'text-success ms-2'
      statusElement.textContent = 'Graded'
    } else {
      showNotification(data.message || 'Failed to save grade', 'danger')
    }
  })
  .catch(error => {
    console.error('Error:', error)
    showNotification('An error occurred while saving the grade', 'danger')
  })
  .finally(() => {
    // Reset button
    buttonElement.innerHTML = originalHtml
    buttonElement.disabled = false
  })
}

// Function to load course materials
function loadCourseMaterials() {
  const container = document.getElementById('materialsContainer')
  
  // Show loading state
  container.innerHTML = `
    <div class="text-center py-4">
      <div class="spinner-border" role="status">
        <span class="visually-hidden">Loading...</span>
      </div>
      <p class="mt-2">Loading materials...</p>
    </div>
  `
  
  // Fetch materials from server
  fetch('../PHP/get_lecturer_course_materials.php')
    .then(response => response.json())
    .then(data => {
      if (data.error) {
        container.innerHTML = `
          <div class="alert alert-danger" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            ${data.error}
          </div>
        `
        return
      }
      
      const courses = Object.values(data)
      
      if (courses.length === 0) {
        container.innerHTML = `
          <div class="text-center py-4">
            <i class="fas fa-file-upload fa-3x text-muted mb-3"></i>
            <p class="text-muted">No course materials uploaded yet.</p>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadMaterialModal">
              <i class="fas fa-upload me-2"></i>Upload Your First Material
            </button>
          </div>
        `
      } else {
        let materialsHtml = ''
        
        courses.forEach(course => {
          materialsHtml += `
            <div class="card shadow mb-4">
              <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                  <i class="fas fa-book me-2"></i>
                  ${course.course_code} - ${course.course_title}
                </h5>
                <span class="badge bg-primary">${course.materials.length} materials</span>
              </div>
              <div class="card-body">
                <div class="row">
                  ${course.materials.map(material => `
                    <div class="col-md-6 col-lg-4 mb-3">
                      <div class="card h-100 border-light">
                        <div class="card-body">
                          <div class="d-flex justify-content-between align-items-start mb-2">
                            <h6 class="card-title mb-0">${material.title}</h6>
                            <span class="badge ${material.is_published ? 'bg-success' : 'bg-secondary'} ms-2">
                              ${material.is_published ? 'Published' : 'Draft'}
                            </span>
                          </div>
                          <p class="card-text text-muted small">
                            ${material.description || 'No description'}
                          </p>
                          <div class="mb-2">
                            <small class="text-muted">
                              <i class="fas fa-tag me-1"></i>
                              ${material.file_type.charAt(0).toUpperCase() + material.file_type.slice(1)}
                            </small>
                          </div>
                          <div class="mb-3">
                            <small class="text-muted">
                              <i class="fas fa-calendar me-1"></i>
                              ${new Date(material.created_at).toLocaleDateString()}
                            </small>
                          </div>
                          <div class="btn-group w-100" role="group">
                            <button class="btn btn-sm btn-outline-primary" 
                                    onclick="window.open('../${material.file_path_url}', '_blank')" 
                                    title="View/Download">
                              <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger delete-material-btn" 
                                    data-material-id="${material.material_id}"
                                    data-title="${material.title}"
                                    title="Delete">
                              <i class="fas fa-trash"></i>
                            </button>
                          </div>
                        </div>
                      </div>
                    </div>
                  `).join('')}
                </div>
              </div>
            </div>
          `
        })
        
        container.innerHTML = materialsHtml
        
        // Add delete functionality
        const deleteButtons = container.querySelectorAll('.delete-material-btn')
        deleteButtons.forEach(button => {
          button.addEventListener('click', function() {
            const materialId = this.getAttribute('data-material-id')
            const title = this.getAttribute('data-title')
            
            if (confirm(`Are you sure you want to delete "${title}"? This action cannot be undone.`)) {
              deleteMaterial(materialId, this)
            }
          })
        })
      }
    })
    .catch(error => {
      console.error('Error loading materials:', error)
      container.innerHTML = `
        <div class="alert alert-danger" role="alert">
          <i class="fas fa-exclamation-triangle me-2"></i>
          Error loading materials. Please try again.
        </div>
      `
    })
}

// Function to delete material
function deleteMaterial(materialId, buttonElement) {
  const originalHtml = buttonElement.innerHTML
  buttonElement.innerHTML = '<i class="fas fa-spinner fa-spin"></i>'
  buttonElement.disabled = true
  
  const formData = new FormData()
  formData.append('material_id', materialId)
  
  fetch('../PHP/delete_material.php', {
    method: 'POST',
    body: formData
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      showNotification(data.message, 'success')
      loadCourseMaterials() // Reload materials
    } else {
      showNotification(data.message || 'Failed to delete material', 'danger')
    }
  })
  .catch(error => {
    console.error('Error:', error)
    showNotification('An error occurred while deleting the material', 'danger')
  })
  .finally(() => {
    buttonElement.innerHTML = originalHtml
    buttonElement.disabled = false
  })
}

// Function to format activity timestamp
function formatActivityTime(timestamp) {
  const now = new Date()
  const activityTime = new Date(timestamp)
  const diffInSeconds = Math.floor((now - activityTime) / 1000)
  
  if (diffInSeconds < 60) {
    return 'Just now'
  } else if (diffInSeconds < 3600) {
    const minutes = Math.floor(diffInSeconds / 60)
    return `${minutes} minute${minutes > 1 ? 's' : ''} ago`
  } else if (diffInSeconds < 86400) {
    const hours = Math.floor(diffInSeconds / 3600)
    return `${hours} hour${hours > 1 ? 's' : ''} ago`
  } else if (diffInSeconds < 604800) {
    const days = Math.floor(diffInSeconds / 86400)
    return `${days} day${days > 1 ? 's' : ''} ago`
  } else {
    return activityTime.toLocaleDateString()
  }
}

// Function to refresh dashboard statistics
function refreshDashboardStats() {
  fetch('../PHP/refresh_lecturer_stats.php')
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        // Update the stats cards
        const stats = data.stats
        
        // Update total courses
        const totalCoursesElement = document.querySelector('.h5.mb-0.font-weight-bold.text-gray-800')
        if (totalCoursesElement) {
          totalCoursesElement.textContent = stats.total_courses
        }
        
        // Update all stats cards
        const statsCards = document.querySelectorAll('.h5.mb-0.font-weight-bold.text-gray-800')
        if (statsCards.length >= 4) {
          statsCards[0].textContent = stats.total_courses
          statsCards[1].textContent = stats.total_students
          statsCards[2].textContent = stats.pending_grades
          statsCards[3].textContent = stats.assignments_due
        }
      }
    })
    .catch(error => {
      console.error('Error refreshing stats:', error)
    })
}