START TRANSACTION;

UPDATE students
SET program = 'AB Psychology'
WHERE college = 'CAS'
  AND program <> 'AB Psychology';

-- Dean accounts remain college-wide and are not assigned to one program.
UPDATE academic_teachers
SET program = NULL
WHERE college = 'CAS';

COMMIT;
