# Attendance System

QR-based employee attendance system. Admins manage offices and employees via a web dashboard; employees check in/out by scanning a QR code from a Flutter mobile app.

## Stack

- **Backend / Admin UI:** Laravel 12, MySQL 8, Tailwind CSS (Breeze)
- **Mobile (Phase 2):** Flutter
- **Auth:** Laravel session (admin web) · Sanctum Bearer tokens 24 h (Flutter API)
- **Local dev:** XAMPP (Apache + MySQL on Windows)
- **Production:** Railway

---

## Local Setup

**Requirements:** PHP 8.2+, Composer, MySQL running (XAMPP)

```bash
# 1. Install dependencies
composer install
npm install && npm run build

# 2. Configure environment
cp .env.example .env
php artisan key:generate
```

Edit `.env` and set your MySQL credentials:
```
DB_DATABASE=attendance_dev
DB_USERNAME=root
DB_PASSWORD=
```

```bash
# 3. Create the database (if it doesn't exist)
mysql -u root -e "CREATE DATABASE IF NOT EXISTS attendance_dev;"

# 4. Run migrations + seed (creates admin user + demo office)
php artisan migrate --seed

# 5. Start the dev server
php artisan serve
```

Visit `http://localhost:8000/admin` and log in with:
- **Email:** `admin@local.test`
- **Password:** `password`

---

## API Endpoints (Flutter)

All API routes are prefixed `/api`. Protected routes require `Authorization: Bearer <token>` and `Accept: application/json`.

| Method | Route | Auth | Description |
|--------|-------|------|-------------|
| POST | `/api/login` | — | Returns Sanctum token (24 h) |
| POST | `/api/logout` | ✓ | Revokes current token |
| POST | `/api/attendance/check-in` | ✓ | QR token + GPS → creates record |
| POST | `/api/attendance/check-out` | ✓ | Closes today's open record |

### Check-in payload
```json
{
  "token": "<QR UUID>",
  "latitude": 11.5564,
  "longitude": 104.9282
}
```

---

## Railway Deployment

1. Create a new Railway project, add a **MySQL** plugin.
2. Set these environment variables in Railway:

| Variable | Value |
|----------|-------|
| `APP_ENV` | `production` |
| `APP_DEBUG` | `false` |
| `APP_KEY` | output of `php artisan key:generate --show` |
| `APP_URL` | your Railway app URL |
| `DB_CONNECTION` | `mysql` |
| `DB_HOST` | from Railway MySQL plugin |
| `DB_PORT` | `3306` |
| `DB_DATABASE` | from Railway MySQL plugin |
| `DB_USERNAME` | from Railway MySQL plugin |
| `DB_PASSWORD` | from Railway MySQL plugin |
| `SANCTUM_STATEFUL_DOMAINS` | your Railway domain |
| `SESSION_DRIVER` | `database` |

3. Set the start command to:
```
php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=$PORT
```
