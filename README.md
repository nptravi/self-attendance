# Self Attendance Management System

A lightweight, single-page web application to track daily attendance, featuring a "Remember Me" login system, dual-view interface, and CSV export functionality.

## 🚀 Features

* **Secure Login/Signup:** User authentication with hashed passwords.
* **Admin-Gated Registration:** Prevents unauthorized account creation via a required Admin Password.
* **Remember Me:** Persistent login sessions using secure tokens stored in browser cookies.
* **Dual Views:** Toggle between a **Calendar View** for a visual overview and a **Grid View** for quick data entry.
* **Data Export:** Generate and download a `.csv` file for reporting or backup.
* **Auto-Setup:** Automatic database table creation on the first run.

## 🛠️ Tech Stack

* **Frontend:** HTML5, CSS3, Vanilla JavaScript.
* **Backend:** PHP 7.x / 8.x.
* **Database:** MySQL / MariaDB.
* **Communication:** Fetch API (AJAX) with JSON.

## 📋 Prerequisites

* A local server environment (XAMPP, WAMP, MAMP, LAMP) or a remote host.
* PHP 7.4 or higher.
* MySQL database.

## 🔧 Installation & Setup

1. **Clone the Repository:**
```bash
git clone https://github.com/nptravi/self-attendance.git

```


2. **Environment Configuration:**
* Locate the `env_template` file in the project root.
* Create a new file named `.env` in the same directory.
* Copy the contents of `env_template` into `.env` and fill in your credentials:


```ini
DB_HOST=localhost
DB_NAME=your_database_name
DB_USER=your_username
DB_PASS=your_password
ADMIN_PASSWORD=your_secure_signup_password

```


* **Note:** The `ADMIN_PASSWORD` defined here is required for creating new user through sign up. This prevents public registration on hosted servers.


3. **Database Initialization:**
You do not need to manually run SQL scripts to create tables.
* Create an empty database in MySQL.
* Update your `.env` file with the database credentials.
* **Run the app in your browser.** The system will automatically detect the empty database and generate the `users` and `attendance` tables.



## 🔐 Sign-Up Process

To create a new account:

1. Click the **Sign Up** button on the login screen.
2. Enter your desired Username and Password.
3. Enter the **Admin Password** (the one you set in your `.env` file).
4. Click **Sign Up**. If the Admin Password matches the `.env` configuration, the account will be created.

## 📝 Attendance Codes

| Code | Meaning | Hours / Description |
| --- | --- | --- |
| **A** | Morning Shift | 6 AM – 2 PM |
| **B** | Afternoon Shift | 2 PM – 10 PM |
| **C** | Night Shift | 10 PM – 6 AM |
| **G** | General | Standard Business Hours |
| **L** | Leave | Full Day Leave |
| **HL** | Half Leave | Mid-day Leave |
| **O** | Off Day | Weekly Rest / Holiday |
| **TR** | Training / Tour | Work-related Travel |

## 📂 Project Structure

* `index.html` - The main UI, CSS styling, and frontend JavaScript logic.
* `db.php` - Database connection and automatic table initialization.
* `api/`
* `create_user.php` - Validates the Admin Password and creates new users.
* `login.php` - Handles authentication and "Remember Me" token generation.
* `check_session.php` - Validates existing sessions or cookies.
* `logout.php` - Clears sessions and expires cookies.
* `get_attendance.php` - Fetches records for the selected date range.
* `update_attendance.php` - Saves or updates specific attendance entries.
