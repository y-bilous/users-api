# User API

Simple Symfony API for user management.

**Warning**, this is not a finished production project, it is just a coding challenge.

## Requirements:

PHP >=8.2, composer

## Initializing:

```bash
git clone https://github.com/y-bilous/users-api.git
cd ./users-api
cp .env.dev .env
composer install
php bin/console doctrine:migrations:migrate
php bin/console doctrine:fixtures:load
php bin/console lexik:jwt:generate-keypair
symfony server:start
```

## Usage

### Login as root user (on test data)

Use `httpie` for simplicity:
```bash
http POST :8000/api/login login=root pass=rootpass
```
In response, we will receive a JWT token:
```
HTTP/1.1 200 OK
{
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9..."
}
```

### Login as regular user (on test data)
```bash
http POST :8000/api/login login=user1 pass=userpass
```
In response, we will receive a JWT token:
```
HTTP/1.1 200 OK
{
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9..."
}
```

### API Documentation
Read the API documentation on http://127.0.0.1:8000/api/doc

## Expected Results

| Request | Root | User1 |
|---------|------|-------|
| `GET /v1/api/users` | ✅ sees everyone | ⚠️ sees only themselves |
| `GET /v1/api/users/{id}` | ✅ any user | ✅ only their own id |
| `POST /v1/api/users` | ✅ can create | ❌ 403 Forbidden |
| `PUT /v1/api/users/{id}` | ✅ any user | ✅ only their own id |
| `DELETE /v1/api/users/{id}` | ✅ any user | ❌ 403 Forbidden |

## Architecture
For architecture decisions look at ADR.md
