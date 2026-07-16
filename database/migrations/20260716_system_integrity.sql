ALTER TABLE academic_teachers ADD COLUMN IF NOT EXISTS college VARCHAR(10) NULL AFTER email;
ALTER TABLE academic_teachers ADD COLUMN IF NOT EXISTS status ENUM('active','inactive','archived') NOT NULL DEFAULT 'active' AFTER password;
ALTER TABLE non_academic_teachers ADD COLUMN IF NOT EXISTS office_key VARCHAR(80) NULL AFTER email;
ALTER TABLE non_academic_teachers ADD COLUMN IF NOT EXISTS service VARCHAR(150) NULL AFTER office_key;
ALTER TABLE non_academic_teachers ADD COLUMN IF NOT EXISTS status ENUM('active','inactive','archived') NOT NULL DEFAULT 'active' AFTER password;

CREATE TABLE IF NOT EXISTS audit_logs (
  id BIGINT NOT NULL AUTO_INCREMENT,
  user_id INT NULL,
  role VARCHAR(30) NOT NULL,
  user_name VARCHAR(120) NOT NULL,
  action VARCHAR(80) NOT NULL,
  details TEXT NULL,
  ip_address VARCHAR(45) NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY audit_created_idx (created_at),
  KEY audit_role_user_idx (role, user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Existing dean records used college codes in their account names/emails.
UPDATE academic_teachers SET college = CASE
  WHEN LOWER(CONCAT(full_name,' ',email)) LIKE '%coed%' THEN 'COED'
  WHEN LOWER(CONCAT(full_name,' ',email)) LIKE '%cihm%' THEN 'CIHM'
  WHEN LOWER(CONCAT(full_name,' ',email)) LIKE '%ccs%' THEN 'CCS'
  WHEN LOWER(CONCAT(full_name,' ',email)) LIKE '%bsa%' OR LOWER(CONCAT(full_name,' ',email)) LIKE '%cba%' THEN 'BSA'
  WHEN LOWER(CONCAT(full_name,' ',email)) LIKE '%cas%' THEN 'CAS'
  WHEN LOWER(CONCAT(full_name,' ',email)) LIKE '%con%' THEN 'CON'
  WHEN LOWER(CONCAT(full_name,' ',email)) LIKE '%coe%' THEN 'COE'
  ELSE college END
WHERE college IS NULL OR college = '';

UPDATE non_academic_teachers SET office_key = 'all', service = 'All non-academic offices'
WHERE office_key IS NULL OR office_key = '';
