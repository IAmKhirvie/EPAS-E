@echo off
echo ========================================
echo    EPAS-E Installation Script
echo ========================================
echo.

:: Clone the repository
echo [1/9] Cloning repository...
git clone https://github.com/IAmKhirvie/EPAS-E.git
cd EPAS-E

:: Create bootstrap/cache directory
echo [2/9] Creating bootstrap/cache directory...
if not exist "bootstrap\cache" mkdir bootstrap\cache

:: Install Composer dependencies
echo [3/9] Installing Composer dependencies...
composer install

:: Install NPM dependencies
echo [4/9] Installing NPM dependencies...
npm install

:: Run npm audit fix
echo [5/9] Running npm audit fix...
npm audit fix --force

:: Environment setup
echo [6/9] Setting up environment file...
copy .env.example .env

:: Generate application key
echo [7/9] Generating application key...
php artisan key:generate

:: Database setup
echo [8/9] Running database migrations...
php artisan migrate

echo [9/9] Seeding database...
php artisan db:seed

echo.
echo ========================================
echo    Installation Complete!
echo ========================================
echo.
echo To start the development server, run:
echo    php artisan serve
echo    npm run dev (in a separate terminal)
echo.
pause
