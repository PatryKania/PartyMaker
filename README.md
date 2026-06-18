## Co trzeba mieć na komputerze

Przed startem zainstaluj:

- PHP 8.2 lub nowsze
- Composer
- Node.js i npm
- MySQL albo MariaDB
- Git

Sprawdzenie wersji:

```bash
php -v
composer -V
node -v
npm -v
git --version
```

## Uruchomienie po pobraniu z GitHub

### 1. Pobierz projekt

git clone https://github.com/PatryKania/PartyMaker.git
cd PartyMaker

### 2. Zainstaluj paczki PHP

composer install

### 3. Zainstaluj paczki JavaScript

npm install

### 4. Utworz plik `.env`

Skopiuj plik przykładowy .env.example i zmień nazwę na .env.
Potem otworz `.env` i wpisz swoje dane. Najważniejsze pola są opisane w pliku `.env.example`.

### 5. Utworz klucz aplikacji

php artisan key:generate

To polecenie samo wpisze `APP_KEY` w pliku `.env`.

### 6. Utworz baze danych

W MySQL utworz pusta baze danych.
Potem w `.env` ustaw:
DB_CONNECTION=
DB_HOST=
DB_PORT=
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=

### 7. Utworz tabele w bazie

php artisan migrate --seed

### 8. Utworz link do plikow publicznych

php artisan storage:link

### 9. Wyczyść cache konfiguracji

php artisan optimize:clear

### 10. Uruchom aplikacje

Otworz kilka terminali w folderze projektu.

Terminal 1, serwer Laravel:

php artisan serve

Terminal 2, frontend Vite:

npm run dev

Terminal 3, realtime Reverb:

php artisan reverb:start

### 11. Wejdz do aplikacji

Otworz w przegladarce:

http://127.0.0.1:8000

Panel aplikacji jest pod adresem:

http://127.0.0.1:8000/dashboard

## Logowanie przez Google i Facebook

Logowanie społecznościowe zadziała dopiero po wpisaniu danych w `.env` i migracji na serwer:

- `GOOGLE_CLIENT_ID`
- `GOOGLE_CLIENT_SECRET`
- `FACEBOOK_CLIENT_ID`
- `FACEBOOK_CLIENT_SECRET`

```

## Mail, SMS i AI

Wysyłanie maili, SMS oraz podpowiedzi AI wymagają własnych kluczy:

- mail: pola `MAIL_*`
- SMS: `SMSAPI_AUTH_TOKEN`
- AI: `LLM_API_KEY`, `LLM_BASE_URL`, `LLM_MODEL`
