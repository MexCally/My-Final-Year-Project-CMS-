// Sidebar toggle functionality
document.getElementById("sidebarCollapse").addEventListener("click", () => {
    document.getElementById("sidebar").classList.toggle("active")
    document.getElementById("content").classList.toggle("active")
  })
  
  // Section navigation function
  function showSection(sectionName) {
    // Hide all sections
    const sections = document.querySelectorAll(".section")
    sections.forEach((section) => {
      section.classList.remove("active")
    })
  
    // Show selected section
    const targetSection = document.getElementById(sectionName + "-section")
    if (targetSection) {
      targetSection.classList.add("active")
    }
  
    // Update sidebar active state
    const sidebarLinks = document.querySelectorAll(".sidebar ul li")
    sidebarLinks.forEach((link) => {
      link.classList.remove("active")
    })
  
    // Find and activate the corresponding sidebar link using data-section attribute
    const activeLink = document.querySelector(`a[data-section="${sectionName}"]`)
    if (activeLink) {
      activeLink.parentElement.classList.add("active")
    }
  }
  
document.addEventListener("DOMContentLoaded", () => {
    // Attach click event listeners to all navigation links with data-section attribute
    document.querySelectorAll("a[data-section], button[data-section]").forEach((link) => {
      link.addEventListener("click", (e) => {
        e.preventDefault()
        const section = link.getAttribute("data-section")
        showSection(section)
      })
    })
  
    document.querySelectorAll(".view-student-btn").forEach((btn) => {
      btn.addEventListener("click", (e) => {
        e.preventDefault()
        const studentId = btn.getAttribute("data-student-id")
        populateStudentViewModal(studentId)
      })
    })
  
    document.querySelectorAll(".edit-student-btn").forEach((btn) => {
      btn.addEventListener("click", (e) => {
        e.preventDefault()
        const studentId = btn.getAttribute("data-student-id")
        populateStudentEditModal(studentId)
      })
    })

    document.querySelectorAll(".delete-student-btn").forEach((btn) => {
      btn.addEventListener("click", (e) => {
        e.preventDefault()
        const studentId = btn.getAttribute("data-student-id")
        if (confirm("Are you sure you want to delete this student? This action cannot be undone.")) {
          deleteStudent(studentId)
        }
      })
    })


    // Search and Filter functionality
    const studentSearchBtn = document.getElementById("studentSearchBtn")
    if (studentSearchBtn) {
      studentSearchBtn.addEventListener("click", performStudentSearch)
    }

    const studentSearchInput = document.getElementById("studentSearchInput")
    if (studentSearchInput) {
      studentSearchInput.addEventListener("keypress", (e) => {
        if (e.key === "Enter") {
          performStudentSearch()
        }
      })
    }

    const departmentFilter = document.getElementById("departmentFilter")
    if (departmentFilter) {
      departmentFilter.addEventListener("change", performStudentSearch)
    }

    const statusFilter = document.getElementById("statusFilter")
    if (statusFilter) {
      statusFilter.addEventListener("change", performStudentSearch)
    }

    // Course Search and Filter functionality
    const courseSearchBtn = document.getElementById("courseSearchBtn")
    if (courseSearchBtn) {
      courseSearchBtn.addEventListener("click", performCourseSearch)
    }

    const courseSearchInput = document.getElementById("courseSearchInput")
    const courseSearchClear = document.getElementById("courseSearchClear")
    
    if (courseSearchInput) {
      // Search on Enter key
      courseSearchInput.addEventListener("keypress", (e) => {
        if (e.key === "Enter") {
          performCourseSearch()
        }
      })
      // Real-time search as user types (with debounce)
      let searchTimeout
      courseSearchInput.addEventListener("input", (e) => {
        // Show/hide clear button
        if (courseSearchClear) {
          courseSearchClear.style.display = e.target.value.trim() ? 'block' : 'none'
        }
        
        clearTimeout(searchTimeout)
        searchTimeout = setTimeout(() => {
          performCourseSearch()
        }, 300) // Wait 300ms after user stops typing
      })
    }

    // Clear search functionality
    if (courseSearchClear) {
      courseSearchClear.addEventListener("click", () => {
        if (courseSearchInput) {
          courseSearchInput.value = ''
          courseSearchClear.style.display = 'none'
          performCourseSearch() // Reload all courses
        }
      })
    }

    const courseDepartmentFilter = document.getElementById("courseDepartmentFilter")
    if (courseDepartmentFilter) {
      courseDepartmentFilter.addEventListener("change", performCourseSearch)
    }

    const courseLevelFilter = document.getElementById("courseLevelFilter")
    if (courseLevelFilter) {
      courseLevelFilter.addEventListener("change", performCourseSearch)
    }

    // Lecturer Search and Filter functionality
    const lecturerSearchBtn = document.getElementById("lecturerSearchBtn")
    if (lecturerSearchBtn) {
      lecturerSearchBtn.addEventListener("click", performLecturerSearch)
    }

    const lecturerSearchInput = document.getElementById("lecturerSearchInput")
    if (lecturerSearchInput) {
      lecturerSearchInput.addEventListener("keypress", (e) => {
        if (e.key === "Enter") {
          performLecturerSearch()
        }
      })
    }

    const lecturerDepartmentFilter = document.getElementById("lecturerDepartmentFilter")
    if (lecturerDepartmentFilter) {
      lecturerDepartmentFilter.addEventListener("change", performLecturerSearch)
    }

    document.querySelectorAll(".edit-course-btn").forEach((btn) => {
      btn.addEventListener("click", (e) => {
        e.preventDefault()
        const courseCode = btn.getAttribute("data-course-code")
        populateCourseEditModal(courseCode)
      })
    })
  
    document.querySelectorAll(".edit-lecturer-btn").forEach((btn) => {
      btn.addEventListener("click", (e) => {
        e.preventDefault()
        const lecturerId = btn.getAttribute("data-lecturer-id")
        populateLecturerEditModal(lecturerId)
      })
    })
  
    // Registration approval/decline buttons are attached dynamically in populateRegistrationsTable
  
    // Grades functionality
    const loadGradesBtn = document.getElementById("loadGradesBtn")
    if (loadGradesBtn) {
      loadGradesBtn.addEventListener("click", loadGrades)
    }

    const gradesSearchBtn = document.getElementById("gradesSearchBtn")
    if (gradesSearchBtn) {
      gradesSearchBtn.addEventListener("click", performGradesSearch)
    }

    const gradesSearchInput = document.getElementById("gradesSearchInput")
    if (gradesSearchInput) {
      gradesSearchInput.addEventListener("keypress", (e) => {
        if (e.key === "Enter") {
          performGradesSearch()
        }
      })
    }

    const gradesCourseFilter = document.getElementById("gradesCourseFilter")
    if (gradesCourseFilter) {
      gradesCourseFilter.addEventListener("change", performGradesSearch)
    }

    const gradesDepartmentFilter = document.getElementById("gradesDepartmentFilter")
    if (gradesDepartmentFilter) {
      gradesDepartmentFilter.addEventListener("change", performGradesSearch)
    }

    document.querySelectorAll(".edit-course-btn").forEach((btn) => {
      btn.addEventListener("click", (e) => {
        e.preventDefault()
        const courseCode = btn.getAttribute("data-course-code")
        populateCourseEditModal(courseCode)
      })
    })
  
    document.querySelectorAll(".edit-lecturer-btn").forEach((btn) => {
      btn.addEventListener("click", (e) => {
        e.preventDefault()
        const lecturerId = btn.getAttribute("data-lecturer-id")
        populateLecturerEditModal(lecturerId)
      })
    })
  
    // Registration approval/decline buttons are attached dynamically in populateRegistrationsTable
  
    const confirmApproveBtn = document.getElementById("confirmApproveBtn")
    if (confirmApproveBtn) {
      confirmApproveBtn.addEventListener("click", approveRegistration)
    }
  
    const confirmDeclineBtn = document.getElementById("confirmDeclineBtn")
    if (confirmDeclineBtn) {
      confirmDeclineBtn.addEventListener("click", declineRegistration)
    }
  
    const saveStudentBtn = document.getElementById("saveStudentBtn")
    if (saveStudentBtn) {
      saveStudentBtn.addEventListener("click", saveStudentChanges)
    }
  
    const saveCourseBtn = document.getElementById("saveCourseBtn")
    if (saveCourseBtn) {
      saveCourseBtn.addEventListener("click", saveCourseChanges)
    }
  
    const saveLecturerBtn = document.getElementById("saveLecturerBtn")
    if (saveLecturerBtn) {
      saveLecturerBtn.addEventListener("click", saveLecturerChanges)
    }

    // Add Lecturer Form Handler
    const addLecturerBtn = document.getElementById("addLecturerBtn")
    const addLecturerForm = document.getElementById("addLecturerForm")
    if (addLecturerBtn && addLecturerForm) {
      addLecturerBtn.addEventListener("click", () => {
        handleAddLecturerForm(addLecturerForm)
      })
    }
  
    document.querySelectorAll("[data-settings-tab]").forEach((btn) => {
      btn.addEventListener("click", (e) => {
        e.preventDefault()
        const tabName = btn.getAttribute("data-settings-tab")
        switchSettingsTab(tabName)
      })
    })
  
    const saveProfileBtn = document.getElementById("saveProfileBtn")
    if (saveProfileBtn) {
      saveProfileBtn.addEventListener("click", saveProfileSettings)
    }
  
    const changePasswordBtn = document.getElementById("changePasswordBtn")
    if (changePasswordBtn) {
      changePasswordBtn.addEventListener("click", changePassword)
    }
  
    const saveNotificationsBtn = document.getElementById("saveNotificationsBtn")
    if (saveNotificationsBtn) {
      saveNotificationsBtn.addEventListener("click", saveNotificationPreferences)
    }
  
    const saveSystemPrefsBtn = document.getElementById("saveSystemPrefsBtn")
    if (saveSystemPrefsBtn) {
      saveSystemPrefsBtn.addEventListener("click", saveSystemPreferences)
    }
  
    const uploadPhotoBtn = document.getElementById("uploadPhotoBtn")
    if (uploadPhotoBtn) {
      uploadPhotoBtn.addEventListener("click", uploadProfilePhoto)
    }
  
    const photoInput = document.getElementById("photoInput")
    if (photoInput) {
      photoInput.addEventListener("change", previewPhoto)
    }
  
    // Add Student Form Handler
    const addStudentForm = document.getElementById("addStudentForm")
    if (addStudentForm) {
      addStudentForm.addEventListener("submit", handleAddStudent)
    }

    const addStudentBtn = document.getElementById("addStudentBtn")
    if (addStudentBtn) {
      addStudentBtn.addEventListener("click", () => {
        const form = document.getElementById("addStudentForm")
        if (form) {
          form.dispatchEvent(new Event('submit'))
        }
      })
    }

    // Add Course Form Handler
    const addCourseForm = document.getElementById("addCourseForm")
    if (addCourseForm) {
      addCourseForm.addEventListener("submit", handleAddCourse)
    }

    const addCourseBtn = document.getElementById("addCourseBtn")
    if (addCourseBtn) {
      addCourseBtn.addEventListener("click", () => {
        const form = document.getElementById("addCourseForm")
        if (form) {
          form.dispatchEvent(new Event('submit'))
        }
      })
    }

    // Load lecturers into course modal when it opens
    const addCourseModal = document.getElementById("addCourseModal")
    if (addCourseModal) {
      addCourseModal.addEventListener('show.bs.modal', function () {
        loadLecturersForCourse()
      })
    }
  
    // Load courses for grades filter
    loadCoursesForGradesFilter()

    // Load data for report selects
    loadStudentsForReports()
    loadCoursesForReports()

    // Report generation event listeners
    const generateStudentReportBtn = document.getElementById("generateStudentReportBtn")
    if (generateStudentReportBtn) {
      generateStudentReportBtn.addEventListener("click", generateStudentReport)
    }

    const generateCourseReportBtn = document.getElementById("generateCourseReportBtn")
    if (generateCourseReportBtn) {
      generateCourseReportBtn.addEventListener("click", generateCourseReport)
    }

    const generateDepartmentReportBtn = document.getElementById("generateDepartmentReportBtn")
    if (generateDepartmentReportBtn) {
      generateDepartmentReportBtn.addEventListener("click", generateDepartmentReport)
    }

    const generateSystemReportBtn = document.getElementById("generateSystemReportBtn")
    if (generateSystemReportBtn) {
      generateSystemReportBtn.addEventListener("click", generateSystemReport)
    }

    // Initialize dashboard as active section
    showSection("dashboard")

    // Load initial data
    loadDashboardStats()
    loadRecentActivities()
    loadStudents()
    loadCourses()
    loadLecturers()
    loadPendingRegistrations()
  })
  
  function switchSettingsTab(tabName) {
    // Hide all settings tabs
    document.querySelectorAll(".settings-tab").forEach((tab) => {
      tab.style.display = "none"
    })
  
    // Remove active class from all buttons
    document.querySelectorAll("[data-settings-tab]").forEach((btn) => {
      btn.classList.remove("active")
    })
  
    // Show selected tab
    const selectedTab = document.getElementById(tabName + "-tab")
    if (selectedTab) {
      selectedTab.style.display = "block"
    }
  
    // Add active class to clicked button
    document.querySelector(`[data-settings-tab="${tabName}"]`).classList.add("active")
  }
  
  function saveProfileSettings() {
    const adminName = document.getElementById("adminName").value
    const adminEmail = document.getElementById("adminEmail").value
    const adminPhone = document.getElementById("adminPhone").value
    const adminDepartment = document.getElementById("adminDepartment").value
  
    if (!adminName || !adminEmail) {
      alert("Please fill in all required fields")
      return
    }
  
    console.log("[v0] Saving profile settings:", {
      name: adminName,
      email: adminEmail,
      phone: adminPhone,
      department: adminDepartment,
    })
  
    // Call backend function for PHP integration
    updateAdminProfile({
      name: adminName,
      email: adminEmail,
      phone: adminPhone,
      department: adminDepartment,
    })
  
    alert("Profile updated successfully!")
  }
  
  function changePassword() {
    const currentPassword = document.getElementById("currentPassword").value
    const newPassword = document.getElementById("newPassword").value
    const confirmPassword = document.getElementById("confirmPassword").value
  
    if (!currentPassword || !newPassword || !confirmPassword) {
      alert("Please fill in all password fields")
      return
    }
  
    if (newPassword !== confirmPassword) {
      alert("New passwords do not match")
      return
    }
  
    if (newPassword.length < 8) {
      alert("New password must be at least 8 characters long")
      return
    }
  
    console.log("[v0] Changing password")
  
    // Call backend function for PHP integration
    updateAdminPassword(currentPassword, newPassword)
  
    // Clear form
    document.getElementById("passwordForm").reset()
    alert("Password changed successfully!")
  }
  
  function saveNotificationPreferences() {
    const preferences = {
      emailNewStudents: document.getElementById("emailNewStudents").checked,
      emailCourseUpdates: document.getElementById("emailCourseUpdates").checked,
      emailGradeSubmissions: document.getElementById("emailGradeSubmissions").checked,
      emailSystemAlerts: document.getElementById("emailSystemAlerts").checked,
      emailWeeklyReport: document.getElementById("emailWeeklyReport").checked,
      inAppRegistrations: document.getElementById("inAppRegistrations").checked,
      inAppGrades: document.getElementById("inAppGrades").checked,
      inAppEnrollments: document.getElementById("inAppEnrollments").checked,
    }
  
    console.log("[v0] Saving notification preferences:", preferences)
  
    // Call backend function for PHP integration
    updateNotificationPreferences(preferences)
  
    alert("Notification preferences saved successfully!")
  }
  
  function saveSystemPreferences() {
    const preferences = {
      theme: document.getElementById("themeSelect").value,
      language: document.getElementById("languageSelect").value,
      dateFormat: document.getElementById("dateFormatSelect").value,
      itemsPerPage: Number.parseInt(document.getElementById("itemsPerPage").value),
      autoSave: document.getElementById("autoSaveToggle").checked,
    }
  
    if (preferences.itemsPerPage < 5 || preferences.itemsPerPage > 100) {
      alert("Items per page must be between 5 and 100")
      return
    }
  
    console.log("[v0] Saving system preferences:", preferences)
  
    // Call backend function for PHP integration
    updateSystemPreferences(preferences)
  
    alert("System preferences saved successfully!")
  }
  
  function previewPhoto() {
    const photoInput = document.getElementById("photoInput")
    const photoPreview = document.getElementById("photoPreview")
  
    if (photoInput.files && photoInput.files[0]) {
      const reader = new FileReader()
      reader.onload = (e) => {
        photoPreview.innerHTML = `<img src="${e.target.result}" class="img-fluid rounded" alt="Preview">`
      }
      reader.readAsDataURL(photoInput.files[0])
    }
  }
  
  function uploadProfilePhoto() {
    const photoInput = document.getElementById("photoInput")
    if (!photoInput.files || !photoInput.files[0]) {
      alert("Please select a photo to upload")
      return
    }
  
    console.log("[v0] Uploading profile photo:", photoInput.files[0].name)
  
    // Call backend function for PHP integration
    uploadAdminPhoto(photoInput.files[0])
  
    // Close modal
    const modalElement = document.getElementById("uploadPhotoModal")
    const modal = window.bootstrap.Modal.getInstance(modalElement)
    if (modal) {
      modal.hide()
    }
  
    alert("Profile photo uploaded successfully!")
  }
  

  
  function populateStudentEditModal(studentId) {
    // Fetch student data from the database
    fetch(`../PHP/get_students.php?student_id=${studentId}`)
    .then(response => response.json())
    .then(data => {
      if (data.error) {
        console.error('Error fetching student:', data.error)
        alert("Student not found")
        return
      }

      const student = data[0] // Assuming single student is returned
      if (student) {
        document.getElementById("editStudentId").value = student.student_id
        document.getElementById("editStudentName").value = `${student.first_name} ${student.last_name}`
        document.getElementById("editStudentEmail").value = student.email
        document.getElementById("editStudentPhone").value = student.Phone_Num || ''
        document.getElementById("editStudentProgram").value = student.Department
        document.getElementById("editStudentYear").value = student.Level
        document.getElementById("editStudentAcademicYear").value = student.academic_year || '2024/2025'
        document.getElementById("editStudentGender").value = student.Gender || 'Male'
        console.log("[v0] Student edit modal populated for:", studentId)
      } else {
        alert("Student not found")
      }
    })
    .catch(error => {
      console.error('Error loading student details:', error)
      alert("Error loading student details")
    })
  }
  
  function populateCourseEditModal(courseCode) {
    console.log("[v0] Attempting to populate course modal for:", courseCode)

    // Load lecturers first
    loadLecturersForCourseEdit()

    // Fetch course data from the database
    fetch(`../PHP/get_courses.php?course_code=${courseCode}`)
    .then(response => response.json())
    .then(data => {
      if (data.error) {
        console.error('Error fetching course:', data.error)
        alert("Course not found")
        return
      }

      const course = data[0] // Assuming single course is returned
      if (course) {
        document.getElementById("editCourseId").value = course.course_id
        document.getElementById("editCourseCode").value = course.course_code || ''
        document.getElementById("editCourseTitle").value = course.course_title || ''
        document.getElementById("editCourseDescription").value = course.course_description || ''
        document.getElementById("editCourseUnit").value = course.course_unit || ''
        document.getElementById("editCourseDepartment").value = course.department || ''
        document.getElementById("editCourseLevel").value = course.level || ''
        document.getElementById("editCourseSemester").value = course.semester || ''
        document.getElementById("editCourseLecturer").value = course.lecturer_id || ''

        console.log("[v0] Course edit modal populated for:", courseCode)
      } else {
        alert("Course not found")
      }
    })
    .catch(error => {
      console.error('Error loading course details:', error)
      alert("Error loading course details")
    })
  }

  function populateCourseViewModal(courseCode) {
    // Fetch course data from the database
    fetch(`../PHP/get_courses.php?course_code=${courseCode}`)
    .then(response => response.json())
    .then(data => {
      if (data.error) {
        console.error('Error fetching course:', data.error)
        alert("Course not found")
        return
      }

      const course = data[0] // Assuming single course is returned
      if (course) {
        document.getElementById("viewCourseCode").textContent = course.course_code
        document.getElementById("viewCourseName").textContent = course.course_name
        document.getElementById("viewCourseLecturer").textContent = course.lecturer_name
        document.getElementById("viewCourseDepartment").textContent = course.department
        document.getElementById("viewCourseLevel").textContent = course.level
        document.getElementById("viewCourseCredits").textContent = course.credits

        // Set status badge - assuming all active for now
        document.getElementById("viewCourseStatus").innerHTML = `<span class="badge bg-success">Active</span>`

        console.log("[v0] Course view modal populated for:", courseCode)
      } else {
        alert("Course not found")
      }
    })
    .catch(error => {
      console.error('Error loading course details:', error)
      alert("Error loading course details")
    })
  }

  function populateCourseStudentsModal(courseCode) {
    // Set course title
    fetch(`../PHP/get_courses.php?course_code=${courseCode}`)
    .then(response => response.json())
    .then(courseData => {
      if (courseData.length > 0) {
        document.getElementById("courseTitle").textContent = courseData[0].course_title || courseData[0].course_name || courseCode
      }
    })
    .catch(error => {
      console.error('Error fetching course details:', error)
      document.getElementById("courseTitle").textContent = courseCode
    })

    // Fetch students enrolled in the course
    fetch(`../PHP/get_course_students.php?course_code=${courseCode}`)
    .then(response => response.json())
    .then(data => {
      if (data.error) {
        console.error('Error fetching course students:', data.error)
        alert("Error loading course students")
        return
      }

      const studentsTableBody = document.getElementById("courseStudentsTableBody")
      if (!studentsTableBody) return

      studentsTableBody.innerHTML = ''

      if (data.length === 0) {
        studentsTableBody.innerHTML = `
          <tr>
            <td colspan="7" class="text-center text-muted">No students enrolled in this course</td>
          </tr>
        `
      } else {
        data.forEach(student => {
          const row = document.createElement('tr')
          const enrollmentDate = new Date(student.enrollment_date).toLocaleDateString()
          const statusBadge = student.status === 'Active' ? 'bg-success' : 'bg-warning'
          row.innerHTML = `
            <td>${student.Matric_No}</td>
            <td>${student.first_name} ${student.last_name}</td>
            <td>${student.email}</td>
            <td>${student.Department}</td>
            <td>${student.Level}</td>
            <td>${enrollmentDate}</td>
            <td><span class="badge ${statusBadge}">${student.status}</span></td>
          `
          studentsTableBody.appendChild(row)
        })
      }

      console.log("[v0] Course students modal populated for:", courseCode)
    })
    .catch(error => {
      console.error('Error loading course students:', error)
      alert("Error loading course students")
    })
  }
  
  function populateLecturerEditModal(lecturerId) {
    // Fetch lecturer data from the database
    fetch(`../PHP/get_lecturers.php`)
    .then(response => response.json())
    .then(data => {
      if (data.error) {
        console.error('Error fetching lecturer:', data.error)
        alert("Lecturer not found")
        return
      }

      const lecturer = data.find(l => l.lecturer_id == lecturerId)
      if (lecturer) {
        document.getElementById("editLecturerName").value = `${lecturer.first_name} ${lecturer.last_name}`
        document.getElementById("editLecturerEmail").value = lecturer.email
        document.getElementById("editLecturerDepartment").value = lecturer.department || ''
        document.getElementById("editLecturerGender").value = lecturer.gender || ''
        document.getElementById("editLecturerPhone").value = lecturer.phone || ''
        
        // Store lecturer_id for the save function
        document.getElementById("editLecturerModal").setAttribute("data-lecturer-id", lecturer.lecturer_id)
        
        console.log("[v0] Lecturer edit modal populated for:", lecturerId)
      } else {
        alert("Lecturer not found")
      }
    })
    .catch(error => {
      console.error('Error loading lecturer details:', error)
      alert("Error loading lecturer details")
    })
  }
  
  function saveStudentChanges() {
    const studentId = document.getElementById("editStudentId").value
    const firstName = document.getElementById("editStudentName").value.split(' ')[0] || ''
    const lastName = document.getElementById("editStudentName").value.split(' ').slice(1).join(' ') || ''
    const email = document.getElementById("editStudentEmail").value
    const phone = document.getElementById("editStudentPhone").value
    const department = document.getElementById("editStudentProgram").value
    const level = document.getElementById("editStudentYear").value
    const academicYear = document.getElementById("editStudentAcademicYear").value
    const gender = document.getElementById("editStudentGender").value

    // Basic client-side validation
    if (!firstName || !lastName || !email || !department || !level || !academicYear || !gender) {
      alert("Please fill in all required fields")
      return
    }

    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
      alert("Please enter a valid email address")
      return
    }

    const formData = new FormData()
    formData.append('student_id', studentId)
    formData.append('first_name', firstName)
    formData.append('last_name', lastName)
    formData.append('email', email)
    formData.append('phone_num', phone)
    formData.append('department', department)
    formData.append('level', level)
    formData.append('academic_year', academicYear)
    formData.append('gender', gender)

    console.log("[v0] Saving student changes:", studentId)

    // Send data to PHP backend
fetch('../PHP/edit_student.php', {
      method: 'POST',
      body: formData
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        alert("Student updated successfully!")
        loadStudents() // Refresh the students list
        loadDashboardStats() // Update dashboard stats

        // Close modal safely
        const modalElement = document.getElementById("editStudentModal")
        const modal = window.bootstrap.Modal.getInstance(modalElement)
        if (modal) {
          modal.hide()
        }
      } else {
        alert("Error: " + data.errors.join(", "))
      }
    })
    .catch(error => {
      console.error('Error:', error)
      alert("An error occurred while updating the student")
    })
  }
  
  function saveCourseChanges() {
    const courseId = document.getElementById("editCourseId").value
    const courseCode = document.getElementById("editCourseCode").value.trim()
    const courseTitle = document.getElementById("editCourseTitle").value.trim()
    const courseDescription = document.getElementById("editCourseDescription").value.trim()
    const courseUnit = document.getElementById("editCourseUnit").value.trim()
    const department = document.getElementById("editCourseDepartment").value
    const level = document.getElementById("editCourseLevel").value
    const semester = document.getElementById("editCourseSemester").value
    const lecturerId = document.getElementById("editCourseLecturer").value

    // Clear previous messages
    const errorDiv = document.getElementById('editCourseErrors')
    const successDiv = document.getElementById('editCourseSuccess')
    if (errorDiv) {
      errorDiv.style.display = 'none'
      errorDiv.innerHTML = ''
    }
    if (successDiv) {
      successDiv.style.display = 'none'
      successDiv.innerHTML = ''
    }

    // Basic validation
    if (!courseId || !courseCode || !courseTitle || !courseUnit || !department || !level || !semester || !lecturerId) {
      if (errorDiv) {
        errorDiv.style.display = 'block'
        errorDiv.textContent = "Please fill in all required fields"
      }
      return
    }

    if (isNaN(courseUnit) || parseInt(courseUnit) < 1) {
      if (errorDiv) {
        errorDiv.style.display = 'block'
        errorDiv.textContent = "Course unit must be a number greater than 0"
      }
      return
    }

    const formData = new FormData()
    formData.append('course_id', courseId)
    formData.append('course_code', courseCode)
    formData.append('course_title', courseTitle)
    formData.append('course_description', courseDescription)
    formData.append('course_unit', courseUnit)
    formData.append('department', department)
    formData.append('level', level)
    formData.append('semester', semester)
    formData.append('lecturer_id', lecturerId)

    console.log("[v0] Saving course changes:", courseCode)

    // Send data to PHP backend
    fetch('../PHP/edit_course.php', {
      method: 'POST',
      body: formData
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        if (successDiv) {
          successDiv.style.display = 'block'
          successDiv.textContent = "Course updated successfully!"
        }
        loadCourses() // Refresh the courses list
        loadDashboardStats() // Update dashboard stats

        // Close modal after 2 seconds
        setTimeout(() => {
          const modalElement = document.getElementById("editCourseModal")
          const modal = window.bootstrap.Modal.getInstance(modalElement)
          if (modal) {
            modal.hide()
          }
        }, 2000)
      } else {
        if (errorDiv) {
          errorDiv.style.display = 'block'
          errorDiv.textContent = data.errors ? data.errors.join(", ") : "Error updating course."
        }
      }
    })
    .catch(error => {
      console.error('Error:', error)
      if (errorDiv) {
        errorDiv.style.display = 'block'
        errorDiv.textContent = "An error occurred while updating the course"
      }
    })
  }

  // Load Lecturers for Course Edit Dropdown
  function loadLecturersForCourseEdit() {
    const lecturerSelect = document.getElementById('editCourseLecturer')
    if (!lecturerSelect) return

    fetch('../PHP/get_lecturers.php')
    .then(response => response.json())
    .then(data => {
      if (data.error) {
        console.error('Error fetching lecturers:', data.error)
        return
      }

      // Clear existing options except the first one
      lecturerSelect.innerHTML = '<option value="">Select Lecturer</option>'

      // Add lecturers to dropdown
      if (Array.isArray(data) && data.length > 0) {
        data.forEach(lecturer => {
          const option = document.createElement('option')
          option.value = lecturer.lecturer_id
          option.textContent = `${lecturer.first_name} ${lecturer.last_name}`
          lecturerSelect.appendChild(option)
        })
      } else {
        const option = document.createElement('option')
        option.textContent = 'No lecturers available'
        option.disabled = true
        lecturerSelect.appendChild(option)
      }
    })
    .catch(error => {
      console.error('Error loading lecturers for course edit:', error)
      lecturerSelect.innerHTML = '<option value="">Error loading lecturers</option>'
    })
  }
  
  function saveLecturerChanges() {
    const modalElement = document.getElementById("editLecturerModal")
    const lecturerId = modalElement.getAttribute("data-lecturer-id")
    const fullName = document.getElementById("editLecturerName").value.trim()
    const email = document.getElementById("editLecturerEmail").value.trim()
    const department = document.getElementById("editLecturerDepartment").value
    const gender = document.getElementById("editLecturerGender").value
    const phone = document.getElementById("editLecturerPhone").value.trim()

    // Basic validation
    if (!fullName || !email || !department || !gender || !phone) {
      alert("Please fill in all required fields")
      return
    }

    // Split name into first and last
    const nameParts = fullName.split(' ')
    const firstName = nameParts[0] || ''
    const lastName = nameParts.slice(1).join(' ') || ''

    if (!firstName || !lastName) {
      alert("Please enter both first and last name")
      return
    }

    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
      alert("Please enter a valid email address")
      return
    }

    const formData = new FormData()
    formData.append('lecturer_id', lecturerId)
    formData.append('first_name', firstName)
    formData.append('last_name', lastName)
    formData.append('email', email)
    formData.append('phone_num', phone)
    formData.append('department', department)
    formData.append('gender', gender)

    console.log("[v0] Saving lecturer changes:", lecturerId)

    // Send data to PHP backend
    fetch('../PHP/edit_lecturer.php', {
      method: 'POST',
      body: formData
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        alert("Lecturer updated successfully!")
        loadLecturers() // Refresh the lecturers list
        loadDashboardStats() // Update dashboard stats

        // Close modal safely
        const modal = window.bootstrap.Modal.getInstance(modalElement)
        if (modal) {
          modal.hide()
        }
      } else {
        alert("Error: " + (data.errors ? data.errors.join(", ") : "Failed to update lecturer"))
      }
    })
    .catch(error => {
      console.error('Error:', error)
      alert("An error occurred while updating the lecturer")
    })
  }
  
  let currentRegistrationId = null
  
  function openApproveModal(studentId, applicantName) {
    currentRegistrationId = studentId
    document.getElementById("approveApplicantName").textContent = applicantName
    document.getElementById("approveComments").value = ""
    const modal = new window.bootstrap.Modal(document.getElementById("approveRegistrationModal"))
    modal.show()
  }
  
  function openDeclineModal(studentId, applicantName) {
    currentRegistrationId = studentId
    document.getElementById("declineApplicantName").textContent = applicantName
    document.getElementById("declineReason").value = ""
    document.getElementById("declineDetailedReason").value = ""
    const modal = new window.bootstrap.Modal(document.getElementById("declineRegistrationModal"))
    modal.show()
  }
  
  function approveRegistration() {
    const comments = document.getElementById("approveComments").value
    const studentId = currentRegistrationId
    const approveBtn = document.querySelector('.approve-registration-btn[data-student-id="' + studentId + '"]')
    const academicYear = approveBtn?.getAttribute('data-academic-year') || '2024/2025'
    const semester = approveBtn?.getAttribute('data-semester') || 'First'

    if (!studentId) {
      alert("Error: Missing student information")
      return
    }

    const formData = new FormData()
    formData.append('student_id', studentId)
    formData.append('academic_year', academicYear)
    formData.append('semester', semester)
    formData.append('comments', comments)

    fetch('../PHP/approve_course_registration.php', {
      method: 'POST',
      body: formData
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        alert("Registration approved successfully!")
        loadPendingRegistrations() // Refresh the registrations list
        loadDashboardStats() // Update dashboard stats

        // Close modal
        const modalElement = document.getElementById("approveRegistrationModal")
        const modal = window.bootstrap.Modal.getInstance(modalElement)
        if (modal) {
          modal.hide()
        }
      } else {
        alert("Error: " + (data.errors ? data.errors.join(", ") : "Failed to approve registration"))
      }
    })
    .catch(error => {
      console.error('Error:', error)
      alert("An error occurred while approving the registration")
    })
  }
  
  function declineRegistration() {
    const reason = document.getElementById("declineReason").value
    const detailedReason = document.getElementById("declineDetailedReason").value
    const studentId = currentRegistrationId
    const declineBtn = document.querySelector('.decline-registration-btn[data-student-id="' + studentId + '"]')
    const academicYear = declineBtn?.getAttribute('data-academic-year') || '2024/2025'
    const semester = declineBtn?.getAttribute('data-semester') || 'First'

    if (!reason || !detailedReason.trim()) {
      alert("Please fill in both the reason and detailed explanation")
      return
    }

    if (!studentId) {
      alert("Error: Missing student information")
      return
    }

    const formData = new FormData()
    formData.append('student_id', studentId)
    formData.append('academic_year', academicYear)
    formData.append('semester', semester)
    formData.append('reason', reason)
    formData.append('detailed_reason', detailedReason)

    fetch('../PHP/decline_course_registration.php', {
      method: 'POST',
      body: formData
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        alert("Registration declined successfully!")
        loadPendingRegistrations() // Refresh the registrations list
        loadDashboardStats() // Update dashboard stats

        // Close modal
        const modalElement = document.getElementById("declineRegistrationModal")
        const modal = window.bootstrap.Modal.getInstance(modalElement)
        if (modal) {
          modal.hide()
        }
      } else {
        alert("Error: " + (data.errors ? data.errors.join(", ") : "Failed to decline registration"))
      }
    })
    .catch(error => {
      console.error('Error:', error)
      alert("An error occurred while declining the registration")
    })
  }
  
  // Placeholder functions for future PHP integration
  function addStudent(studentData) {
    console.log("Adding student:", studentData)
    // This will be replaced with PHP/AJAX call
  }
  
  function editStudent(studentId, studentData) {
    console.log("Editing student:", studentId, studentData)
    // This will be replaced with PHP/AJAX call
  }
  
  function editCourse(courseCode, courseData) {
    console.log("Editing course:", courseCode, courseData)
    // This will be replaced with PHP/AJAX call
  }
  
  function editLecturer(lecturerName, lecturerData) {
    console.log("Editing lecturer:", lecturerName, lecturerData)
    // This will be replaced with PHP/AJAX call
  }
  
  function deleteStudent(studentId) {
    const formData = new FormData()
    formData.append('student_id', studentId)

fetch('../PHP/delete_student.php', {
      method: 'POST',
      body: formData
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        alert("Student deleted successfully!")
        loadStudents() // Refresh the students list
        loadDashboardStats() // Update dashboard stats
      } else {
        alert("Error: " + data.errors.join(", "))
      }
    })
    .catch(error => {
      console.error('Error:', error)
      alert("An error occurred while deleting the student")
    })
  }

  function deleteCourse(courseCode) {
    const formData = new FormData()
    formData.append('course_code', courseCode)

    fetch('../PHP/delete_course.php', {
      method: 'POST',
      body: formData
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        alert("Course deleted successfully!")
        loadCourses() // Refresh the courses list
        loadDashboardStats() // Update dashboard stats
      } else {
        alert("Error: " + (data.errors ? data.errors.join(", ") : "Failed to delete course"))
      }
    })
    .catch(error => {
      console.error('Error:', error)
      alert("An error occurred while deleting the course")
    })
  }
  
  // Handle Add Course Form Submission
  function handleAddCourse(e) {
    e.preventDefault()

    const formData = new FormData(e.target)
    const courseData = {
      course_code: formData.get('course_code'),
      course_title: formData.get('course_title'),
      course_description: formData.get('course_description'),
      course_unit: formData.get('course_unit'),
      lecturer_id: formData.get('lecturer_id'),
      department: formData.get('department'),
      level: formData.get('level'),
      semester: formData.get('semester')
    }

    // Clear previous messages
    const errorDiv = document.getElementById('addCourseErrors')
    const successDiv = document.getElementById('addCourseSuccess')
    errorDiv.style.display = 'none'
    errorDiv.innerHTML = ''
    successDiv.style.display = 'none'
    successDiv.innerHTML = ''

    // Basic client-side validation
    if (!courseData.course_code || !courseData.course_title || !courseData.course_unit ||
        !courseData.lecturer_id || !courseData.department || !courseData.level || !courseData.semester) {
      errorDiv.style.display = 'block'
      errorDiv.textContent = "Please fill in all required fields"
      return
    }

    if (isNaN(courseData.course_unit) || parseInt(courseData.course_unit) < 1) {
      errorDiv.style.display = 'block'
      errorDiv.textContent = "Course unit must be a number greater than 0"
      return
    }

    // Send data to PHP backend
    fetch('../PHP/add_course.php', {
      method: 'POST',
      body: formData
    })
    .then(response => {
      if (!response.ok) {
        return response.json()
          .then(errData => {
            let msg = 'Failed to add course.'
            if (errData && errData.errors) {
              msg = errData.errors.join(", ")
            }
            throw new Error(msg)
          })
          .catch(() => {
            throw new Error('Failed to add course due to server error.')
          })
      }
      return response.json()
    })
    .then(data => {
      if (data.success) {
        successDiv.style.display = 'block'
        successDiv.textContent = "Course added successfully!"
        e.target.reset()
        loadCourses() // Refresh the courses list
        loadDashboardStats() // Update dashboard stats

        // Close modal after 3 seconds
        setTimeout(() => {
          const modalElement = document.getElementById("addCourseModal")
          const modal = window.bootstrap.Modal.getInstance(modalElement)
          if (modal) {
            modal.hide()
          }
        }, 3000)
      } else {
        errorDiv.style.display = 'block'
        errorDiv.textContent = data.errors ? data.errors.join(", ") : "Error adding course."
      }
    })
    .catch(error => {
      console.error('Add course error:', error)
      errorDiv.style.display = 'block'
      errorDiv.textContent = error.message || "An error occurred while adding the course"
    })
  }

  function addCourse(courseData) {
    console.log("Adding course:", courseData)
    // This will be replaced with PHP/AJAX call
  }
  
  function addLecturer(lecturerData) {
    console.log("Adding lecturer:", lecturerData)
    // This will be replaced with PHP/AJAX call
  }
  
  function generateReport(reportType, parameters) {
    console.log("Generating report:", reportType, parameters)
    // This will be replaced with PHP/AJAX call
  }
  
  // Load Pending Registrations
  function loadPendingRegistrations() {
    fetch('../PHP/get_pending_registrations.php')
    .then(response => response.json())
    .then(data => {
      if (data.error) {
        console.error('Error fetching registrations:', data.error)
        const registrationsTableBody = document.getElementById('registrationsTableBody')
        if (registrationsTableBody) {
          registrationsTableBody.innerHTML = `
            <tr>
              <td colspan="8" class="text-center text-muted">Error loading registrations</td>
            </tr>
          `
        }
        return
      }

      populateRegistrationsTable(data)
      updateRegistrationStats(data)
    })
    .catch(error => {
      console.error('Error loading registrations:', error)
      const registrationsTableBody = document.getElementById('registrationsTableBody')
      if (registrationsTableBody) {
        registrationsTableBody.innerHTML = `
          <tr>
            <td colspan="8" class="text-center text-muted">Error loading registrations</td>
          </tr>
        `
      }
    })
  }

  // Populate Registrations Table
  function populateRegistrationsTable(data) {
    const registrationsTableBody = document.getElementById('registrationsTableBody')
    if (!registrationsTableBody) return

    registrationsTableBody.innerHTML = ''

    if (data.length === 0) {
      registrationsTableBody.innerHTML = `
        <tr>
          <td colspan="8" class="text-center text-muted">No student registrations found</td>
        </tr>
      `
      return
    }

    data.forEach((registration, index) => {
      const row = document.createElement('tr')
      const applicationDate = new Date(registration.application_date).toLocaleDateString()
      const fullName = `${registration.first_name} ${registration.last_name}`
      const academicInfo = `${registration.academic_year || 'N/A'} - ${registration.semester || 'N/A'}`
      
      row.innerHTML = `
        <td class="text-center">${index + 1}</td>
        <td>${fullName}<br><small class="text-muted">${registration.Matric_No}</small></td>
        <td>${registration.email || 'N/A'}</td>
        <td>${registration.academic_year || '2024/2025'}<br><small class="text-muted">${registration.semester || 'First'}</small></td>
        <td>${registration.department || 'N/A'}<br><small class="text-muted">${registration.Level}</small></td>
        <td>${applicationDate}</td>
        <td><span class="badge bg-warning">Pending</span><br><small class="text-muted">${registration.course_count} courses</small></td>
        <td>
          <button class="btn btn-sm btn-outline-success approve-registration-btn" data-student-id="${registration.student_id}" data-applicant-name="${fullName}" data-academic-year="${registration.academic_year}" data-semester="${registration.semester}">
            <i class="fas fa-check"></i> Approve
          </button>
          <button class="btn btn-sm btn-outline-danger decline-registration-btn" data-student-id="${registration.student_id}" data-applicant-name="${fullName}" data-academic-year="${registration.academic_year}" data-semester="${registration.semester}">
            <i class="fas fa-times"></i> Decline
          </button>
        </td>
      `
      registrationsTableBody.appendChild(row)
    })

    // Re-attach event listeners for approve and decline buttons
    document.querySelectorAll(".approve-registration-btn").forEach((btn) => {
      btn.addEventListener("click", (e) => {
        e.preventDefault()
        const studentId = btn.getAttribute("data-student-id")
        const applicantName = btn.getAttribute("data-applicant-name")
        openApproveModal(studentId, applicantName)
      })
    })

    document.querySelectorAll(".decline-registration-btn").forEach((btn) => {
      btn.addEventListener("click", (e) => {
        e.preventDefault()
        const studentId = btn.getAttribute("data-student-id")
        const applicantName = btn.getAttribute("data-applicant-name")
        openDeclineModal(studentId, applicantName)
      })
    })
  }

  // Update Registration Stats
  function updateRegistrationStats(data) {
    const pendingCount = data.length
    const pendingCountEl = document.getElementById('pendingApprovalsCount')
    if (pendingCountEl) {
      pendingCountEl.textContent = pendingCount
    }

    // Fetch approval stats
    fetch('../PHP/get_approval_stats.php')
    .then(response => response.json())
    .then(statsData => {
      if (statsData.success) {
        const approvedTodayEl = document.getElementById('approvedTodayCount')
        if (approvedTodayEl) {
          approvedTodayEl.textContent = statsData.approved_today
        }
        const totalApprovedEl = document.getElementById('totalApprovedCount')
        if (totalApprovedEl) {
          totalApprovedEl.textContent = statsData.total_approved
        }
      }
    })
    .catch(error => {
      console.error('Error loading approval stats:', error)
    })
  }
  
  // Search functionality placeholder
  function searchStudents(query) {
    console.log("Searching students:", query)
    // This will be replaced with actual search implementation
  }
  
  // Filter functionality placeholder
  function filterByProgram(program) {
    console.log("Filtering by program:", program)
    // This will be replaced with actual filter implementation
  }
  
  function filterByStatus(status) {
    console.log("Filtering by status:", status)
    // This will be replaced with actual filter implementation
  }
  
  function populateStudentViewModal(studentId) {
    // Fetch student data from the database
    fetch(`../PHP/get_students.php?student_id=${studentId}`)
    .then(response => response.json())
    .then(data => {
      if (data.error) {
        console.error('Error fetching student:', data.error)
        alert("Student not found")
        return
      }

      const student = data[0] // Assuming single student is returned
      if (student) {
        document.getElementById("viewStudentId").textContent = student.student_id
        document.getElementById("viewStudentName").textContent = `${student.first_name} ${student.last_name}`
        document.getElementById("viewStudentEmail").textContent = student.email
        document.getElementById("viewStudentProgram").textContent = student.Department
        document.getElementById("viewStudentYear").textContent = student.Level

        // Set status badge - assuming all active for now since we don't have status in DB
        const statusBadge = "bg-success"
        document.getElementById("viewStudentStatus").innerHTML =
          `<span class="badge ${statusBadge}">Active</span>`

        // Set additional view-only data (courses and GPA are examples - would need separate queries)
        document.getElementById("viewStudentCourses").textContent = "N/A" // Placeholder
        document.getElementById("viewStudentGPA").textContent = "N/A" // Placeholder

        console.log("[v0] Student view modal populated for:", studentId)
      } else {
        alert("Student not found")
      }
    })
    .catch(error => {
      console.error('Error loading student details:', error)
      alert("Error loading student details")
    })
  }
  
  function updateAdminProfile(profileData) {
    console.log("Updating admin profile:", profileData)
    // This will be replaced with PHP/AJAX call
  }
  
  function updateAdminPassword(currentPassword, newPassword) {
    console.log("Updating admin password")
    // This will be replaced with PHP/AJAX call
  }
  
  function updateNotificationPreferences(preferences) {
    console.log("Updating notification preferences:", preferences)
    // This will be replaced with PHP/AJAX call
  }
  
  function updateSystemPreferences(preferences) {
    console.log("Updating system preferences:", preferences)
    // This will be replaced with PHP/AJAX call
  }
  
  function uploadAdminPhoto(photoFile) {
    console.log("Uploading admin photo:", photoFile)
    // This will be replaced with PHP/AJAX call
  }

  // Handle Add Student Form Submission
  function handleAddStudent(e) {
    e.preventDefault()

    const formData = new FormData(e.target)
    const studentData = {
      matric_no: formData.get('matric_no'),
      first_name: formData.get('first_name'),
      last_name: formData.get('last_name'),
      email: formData.get('email'),
      phone_num: formData.get('phone_num'),
      department: formData.get('department'),
      level: formData.get('level'),
      academic_year: formData.get('academic_year'),
      gender: formData.get('gender'),
      password: formData.get('password')
    }

    // Clear previous messages
    const errorDiv = document.getElementById('addStudentErrors')
    const successDiv = document.getElementById('addStudentSuccess')
    errorDiv.style.display = 'none'
    errorDiv.innerHTML = ''
    successDiv.style.display = 'none'
    successDiv.innerHTML = ''

    // Basic client-side validation
    if (!studentData.matric_no || !studentData.first_name || !studentData.last_name ||
        !studentData.email || !studentData.phone_num || !studentData.department ||
        !studentData.level || !studentData.academic_year || !studentData.gender || !studentData.password) {
      errorDiv.style.display = 'block'
      errorDiv.textContent = "Please fill in all required fields"
      return
    }

    if (studentData.password.length < 8) {
      errorDiv.style.display = 'block'
      errorDiv.textContent = "Password must be at least 8 characters long"
      return
    }

    // Send data to PHP backend
fetch('../PHP/add_student.php', {
      method: 'POST',
      body: formData
    })
    .then(response => {
      if (!response.ok) {
        return response.json()
          .then(errData => {
            let msg = 'Failed to add student.'
            if (errData && errData.errors) {
              msg = errData.errors.join(", ")
            }
            throw new Error(msg)
          })
          .catch(() => {
            throw new Error('Failed to add student due to server error.')
          })
      }
      return response.json()
    })
    .then(data => {
      if (data.success) {
        successDiv.style.display = 'block'
        successDiv.textContent = "Student added successfully!"
        e.target.reset()
        loadStudents() // Refresh the students list
        loadDashboardStats() // Update dashboard stats

        // Close modal after 3 seconds
        setTimeout(() => {
          const modalElement = document.getElementById("addStudentModal")
          const modal = window.bootstrap.Modal.getInstance(modalElement)
          if (modal) {
            modal.hide()
          }
        }, 3000)
      } else {
        errorDiv.style.display = 'block'
        errorDiv.textContent = data.errors.join(", ")
      }
    })
    .catch(error => {
      console.error('Add student error:', error)
      errorDiv.style.display = 'block'
      errorDiv.textContent = error.message || "An error occurred while adding the student"
    })
  }

  // Load Dashboard Statistics
  function loadDashboardStats() {
    // Fetch student count
    fetch('../PHP/get_students.php')
    .then(response => response.json())
    .then(data => {
      if (data.error) {
        console.error('Error fetching students:', data.error)
        document.getElementById('totalStudents').textContent = '0'
        return
      }

      const studentCount = data.length
      document.getElementById('totalStudents').textContent = studentCount
    })
    .catch(error => {
      console.error('Error loading student stats:', error)
      document.getElementById('totalStudents').textContent = '0'
    })

    // Fetch active courses count
    fetch('../PHP/get_courses.php')
    .then(response => response.json())
    .then(data => {
      if (data.error) {
        console.error('Error fetching courses:', data.error)
        document.getElementById('activeCourses').textContent = '0'
        return
      }

      // Count the number of courses returned
      const courseCount = Array.isArray(data) ? data.length : 0
      document.getElementById('activeCourses').textContent = courseCount
    })
    .catch(error => {
      console.error('Error loading course stats:', error)
      document.getElementById('activeCourses').textContent = '0'
    })

    // Fetch lecturers count
fetch('../PHP/get_lecturers.php')
    .then(response => response.json())
    .then(data => {
      if (data.error) {
        console.error('Error fetching lecturers:', data.error)
        document.getElementById('totalLecturers').textContent = '0'
        return
      }

      const lecturerCount = Array.isArray(data) ? data.length : 0
      document.getElementById('totalLecturers').textContent = lecturerCount
    })
    .catch(error => {
      console.error('Error loading lecturer stats:', error)
      document.getElementById('totalLecturers').textContent = '0'
    })

    // Fetch pending grades count
fetch('../PHP/get_pending_grades.php')
    .then(response => response.json())
    .then(data => {
      if (data.error) {
        console.error('Error fetching pending grades:', data.error)
        document.getElementById('pendingGrades').textContent = '0'
        return
      }

      document.getElementById('pendingGrades').textContent = data.count
    })
    .catch(error => {
      console.error('Error loading pending grades stats:', error)
      document.getElementById('pendingGrades').textContent = '0'
    })
  }

  // Populate Students Table
  function populateStudentsTable(data) {
    const studentsTableBody = document.getElementById('studentsTableBody')
    if (!studentsTableBody) return

    studentsTableBody.innerHTML = ''

    data.forEach((student, index) => {
      const row = document.createElement('tr')
      row.innerHTML = `
        <td class="text-center">${index + 1}</td>
        <td>${student.Matric_No}</td>
        <td>${student.first_name} ${student.last_name}</td>
        <td>${student.email}</td>
        <td>${student.Department}</td>
        <td>${student.Level}</td>
        <td><span class="badge bg-success">Active</span></td>
        <td>
          <button class="btn btn-sm btn-outline-info view-student-btn" data-student-id="${student.student_id}" data-bs-toggle="modal" data-bs-target="#viewStudentModal">
            <i class="fas fa-eye"></i>
          </button>
          <button class="btn btn-sm btn-outline-primary edit-student-btn" data-student-id="${student.student_id}" data-bs-toggle="modal" data-bs-target="#editStudentModal">
            <i class="fas fa-edit"></i>
          </button>
          <button class="btn btn-sm btn-outline-danger delete-student-btn" data-student-id="${student.student_id}">
            <i class="fas fa-trash"></i>
          </button>
        </td>
      `
      studentsTableBody.appendChild(row)
    })

    // Re-attach event listeners for view, edit, and delete buttons
    document.querySelectorAll("#studentsTableBody .view-student-btn").forEach((btn) => {
      btn.addEventListener("click", (e) => {
        e.preventDefault()
        const studentId = btn.getAttribute("data-student-id")
        populateStudentViewModal(studentId)
      })
    })

    document.querySelectorAll("#studentsTableBody .edit-student-btn").forEach((btn) => {
      btn.addEventListener("click", (e) => {
        e.preventDefault()
        const studentId = btn.getAttribute("data-student-id")
        populateStudentEditModal(studentId)
      })
    })

    document.querySelectorAll("#studentsTableBody .delete-student-btn").forEach((btn) => {
      btn.addEventListener("click", (e) => {
        e.preventDefault()
        const studentId = btn.getAttribute("data-student-id")
        if (confirm("Are you really sure you want to delete this student?")) {
          deleteStudent(studentId)
        }
      })
    })
  }

  // Load Students List
  function loadStudents() {
    fetch('../PHP/get_students.php')
    .then(response => response.json())
    .then(data => {
      if (data.error) {
        console.error('Error fetching students:', data.error)
        return
      }

      populateStudentsTable(data)
    })
    .catch(error => {
      console.error('Error loading students:', error)
    })
  }

  // Perform Student Search and Filter
  function performStudentSearch() {
    const searchQuery = document.getElementById("studentSearchInput").value.toLowerCase().trim()
    const departmentFilter = document.getElementById("departmentFilter").value
    const statusFilter = document.getElementById("statusFilter").value

    fetch('../PHP/get_students.php')
    .then(response => response.json())
    .then(data => {
      if (data.error) {
        console.error('Error fetching students:', data.error)
        return
      }

      let filteredData = data

      // Apply search filter
      if (searchQuery) {
        filteredData = filteredData.filter(student =>
          student.first_name.toLowerCase().includes(searchQuery) ||
          student.last_name.toLowerCase().includes(searchQuery) ||
          student.email.toLowerCase().includes(searchQuery) ||
          student.Matric_No.toLowerCase().includes(searchQuery) ||
          student.student_id.toString().toLowerCase().includes(searchQuery)
        )
      }

      // Apply department filter
      if (departmentFilter) {
        filteredData = filteredData.filter(student => student.Department === departmentFilter)
      }

      // Apply status filter (assuming all students are Active since status not in DB)
      if (statusFilter && statusFilter !== 'All') {
        if (statusFilter === 'Active') {
          // Show all (assuming all are active)
        } else {
          // If other statuses, show none for now
          filteredData = []
        }
      }

      populateStudentsTable(filteredData)
    })
    .catch(error => {
      console.error('Error performing search:', error)
    })
  }

  // Load Recent Activities
  function loadRecentActivities() {
fetch('../PHP/get_recent_activities.php')
    .then(response => response.json())
    .then(data => {
      if (data.error) {
        console.error('Error fetching recent activities:', data.error)
        return
      }

      const container = document.getElementById('recentActivitiesContainer')
      if (!container) return

      if (data.length === 0) {
        container.innerHTML = `
          <div class="text-center text-muted py-3">
            <i class="fas fa-info-circle fa-2x mb-2"></i>
            <p>No recent activity yet</p>
          </div>
        `
        return
      }

      container.innerHTML = ''

      data.forEach(activity => {
        const activityItem = document.createElement('div')
        activityItem.className = 'd-flex align-items-center mb-3'
        activityItem.innerHTML = `
          <div class="me-3">
            <i class="${activity.icon}"></i>
          </div>
          <div class="flex-grow-1">
            <div class="small text-gray-900">${activity.description}</div>
            <div class="small text-gray-500">${new Date(activity.timestamp).toLocaleString()}</div>
          </div>
        `
        container.appendChild(activityItem)
      })
    })
    .catch(error => {
      console.error('Error loading recent activities:', error)
      const container = document.getElementById('recentActivitiesContainer')
      if (container) {
        container.innerHTML = `
          <div class="text-center text-muted py-3">
            <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
            <p>Failed to load activities</p>
          </div>
        `
      }
    })
  }

  // Load Courses List
  function loadCourses() {
    fetch('../PHP/get_courses.php')
    .then(response => response.json())
    .then(data => {
      if (data.error) {
        console.error('Error fetching courses:', data.error)
        return
      }

      populateCoursesTable(data)
    })
    .catch(error => {
      console.error('Error loading courses:', error)
    })
  }

  // Populate Courses Table
  function populateCoursesTable(data) {
    const coursesTableBody = document.getElementById('coursesTableBody')
    if (!coursesTableBody) return

    coursesTableBody.innerHTML = ''

    if (data.length === 0) {
      coursesTableBody.innerHTML = `
        <tr>
          <td colspan="9" class="text-center text-muted">No courses found</td>
        </tr>
      `
      return
    }

    data.forEach((course, index) => {
      const row = document.createElement('tr')
      row.innerHTML = `
        <td class="text-center">${index + 1}</td>
        <td>${course.course_code || 'N/A'}</td>
        <td>${course.course_title || 'N/A'}</td>
        <td>${course.department || 'N/A'}</td>
        <td>${course.lecturer_name || 'Unassigned'}</td>
        <td>${course.level || 'N/A'}</td>
        <td>${course.semester && course.semester.trim() !== '' ? course.semester : 'N/A'}</td>
        <td>${course.course_unit || 'N/A'}</td>
        <td>
          <button class="btn btn-sm btn-outline-info view-students-btn" data-course-code="${course.course_code}" data-bs-toggle="modal" data-bs-target="#viewCourseStudentsModal">
            <i class="fas fa-users"></i>
          </button>
          <button class="btn btn-sm btn-outline-primary edit-course-btn" data-course-code="${course.course_code}" data-bs-toggle="modal" data-bs-target="#editCourseModal">
            <i class="fas fa-edit"></i>
          </button>
          <button class="btn btn-sm btn-outline-danger delete-course-btn" data-course-code="${course.course_code}">
            <i class="fas fa-trash"></i>
          </button>
        </td>
      `
      coursesTableBody.appendChild(row)
    })

    // Re-attach event listeners for view, edit, and delete buttons
    document.querySelectorAll(".view-students-btn").forEach((btn) => {
      btn.addEventListener("click", (e) => {
        e.preventDefault()
        const courseCode = btn.getAttribute("data-course-code")
        populateCourseStudentsModal(courseCode)
      })
    })

    document.querySelectorAll(".edit-course-btn").forEach((btn) => {
      btn.addEventListener("click", (e) => {
        e.preventDefault()
        const courseCode = btn.getAttribute("data-course-code")
        populateCourseEditModal(courseCode)
      })
    })

    document.querySelectorAll(".delete-course-btn").forEach((btn) => {
      btn.addEventListener("click", (e) => {
        e.preventDefault()
        const courseCode = btn.getAttribute("data-course-code")
        if (confirm("Are you sure you want to delete this course? This action cannot be undone.")) {
          deleteCourse(courseCode)
        }
      })
    })
  }

  // Perform Course Search and Filter
  function performCourseSearch() {
    const searchQuery = document.getElementById("courseSearchInput").value.toLowerCase().trim()
    const departmentFilter = document.getElementById("courseDepartmentFilter").value
    const levelFilter = document.getElementById("courseLevelFilter").value

    fetch('../PHP/get_courses.php')
    .then(response => response.json())
    .then(data => {
      if (data.error) {
        console.error('Error fetching courses:', data.error)
        return
      }

      let filteredData = Array.isArray(data) ? data : []

      // Apply search filter - search across ALL fields
      if (searchQuery) {
        filteredData = filteredData.filter(course => {
          // Convert all searchable fields to strings and check if they include the search query
          const courseCode = (course.course_code || '').toLowerCase()
          const courseTitle = (course.course_title || '').toLowerCase()
          const courseDescription = (course.course_description || '').toLowerCase()
          const courseUnit = String(course.course_unit || '').toLowerCase()
          const department = (course.department || '').toLowerCase()
          const level = (course.level || '').toLowerCase()
          const semester = (course.semester || '').toLowerCase()
          const lecturerName = (course.lecturer_name || '').toLowerCase()
          const courseId = String(course.course_id || '').toLowerCase()

          return courseCode.includes(searchQuery) ||
                 courseTitle.includes(searchQuery) ||
                 courseDescription.includes(searchQuery) ||
                 courseUnit.includes(searchQuery) ||
                 department.includes(searchQuery) ||
                 level.includes(searchQuery) ||
                 semester.includes(searchQuery) ||
                 lecturerName.includes(searchQuery) ||
                 courseId.includes(searchQuery)
        })
      }

      // Apply department filter
      if (departmentFilter) {
        filteredData = filteredData.filter(course => course.department === departmentFilter)
      }

      // Apply level filter
      if (levelFilter) {
        filteredData = filteredData.filter(course => course.level === levelFilter)
      }

      populateCoursesTable(filteredData)
    })
    .catch(error => {
      console.error('Error performing course search:', error)
    })
  }

  // Load Lecturers List
  function loadLecturers() {
    fetch('../PHP/get_lecturers.php')
    .then(response => response.json())
    .then(data => {
      if (data.error) {
        console.error('Error fetching lecturers:', data.error)
        return
      }

      populateLecturersTable(data)
    })
    .catch(error => {
      console.error('Error loading lecturers:', error)
    })
  }

  // Load Lecturers for Course Dropdown
  function loadLecturersForCourse() {
    const lecturerSelect = document.getElementById('course_lecturer')
    if (!lecturerSelect) return

    fetch('../PHP/get_lecturers.php')
    .then(response => response.json())
    .then(data => {
      if (data.error) {
        console.error('Error fetching lecturers:', data.error)
        return
      }

      // Clear existing options except the first one
      lecturerSelect.innerHTML = '<option value="">Select Lecturer</option>'

      // Add lecturers to dropdown
      if (Array.isArray(data) && data.length > 0) {
        data.forEach(lecturer => {
          const option = document.createElement('option')
          option.value = lecturer.lecturer_id
          option.textContent = `${lecturer.first_name} ${lecturer.last_name}`
          lecturerSelect.appendChild(option)
        })
      } else {
        const option = document.createElement('option')
        option.textContent = 'No lecturers available'
        option.disabled = true
        lecturerSelect.appendChild(option)
      }
    })
    .catch(error => {
      console.error('Error loading lecturers for course:', error)
      lecturerSelect.innerHTML = '<option value="">Error loading lecturers</option>'
    })
  }

  // Populate Lecturers Table
  function populateLecturersTable(data) {
    const lecturersTableBody = document.getElementById('lecturersTableBody')
    if (!lecturersTableBody) return

    lecturersTableBody.innerHTML = ''

    if (data.length === 0) {
      lecturersTableBody.innerHTML = `
        <tr>
          <td colspan="8" class="text-center text-muted">No lecturers found</td>
        </tr>
      `
      return
    }

    data.forEach((lecturer, index) => {
      const row = document.createElement('tr')
      row.innerHTML = `
        <td class="text-center">${index + 1}</td>
        <td>${lecturer.first_name} ${lecturer.last_name}</td>
        <td>${lecturer.email}</td>
        <td>${lecturer.department || 'N/A'}</td>
        <td>${lecturer.gender || 'N/A'}</td>
        <td>${lecturer.phone || 'N/A'}</td>
        <td><span class="badge bg-success">Active</span></td>
        <td>
          <button class="btn btn-sm btn-outline-primary edit-lecturer-btn" data-lecturer-id="${lecturer.lecturer_id}" data-bs-toggle="modal" data-bs-target="#editLecturerModal">
            <i class="fas fa-edit"></i>
          </button>
          <button class="btn btn-sm btn-outline-danger delete-lecturer-btn" data-lecturer-id="${lecturer.lecturer_id}">
            <i class="fas fa-trash"></i>
          </button>
        </td>
      `
      lecturersTableBody.appendChild(row)
    })

    // Re-attach event listeners for edit and delete buttons
    document.querySelectorAll(".edit-lecturer-btn").forEach((btn) => {
      btn.addEventListener("click", (e) => {
        e.preventDefault()
        const lecturerId = btn.getAttribute("data-lecturer-id")
        populateLecturerEditModal(lecturerId)
      })
    })

    document.querySelectorAll(".delete-lecturer-btn").forEach((btn) => {
      btn.addEventListener("click", (e) => {
        e.preventDefault()
        const lecturerId = btn.getAttribute("data-lecturer-id")
        if (confirm("Are you sure you want to delete this lecturer? This action cannot be undone.")) {
          deleteLecturer(lecturerId)
        }
      })
    })
  }

  function deleteLecturer(lecturerId) {
    const formData = new FormData();
    formData.append('lecturer_id', lecturerId);

    fetch('../PHP/delete_lecturer.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert("Lecturer deleted successfully!");
            loadLecturers();
            loadDashboardStats();
        } else {
            alert("Error: " + (data.errors ? data.errors.join(", ") : "Failed to delete lecturer"));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert("An error occurred while deleting the lecturer");
    });
}


  // Perform Lecturer Search and Filter
  function performLecturerSearch() {
    const searchQuery = document.getElementById("lecturerSearchInput").value.toLowerCase().trim()
    const departmentFilter = document.getElementById("lecturerDepartmentFilter").value

    fetch('../PHP/get_lecturers.php')
    .then(response => response.json())
    .then(data => {
      if (data.error) {
        console.error('Error fetching lecturers:', data.error)
        return
      }

      let filteredData = Array.isArray(data) ? data : []

      // Apply search filter
      if (searchQuery) {
        filteredData = filteredData.filter(lecturer =>
          (lecturer.first_name && lecturer.first_name.toLowerCase().includes(searchQuery)) ||
          (lecturer.last_name && lecturer.last_name.toLowerCase().includes(searchQuery)) ||
          (lecturer.email && lecturer.email.toLowerCase().includes(searchQuery)) ||
          (lecturer.phone && lecturer.phone.toLowerCase().includes(searchQuery)) ||
          (lecturer.lecturer_id && lecturer.lecturer_id.toString().toLowerCase().includes(searchQuery))
        )
      }

      // Apply department filter
      if (departmentFilter) {
        filteredData = filteredData.filter(lecturer => lecturer.department === departmentFilter)
      }

      populateLecturersTable(filteredData)
    })
    .catch(error => {
      console.error('Error performing lecturer search:', error)
    })
  }

  // Handler function for Add Lecturer Form
  function handleAddLecturerForm(form) {
    const errorDiv = document.getElementById('addLecturerErrors')
    const successDiv = document.getElementById('addLecturerSuccess')

    if (errorDiv) {
      errorDiv.style.display = 'none'
      errorDiv.innerHTML = ''
    }
    if (successDiv) {
      successDiv.style.display = 'none'
      successDiv.innerHTML = ''
    }

    const formData = new FormData(form)
    const firstName = formData.get('first_name')?.trim() || ''
    const lastName = formData.get('last_name')?.trim() || ''
    const email = formData.get('email')?.trim() || ''
    const phoneNum = formData.get('phone_num')?.trim() || ''
    const department = formData.get('department')?.trim() || ''
    const gender = formData.get('gender')?.trim() || ''
    const password = formData.get('password') || ''

    if (!firstName || !lastName || !email || !phoneNum || !department || !gender || !password) {
      if (errorDiv) {
        errorDiv.style.display = 'block'
        errorDiv.textContent = "Please fill in all required fields."
      }
      return
    }

    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
    if (!emailRegex.test(email)) {
      if (errorDiv) {
        errorDiv.style.display = 'block'
        errorDiv.textContent = "Please enter a valid email address."
      }
      return
    }

    if (password.length < 8) {
      if (errorDiv) {
        errorDiv.style.display = 'block'
        errorDiv.textContent = "Password must be at least 8 characters long."
      }
      return
    }

    fetch('../PHP/add_lecturer.php', {
      method: 'POST',
      body: formData,
      credentials: 'include'
    })
    .then(response => {
      const contentType = response.headers.get("content-type") || "";
      if (!contentType.includes("application/json")) {
        return response.text().then(text => {
          throw new Error("Unexpected server response: " + text);
        });
      }
      if (response.status === 403) {
        throw new Error("Unauthorized access. Please login as admin.");
      }
      return response.json();
    })
    .then(data => {
      if (data.success) {
        if (successDiv) {
          successDiv.style.display = 'block'
          successDiv.textContent = "Lecturer added successfully!"
        }
        form.reset()
        loadLecturers()
        loadDashboardStats()

        setTimeout(() => {
          const modalElement = document.getElementById("addLecturerModal")
          const modal = window.bootstrap.Modal.getInstance(modalElement)
          if (modal) modal.hide()
        }, 3000)
      } else {
        if (errorDiv) {
          errorDiv.style.display = 'block'
          errorDiv.textContent = data.errors ? data.errors.join(", ") : "Error adding lecturer."
        }
      }
    })
    .catch(error => {
      console.error('Error adding lecturer:', error)
      if (errorDiv) {
        errorDiv.style.display = 'block'
        errorDiv.textContent = "An error occurred while adding the lecturer."
      }
    })
  }

// Grades Functions
function loadGrades() {
  fetch('../PHP/get_grades.php')
  .then(response => response.json())
  .then(data => {
    if (data.error) {
      console.error('Error fetching grades:', data.error)
      return
    }
    populateGradesTable(data)
  })
  .catch(error => {
    console.error('Error loading grades:', error)
  })
}

function performGradesSearch() {
  const searchQuery = document.getElementById("gradesSearchInput").value.toLowerCase().trim()
  const courseFilter = document.getElementById("gradesCourseFilter").value
  const departmentFilter = document.getElementById("gradesDepartmentFilter").value

  fetch('../PHP/get_grades.php')
  .then(response => response.json())
  .then(data => {
    if (data.error) {
      console.error('Error fetching grades:', data.error)
      return
    }

    let filteredData = Array.isArray(data) ? data : []

    // Apply search filter - search across all relevant fields
    if (searchQuery) {
      filteredData = filteredData.filter(grade => {
        // Student-related fields
        const studentName = `${grade.first_name || ''} ${grade.last_name || ''}`.toLowerCase().trim()
        const matricNo = (grade.Matric_No || '').toLowerCase()
        const studentEmail = (grade.email || '').toLowerCase()
        const studentDepartment = (grade.Department || '').toLowerCase()
        const studentLevel = (grade.Level || '').toLowerCase()
        const studentId = String(grade.student_id || '').toLowerCase()

        // Course-related fields
        const courseCode = (grade.course_code || '').toLowerCase()
        const courseTitle = (grade.course_title || '').toLowerCase()
        const courseDepartment = (grade.course_department || '').toLowerCase()
        const courseLevel = (grade.course_level || '').toLowerCase()
        const semester = (grade.semester || '').toLowerCase()
        const courseUnit = String(grade.course_unit || '').toLowerCase()

        // Lecturer-related fields
        const lecturerName = (grade.lecturer_name || '').toLowerCase()

        // Grade-related fields
        const gradeValue = (grade.grade || '').toLowerCase()

        return studentName.includes(searchQuery) ||
               matricNo.includes(searchQuery) ||
               studentEmail.includes(searchQuery) ||
               studentDepartment.includes(searchQuery) ||
               studentLevel.includes(searchQuery) ||
               studentId.includes(searchQuery) ||
               courseCode.includes(searchQuery) ||
               courseTitle.includes(searchQuery) ||
               courseDepartment.includes(searchQuery) ||
               courseLevel.includes(searchQuery) ||
               semester.includes(searchQuery) ||
               courseUnit.includes(searchQuery) ||
               lecturerName.includes(searchQuery) ||
               gradeValue.includes(searchQuery)
      })
    }

    // Apply course filter
    if (courseFilter) {
      filteredData = filteredData.filter(grade => grade.course_code === courseFilter)
    }

    // Apply department filter
    if (departmentFilter) {
      filteredData = filteredData.filter(grade => grade.Department === departmentFilter)
    }

    populateGradesTable(filteredData)
  })
  .catch(error => {
    console.error('Error performing grades search:', error)
  })
}

function populateGradesTable(data) {
  const gradesTableBody = document.getElementById('gradesTableBody')
  if (!gradesTableBody) return

  gradesTableBody.innerHTML = ''

  if (data.length === 0) {
    gradesTableBody.innerHTML = `
      <tr>
        <td colspan="10" class="text-center text-muted">No grades found</td>
      </tr>
    `
    return
  }

  data.forEach((grade, index) => {
    const row = document.createElement('tr')
    row.innerHTML = `
      <td class="text-center">${index + 1}</td>
      <td>${grade.student_name || 'N/A'}</td>
      <td>${grade.matric_no || 'N/A'}</td>
      <td>${grade.course_code || 'N/A'}</td>
      <td>${grade.course_title || 'N/A'}</td>
      <td>${grade.Department || 'N/A'}</td>
      <td>${grade.Level || 'N/A'}</td>
      <td>${grade.lecturer_name || 'N/A'}</td>
      <td>${grade.grade || 'N/A'}</td>
      <td>${grade.grade_point || 'N/A'}</td>
    `
    gradesTableBody.appendChild(row)
  })
}

// Load courses for grades filter
function loadCoursesForGradesFilter() {
  const courseFilter = document.getElementById('gradesCourseFilter')
  if (!courseFilter) return

  fetch('../PHP/get_courses.php')
  .then(response => response.json())
  .then(data => {
    if (data.error) {
      console.error('Error fetching courses for grades filter:', data.error)
      return
    }

    // Clear existing options
    courseFilter.innerHTML = '<option value="">All Courses</option>'

    // Add courses to dropdown
    if (Array.isArray(data) && data.length > 0) {
      data.forEach(course => {
        const option = document.createElement('option')
        option.value = course.course_code
        option.textContent = `${course.course_code} - ${course.course_title}`
        courseFilter.appendChild(option)
      })
    }
  })
  .catch(error => {
    console.error('Error loading courses for grades filter:', error)
  })
}

// Load students for reports
function loadStudentsForReports() {
  const studentSelect = document.getElementById('studentReportSelect')
  if (!studentSelect) return

  fetch('../PHP/get_students.php')
  .then(response => response.json())
  .then(data => {
    if (data.error) {
      console.error('Error fetching students for reports:', data.error)
      return
    }

    // Clear existing options
    studentSelect.innerHTML = '<option value="">All Students</option>'

    // Add students to dropdown
    if (Array.isArray(data) && data.length > 0) {
      data.forEach(student => {
        const option = document.createElement('option')
        option.value = student.student_id
        option.textContent = `${student.Matric_No} - ${student.first_name} ${student.last_name}`
        studentSelect.appendChild(option)
      })
    }
  })
  .catch(error => {
    console.error('Error loading students for reports:', error)
  })
}

// Load courses for reports
function loadCoursesForReports() {
  const courseSelect = document.getElementById('courseReportSelect')
  if (!courseSelect) return

  fetch('../PHP/get_courses.php')
  .then(response => response.json())
  .then(data => {
    if (data.error) {
      console.error('Error fetching courses for reports:', data.error)
      return
    }

    // Clear existing options
    courseSelect.innerHTML = '<option value="">All Courses</option>'

    // Add courses to dropdown
    if (Array.isArray(data) && data.length > 0) {
      data.forEach(course => {
        const option = document.createElement('option')
        option.value = course.course_code
        option.textContent = `${course.course_code} - ${course.course_title}`
        courseSelect.appendChild(option)
      })
    }
  })
  .catch(error => {
    console.error('Error loading courses for reports:', error)
  })
}

// Report generation functions
function generateStudentReport() {
  const studentId = document.getElementById('studentReportSelect').value
  const startDate = document.getElementById('studentReportStartDate').value
  const endDate = document.getElementById('studentReportEndDate').value

  if (!studentId) {
    alert('Please select a student')
    return
  }

  const params = {
    student_id: studentId,
    start_date: startDate,
    end_date: endDate
  }

  console.log('Generating student report:', params)

  // For now, show a placeholder message
  // In a real implementation, this would fetch from a PHP endpoint
  alert('Student report generation feature will be implemented with PHP backend. Selected student: ' + studentId)
}

function generateCourseReport() {
  const courseCode = document.getElementById('courseReportSelect').value
  const startDate = document.getElementById('courseReportStartDate').value
  const endDate = document.getElementById('courseReportEndDate').value

  if (!courseCode) {
    alert('Please select a course')
    return
  }

  const params = {
    course_code: courseCode,
    start_date: startDate,
    end_date: endDate
  }

  console.log('Generating course report:', params)

  // For now, show a placeholder message
  // In a real implementation, this would fetch from a PHP endpoint
  alert('Course report generation feature will be implemented with PHP backend. Selected course: ' + courseCode)
}

function generateDepartmentReport() {
  const department = document.getElementById('departmentReportSelect').value
  const startDate = document.getElementById('departmentReportStartDate').value
  const endDate = document.getElementById('departmentReportEndDate').value

  if (!department) {
    alert('Please select a department')
    return
  }

  const params = {
    department: department,
    start_date: startDate,
    end_date: endDate
  }

  console.log('Generating department report:', params)

  // For now, show a placeholder message
  // In a real implementation, this would fetch from a PHP endpoint
  alert('Department report generation feature will be implemented with PHP backend. Selected department: ' + department)
}

function generateSystemReport() {
  const reportType = document.getElementById('systemReportType').value
  const startDate = document.getElementById('systemReportStartDate').value
  const endDate = document.getElementById('systemReportEndDate').value

  const params = {
    report_type: reportType,
    start_date: startDate,
    end_date: endDate
  }

  console.log('Generating system report:', params)

  // For now, show a placeholder message
  // In a real implementation, this would fetch from a PHP endpoint
  alert('System report generation feature will be implemented with PHP backend. Report type: ' + reportType)
}
  