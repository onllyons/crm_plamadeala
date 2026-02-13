-- Migration: support extra employee payments (advance/bonus/extra)
-- Run this once on an existing DB.

ALTER TABLE employee_payments
  MODIFY project_id int(11) NULL,
  ADD COLUMN payment_type ENUM('project','advance','bonus','extra') NOT NULL DEFAULT 'project' AFTER project_id;

UPDATE employee_payments
SET payment_type = 'project'
WHERE payment_type IS NULL;

ALTER TABLE employee_payments
  ADD INDEX idx_payment_type (payment_type),
  ADD INDEX idx_emp_proj_type (employee_id, project_id, payment_type);
