# 🚗 Car Rental Management System

A full-stack web application for managing car rentals with **separate dashboards** for Customers and Car Rental Agencies. Built with PHP, MySQL, and Bootstrap 5.

---

## 📋 Table of Contents

- [Project Overview](#-project-overview)
- [Features](#-features)
- [Technologies Used](#-technologies-used)
- [Prerequisites](#-prerequisites)
- [Installation & Setup](#-installation--setup)
  - [Step 1 – Download the Project](#step-1--download-the-project)
  - [Step 2 – Place in Server Directory](#step-2--place-in-server-directory)
  - [Step 3 – Import the Database](#step-3--import-the-database)
  - [Step 4 – Configure Database Connection](#step-4--configure-database-connection)
  - [Step 5 – Start the Server & Open in Browser](#step-5--start-the-server--open-in-browser)
- [Default Login Credentials](#-default-login-credentials)
- [Project Structure](#-project-structure)
- [Database Schema](#-database-schema)
- [Security Features](#-security-features)
- [Troubleshooting](#-troubleshooting)
- [Railway Deployment](#-railway-deployment)

---

## 🏷️ Project Overview

The **Car Rental Management System** allows:

- **Agencies** to register, list their cars, manage inventory, and view bookings.
- **Customers** to register, browse available cars, and rent them for a chosen duration.

The system calculates total rental cost automatically, tracks booking status (active / completed / cancelled), and ensures only available cars are shown to customers.

---

## ✨ Features

### For Customers
| Feature | Description |
|---------|------------|
| 🔐 Register & Login | Create an account and securely log in |
| 🚘 Browse Available Cars | View all cars currently available for rent |
| 📅 Rent a Car | Select a start date and number of days; total cost is auto-calculated |
| 📜 View My Bookings | See all your past and active bookings |

### For Agencies
| Feature | Description |
|---------|------------|
| 🔐 Register & Login | Create an agency account with company name |
| ➕ Add Cars | Add new cars to your fleet (model, vehicle number, seats, daily rate) |
| ✏️ Edit Cars | Update car details or toggle availability |
| 📊 View Bookings | See all bookings made on your cars |

### General
- Responsive design using **Bootstrap 5** — works on desktop, tablet, and mobile
- Role-based access control — customers and agencies see different dashboards
- Clean, modern UI with a consistent header and footer

---

## 🛠️ Technologies Used

| Layer | Technology |
|-------|-----------|
| **Backend** | PHP 7.4+ (Procedural PHP with `mysqli`) |
| **Database** | MySQL 5.7+ |
| **Frontend** | HTML5, CSS3, JavaScript |
| **CSS Framework** | Bootstrap 5 (via CDN) |
| **Server** | Apache (XAMPP / WAMP / MAMP) or PHP Built-in Server |

---

## 📦 Prerequisites

Before you begin, make sure you have **one** of the following installed:

| Software | Download Link | Includes |
|----------|--------------|----------|
| **XAMPP** (Recommended) | [https://www.apachefriends.org/](https://www.apachefriends.org/) | Apache + PHP + MySQL + phpMyAdmin |
| **WAMP** (Windows) | [https://www.wampserver.com/](https://www.wampserver.com/) | Apache + PHP + MySQL + phpMyAdmin |
| **MAMP** (macOS) | [https://www.mamp.info/](https://www.mamp.info/) | Apache + PHP + MySQL + phpMyAdmin |

> **Minimum Required Versions:**
> - PHP **7.4** or higher
> - MySQL **5.7** or higher

---

## 🚀 Installation & Setup

Follow these steps carefully to get the project running on your local machine.

### Step 1 – Download the Project

**Option A: Download ZIP**
1. Download the `.zip` file (or receive it via shared link).
2. Extract the ZIP — you should see a folder called `car-rental-system`.

**Option B: Clone from Git (if hosted)**
```bash
git clone <repository-url>
```

---

### Step 2 – Place in Server Directory

Move or copy the `car-rental-system` folder into your web server's root directory:

| Software | Server Root Directory |
|----------|-----------------------|
| **XAMPP** | `C:\xampp\htdocs\` |
| **WAMP** | `C:\wamp64\www\` |
| **MAMP** | `/Applications/MAMP/htdocs/` |

After this step, your folder should be at (for XAMPP example):
```
C:\xampp\htdocs\car-rental-system\
```

---

### Step 3 – Import the Database

The project includes a `database.sql` file that creates the database, all tables, and sample data automatically. You can import it using **either** of the two methods below.

---

#### 📌 Method A: Import via phpMyAdmin (Easiest — Recommended for Beginners)

1. **Start your server** — Open XAMPP Control Panel and click **Start** next to both **Apache** and **MySQL**.

2. **Open phpMyAdmin** — Go to your browser and visit:
   ```
   http://localhost/phpmyadmin
   ```

3. **Go to the Import tab:**
   - In the top navigation bar, click on **"Import"**.

4. **Choose the file:**
   - Click the **"Choose File"** / **"Browse"** button.
   - Navigate to your project folder and select the file:
     ```
     car-rental-system/database.sql
     ```

5. **Run the import:**
   - Leave all default settings as they are.
   - Scroll down and click the **"Import"** button (usually at the bottom right).

6. **Verify success:**
   - You should see a green success message: _"Import has been successfully finished"_.
   - In the left sidebar, you should now see a database named **`car_rental_system`** with three tables:
     - `users`
     - `cars`
     - `bookings`

> **💡 Tip:** If you see an error saying the database already exists, don't worry — the SQL file uses `CREATE DATABASE IF NOT EXISTS`, so it won't overwrite existing data. To start fresh, drop the database first from phpMyAdmin and then re-import.

---

#### 📌 Method B: Import via MySQL Command Line

1. **Open your terminal** (Command Prompt / PowerShell / Terminal).

2. **Navigate to the project folder:**
   ```bash
   cd C:\xampp\htdocs\car-rental-system
   ```

3. **Run the import command:**

   _If your MySQL has **no password** (default XAMPP):_
   ```bash
   C:\xampp\mysql\bin\mysql.exe -u root < database.sql
   ```

   _If your MySQL **has a password**:_
   ```bash
   C:\xampp\mysql\bin\mysql.exe -u root -p < database.sql
   ```
   You will be prompted to enter your MySQL password.

   _On macOS/Linux (MAMP or standalone MySQL):_
   ```bash
   mysql -u root -p < database.sql
   ```

4. **Verify the import:**
   ```bash
   C:\xampp\mysql\bin\mysql.exe -u root -e "USE car_rental_system; SHOW TABLES;"
   ```
   You should see:
   ```
   +------------------------------+
   | Tables_in_car_rental_system  |
   +------------------------------+
   | bookings                     |
   | cars                         |
   | users                        |
   +------------------------------+
   ```

---

### Step 4 – Configure Database Connection

The project includes a `config/database.example.php` template. Copy it to create your config file:

```bash
cp config/database.example.php config/database.php
```

The config file **automatically detects** which environment it's running in:

- **Locally (XAMPP/WAMP):** Uses the fallback values — `localhost`, `root`, empty password.
- **On Railway:** Reads the `MYSQLHOST`, `MYSQLUSER`, `MYSQLPASSWORD`, `MYSQLDATABASE`, and `MYSQLPORT` environment variables that Railway injects automatically.

If you need to customise the local fallback values, edit `config/database.php`:

```php
define('DB_HOST', getenv('MYSQLHOST')     ?: 'localhost');   // Railway auto-injects; fallback for local
define('DB_USER', getenv('MYSQLUSER')     ?: 'root');        // Default XAMPP/WAMP user
define('DB_PASS', getenv('MYSQLPASSWORD') ?: '');            // Default XAMPP password (empty)
define('DB_NAME', getenv('MYSQLDATABASE') ?: 'car_rental_system');
$dbPort =         getenv('MYSQLPORT')     ?: 3307;          // Change to 3306 if using default port
```

> **⚠️ Important Notes:**
> - **XAMPP** default: host = `localhost`, user = `root`, password = _(empty)_
> - **WAMP** default: host = `localhost`, user = `root`, password = _(empty)_
> - **MAMP** default: host = `localhost`, user = `root`, password = `root`
> - If your MySQL uses the default port **3306**, change the `3307` fallback above to `3306`.

---

### Step 5 – Start the Server & Open in Browser

#### Option A: Using XAMPP / WAMP (Recommended)

1. Open XAMPP/WAMP Control Panel.
2. Start **Apache** and **MySQL**.
3. Open your browser and go to:
   ```
   http://localhost/car-rental-system/
   ```

#### Option B: Using PHP's Built-in Server

1. Open a terminal and navigate to the project folder:
   ```bash
   cd C:\xampp\htdocs\car-rental-system
   ```

2. Start the PHP development server:
   ```bash
   php -S localhost:8000
   ```

3. Open your browser and go to:
   ```
   http://localhost:8000
   ```

---

## 🔑 Default Login Credentials

The `database.sql` file includes sample users for testing. All accounts share the same password:

> **Password: `password123`**

| Role | Email | Name | Company |
|------|-------|------|---------|
| 🏢 Agency | `agency1@example.com` | Rahul Sharma | Sharma Car Rentals |
| 🏢 Agency | `agency2@example.com` | Priya Patel | Patel Auto Rentals |
| 👤 Customer | `customer1@example.com` | Amit Kumar | — |
| 👤 Customer | `customer2@example.com` | Sneha Gupta | — |
| 👤 Customer | `customer3@example.com` | Vikram Singh | — |

> **💡 Tip:** You can also register new accounts through the application itself!

---

## 📁 Project Structure

```
car-rental-system/
│
├── config/
│   ├── database.php          # Database connection (env vars + local fallback)
│   └── database.example.php  # Template — copy to database.php and edit
│
├── includes/
│   ├── header.php             # Common HTML <head>, navbar, and session handling
│   ├── footer.php             # Common page footer and closing tags
│   └── functions.php          # Reusable helper functions (redirect, sanitize, etc.)
│
├── auth/
│   ├── login.php              # Login page for both customers and agencies
│   ├── register_customer.php  # Customer registration form
│   ├── register_agency.php    # Agency registration form (includes company name)
│   └── logout.php             # Destroys session and redirects to login
│
├── customer/
│   ├── available_cars.php     # Browse all available cars and rent them
│   └── rent_car.php           # Process car rental (date, duration, cost)
│
├── agency/
│   ├── add_car.php            # Form to add a new car to inventory
│   ├── edit_car.php           # Edit existing car details / toggle availability
│   └── view_bookings.php      # View all bookings for the agency's cars
│
├── css/
│   └── style.css              # Custom styles (supplements Bootstrap)
│
├── js/
│   └── script.js              # Custom JavaScript (form validation, interactivity)
│
├── assets/                    # Static assets (images, icons, etc.)
│
├── index.php                  # Entry point — redirects to available cars page
├── database.sql               # Complete database schema + sample data
├── .gitignore                 # Files excluded from Git (credentials, OS files, etc.)
└── README.md                  # This file
```

---

## 🗄️ Database Schema

The system uses **3 tables** in a MySQL database named `car_rental_system`:

### `users` table
Stores both customers and agencies in a single table with a `user_type` field.

| Column | Type | Description |
|--------|------|-------------|
| `id` | INT (PK) | Auto-increment primary key |
| `user_type` | ENUM | `'customer'` or `'agency'` |
| `email` | VARCHAR(255) | Unique email address |
| `password` | VARCHAR(255) | Bcrypt-hashed password |
| `full_name` | VARCHAR(255) | User's full name |
| `phone_number` | VARCHAR(20) | Contact number |
| `company_name` | VARCHAR(255) | Agency company name (NULL for customers) |
| `created_at` | TIMESTAMP | Account creation time |
| `updated_at` | TIMESTAMP | Last update time |

### `cars` table
Stores car inventory managed by agencies.

| Column | Type | Description |
|--------|------|-------------|
| `id` | INT (PK) | Auto-increment primary key |
| `agency_id` | INT (FK) | References `users.id` |
| `vehicle_model` | VARCHAR(255) | Car model name |
| `vehicle_number` | VARCHAR(50) | Unique registration number |
| `seating_capacity` | INT | Number of seats |
| `rent_per_day` | DECIMAL(10,2) | Daily rental rate (₹) |
| `is_available` | BOOLEAN | Availability status |
| `created_at` | TIMESTAMP | Record creation time |
| `updated_at` | TIMESTAMP | Last update time |

### `bookings` table
Stores rental bookings made by customers.

| Column | Type | Description |
|--------|------|-------------|
| `id` | INT (PK) | Auto-increment primary key |
| `car_id` | INT (FK) | References `cars.id` |
| `customer_id` | INT (FK) | References `users.id` |
| `start_date` | DATE | Rental start date |
| `number_of_days` | INT | Duration of rental |
| `end_date` | DATE | Rental end date |
| `total_cost` | DECIMAL(10,2) | Total cost (rent_per_day × days) |
| `booking_status` | ENUM | `'active'`, `'completed'`, or `'cancelled'` |
| `created_at` | TIMESTAMP | Booking creation time |

---

## 🔒 Security Features

| Feature | Implementation |
|---------|---------------|
| **Password Hashing** | `password_hash()` with `PASSWORD_DEFAULT` (bcrypt) |
| **Password Verification** | `password_verify()` for secure login |
| **SQL Injection Prevention** | All queries use **prepared statements** with `bind_param()` |
| **XSS Prevention** | All user output escaped with `htmlspecialchars()` |
| **Session Security** | Session-based authentication with role-based access control |
| **Access Control** | Customers cannot access agency pages and vice versa |

---

## ❓ Troubleshooting

### 1. "Database connection failed" error
- **Cause:** MySQL is not running or the credentials in `config/database.php` are incorrect.
- **Fix:**
  - Make sure MySQL is running in XAMPP/WAMP Control Panel.
  - Check that the fallback values in `config/database.php` match your local MySQL setup.
  - If your MySQL uses port **3306** (default), change the `$dbPort` fallback from `3307` to `3306`.

### 2. "Unknown database 'car_rental_system'" error
- **Cause:** The database has not been imported yet.
- **Fix:** Import `database.sql` using phpMyAdmin or the MySQL command line (see [Step 3](#step-3--import-the-database) above).

### 3. phpMyAdmin shows "No tables found" after import
- **Cause:** You may have imported into the wrong database.
- **Fix:** Make sure you select the **`car_rental_system`** database from the left sidebar. If it doesn't exist, re-import the `database.sql` file.

### 4. Page shows blank / white screen
- **Cause:** PHP errors are hidden.
- **Fix:** Open `php.ini` (in XAMPP: `C:\xampp\php\php.ini`) and set:
  ```ini
  display_errors = On
  error_reporting = E_ALL
  ```
  Then restart Apache and reload the page.

### 5. "Access denied for user 'root'@'localhost'" error
- **Cause:** Your MySQL root user has a password, but `config/database.php` has an empty password.
- **Fix:** Update `DB_PASS` in `config/database.php` with your MySQL password:
  ```php
  define('DB_PASS', 'your_password_here');
  ```

### 6. CSS / Styles not loading properly
- **Cause:** The project path may be incorrect.
- **Fix:** Make sure the folder is placed directly inside `htdocs/` (not nested inside extra folders). The URL should be:
  ```
  http://localhost/car-rental-system/
  ```

### 7. "Port 80 is already in use" (Apache won't start)
- **Cause:** Another application (like Skype or IIS) is using port 80.
- **Fix:**
  - Close the conflicting application, OR
  - Change Apache's port in XAMPP: open `httpd.conf`, find `Listen 80`, and change it to `Listen 8080`. Then access the site at `http://localhost:8080/car-rental-system/`.

---

## 🚀 Railway Deployment

Follow these steps to deploy the project on [Railway](https://railway.app).

### Step 1 — Push to GitHub

1. Create a new repository on GitHub.
2. Push your project:
   ```bash
   git init
   git add .
   git commit -m "Initial commit"
   git remote add origin https://github.com/<your-username>/<your-repo>.git
   git branch -M main
   git push -u origin main
   ```

> **Note:** `config/database.php` contains only `getenv()` calls with generic local defaults — no real credentials are exposed.

### Step 2 — Create a Railway Project

1. Go to [railway.app](https://railway.app) and sign in with GitHub.
2. Click **"New Project"** → **"Deploy from GitHub Repo"** → select your repository.
3. Click **"Add Service"** → **"Database"** → **"MySQL"**.

Railway will automatically inject these environment variables into your PHP service:

| Variable | Description |
|----------|-------------|
| `MYSQLHOST` | Database hostname |
| `MYSQLUSER` | Database username |
| `MYSQLPASSWORD` | Database password |
| `MYSQLDATABASE` | Database name |
| `MYSQLPORT` | Database port |

Your `config/database.php` reads these automatically — **no manual config needed**.

### Step 3 — Import the Database Schema

Railway does not auto-import SQL files. After your MySQL service is running:

1. Go to your MySQL service on the Railway dashboard.
2. Open the **"Data"** tab.
3. Paste the contents of `database.sql` and run it.

Alternatively, use the connection string from Railway's **"Connect"** tab:
```bash
mysql -h <MYSQLHOST> -P <MYSQLPORT> -u <MYSQLUSER> -p<MYSQLPASSWORD> <MYSQLDATABASE> < database.sql
```

### Step 4 — Verify

Once deployed, Railway will give you a public URL (e.g., `https://your-app.up.railway.app`). Open it and verify:
- The homepage redirects to the Available Cars page
- Login and registration work correctly
- Agency can add cars; customers can rent them

---

## 📄 License

This project is created for educational purposes.

---

> **Made with ❤️ using PHP, MySQL & Bootstrap 5**
