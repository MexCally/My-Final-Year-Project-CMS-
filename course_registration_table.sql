CREATE TABLE course_registrations (
    registration_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    semester VARCHAR(20) NOT NULL,
    academic_year VARCHAR(10) NOT NULL,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    submission_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    approval_date TIMESTAMP NULL,
    approved_by INT NULL,
    total_units INT NOT NULL,
    FOREIGN KEY (student_id) REFERENCES studenttbl(student_id),
    FOREIGN KEY (approved_by) REFERENCES admintbl(admin_id)
);

CREATE TABLE course_registration_items (
    item_id INT AUTO_INCREMENT PRIMARY KEY,
    registration_id INT NOT NULL,
    course_id INT NOT NULL,
    FOREIGN KEY (registration_id) REFERENCES course_registrations(registration_id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES coursetbl(course_id)
);