# 🚀 Railway Deployment Guide (BW Store SaaS)

Yeh guide aapko **BW Store** ko Railway par 100% successfully deploy karne ke step-by-step instructions deti hai.

---

## 📌 Railway par Deploy Karne Ke Steps

### Step 1: Railway par Naya Project Banayein
1. [Railway.app](https://railway.app) par login karein.
2. **"New Project"** par click karein.

---

### Step 2: MySQL Database Add Karein
1. **"Provision MySQL"** ya **"Add Service" -> "Database" -> "MySQL"** select karein.
2. Railway aapke project me ek MySQL database create kar dega.
3. Iske variables (`MYSQLHOST`, `MYSQLPORT`, `MYSQLDATABASE`, `MYSQLUSER`, `MYSQLPASSWORD`, `MYSQL_URL`) automatically provide ho jaate hain.

---

### Step 3: GitHub Repo Connect Karein (Web Service)
1. Project me **"+ Create"** / **"Add Service"** par click karein.
2. **"GitHub Repo"** choose karein aur apna yeh repository select karein (`Saas bw web studio` / `bw-stores`).
3. Railway automatically `Dockerfile` & `railway.json` ko detect karega aur build start kar dega.

---

### Step 4: Environment Variables Configure Karein
Web service ke **"Variables"** tab me jayein aur yeh variables set karein:

| Variable | Recommended Value / Note |
| :--- | :--- |
| `APP_NAME` | `BW Store` |
| `APP_ENV` | `production` |
| `APP_DEBUG` | `false` (ya `true` testing ke liye) |
| `APP_KEY` | Koi bhi 32-character random string (e.g. `base64:random32chars...`) |
| `APP_TIMEZONE` | `Asia/Kolkata` |
| `ADMIN_EMAIL` | `admin@bwwebstudio.com` |
| `ADMIN_PASSWORD` | *Apna strong password daalein* |
| `ADMIN_NAME` | `BW Admin` |

> 💡 **Database Auto-Link:**
> Railway project ke andar MySQL service hone par humara code automatically `MYSQLHOST`, `MYSQL_URL`, `DATABASE_URL` detect kar leta hai. Agar alag service me hai to aap `MYSQL_URL` ya `DB_HOST`, `DB_USER`, `DB_PASSWORD`, `DB_NAME` manually daal sakte hain.

---

### Step 5: Public Domain Generate Karein
1. Web service par click karein -> **"Settings"** tab me jayein.
2. **"Networking"** section me jayein.
3. **"Generate Domain"** par click karein (e.g., `bw-store-production.up.railway.app`).
4. Uss domain URL ko copy karke `APP_URL` variable me set kar dein.

---

## ⚙️ Deployment Automation Details

Humne deployment ko 100% fail-proof banane ke liye yeh files setup ki hain:

1. **`Dockerfile`**: Production-ready PHP 8.2 + Apache image jisme saare required extensions (`pdo_mysql`, `gd`, `zip`, `opcache`, `bcmath`) aur Composer auto-configured hain.
2. **`docker-entrypoint.sh`**: Dynamic Railway `$PORT` ko bind karta hai, database connection retry loop chalata hai (taaki MySQL boot hone ka wait kare), aur automatic migrations & seeders run karta hai.
3. **`railway.json`**: Railway builder config jo Dockerfile build ko enforce karta hai.
4. **`nixpacks.toml`**: Fallback configuration agar Dockerfile ke bina Nixpacks use ho.
5. **`Procfile` & `start.sh`**: Built-in CLI server fallback configuration.
6. **`config/database.php`**: `MYSQL_URL`, `DATABASE_URL`, `MYSQLHOST`, etc. sabhi formats ko smoothly parse karta hai.
7. **`database/migrate.php`**: 25-retries (50 seconds) loop ke sath database ready hone ka wait karta hai taaki container kabhi crash loop me na fase.

---

## 🔑 Default Login Credentials

- **Admin Login URL:** `https://<your-railway-domain>/admin/login`
- **Email:** `ADMIN_EMAIL` me jo set kiya ho (Default: `admin@bwwebstudio.com`)
- **Password:** `ADMIN_PASSWORD` me jo set kiya ho

---
🎉 **Ab aap direct Railway par bina kisi error ke deploy kar sakte hain!**
