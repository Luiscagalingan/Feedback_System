ALTER TABLE students ADD COLUMN IF NOT EXISTS archived_at DATETIME NULL AFTER status;
ALTER TABLE non_academic_teachers ADD UNIQUE KEY IF NOT EXISTS non_academic_office_unique (office_key);

-- Preserve and migrate records from the legacy archive table into soft-archived students.
INSERT IGNORE INTO students (student_id, student_name, program, college, section, email, password, created_at, otp, status, archived_at)
SELECT student_id, student_name, program, COALESCE(NULLIF(college,''),'CAS'), section, email, password, created_at, otp, 'archived', NOW()
FROM archived_students;
