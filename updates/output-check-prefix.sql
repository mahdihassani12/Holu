-- Back up the database before running this once in phpMyAdmin.
-- Only changes the initial X- in output check numbers. No schema changes.
-- Numbers that would conflict with another saved output are skipped.
START TRANSACTION;
UPDATE expenses AS old_output
LEFT JOIN expenses AS existing_output
  ON existing_output.check_number = CONCAT('O-', SUBSTRING(old_output.check_number, 3))
  AND existing_output.id <> old_output.id
SET old_output.check_number = CONCAT('O-', SUBSTRING(old_output.check_number, 3))
WHERE old_output.check_number LIKE 'X-%'
  AND existing_output.id IS NULL;
COMMIT;
-- Any remaining rows need review because their O- number already exists.
SELECT id, check_number FROM expenses WHERE check_number LIKE 'X-%';
