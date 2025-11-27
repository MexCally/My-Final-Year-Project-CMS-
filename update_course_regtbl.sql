-- Add approval status column to course_regtbl if it doesn't exist
ALTER TABLE course_regtbl 
ADD COLUMN IF NOT EXISTS approval_status ENUM('pending', 'approved', 'declined') DEFAULT 'pending',
ADD COLUMN IF NOT EXISTS approved_by INT NULL,
ADD COLUMN IF NOT EXISTS approval_date DATETIME NULL,
ADD COLUMN IF NOT EXISTS approval_comments TEXT NULL;