# ⚡ Electricity Billing System

A web-based Electricity Billing Management System built with PHP and MySQL for 2nd Year, 2nd Semester Midterm Project (PIT 1 & 2).

---

## 👥 Contributors

| Name | GitHub | Branch |
|---|---|---|
| Redeemer Aparece | [@redeemer-xu](https://github.com/redeemer-xu) | `redeemer` |
| Dominique Pimentel | [@domvipimentel4-jpg](https://github.com/domvipimentel4-jpg) | `domvi` |

---

## 📋 Features

### 🔐 Authentication
- Secure login and registration with session-based authentication
- Role-based access control (Admin / User)
- Password hashing using `password_hash()` / `password_verify()`
- Middleware protection on all protected pages

### 🛠️ Admin Panel
- Dashboard with statistics (total users, bills, revenue)
- Manage Users — view, activate/deactivate, delete accounts
- Add Bills — assign bills to users with kWh input and auto-calculated amount
- View Bills — toggle payment status
- Reports — monthly revenue summary, highest electricity consumers
- Settings — configure rate per kWh and due days

### 👤 User Panel
- Dashboard with personal bill summary
- My Bills — view all bills with overdue detection
- Pay Bill — select payment method and confirm payment
- Download Receipt — download payment receipt as text file
- My Profile — update personal information and change password
- Profile Picture — upload and update profile photo with live preview

### 🎨 UI / UX
- Responsive Bootstrap 5 layout
- Collapsible sidebar with smooth animations
- 🌙 Dark Mode / ☀️ Light Mode toggle (preference saved per session)
- Mobile-friendly with overlay backdrop

---

## 🗂️ Project Structure

```
e_bill/
├── app/
│   ├── config/
│   │   └── config.php              ← DB connection & constants (NOT on GitHub)
│   ├── controller/
│   │   ├── auth_controller.php     ← Login, register, logout
│   │   ├── bill_controller.php     ← Bill & payment logic
│   │   └── customer_controller.php ← User management & profile pictures
│   ├── middleware/
│   │   └── auth_middleware.php     ← Session guard for protected pages
│   └── uploads/
│       └── profile_pictures/       ← User profile photos (NOT on GitHub)
├── public/
│   ├── admin/                      ← Admin panel pages
│   │   └── includes/               ← header, footer, sidebar, topbar
│   └── user/                       ← User panel pages
│       └── includes/               ← header, footer, sidebar, topbar
├── sql/
│   └── electricity_db2.sql         ← Database schema and seed data
├── .htaccess                        ← URL rewriting (removes .php extension)
├── .gitignore
└── README.md
```

---

## 🗃️ Database

**Database name:** `electricity_db2`

| Table | Purpose |
|---|---|
| `admin` | Admin accounts |
| `user` | Registered customer accounts |
| `bill` | Electricity bills per user |
| `payment` | Payment records linked to bills |
| `settings` | System settings (rate per kWh, due days, etc.) |

---

## ⚙️ Setup Instructions

### Requirements
- XAMPP (Apache + MySQL)
- PHP 8.x
- Git

### Steps

**1. Clone the repository**
```bash
cd C:/xampp/htdocs/pit
git clone https://github.com/domvipimentel4-jpg/Electricity-billing-system-2nd-year-2nd-sem.git e_bill
cd e_bill
git checkout redeemer
```

**2. Create `config.php` manually**

Create the file at `app/config/config.php` — this file is excluded from GitHub for security:

```php
<?php
define('DB_HOST', '127.0.0.1');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'electricity_db2');
define('DB_PORT', 3307); // change to 3306 if needed

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

define('BASE_URL',    'http://localhost/pit/e_bill/public/');
define('ADMIN_URL',   'http://localhost/pit/e_bill/public/admin/');
define('USER_URL',    'http://localhost/pit/e_bill/public/user/');
define('UPLOADS_PATH', __DIR__ . '/../uploads/');
define('UPLOADS_URL',  'http://localhost/pit/e_bill/app/uploads/');
?>
```

**3. Import the database**
- Open `http://localhost/phpmyadmin`
- Create database: `electricity_db2` with collation `utf8mb4_general_ci`
- Import: `sql/electricity_db2.sql`

**4. Create the uploads folder**

Create this folder manually (it is gitignored):
```
e_bill/app/uploads/profile_pictures/
```

**5. Run the system**
```
http://localhost/pit/e_bill/public/index.php
```

---

## 🔑 Default Admin Account

| Field | Value |
|---|---|
| Username | `admin` |
| Password | `admin123` |

> ⚠️ Change the admin password after first login.

---

## 🔒 Security Features

- Prepared statements on all database queries (prevents SQL Injection)
- `htmlspecialchars()` on all output (prevents XSS)
- `password_hash()` / `password_verify()` for passwords
- Session-based authentication with role checking
- File type and size validation on profile picture uploads
- `intval()` on all ID parameters from GET requests

---

## 🛠️ Tech Stack

| Technology | Usage |
|---|---|
| PHP 8.x | Backend / Server-side logic |
| MySQL (MariaDB) | Database |
| Bootstrap 5.3 | Frontend UI framework |
| Bootstrap Icons | Icon library |
| XAMPP | Local development server |
| Git / GitHub | Version control |

---

## 📌 Notes

- `config.php` is excluded from GitHub — must be created manually on each device
- `app/uploads/profile_pictures/` is excluded from GitHub — create the folder manually
- The `.htaccess` file removes `.php` extensions from URLs
- Dark mode preference is saved per browser using `localStorage`

---

*2nd Year — 2nd Semester Midterm Project · Web Systems & Technologies*
