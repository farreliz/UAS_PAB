# Setup Instructions - UAS API with Passport OAuth 2.0

## 📋 Overview

Proyek ini mengimplementasikan OAuth 2.0 **Client Credentials Grant** untuk skenario **M2M (Machine to Machine)** atau **H2H (Host to Host)** menggunakan Laravel Passport.

---

## 🚀 Step 1: Clone Repository dan Install Dependencies

```bash
# Clone repository
git clone https://github.com/rifkstwan/UAS_API.git
cd UAS_API

# Install dependencies
composer install

# Copy .env file
cp .env.example .env

# Generate APP_KEY
php artisan key:generate
```

---

## 🔐 Step 2: Setup Database

```bash
# Jalankan migrations (termasuk OAuth tables)
php artisan migrate

# (Optional) Seed database dengan sample data
php artisan db:seed
```

---

## 🔑 Step 3: Install dan Setup Passport

### 3a. Generate Encryption Keys

```bash
php artisan passport:install
```

**Output yang diharapkan:**
```
Encryption key generated successfully.
Personal access client created successfully.
Client ID: 1
Client secret: <long-random-string>

Password grant client created successfully.
Client ID: 2  
Client secret: <long-random-string>
```

### 3b. Buat OAuth Client untuk Client Credentials Grant

Ini adalah client yang akan digunakan untuk M2M/H2H communication:

```bash
php artisan passport:client --client
```

**Interaksi:**
```
What should we name the client? [Laravel Password Grant Client]
 > GoApi M2M Client

Which user should this client be assigned to?:
  [0] admin@example.com
  [1] user@example.com  
 > 0
```

**Output (SIMPAN INI!):**
```
✔ Client created successfully

Client ID: 3
Client secret: <long-random-string>
```

---

## 📝 Step 4: Konfigurasi .env

Update file `.env` dengan:

```env
APP_NAME="UAS API"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=sqlite
```

---

## ✅ Step 5: Verifikasi Installation

Pastikan semuanya berjalan dengan baik:

```bash
# Test routes
php artisan route:list | grep api

# Cek database
php artisan tinker
# Di dalam tinker:
# >>> DB::table('oauth_clients')->get();
# >>> exit
```

---

## 🎯 Step 6: Testing API dengan Client Credentials

### 6a. Start Development Server

```bash
php artisan serve
```

Server akan berjalan di: `http://localhost:8000`

### 6b. Dapatkan Access Token

Gunakan **Client ID** dan **Client Secret** dari Step 3b:

**Method:** POST  
**URL:** `http://localhost:8000/api/oauth/token`

**Body (JSON):**
```json
{
  "grant_type": "client_credentials",
  "client_id": "3",
  "client_secret": "<your-client-secret>",
  "scope": ""
}
```

**Dengan cURL:**
```bash
curl -X POST http://localhost:8000/api/oauth/token \
  -H "Content-Type: application/json" \
  -d '{
    "grant_type": "client_credentials",
    "client_id": "3",
    "client_secret": "your-client-secret-here",
    "scope": ""
  }'
```

**Response (Success):**
```json
{
  "token_type": "Bearer",
  "expires_in": 31536000,
  "access_token": "eyJ0eXAiOiJKV1QiLCJhbGc..."
}
```

### 6c. Gunakan Token untuk Akses API

Simpan `access_token` dari response di atas, kemudian gunakan untuk akses endpoints:

#### **1. Get Weather**

```bash
curl -X GET "http://localhost:8000/api/weather?city=Jakarta" \
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN" \
  -H "Accept: application/json"
```

**Response:**
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

#### **2. Get Currency**

```bash
curl -X GET "http://localhost:8000/api/currency?from=USD&to=IDR" \
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN" \
  -H "Accept: application/json"
```

**Response:**
```json
{
  "success": true,
  "data": {
    "from": "USD",
    "to": "IDR",
    "rate": 15750.5,
    "amount": 1,
    "timestamp": "2025-12-11T01:10:00.000000Z"
  }
}
```

#### **3. Get News**

```bash
curl -X GET "http://localhost:8000/api/news?category=technology" \
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN" \
  -H "Accept: application/json"
```

**Response:**
```json
{
  "success": true,
  "data": {
    "category": "technology",
    "total": 2,
    "articles": [
      {
        "id": 1,
        "title": "Latest Technology News 1",
        "description": "This is a sample technology news article",
        "date": "2025-12-10",
        "source": "API Server"
      },
      {
        "id": 2,
        "title": "Latest Technology News 2",
        "description": "Another sample technology news article",
        "date": "2025-12-11",
        "source": "API Server"
      }
    ],
    "timestamp": "2025-12-11T01:10:00.000000Z"
  }
}
```

#### **4. Post Data**

```bash
curl -X POST "http://localhost:8000/api/data" \
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "payload": {
      "name": "John Doe",
      "email": "john@example.com",
      "message": "Test data from M2M communication"
    }
  }'
```

**Response:**
```json
{
  "success": true,
  "message": "Data received successfully",
  "data": {
    "id": "657a8f9c1234e",
    "received_payload": {
      "name": "John Doe",
      "email": "john@example.com",
      "message": "Test data from M2M communication"
    },
    "timestamp": "2025-12-11T01:10:00.000000Z"
  }
}
```

---

## 🧪 Using Postman Collection

### Import Collection

1. Buka Postman
2. Klik `Import` → `Raw text`
3. Paste collection JSON (lihat file: `postman_collection.json`)
4. Klik `Import`

### Setup Environment Variables di Postman

```json
{
  "api_url": "http://localhost:8000",
  "client_id": "3",
  "client_secret": "your-client-secret",
  "access_token": "" // Akan di-generate otomatis
}
```

### Run Pre-request Script untuk Auto Token

Pastikan request pertama adalah OAuth token request dengan script:

```javascript
if (!pm.environment.get('access_token') || pm.environment.get('token_expires_at') < Date.now()) {
    pm.sendRequest({
        url: pm.environment.get('api_url') + '/api/oauth/token',
        method: 'POST',
        header: {
            'Content-Type': 'application/json'
        },
        body: {
            mode: 'raw',
            raw: JSON.stringify({
                grant_type: 'client_credentials',
                client_id: pm.environment.get('client_id'),
                client_secret: pm.environment.get('client_secret'),
                scope: ''
            })
        }
    }, (err, response) => {
        if (!err) {
            pm.environment.set('access_token', response.json().access_token);
            pm.environment.set('token_expires_at', Date.now() + (response.json().expires_in * 1000));
        }
    });
}
```

---

## 🔧 Troubleshooting

### Error: "Unauthenticated" (401)

**Penyebab:** Token tidak dikirim atau token expired

**Solusi:**
- Pastikan header `Authorization: Bearer {token}` ada
- Generate token baru jika sudah expired
- Periksa Client ID dan Client Secret

### Error: "Invalid client credentials" (400)

**Penyebab:** Client ID atau Client Secret salah

**Solusi:**
- Verifikasi Client ID dan Secret dari database:
  ```bash
  php artisan tinker
  >>> DB::table('oauth_clients')->where('secret', 'like', '%secret%')->first();
  ```

### Error: "Route not found" untuk `/api/oauth/token`

**Penyebab:** Passport routes tidak terdaftar

**Solusi:**
```bash
# Jalankan ulang passport install
php artisan passport:install

# Atau publish passport views
php artisan vendor:publish --tag=passport-views
```

### Error: "SQLSTATE[HY000]: General error: 15 'database is locked'"

**Penyebab:** SQLite database sedang diakses dari multiple process

**Solusi:**
```bash
# Gunakan database lain (MySQL) untuk development
# Atau jalankan semuanya di 1 terminal:
php artisan serve

# Terminal lain untuk testing
curl ...
```

---

## 📚 File Structure

```
UAS_API/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── Api/
│   │   │       └── GoApiController.php       ← API Endpoints
│   │   └── Middleware/
│   │       └── CheckPassportToken.php        ← Passport Middleware
│   └── Models/
│       └── User.php                          ← User Model dengan Passport
├── bootstrap/
│   └── app.php                               ← App Configuration
├── config/
│   ├── auth.php                              ← Auth Configuration
│   └── passport.php                          ← Passport Configuration
├── database/
│   └── migrations/                           ← Passport OAuth Tables
├── routes/
│   ├── api.php                               ← API Routes
│   └── web.php
├── .env.example                              ← Environment Template
├── composer.json                             ← Dependencies
└── SETUP_INSTRUCTIONS.md                     ← File ini
```

---

## 🎓 Konsep Penting

### Client Credentials Grant Flow

```
┌─────────────┐                                  ┌─────────────┐
│   Client    │                                  │ Authorization│
│  (M2M/H2H)  │                                  │   Server    │
└─────────────┘                                  └─────────────┘
      │                                                 │
      │ 1. Request Token dengan client_id + secret    │
      ├────────────────────────────────────────────→ │
      │                                                 │
      │ 2. Validate credentials                        │
      │                                        (validation)
      │                                                 │
      │           3. Return Access Token              │
      │ ←────────────────────────────────────────────┤
      │                                                 │
      │                          ┌─────────────┐      │
      │                          │ API Server  │      │
      │                          └─────────────┘      │
      │                                 │             │
      │ 4. Request API dengan token    │             │
      ├────────────────────────────────→             │
      │                                 │             │
      │ 5. Validate token                            │
      │                    (validation)              │
      │                                 │             │
      │                  6. Return Data│             │
      │ ←────────────────────────────────┤           │
      │                                 │             │
```

### Endpoint Security Status

| Endpoint | Method | Auth | Status |
|----------|--------|------|--------|
| `/api/oauth/token` | POST | - | 🔓 Public (untuk dapat token) |
| `/api/weather` | GET | Bearer Token | 🔒 Protected |
| `/api/currency` | GET | Bearer Token | 🔒 Protected |
| `/api/news` | GET | Bearer Token | 🔒 Protected |
| `/api/data` | POST | Bearer Token | 🔒 Protected |

---

## 📞 Support

Untuk masalah lebih lanjut, lihat:
- [Laravel Passport Documentation](https://laravel.com/docs/11.x/passport)
- [OAuth 2.0 Specification](https://tools.ietf.org/html/rfc6749)

---

**Last Updated:** December 11, 2025  
**Version:** 1.0
