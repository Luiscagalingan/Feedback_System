START TRANSACTION;

DROP TEMPORARY TABLE IF EXISTS student_id_migration;
CREATE TEMPORARY TABLE student_id_migration (
    old_student_id VARCHAR(50) NOT NULL PRIMARY KEY,
    new_student_id VARCHAR(50) NOT NULL UNIQUE
);

-- Convert every nonstandard ID to 23-xxxxx. Adding 10000 to the database
-- primary key keeps the generated values deterministic and collision-free
-- from the existing 23-00000/23-01000 ranges.
INSERT INTO student_id_migration (old_student_id, new_student_id)
SELECT student_id, CONCAT('23-', LPAD(id + 10000, 5, '0'))
FROM students
WHERE student_id NOT REGEXP '^23-[0-9]{5}$';

-- Preserve feedback and archived-record relationships before changing students.
UPDATE office_feedback f
JOIN student_id_migration m ON m.old_student_id = f.student_id
SET f.student_id = m.new_student_id;

UPDATE archived_students a
JOIN student_id_migration m ON m.old_student_id = a.student_id
SET a.student_id = m.new_student_id;

UPDATE students s
JOIN student_id_migration m ON m.old_student_id = s.student_id
SET s.student_id = m.new_student_id;

DROP TEMPORARY TABLE student_id_migration;
COMMIT;
