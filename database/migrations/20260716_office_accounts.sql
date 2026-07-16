ALTER TABLE non_academic_teachers
  ADD COLUMN IF NOT EXISTS office_category ENUM('academic','nonacademic') NOT NULL DEFAULT 'nonacademic' AFTER office_key;

DELETE FROM non_academic_teachers WHERE office_key = 'utility';
DELETE FROM office_feedback WHERE office_key = 'utility';
UPDATE non_academic_teachers SET status = 'inactive' WHERE office_key = 'all';

INSERT INTO non_academic_teachers (full_name,email,office_key,office_category,service,password,status) VALUES
('office_library','library.office@plp.edu.ph','library','academic','Library Office','$2y$10$5PJuNKfyK5hrhuf8ydRyJ.EcUI/2Av42IsuaAqQr/pon2gH5Rssvu','active'),
('office_college_enrollment','college.enrollment@plp.edu.ph','college-enrollment','academic','College Enrollment Offices','$2y$10$5PJuNKfyK5hrhuf8ydRyJ.EcUI/2Av42IsuaAqQr/pon2gH5Rssvu','active'),
('office_teacher_consultation','teacher.consultation@plp.edu.ph','teacher-consultation','academic','Faculty / Teacher Consultation Services','$2y$10$5PJuNKfyK5hrhuf8ydRyJ.EcUI/2Av42IsuaAqQr/pon2gH5Rssvu','active'),
('office_registrar','registrar.office@plp.edu.ph','registrar','nonacademic','Registrar''s Office','$2y$10$5PJuNKfyK5hrhuf8ydRyJ.EcUI/2Av42IsuaAqQr/pon2gH5Rssvu','active'),
('office_guidance','guidance.office@plp.edu.ph','guidance','nonacademic','Guidance Office','$2y$10$5PJuNKfyK5hrhuf8ydRyJ.EcUI/2Av42IsuaAqQr/pon2gH5Rssvu','active'),
('office_clinic','clinic.office@plp.edu.ph','clinic','nonacademic','Clinic Office','$2y$10$5PJuNKfyK5hrhuf8ydRyJ.EcUI/2Av42IsuaAqQr/pon2gH5Rssvu','active'),
('office_accounting','accounting.office@plp.edu.ph','accounting','nonacademic','Accounting Office','$2y$10$5PJuNKfyK5hrhuf8ydRyJ.EcUI/2Av42IsuaAqQr/pon2gH5Rssvu','active'),
('office_student_organizations','student.organizations@plp.edu.ph','student-organizations','nonacademic','Office of Student Organizations','$2y$10$5PJuNKfyK5hrhuf8ydRyJ.EcUI/2Av42IsuaAqQr/pon2gH5Rssvu','active'),
('office_security','security.office@plp.edu.ph','security','nonacademic','Security Office','$2y$10$5PJuNKfyK5hrhuf8ydRyJ.EcUI/2Av42IsuaAqQr/pon2gH5Rssvu','active'),
('office_mis','mis.office@plp.edu.ph','mis','nonacademic','Management Information Systems (MIS) Office','$2y$10$5PJuNKfyK5hrhuf8ydRyJ.EcUI/2Av42IsuaAqQr/pon2gH5Rssvu','active'),
('office_admission','admission.office@plp.edu.ph','admission','nonacademic','Admission Services Office','$2y$10$5PJuNKfyK5hrhuf8ydRyJ.EcUI/2Av42IsuaAqQr/pon2gH5Rssvu','active'),
('office_student_services','student.services@plp.edu.ph','student-services','nonacademic','Student Services Office (SSO)','$2y$10$5PJuNKfyK5hrhuf8ydRyJ.EcUI/2Av42IsuaAqQr/pon2gH5Rssvu','active'),
('office_vp_administration_finance','vp.admin.finance@plp.edu.ph','vp-administration-finance','nonacademic','Office of the VP for Administration and Finance','$2y$10$5PJuNKfyK5hrhuf8ydRyJ.EcUI/2Av42IsuaAqQr/pon2gH5Rssvu','active')
ON DUPLICATE KEY UPDATE office_category=VALUES(office_category),service=VALUES(service),password=VALUES(password),status='active';
