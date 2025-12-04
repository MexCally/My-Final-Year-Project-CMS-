-- Create table for tracking grade submissions to records office
CREATE TABLE IF NOT EXISTS grade_submissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    course_id INT NOT NULL,
    lecturer_id INT NOT NULL,
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    academic_year VARCHAR(20),
    semester VARCHAR(20),
    UNIQUE KEY unique_submission (course_id, lecturer_id, academic_year, semester),
    FOREIGN KEY (course_id) REFERENCES coursetbl(course_id),
    FOREIGN KEY (lecturer_id) REFERENCES lecturertbl(LecturerID)
);

-- Add columns to evaluationtbl for tracking submission status
ALTER TABLE evaluationtbl 
ADD COLUMN IF NOT EXISTS submitted_to_records TINYINT(1) DEFAULT 0,
ADD COLUMN IF NOT EXISTS submitted_at TIMESTAMP NULL,
ADD COLUMN IF NOT EXISTS submitted_by INT NULL,
ADD COLUMN IF NOT EXISTS published TINYINT(1) DEFAULT 0,
ADD COLUMN IF NOT EXISTS published_at TIMESTAMP NULL,
ADD COLUMN IF NOT EXISTS published_by INT NULL;

-- Add columns to grade_submissions for approval tracking
ALTER TABLE grade_submissions
ADD COLUMN IF NOT EXISTS approved_at TIMESTAMP NULL,
ADD COLUMN IF NOT EXISTS approved_by INT NULL;