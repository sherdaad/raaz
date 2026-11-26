USE raaz;

-- Additive migration: preserves all existing users, messages and requests.
ALTER TABLE password_requests
  ADD COLUMN reset_token_hash VARCHAR(255) NULL,
  ADD COLUMN reset_token_expires_at DATETIME NULL,
  ADD COLUMN reset_token_used_at DATETIME NULL;
