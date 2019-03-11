CREATE DATABASE yeticave
DEFAULT CHARACTER SET utf8
DEFAULT COLLATE utf8_general_ci;
USE yeticave;
CREATE TABLE categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  cat_name CHAR(50) NOT NULL UNIQUE,
  css_cl CHAR(50)
);
CREATE TABLE lots (
  id INT AUTO_INCREMENT PRIMARY KEY,
  date_add TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  lot_name CHAR(150) NOT NULL,
  descr CHAR(200) NOT NULL,
  img_src TEXT NOT NULL,
  start_price INT NOT NULL,
  date_end TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  price_step INT NOT NULL,
  author_id INT,
  winner_id INT,
  cat_id INT,
  FULLTEXT KEY lot_search (lot_name, descr)
);
CREATE TABLE lot_rates (
  id INT AUTO_INCREMENT PRIMARY KEY,
  date_add TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  rate INT NOT NULL,
  user_id INT,
  lot_id INT,
  FOREIGN KEY (lot_id) REFERENCES lots(id)
);
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  reg_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  email CHAR(128) NOT NULL UNIQUE,
  user_name CHAR(200) NOT NULL,
  user_pass CHAR(200) NOT NULL,
  avatar_src TEXT,
  contacts CHAR(250)
);
ALTER TABLE lots
ADD FOREIGN KEY (author_id) REFERENCES users(id);
ALTER TABLE lots
ADD FOREIGN KEY (winner_id) REFERENCES users(id);
ALTER TABLE lots
ADD FOREIGN KEY (cat_id) REFERENCES categories(id);
ALTER TABLE lot_rates
ADD FOREIGN KEY (user_id) REFERENCES users(id);
ALTER TABLE lot_rates
ADD FOREIGN KEY (lot_id) REFERENCES lots(id);
CREATE INDEX winner_lot ON lots (date_end, winner_id);
