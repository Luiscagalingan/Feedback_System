START TRANSACTION;

-- College of Engineering: retain only Mechanical and Electronics.
UPDATE students
SET program = CASE
    WHEN program LIKE '%Electronics%' THEN 'BS Electronics Engineering'
    ELSE 'BS Mechanical Engineering'
END
WHERE college = 'COE';

-- Business/Accountancy: retain only Entrepreneurship and Accountancy.
UPDATE students
SET program = CASE
    WHEN program LIKE '%Accountancy%' THEN 'BS Accountancy'
    ELSE 'BS Entrepreneurship'
END
WHERE college IN ('BSA', 'CBA');

-- Keep existing Dean program assignments valid after normalization.
UPDATE academic_teachers
SET program = CASE
    WHEN program LIKE '%Electronics%' THEN 'BS Electronics Engineering'
    ELSE 'BS Mechanical Engineering'
END
WHERE college = 'COE' AND program IS NOT NULL AND program <> '';

UPDATE academic_teachers
SET program = CASE
    WHEN program LIKE '%Accountancy%' THEN 'BS Accountancy'
    ELSE 'BS Entrepreneurship'
END
WHERE college IN ('BSA', 'CBA') AND program IS NOT NULL AND program <> '';

COMMIT;
