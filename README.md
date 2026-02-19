# Freemiusfast | Laravel & React Freemius SaaS Boilerplate

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" />
  <img src="https://img.shields.io/badge/React-20232A?style=for-the-badge&logo=react&logoColor=61DAFB" />
  <img src="https://img.shields.io/badge/Monetization-Freemius-blue?style=for-the-badge" />
</p>

[![Download Freemiusfast](https://a.fsdn.com/con/app/sf-download-button)](https://sourceforge.net/projects/freemiusfast/files/latest/download)


**Freemiusfast** is a professional-grade SaaS boilerplate designed specifically for developers selling web applications through the [Freemius](https://freemius.com/) ecosystem. It combines the power of a Laravel backend with a modern React SPA frontend to create a seamless licensing and management experience.

---

## ✨ Features
* **Freemius SDK Integration:** Pre-configured logic for license validation, trial management, and subscription syncing.
* **Modern Tech Stack:** Laravel 12, React, and Inertia.js for a smooth Single Page Application (SPA) feel.
* **Dynamic Menu System:** Database-driven sidebar management with automatic active-state detection.
* **Advanced RBAC:** Robust Role-Based Access Control to restrict features based on license tiers (Free vs. Pro).
* **Responsive UI:** Beautiful dashboard built with Tailwind CSS and Lucide icons.

### Freemius Setting
<p align="center">
  <img src="/public/screenshot/admin_freemius_settings.jpg" width="800" alt="Freemius Setting" />
</p>

### Inactive Plan Account
<p align="center">
  <img src="/public/screenshot/inactive_plan.png" width="800" alt="Inactive Plan Screenshot" />
</p>

### Active Plan Account
<p align="center">
  <img src="/public/screenshot/active_plan_account.png" width="800" alt="Active Plan Screenshot" />
</p>



---

## 🛠 Installation

### 1. Clone the Repository
```bash
git clone https://github.com/naharsoftbd/freemiusfast.git
cd freemiusfast
```

### 2. Install Dependencies

**Install PHP dependencies**
```bash
composer install
```

**Install JavaScript dependencies**
```bash
npm install
```

### 3. Environment Setup
Create your environment file and generate the application key:

```bash
cp .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed
php artisan user:create-admin
```

### 4. Freemius Configuration
Add your Freemius developer credentials to your dashobard to enable licensing features:
<p align="center">
  <img src="/public/screenshot/admin_freemius_settings.jpg" width="800" alt="Freemius Setting" />
</p>


## 🔑 Licensing Architecture
Freemiusfast utilizes a middleware-based approach to verify Freemiusfast licenses. This ensures that premium React components and Laravel routes are only accessible to authorized users.

Handshake: On Istallation, the app verifies the user's license key via the license API.

Caching: Subscription status is cached locally to ensure high performance and reduce API overhead.

Gatekeeping: The CheckLicense middleware intercepts requests to "Pro" features.

## 🤝 Contributing
Contributions are welcome! If you have ideas for better Freemius integration or UI improvements, please fork the repo and submit a pull request.

## 📄 License
This project is open-source software licensed under the GNU license.


---

**Would you like me to generate a `LICENSE` file or a `.gitignore` specifically optimized for a Laravel-Rea
