-- College-level Dean account. A NULL program means the Dean can manage
-- every program and student within the assigned COED college scope.
INSERT INTO academic_teachers
    (full_name, email, college, program, password, status)
VALUES
    ('dean_COED', 'dean.coed@college.edu', 'COED', NULL,
     '$2y$10$5PJuNKfyK5hrhuf8ydRyJ.EcUI/2Av42IsuaAqQr/pon2gH5Rssvu', 'active')
ON DUPLICATE KEY UPDATE
    full_name = VALUES(full_name),
    college = VALUES(college),
    program = NULL,
    password = VALUES(password),
    status = 'active';
