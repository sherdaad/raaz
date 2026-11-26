RAAZ — ALL-IN-ONE PROJECT PACKAGE

Target: XAMPP, PHP 8+, MySQL/MariaDB, VS Code.
Folder name should be: C:\xampp\htdocs\raaz

INSTALL:
1. Extract this ZIP into C:\xampp\htdocs\raaz
2. Start Apache + MySQL in XAMPP.
3. Open http://localhost/phpmyadmin and import database/raaz.sql.
4. Open config/config.php and set DB credentials if your XAMPP differs.
5. Visit http://localhost/raaz/
6. Register a normal user.
7. Create the first admin by visiting http://localhost/raaz/admin/setup.php.
   Save the generated password. DELETE admin/setup.php immediately.
8. Admin login: http://localhost/raaz/admin/login.php

IMPLEMENTED IN THIS PACKAGE:
- Registration/login/logout with password_hash/password_verify
- Unique usernames, validation, PDO prepared statements, CSRF tokens
- Profile name/status editing
- Search users and start one-to-one conversations
- Message send, history, seen timestamps and unread counts
- Server-side 60-second expiration after recipient reads a message
- Text status posting and 24-hour server expiry
- Screenshot/screen-recording request submissions
- Password recovery request submission (admin approval queue)
- User reports and block-aware messaging checks
- Admin statistics, user suspend/reactivate, request approve/reject
- Admin-only conversation directory: select a user, see conversation partners, and review archived messages
- Separate moderation archive survives 60-second expiry in normal chat; admin conversation views are audit-logged
- Signup disclosure and in-app privacy notice for moderation retention
- PWA manifest and service worker shell caching
- Capacitor configuration and Android packaging instructions

IMPORTANT TECHNICAL LIMITS — READ BEFORE DEPLOYMENT:
1. Message updates currently use AJAX polling (3 seconds), not a WebSocket server.
2. Messages are NOT end-to-end encrypted in this build. Do not use this build for highly sensitive conversations. Proper E2EE requires audited client crypto, public-key identity verification, safe key backup/recovery and key rotation; this package does not pretend otherwise.
3. Image status/profile upload UI is not included; text statuses are implemented.
4. Status viewer list UI, native Android FLAG_SECURE implementation, and APK build are not included. The Capacitor config and setup guide are included.
5. Website screenshot prevention is not possible to guarantee. External cameras and modified devices cannot be blocked.
6. Rate limiting, phone/SMS verification, production HTTPS, security audit, automated tests and production deployment hardening remain necessary.
7. Message content is deleted from the active messages table after expiry when the API next runs cleanup. For strict scheduled deletion, configure a server cron job to run a cleanup script regularly.

This is a working full-stack starter/MVP, not a security-audited production messenger. Verify all flows locally and complete the listed security/native steps before public launch.


MODERATION ARCHIVE UPDATE (EXISTING DATABASE):
If you already imported the previous raaz.sql, DO NOT re-import raaz.sql because it contains DROP TABLE statements and will erase existing data. Instead, import database/migration_moderation_archive.sql once in phpMyAdmin. It creates the archive and audit tables and copies currently-existing messages into the archive. Messages already deleted before this migration cannot be recovered.

ADMIN CONVERSATION REVIEW:
1. Sign in at /raaz/admin/login.php.
2. In Users, select the conversation count / View link.
3. Select View chat beside a conversation partner.
4. Every conversation view is recorded in admin_audit_logs.

IMPORTANT: The archive stores message content separately after normal chat expiry. This is not end-to-end encrypted. The signup screen and privacy page disclose admin review and retention. Before public deployment, define a reasonable retention period, secure the server and database, publish a complete privacy policy, and obtain review from a qualified Pakistani lawyer.

EXISTING USERS: After applying the migration, each existing user must sign in and accept the updated moderation/privacy notice before accessing chats. New registrations must accept the notice during signup.


PASSWORD RECOVERY APPROVAL UPDATE:
If you already use an existing RAAZ database, import database/migration_password_reset.sql once in phpMyAdmin (select the raaz database). Do not re-import database/raaz.sql. When an admin approves a password request, a one-time code is displayed once in the admin panel and expires after 30 minutes. Verify the requester identity before sharing the code through a separate trusted channel. The user opens /raaz/auth/reset-password.php, enters their username and code, then sets a new password (minimum 8 characters). The code is single-use.


LATEST UPDATE — WHATSAPP PERMISSION CONTACT + PROFILE PICTURES:
- Privacy & moderation page now opens WhatsApp chat with the RAAZ admin (+92 342 5834947) with a pre-filled request for Rs. 250 lifetime screenshot access or Rs. 500 lifetime screen-recording access. Users review and send the message themselves. Payment verification and permission activation are manual; sending a WhatsApp message does not automatically activate access.
- Users can upload/change/remove profile pictures from Profile. JPG, PNG and WEBP only; maximum 3 MB. Pictures are shown in chat list/search results.
- The existing users.profile_picture database column is used. No database migration is required for this update.
- Ensure uploads/profiles is writable by the PHP/Apache process on your server.
