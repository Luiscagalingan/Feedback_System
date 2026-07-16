ALTER TABLE audit_logs ADD COLUMN IF NOT EXISTS module VARCHAR(80) NOT NULL DEFAULT 'system' AFTER action;
ALTER TABLE audit_logs ADD COLUMN IF NOT EXISTS status ENUM('success','failed') NOT NULL DEFAULT 'success' AFTER module;
