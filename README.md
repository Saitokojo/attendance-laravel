# Attendance System

QR-based employee attendance system. Admins manage offices and employees via a web dashboard; employees check in/out by scanning a QR code from a Flutter mobile app.

## Stack

- **Backend / Admin UI:** Laravel 12, MySQL 8, Tailwind CSS (Breeze)
- **Mobile (Phase 2):** Flutter
- **Auth:** Laravel session (admin web) · Sanctum Bearer tokens 24 h (Flutter API)
- **Local dev:** XAMPP (Apache + MySQL on Windows)
- **Production:** Railway

---

## Local/Development Setup

> [!NOTE]
> You Need Docker for this.

This Will Create Network Bridge For our laravel container & mysql-container to communte with each other.
```bash
docker network create attendance-network
```
First, We'll create mysql-container
```bash
docker run -d \
  --name attendance-mysql \
  --network attendance-network \
  -e MYSQL_ROOT_PASSWORD=root123 \
  -e MYSQL_DATABASE=attendance_db \
  -e MYSQL_USER=user \
  -e MYSQL_PASSWORD=user123 \
  -p 3306:3306 \
  -v attendance-mysql-data:/var/lib/mysql \
  mysql:8.4
```

Then create .env file from .env.example, And Input these Value into variables.

Example:
```
DB_CONNECTION=mysql
DB_HOST=attendance-mysql        # mysql container name
DB_PORT=3306
DB_DATABASE=attendance_db       # our database name
DB_USERNAME=user
DB_PASSWORD=user123
```
cd ~/attendance-system/laravel:

```
docker build -t attendance-app
```
Then create container with our newly created image:
```
docker run -it \
  --network attendance-network \
  --name attendance-app \
  -p 8000:8000 \
  -v "$(pwd):/var/www/html" \
  -v attendance-vendor:/var/www/html/vendor \
  -w /var/www/html \
  attendance-app
```

```bash
# 1. Generate key for our app-key in env
php artisan key:generate

# 2. Run migrations + seed (creates admin user + demo office)
php artisan migrate --seed
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
