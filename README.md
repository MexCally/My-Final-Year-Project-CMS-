# Course Management System (CMS)
# My-Final-Year-Project-CMS-

A full-featured **web-based Course Management System** designed to streamline academic workflows for institutions.
This project serves as a **Final Year Project (FYP)** and demonstrates efficient management of courses, students, lecturers, and academic content.

The CMS provides dynamic dashboards for Admin, Lecturer, and Student roles, ensuring smooth communication, task management, and resource accessibility.

---

## 🚀 **Project Overview**

The Course Management System is built to simplify common academic operations within tertiary institutions.
It provides:

* Centralized course registration and approval
* Easy communication between admin, lecturers, and students
* Upload/download of course materials & assignments
* Automated dashboard statistics
* Secure authentication for all user roles

The system is optimized for scalability, maintainability, and real-world deployment.

---

## 🎯 **Key Features**

### **1. Admin Dashboard**

* Add, edit, delete:

  * Students
  * Lecturers
  * Courses
* Approve student course registration before PDF download
* Update school details
* View system-wide statistics
* Manage academic sessions
* Secure control panel for full system configuration

---

### **2. Lecturer Dashboard**

* Dynamic display of lecturer profile and departmental info
* Upload:

  * Lecture materials
  * Assignments
* View submitted student assignments
* Post announcements
* Manage personal contact information
* Real-time dashboard statistics

---

### **3. Student Dashboard**

* Register courses (cannot edit department & level)
* Download approved course registration PDF
* View and download lecture materials
* Submit assignments
* Update personal information (name, email, phone only)
* Track submission deadlines and lecturer announcements

---

## 🛠️ **Tech Stack**

### **Frontend**

* HTML5
* CSS3
* JavaScript
* AJAX (dynamic dashboard updates)

### **Backend**

* PHP OOP
* MySQL / PDO
* Sessions for authentication
* Secure form validation

### **Tools / Other**

* XAMPP / WAMP for local testing
* Git & GitHub for version control
* PHPMailer (optional for notifications)

---

## 🔐 **User Roles and Access Levels**

| Role         | Permissions                                                    |
| ------------ | -------------------------------------------------------------- |
| **Admin**    | Full system control, approval rights, CRUD operations          |
| **Lecturer** | Upload materials/assignments, view submissions, update profile |
| **Student**  | Register courses, submit assignments, download materials       |

Each role has its own dashboard with personalized statistics and UI.

---

## 📁 **Project Structure**

```
/CMS
│── /admin
│── /lecturer
│── /student
│── /config
│── /PHP
│── /assets
│── index.php
│── login.php
│── README.md
```

---

## ⚙️ **Setup Instructions**

### **1. Clone the Repository**

```
git clone https://github.com/YourUsername/My-Final-Year-Project-CMS.git
```

### **2. Configure Database**

* Import the SQL file into phpMyAdmin
* Update `/config/db.php` with your database credentials

### **3. Start Local Server**

Use XAMPP, WAMP, or Laragon:

* Start **Apache**
* Start **MySQL**
* Visit:

```
http://localhost/CMS/
```

---

## 📄 **Core Modules**

### **Admin Modules**

* Manage Students
* Manage Lecturers
* Manage Courses
* Approve Registrations
* Dashboard Statistics

### **Lecturer Modules**

* Upload Materials
* Post Assignments
* View Assignment Submissions
* Update Profile

### **Student Modules**

* Course Registration
* Submission Upload
* PDF Download (after admin approval)
* View Materials

---

## 🧪 **Testing & Validation**

The system includes:

* Server-side validation
* Client-side validation
* Error handling
* Session timeout security
* Database sanitization using PDO prepared statements

---

## 🔮 **Future Improvements**

* Add email notifications for assignment updates
* Integrate real-time chat
* Add admin analytics charts
* Implement API for mobile app version
* Add role-based permissions middleware

---

## 📝 **License**

This project is open-source and free to modify for educational purposes.

---

## 👤 **Developer**

**NAME:** **Olisa Emeka Callistus**

**Final Year Project** – Course Management System(**CMS**)

**Contact:** 07048508115 / 09051171814

**Email:** [emekaolisa232@gmail.com](mailto:emekaolisa232@gmail.com)

---
