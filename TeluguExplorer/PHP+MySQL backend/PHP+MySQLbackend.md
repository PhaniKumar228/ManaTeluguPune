
🚀 Deployment Checklist
Step 1 — Create the MySQL Database
hPanel → Databases → MySQL Databases → Create DB + User + assign All Privileges

Step 2 — Run setup.sql
hPanel → phpMyAdmin → Select your DB → SQL tab → paste setup.sql → Go

Step 3 — Update DB credentials in api.php
php
define('DB_NAME', 'u123456_yourdb');   // ← your actual DB name
define('DB_USER', 'u123456_user');     // ← your actual DB user
define('DB_PASS', 'YourPassword');     // ← your actual password
Step 4 — Upload Files
hPanel → File Manager → public_html → Upload:

index.html
api.php
Do NOT upload setup.sql to public_html — it's only for phpMyAdmin.

🔄 What Changed (localStorage → Database)
Before	After
localStorage users array	users MySQL table with hashed passwords
localStorage paid status	is_paid column in users table
localStorage session	PHP server-side session (secure cookie)
Hardcoded DEFAULT_PLACES array	places MySQL table (seeded via SQL)
JS CRUD (client-side)	PHP PDO prepared statements (SQL injection safe)
Admin data in browser	Admin check on every protected API route server-side







