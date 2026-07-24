# 💻 Arunella - GitHub Code Upload Guide

Follow this step-by-step checklist to upload your project codebase to your public GitHub repository to show your examiners and verify your work.

---

## 🛠️ Step 1: Install Git (If Not Already Installed)
Ensure you have Git installed on your computer. If not:
1. Download Git for Windows from the official website: [git-scm.com](https://git-scm.com/).
2. Run the installer and keep the default options.

---

## 🛠️ Step 2: Initialize Git in your project folder
1. Open **Command Prompt (cmd)** or **Git Bash**.
2. Navigate to your project directory inside the WampServer root (or whichever folder your project is active in):
   ```bash
   cd C:\wamp64\www\arunella
   ```
3. Initialize a new Git repository:
   ```bash
   git init
   ```

---

## 🛠️ Step 3: Stage and Commit the Files
Prepare all project files for uploading:
1. Stage all files in the directory:
   ```bash
   git add .
   ```
2. Commit the staged files with an initial message:
   ```bash
   git commit -m "Initial commit - Arunella Smart Agri Supply Chain System"
   ```

---

## 🛠️ Step 4: Create a Repository on GitHub
1. Go to [github.com](https://github.com/) and log in (or create an account if you don't have one).
2. Click the **`+`** icon in the top-right corner and select **New repository** (or click the green **New** button).
3. Fill in the repository details:
   - **Repository name:** `arunella`
   - **Description (Optional):** `Smart Agricultural Supply Chain Management System for Service Oriented Computing (CSC 313)`
   - **Visibility:** **Public** (required so the examiners can see it)
   - **Do NOT** check "Add a README file", "Add .gitignore", or "Choose a license" (we already have a README and project structure).
4. Click **Create repository**.

---

## 🛠️ Step 5: Link the Local Repository to GitHub and Push
Copy the commands from your new GitHub repository page or run the following commands (replace `YOUR_GITHUB_USERNAME` with your actual GitHub account username):

1. Set the default branch name to `main`:
   ```bash
   git branch -M main
   ```
2. Link your local project directory to your new remote repository:
   ```bash
   git remote add origin https://github.com/YOUR_GITHUB_USERNAME/arunella.git
   ```
3. Push your committed code up to GitHub:
   ```bash
   git push -u origin main
   ```

*(Note: Git might open a window asking you to authenticate/log in to your GitHub account. Complete the login to authorize the upload).*

---

## 🔄 Step 6: How to Push Future Updates
If you make changes to any files later and want to update the code on GitHub:
1. Stage the changed files:
   ```bash
   git add .
   ```
2. Commit the changes with a short description:
   ```bash
   git commit -m "Updated UI styling and mobile routing guides"
   ```
3. Push the changes to GitHub:
   ```bash
   git push
   ```
