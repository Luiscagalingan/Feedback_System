START TRANSACTION;

UPDATE students
SET program = CASE UPPER(TRIM(program))
    WHEN 'BSIT' THEN 'BS Information Technology'
    WHEN 'BSCS' THEN 'BS Computer Science'
    ELSE program
END
WHERE college = 'CCS'
  AND UPPER(TRIM(program)) IN ('BSIT', 'BSCS');

UPDATE academic_teachers
SET program = CASE UPPER(TRIM(program))
    WHEN 'BSIT' THEN 'BS Information Technology'
    WHEN 'BSCS' THEN 'BS Computer Science'
    ELSE program
END
WHERE college = 'CCS'
  AND UPPER(TRIM(program)) IN ('BSIT', 'BSCS');

COMMIT;
