=========================================
OVPSAS PORTAL - SYSTEM SETUP INSTRUCTIONS
=========================================

Hello! To run this Laravel application locally, your computer needs a PHP environment and a MySQL database. We have pre-packaged all the project files so you do not need to use Composer or NPM.

-----------------------------------------
PART 1: REQUIRED SOFTWARE
-----------------------------------------
Please ensure you have ONE of the following local server environments installed before proceeding:

OPTION A: Laravel Herd & DBngin (Recommended - Easiest Setup)
1. Download Laravel Herd (provides PHP): https://herd.laravel.com/
2. Download DBngin (provides MySQL): https://dbngin.com/
3. Open DBngin, click "+" to create a new MySQL service (default settings), and click "Start".
4. Open Laravel Herd to ensure PHP is running.

OPTION B: XAMPP (Traditional)
1. Download XAMPP: https://www.apachefriends.org/
2. Open the XAMPP Control Panel.
3. Click "Start" next to both Apache and MySQL.
*(Note: Ensure PHP is added to your Windows Environment Variables/PATH so the terminal recognizes 'php' commands).*

-----------------------------------------
PART 2: PREPARE THE DATABASE
-----------------------------------------
1. Open your database manager (e.g., HeidiSQL, phpMyAdmin, or DBngin).
2. Connect to your local MySQL server (Host: 127.0.0.1, Port: 3306, User: root, Password: [leave blank]).
3. Create a brand new, empty database and name it exactly: projdbms

-----------------------------------------
PART 3: AUTOMATED SYSTEM BUILD
-----------------------------------------
We have created a script that automatically configures the environment, secures the app, and builds the database tables with our default system data.

1. Open this project folder.
2. Double-click the "setup.bat" file.
3. A black terminal window will appear, run the database migrations, and inject the Super Admin account. Wait for it to say "Setup Complete!" before pressing any key to close it.

-----------------------------------------
PART 4: LAUNCH THE PORTAL
-----------------------------------------
If you are using Laravel Herd:
1. Drag and drop this entire project folder into your "Herd" folder (usually located in your Documents or Users directory).
2. Open your web browser and navigate to: http://ovpsas.test

If you are using XAMPP or the Terminal:
1. Open your computer's terminal (Command Prompt or PowerShell) inside this project folder.
2. Type the following command and press Enter: php artisan serve
3. Open your web browser and go to: http://127.0.0.1:8000

-----------------------------------------
PART 5: SYSTEM ACCESS
-----------------------------------------
The automated setup has generated the core offices and the master administrator account. You may log in using the following credentials:

Email: superadmin@sasis.edu
Password: password123

Email: admin@sasis.edu
Password: password123

Email: staff@sasis.edu
Password: password123

Email: viewer@sasis.edu
Password: password123

Email: test@example.com
Password: password123