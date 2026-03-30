@echo off
echo ========================================
echo Setting up the OVPSAS Portal...
echo ========================================

echo Creating environment file...
copy .env.example .env

echo Configuring database settings...
echo DB_CONNECTION=mysql >> .env
echo DB_HOST=127.0.0.1 >> .env
echo DB_PORT=3306 >> .env
echo DB_DATABASE=projdbms >> .env
echo DB_USERNAME=root >> .env
echo DB_PASSWORD= >> .env

echo Generating application key...
call php artisan key:generate

echo Running database migrations and injecting default data...
call php artisan migrate:fresh --seed --force

echo ========================================
echo Setup Complete! 
echo ========================================
pause