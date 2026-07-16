ALTER TABLE academic_teachers
  ADD COLUMN IF NOT EXISTS program VARCHAR(150) NULL AFTER college;
