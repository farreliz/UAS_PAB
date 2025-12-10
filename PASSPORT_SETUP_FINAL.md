# Passport OAuth 2.0 Setup - Complete Guide

## 📋 Status

✅ **Duplicate migrations telah dihapus dari repository**

Anda sekarang siap untuk melanjutkan setup. Ikuti langkah-langkah di bawah ini **DI LOCAL MACHINE ANDA**.

---

## 🚀 Setup Steps (Local Machine)

### Step 1: Pull Latest Changes dari GitHub

```bash
cd UAS_API
git pull origin main
```

Verifikasi bahwa duplicate migrations sudah dihapus:
```bash
ls -la database/migrations/ | grep oauth
```

Harus ada 5 file Passport saja (tidak ada duplikat dengan timestamp 152907-152911):
```
2025_12_10_152658_create_oauth_auth_codes_table.php
2025_12_10_152659_create_oauth_access_tokens_table.php
2025_12_10_152700_create_oauth_refresh_tokens_table.php
2025_12_10_152701_create_oauth_clients_table.php
2025_12_10_152702_create_oauth_device_codes_table.php
```

---

### Step 2: Fresh Database Setup

Karena database sudah memiliki tabel OAuth, mari kita reset:

```bash
# Delete database file jika menggunakan SQLite
rm -f database/database.sqlite

# OR jika menggunakan MySQL, drop dan recreate database
# mysql -u root -p
# > DROP DATABASE uas_api;
# > CREATE DATABASE uas_api;
# > exit
```

---

### Step 3: Fresh Migrate

```bash
# Jalankan semua migrations (tanpa duplikat)
php artisan migrate --fresh
```

**Output yang diharapkan:**
```
Creating migration table .............................. 6ms
Migrating: 0001_01_01_000000_create_users_table .... 3ms
Migrating: 0001_01_01_000001_create_cache_table .... 2ms  
Migrating: 0001_01_01_000002_create_jobs_table ..... 2ms
Migrating: 2025_12_10_152658_create_oauth_auth_codes_table ......... 1ms
Migrating: 2025_12_10_152659_create_oauth_access_tokens_table ..... 1ms
Migrating: 2025_12_10_152700_create_oauth_refresh_tokens_table .... 1ms
Migrating: 2025_12_10_152701_create_oauth_clients_table ........... 1ms
Migrating: 2025_12_10_152702_create_oauth_device_codes_table ...... 1ms

Migrated successfully.
```

---

### Step 4: Run Passport Install

Kali ini tidak akan ada error karena duplikat sudah dihapus:

```bash
php artisan passport:install
```

**Output yang diharapkan:**
```
   INFO  Publishing [passport-config] assets.

  File [config/passport.php] already exists ........................ SKIPPED

   INFO  Publishing [passport-migrations] assets.

  Copying directory [vendor/laravel/passport/database/migrations] to [database/migrations] DONE

   INFO  Generating encryption keys.

  Encryption keys generated successfully.

   INFO  Creating oauth clients.

  Creating personal access client...
  Personal access client created successfully.
  Client ID: 1
  Client secret: eyJ0eXAiOiJKV1QiLCJhbGc...

  Creating password grant client...
  Password grant client created successfully.
  Client ID: 2
  Client secret: eyJ0eXAiOiJKV1QiLCJhbGc...
```

---

### Step 5: Create OAuth Client for M2M/H2H

Ini adalah client yang akan digunakan untuk testing API:

```bash
php artisan passport:client --client
```

**Interaksi:**
```
What should we name the client? [Laravel Password Grant Client]
 > GoApi M2M Client

Which user should this client be assigned to?:
  [0] root@example.com
  [1] admin@example.com
  [2] user@example.com
 > 0

✓ Client created successfully
```

**⚠️ PENTING: SIMPAN OUTPUT INI!**

Client output akan seperti ini:
```
Client ID: 3
Client secret: 8I2d7K9mL4pQ6vW1xY8zA5bC3dE9fG2hJ7kL0mN4oPqRsT
```

**Simpan di tempat aman untuk testing!**

---

### Step 6: Verify Database Setup

Verifikasi bahwa semua tabel OAuth sudah dibuat dengan benar:

```bash
php artisan tinker
```

Dalam Tinker:
```php
# Check OAuth clients
>>> DB::table('oauth_clients')->get();
# Output akan menampilkan 3 clients (ID: 1, 2, 3)

# Check users
>>> DB::table('users')->count();
# Jika 0, kita perlu seed data

# Exit
>>> exit
```

---

### Step 7: Seed Database dengan Sample User (Optional)

Jika users table kosong, mari kita buat sample user:

```bash
# Menggunakan tinker
php artisan tinker
```

Dalam Tinker:
```php
>>> use App\Models\User;
>>> User::create([
...     'name' => 'Admin User',
...     'email' => 'admin@example.com',
...     'password' => bcrypt('password123')
... ]);
>>> exit
```

---

### Step 8: Test API Server

Sekarang mari kita test API dengan Passport OAuth:

#### 8a. Start Development Server

```bash
# Terminal 1
php artisan serve
```

Output:
```
 Laravel development server started: http://127.0.0.1:8000
```

#### 8b. Get Access Token (Terminal 2)

Gunakan **Client ID 3** dan **Client Secret** dari Step 5:

```bash
curl -X POST http://localhost:8000/api/oauth/token \
  -H "Content-Type: application/json" \
  -d '{
    "grant_type": "client_credentials",
    "client_id": "3",
    "client_secret": "YOUR_CLIENT_SECRET_FROM_STEP_5",
    "scope": ""
  }'
```

**Response (Success):**
```json
{
  "token_type": "Bearer",
  "expires_in": 31536000,
  "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6IjJlMzc5ZjAyMmQwN2E0ZjA2YWI5ZDc5MWFhZjYxZGFhMjM2MDQzZmI0ZDU2OGQ5NDQ3MTEzNDAwZTZhZWE2YTA5OGNkZWZiZjI2OGZjMzI1In0..."
}
```

**SIMPAN `access_token` ini!**

#### 8c. Test Endpoint dengan Token

**Test 1: Get Weather**

```bash
curl -X GET "http://localhost:8000/api/weather?city=Jakarta" \
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN" \
  -H "Accept: application/json"
```

**Expected Response:**
```json
{
  "success": true,
  "data": {
    "city": "Jakarta",
    "temperature": "28°C",
    "condition": "Sunny",
    "humidity": "65%",
    "timestamp": "2025-12-11T01:10:00.000000Z"
  }
}
```

**Test 2: Get Currency**

```bash
curl -X GET "http://localhost:8000/api/currency?from=USD&to=IDR" \
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN" \
  -H "Accept: application/json"
```

**Test 3: Get News**

```bash
curl -X GET "http://localhost:8000/api/news?category=technology" \
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN" \
  -H "Accept: application/json"
```

**Test 4: Post Data**

```bash
curl -X POST "http://localhost:8000/api/data" \
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "payload": {
      "name": "Test M2M Client",
      "message": "Testing Client Credentials Grant"
    }
  }'
```

---

## ✅ Verification Checklist

Setelah semua step selesai, pastikan:

- [ ] Tidak ada error saat `php artisan migrate --fresh`
- [ ] Tidak ada error saat `php artisan passport:install`
- [ ] OAuth client (ID: 3) berhasil dibuat
- [ ] Dapat mendapatkan access token dari `/api/oauth/token`
- [ ] Dapat mengakses semua 4 endpoint dengan valid token
- [ ] Mendapat error 401 jika tidak menggunakan token

---

## 🐛 Troubleshooting

### Error: "No application encryption key has been generated"

```bash
php artisan key:generate
```

### Error: "database is locked" (SQLite)

Hapus database dan jalankan fresh migrate:
```bash
rm database/database.sqlite
php artisan migrate --fresh
```

### Error: "Invalid client credentials" saat get token

Verifikasi Client ID dan Secret:
```bash
php artisan tinker
>>> DB::table('oauth_clients')->where('id', 3)->first();
>>> exit
```

### Error: "CORS" saat testing dari browser

Anda harus menggunakan cURL atau Postman, bukan browser langsung.

---

## 📊 Final Architecture

```
┌─────────────────────────────────────────────────────┐
│         Client (M2M/H2H System)                     │
└────────────────────┬────────────────────────────────┘
                     │
          ┌──────────┴──────────┐
          │                     │
          ▼                     ▼
   ┌─────────────┐      ┌──────────────────┐
   │ POST /oauth │      │ GET/POST /api/*  │
   │    /token   │      │  (Protected)     │
   └─────────────┘      └──────────────────┘
          │                     ▲
          │                     │
    Exchange Credentials   Validate Token
    (client_id +              (Bearer Token)
     client_secret)
          │                     │
          └─────────┬───────────┘
                    ▼
        ┌─────────────────────────┐
        │   Passport Guard        │
        │   (Laravel Passport)    │
        └─────────────────────────┘
```

---

## 📝 Summary

**Apa yang sudah selesai:**
- ✅ Laravel 11 dengan Passport OAuth 2.0
- ✅ 4 Endpoints (Weather, Currency, News, Post Data)
- ✅ Client Credentials Grant untuk M2M/H2H
- ✅ Proper error handling dan validation
- ✅ Database setup dengan OAuth tables

**Next Steps untuk UAS:**
1. 📄 Buat dokumentasi Flow (Flowchart/Activity Diagram)
2. 📚 Buat dokumentasi Swagger untuk semua endpoints
3. 📖 Update README.md dengan panduan lengkap

---

**Last Updated:** December 11, 2025  
**Status:** Ready for Testing ✅
