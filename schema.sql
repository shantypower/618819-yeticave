CREATE DATABASE yeticave
DEFAULT CHARACTER SET utf8
DEFAULT COLLATE utf8_general_ci;
USE yeticave;
CREATE TABLE caregories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  cat_name CHAR(30) UNIQUE
);
CREATE TABLE lot (
  id INT AUTO_INCREMENT PRIMARY KEY,
  date_add DATETIME,
  descr CHAR(200),
  img_src CHAR(300),
  start_price INT,
  date_end DATETIME,
  price_step int,
  author_id INT UNIQUE,
  vinner_id INT UNIQUE,
  cat_id INT
);
CREATE TABLE lot_rate (
  id INT AUTO_INCREMENT PRIMARY KEY,
  date_add DATETIME,
  rate INT,
  user_id INT UNIQUE,
  lot_id INT UNIQUE
);
CREATE TABLE user (
  id INT AUTO_INCREMENT PRIMARY KEY,
  reg_date DATETIME,
  email CHAR(128) NOT NULL UNIQUE,
  user_name CHAR(300) NOT NULL UNIQUE,
  user_pass CHAR(200) NOT NULL,
  avatar_src CHAR(300),
  contacts CHAR,
  lot_id INT UNIQUE,
  rate_id INT
);



