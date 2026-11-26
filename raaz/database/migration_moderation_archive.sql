USE raaz;

-- Run this migration ONCE on an existing RAAZ database.
ALTER TABLE users ADD COLUMN moderation_notice_accepted_at DATETIME NULL;

CREATE TABLE IF NOT EXISTS admin_message_archive (
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

CREATE TABLE IF NOT EXISTS admin_audit_logs (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 admin_id BIGINT UNSIGNED NOT NULL,
 action VARCHAR(60) NOT NULL,
 conversation_id BIGINT UNSIGNED NULL,
 created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 INDEX idx_admin_audit_created(created_at),
 INDEX idx_admin_audit_conversation(conversation_id),
 FOREIGN KEY(admin_id) REFERENCES admin(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Backfill messages that still exist at migration time. Already-deleted messages cannot be recovered.
INSERT INTO admin_message_archive(conversation_id,sender_id,body,sent_at)
SELECT m.conversation_id,m.sender_id,m.body,m.created_at
FROM messages m
WHERE NOT EXISTS (
 SELECT 1 FROM admin_message_archive a
 WHERE a.conversation_id=m.conversation_id
   AND a.sender_id=m.sender_id
   AND a.sent_at=m.created_at
   AND a.body=m.body
);
