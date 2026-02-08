-- Run from project root: sudo mariadb < fix-levelup-user.sql
-- Fixes "Access denied for user 'levelup'@'localhost'"

DROP USER IF EXISTS 'levelup'@'localhost';
DROP USER IF EXISTS 'levelup'@'127.0.0.1';

CREATE USER 'levelup'@'localhost' IDENTIFIED BY 'localdev';
CREATE USER 'levelup'@'127.0.0.1' IDENTIFIED BY 'localdev';

GRANT ALL PRIVILEGES ON levelup.* TO 'levelup'@'localhost';
GRANT ALL PRIVILEGES ON levelup.* TO 'levelup'@'127.0.0.1';
FLUSH PRIVILEGES;

SELECT User, Host FROM mysql.user WHERE User = 'levelup';
