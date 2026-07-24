# Arunella - Smart Agricultural Supply Chain Management System

Arunella is a digital supply chain solution built for the **CSC 313 1.5 Service Oriented Computing** course at the **University of Sri Jayewardenepura**. It connects **Farmers, Buyers, and Transporters** across Sri Lanka directly, eliminating intermediary markups, ensuring fair crop pricing, and managing automated logistics scheduling.

---

## 🚀 Setup & Localhost Execution (WampServer)

To deploy and test the project on your local machine using **WampServer**:

### Step 1: Install WampServer
If you haven't already, download and install WampServer from the official website (select 64-bit or 32-bit depending on your Windows OS). Start WampServer and ensure the tray icon is green.

### Step 2: Copy Code Files
Copy the entire `arunella` folder into your WampServer's root document directory:
- Usually located at: `C:\wamp64\www\` or `C:\wamp\www\`
- Your project path should be: `C:\wamp64\www\arunella\`

### Step 3: Setup the Database
1. Open your browser and go to phpMyAdmin: `http://localhost/phpmyadmin/`
2. Login (Default username is `root`, password should be empty/blank).
3. Click on the **Databases** tab, type `arunella` in the Database Name field, and click **Create**.
4. Select the newly created `arunella` database from the left-side list.
5. Go to the **Import** tab at the top.
6. Click **Choose File** and select the schema script located at `arunella/db/schema.sql`.
7. Scroll down and click **Import** (or **Go**).
8. The tables (`ADMIN`, `FARMER`, `BUYER`, `TRANSPORTER`, `CROP`, `ORDER`, `DELIVERY`, `HAS`) will be created and seeded with mock data.

### Step 4: Verify the Connection Configuration
Open the database connection file in a text editor: `arunella/config/db.php`.
Verify that host, dbname, username, and password align with your local Wampserver database credentials (default is root with no password).

### Step 5: Run on Localhost
Open your browser and navigate to:
👉 **`http://localhost/arunella/`**

---

## 💻 Git & GitHub Upload Instructions

To upload your project code to GitHub to present to your examiners:

### Step 1: Install Git
Make sure Git is installed on your computer. Download from [git-scm.com](https://git-scm.com/) if needed.

### Step 2: Initialize Git in your project folder
Open command prompt or Git Bash, change directory to your project folder, and run:
```bash
cd C:\wamp64\www\arunella
git init
```

### Step 3: Create a `.gitignore` file (Optional)
If you have local files or configs that you don't want to share, add them to a `.gitignore` file. For our case, everything can be uploaded!

### Step 4: Stage and Commit the files
Add all the files to git staging and commit them:
```bash
git add .
git commit -m "Initial commit - Arunella Agri Supply Chain Management System"
```

### Step 5: Push to GitHub
1. Go to [github.com](https://github.com/) and log in.
2. Click **New Repository** at the top left.
3. Name it `arunella`, choose **Public**, and do NOT check "Initialize this repository with a README" (as we already have one!). Click **Create repository**.
4. Copy the remote URL commands provided. It should look like this:
```bash
git branch -M main
git remote add origin https://github.com/YOUR_GITHUB_USERNAME/arunella.git
git push -u origin main
```
5. Paste these commands into your terminal inside `C:\wamp64\www\arunella` and run them. Your code is now live on GitHub!

---

## 🎓 VIVA Preparation Sheet (CSC 313)

Be ready to answer these questions during the project demonstration and viva:

### 1. Database Concepts

*   **Q: Why do FARMER, BUYER, and TRANSPORTER have columns like name, email, and password duplicated instead of one single USERS table?**
    *   *Answer:* This implements **Concrete Table Inheritance** (or Table Per Concrete Class mapping). Since each role contains distinct specialized fields (e.g. farmers have bank details and farm locations, buyers have business registrations, transporters have vehicle plates and max capacities), mapping them into concrete tables keeps queries clean, eliminates unnecessary NULL columns in a combined table, and allows us to enforce strict database constraints (like UNIQUE vehicle plate numbers only for transporters).

*   **Q: What is the purpose of the `HAS` table?**
    *   *Answer:* The `HAS` table is a **Junction Table** (or Association table) that handles the **Many-to-Many relationship** between `ORDER` and `CROP`. A single order can contain multiple crops, and a single crop can be part of multiple orders. This prevents multi-valued attributes and normalizes the database structure to **3NF (Third Normal Form)**.

*   **Q: How does the system handle referential integrity?**
    *   *Answer:* We use **Foreign Key Constraints** with cascades. For example:
        *   `CROP` has a foreign key referencing `FARMER` with `ON DELETE CASCADE`. If a farmer is deleted, their crop listings are automatically cleared.
        *   `DELIVERY` has a foreign key referencing `TRANSPORTER` with `ON DELETE SET NULL`. If a transporter leaves the platform, the delivery is not deleted but set back to unassigned, allowing other transporters to claim it.

### 2. Transaction Management & Data Integrity

*   **Q: What happens if a buyer checks out, a crop is sold out, or the system crashes mid-transaction? How is this prevented?**
    *   *Answer:* We implement **ACID Database Transactions** in `buyer_dashboard.php` using PHP PDO's `$db->beginTransaction()`, `$db->commit()`, and `$db->rollBack()`. If any step fails (e.g., a stock check fails or a network issue occurs), the database reverts (*rolls back*) all changes to ensure no orphan orders are created and stock is not deducted incorrectly. We also use `FOR UPDATE` in our select queries to lock rows and prevent race conditions.

### 3. Service-Oriented Concepts & Code Architecture

*   **Q: How is this application structured? Is it Service-Oriented?**
    *   *Answer:* The backend is modularly structured:
        *   **Config Layer (`config/db.php`)**: Manages the data connections.
        *   **Auth / Session Layer (`includes/auth.php`)**: Protects the dashboards by checking authentication status.
        *   **Service Pages (Dashboards)**: Offer user-specific workflows (Farmer crop listings, Buyer marketplace shopping cart, Transporter delivery updates, Admin system audit logs). This represents clear **Separation of Concerns**.

### 4. Security Implementation

*   **Q: How did you protect the database from SQL Injection?**
    *   *Answer:* We avoid using direct SQL concatenation. Instead, we use **PDO Prepared Statements** with parameter binding (`$stmt->execute(['param' => $value])`). This compiles the SQL query beforehand, preventing malicious input from altering the SQL commands.

*   **Q: How are user passwords secured?**
    *   *Answer:* We never store passwords in plaintext. We use PHP's native **`password_hash()`** function utilizing the bcrypt algorithm. During login, we verify passwords securely using **`password_verify()`**.

*   **Q: How do you prevent Cross-Site Scripting (XSS)?**
    *   *Answer:* We sanitize all output displayed on the screens using **`htmlspecialchars()`** to render HTML tags as text literals rather than executing them in the browser.

---

## 👥 Authors
*   **R K Gagan (AS2023530)**
*   **G R G T Jayawardhana (AS2023552)**
*   **A C Senanayaka (AS2023478)**
