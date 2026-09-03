# Starlight Soirées

A small PHP and MySQL event-management system for managing events, venues, catering packages, payments, a calendar, and printable reports.

## Main features

- Employee login with session timeout
- Event, venue, catering, and payment CRUD operations
- Venue-capacity and assignment validation
- Automatic event-price calculation
- Receipt uploads for payments
- Search and status/date filters
- FullCalendar dashboard integration
- Responsive desktop, tablet, and mobile layouts

## Project structure

```text
css/                 Shared dashboard and login styles
database/            Fresh schema and one-time legacy migration
employee/            Authenticated controllers and reusable PHP helpers
employee/views/      Server-rendered HTML/PHP templates
images/              Local visual assets
javascript/          Calendar and browser-side behavior
check_auth.php       Shared session and timeout protection
config.php           Shared event/payment options
db.php               PDO database connection
login.php            Employee authentication
sidebar.php          Shared employee navigation
```

Each controller loads `employee/bootstrap.php`, retrieves or changes data with PDO, prepares variables, and includes a template from `employee/views`.

## Local setup

1. Start Apache and MySQL in XAMPP.
2. Import `database/event_management.sql` into MySQL.
3. Create an employee password hash:

   ```powershell
   C:\xampp\php\php.exe -r "echo password_hash('YourPassword', PASSWORD_DEFAULT), PHP_EOL;"
   ```

4. Insert an employee account in phpMyAdmin, using the generated hash in the `password` column:

   ```sql
   INSERT INTO users (username, password)
   VALUES ('EmployeeName', 'PASTE_GENERATED_HASH_HERE');
   ```

5. Open the project through `http://localhost/.../login.php` rather than opening template files directly.

The `.html` files inside `employee/views` contain PHP template expressions and are processed through their controller files.

## Technology

- PHP with PDO
- MySQL
- HTML and CSS
- JavaScript
- FullCalendar
- XAMPP
