# Run Joystick-Store locally (Arch Linux)

## 1. Install PHP and MariaDB

```bash
sudo pacman -S php mariadb
```

Enable the **mysqli** extension (it’s included in the `php` package but disabled by default). Edit `/etc/php/php.ini` and uncomment this line (remove the leading `;`):

```ini
;extension=mysqli   →   extension=mysqli
```

One-line fix:

```bash
sudo sed -i 's/^;extension=mysqli/extension=mysqli/' /etc/php/php.ini
```

Verify: `php -m | grep mysqli` should show `mysqli`.

## 2. Start MariaDB and create database

```bash
sudo systemctl start mariadb   # or: sudo mariadb-service start
sudo mariadb -e "CREATE DATABASE IF NOT EXISTS levelup;"
sudo mariadb levelup < /home/raj/Joystick-Store/levelup.sql
```

If MariaDB asks for a password, default on Arch is often empty (press Enter). If you set a root password, use:

```bash
mariadb -u root -p -e "CREATE DATABASE IF NOT EXISTS levelup;"
mariadb -u root -p levelup < /home/raj/Joystick-Store/levelup.sql
```

## 3. Use local DB in .env (so it doesn’t use AWS RDS)

Edit `.env` and set **local** DB values (comment out or replace the AWS ones):

```env
# Local database
DB_HOST=127.0.0.1
DB_USER=root
DB_PASSWORD=
DB_NAME=levelup

# Leave SQS_QUEUE_URL empty or commented so SQS is disabled locally
# SQS_QUEUE_URL=...
```

Or create a separate local env file and symlink:

```bash
cp .env .env.aws.backup
# Edit .env with the local values above
```

## 4. Run the PHP built-in server

From the project root:

```bash
cd /home/raj/Joystick-Store
php -S localhost:8000
```

Open in browser: **http://localhost:8000**

- Shop: http://localhost:8000/shop.php  
- Customer login: http://localhost:8000/customer-login.php  
- Admin: http://localhost:8000/Admin/ (or admin-login.php)

## 5. Optional – Composer (only if you need SQS locally)

If you want to test SQS locally you need the AWS SDK:

```bash
cd /home/raj/Joystick-Store
composer install
```

For local testing, leaving `SQS_QUEUE_URL` unset is fine; the app will run without sending messages.

## Troubleshooting

- **Database connection failed**  
  - MariaDB must be running: `sudo systemctl status mariadb`  
  - `.env` must have `DB_HOST=127.0.0.1` (and correct user/password) for local DB.

- **Access denied for user 'levelup'@'localhost' (using password: YES)**  
  When `DB_HOST=127.0.0.1`, MariaDB treats the connection as coming from host `127.0.0.1`, not `localhost`. Create the user for **both** hosts:

  ```bash
  sudo mariadb -e "
  CREATE USER IF NOT EXISTS 'levelup'@'localhost' IDENTIFIED BY 'localdev';
  CREATE USER IF NOT EXISTS 'levelup'@'127.0.0.1' IDENTIFIED BY 'localdev';
  GRANT ALL PRIVILEGES ON levelup.* TO 'levelup'@'localhost';
  GRANT ALL PRIVILEGES ON levelup.* TO 'levelup'@'127.0.0.1';
  FLUSH PRIVILEGES;
  "
  ```

- **Page blank or 500**  
  - Run from terminal to see errors: `php -S localhost:8000` and check the console.  
  - Or enable errors in PHP: create `index.php` wrapper or set `php -d display_errors=1 -S localhost:8000`.

- **Session / login issues**  
  - Use the same host (e.g. always http://localhost:8000) so cookies work.
