CREATE DATABASE yeticave
DEFAULT CHARACTER SET utf8
DEFAULT COLLATE utf8_general_ci;
USE yeticave;
CREATE TABLE caregories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  cat_name CHAR(50) NOT NULL UNIQUE
);
CREATE TABLE lots (
  id INT AUTO_INCREMENT PRIMARY KEY,
  date_add TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  lot_name CHAR(150) NOT NULL,
  descr CHAR(200) NOT NULL,
  img_src TEXT NOT NULL,
  start_price INT NOT NULL,
  date_end TIMESTAMP NOT NULL,
  price_step INT NOT NULL,
  author_id INT UNIQUE,
  vinner_id INT UNIQUE,
  cat_id INT NOT NULL
);
CREATE TABLE lot_rates (
  id INT AUTO_INCREMENT PRIMARY KEY,
  date_add TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  rate INT NOT NULL,
  user_id INT UNIQUE,
  lot_id INT UNIQUE
);
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  reg_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  email CHAR(128) NOT NULL UNIQUE,
  user_name CHAR(200) NOT NULL UNIQUE,
  user_pass CHAR(200) NOT NULL,
  avatar_src TEXT,
  contacts CHAR,
  lot_id INT UNIQUE,
  rate_id INT
);
CREATE INDEX lot_n ON lots(lot_name);
CREATE INDEX user_n ON users(user_name);



