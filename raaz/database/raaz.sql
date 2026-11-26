CREATE DATABASE IF NOT EXISTS raaz CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE raaz;
SET FOREIGN_KEY_CHECKS=0;
DROP TABLE IF EXISTS admin_audit_logs,admin_message_archive,reports,status_views,statuses,message_receipts,messages,conversation_members,conversations,blocked_users,password_requests,screenshot_requests,recording_requests,admin,users;
SET FOREIGN_KEY_CHECKS=1;

CREATE TABLE users (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 username VARCHAR(30) NOT NULL UNIQUE,
 full_name VARCHAR(80) NOT NULL,
 phone VARCHAR(24) NOT NULL,
 password_hash VARCHAR(255) NOT NULL,
 profile_picture VARCHAR(255) NULL,
 status_text VARCHAR(160) NULL,
 account_status ENUM('active','suspended') NOT NULL DEFAULT 'active',
 last_seen DATETIME NULL,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 moderation_notice_accepted_at DATETIME NULL,
 INDEX idx_users_name(full_name), INDEX idx_users_active(account_status)
) ENGINE=InnoDB;

CREATE TABLE conversations (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 UNIQUE KEY uq_conversation_id(id)
) ENGINE=InnoDB;

CREATE TABLE conversation_members (
 conversation_id BIGINT UNSIGNED NOT NULL,
 user_id BIGINT UNSIGNED NOT NULL,
 joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 PRIMARY KEY(conversation_id,user_id),
 FOREIGN KEY(conversation_id) REFERENCES conversations(id) ON DELETE CASCADE,
 FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE,
 INDEX idx_member_user(user_id)
) ENGINE=InnoDB;

CREATE TABLE messages (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 conversation_id BIGINT UNSIGNED NOT NULL,
 sender_id BIGINT UNSIGNED NOT NULL,
 body MEDIUMTEXT NOT NULL,
 created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 read_at DATETIME NULL,
 expires_at DATETIME NULL,
 FOREIGN KEY(conversation_id) REFERENCES conversations(id) ON DELETE CASCADE,
 FOREIGN KEY(sender_id) REFERENCES users(id) ON DELETE CASCADE,
 INDEX idx_messages_conversation(conversation_id,id),
 INDEX idx_messages_expiry(expires_at),
 INDEX idx_messages_read(read_at)
) ENGINE=InnoDB;

-- Separate moderation archive: independent of the short-lived user message table.
-- Users must be informed before using messaging that authorized admins can review messages.
CREATE TABLE admin_message_archive (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 conversation_id BIGINT UNSIGNED NOT NULL,
 sender_id BIGINT UNSIGNED NOT NULL,
 body MEDIUMTEXT NOT NULL,
 sent_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 INDEX idx_archive_conversation(conversation_id,id),
 INDEX idx_archive_sender(sender_id),
 INDEX idx_archive_sent(sent_at),
 FOREIGN KEY(conversation_id) REFERENCES conversations(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE message_receipts (
 message_id BIGINT UNSIGNED NOT NULL,
 user_id BIGINT UNSIGNED NOT NULL,
 delivered_at DATETIME NULL,
 seen_at DATETIME NULL,
 PRIMARY KEY(message_id,user_id),
 FOREIGN KEY(message_id) REFERENCES messages(id) ON DELETE CASCADE,
 FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE statuses (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 user_id BIGINT UNSIGNED NOT NULL,
 type ENUM('text','image') NOT NULL DEFAULT 'text',
 content TEXT NOT NULL,
 created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 expires_at DATETIME NOT NULL,
 FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE,
 INDEX idx_status_expiry(expires_at)
) ENGINE=InnoDB;

CREATE TABLE status_views (
 status_id BIGINT UNSIGNED NOT NULL,
 viewer_id BIGINT UNSIGNED NOT NULL,
 viewed_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 PRIMARY KEY(status_id,viewer_id),
 FOREIGN KEY(status_id) REFERENCES statuses(id) ON DELETE CASCADE,
 FOREIGN KEY(viewer_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE password_requests (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 username VARCHAR(30) NOT NULL,
 full_name VARCHAR(80) NOT NULL,
 phone VARCHAR(24) NOT NULL,
 status ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 reviewed_at DATETIME NULL,
 INDEX idx_password_status(status)
) ENGINE=InnoDB;

CREATE TABLE screenshot_requests (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 user_id BIGINT UNSIGNED NOT NULL,
 request_type ENUM('screenshot') NOT NULL DEFAULT 'screenshot',
 status ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 reviewed_at DATETIME NULL,
 FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE,
 INDEX idx_screenshot_status(status)
) ENGINE=InnoDB;

CREATE TABLE recording_requests (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 user_id BIGINT UNSIGNED NOT NULL,
 request_type ENUM('screen_recording') NOT NULL DEFAULT 'screen_recording',
 status ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 reviewed_at DATETIME NULL,
 FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE,
 INDEX idx_recording_status(status)
) ENGINE=InnoDB;

CREATE TABLE reports (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 reporter_id BIGINT UNSIGNED NOT NULL,
 reported_user_id BIGINT UNSIGNED NOT NULL,
 reason VARCHAR(500) NOT NULL,
 status ENUM('pending','reviewed','dismissed') NOT NULL DEFAULT 'pending',
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY(reporter_id) REFERENCES users(id) ON DELETE CASCADE,
 FOREIGN KEY(reported_user_id) REFERENCES users(id) ON DELETE CASCADE,
 INDEX idx_report_status(status)
) ENGINE=InnoDB;

CREATE TABLE blocked_users (
 blocker_id BIGINT UNSIGNED NOT NULL,
 blocked_id BIGINT UNSIGNED NOT NULL,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 PRIMARY KEY(blocker_id,blocked_id),
 FOREIGN KEY(blocker_id) REFERENCES users(id) ON DELETE CASCADE,
 FOREIGN KEY(blocked_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE admin (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 username VARCHAR(40) NOT NULL UNIQUE,
 password_hash VARCHAR(255) NOT NULL,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE admin_audit_logs (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 admin_id BIGINT UNSIGNED NOT NULL,
 action VARCHAR(60) NOT NULL,
 conversation_id BIGINT UNSIGNED NULL,
 created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 INDEX idx_admin_audit_created(created_at),
 INDEX idx_admin_audit_conversation(conversation_id),
 FOREIGN KEY(admin_id) REFERENCES admin(id) ON DELETE CASCADE
) ENGINE=InnoDB;
