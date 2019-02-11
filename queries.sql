/*ЗАПРОСЫ ДЛЯ ДОБАВЛЕНИЯ информации в БД*/

/*Добавляем список категорий*/
INSERT INTO categories
(cat_name, css_cl) VALUES ('Доски и лыжи', 'promo__item--boards');
INSERT INTO categories
(cat_name, css_cl) VALUES ('Крепления', 'promo__item--attachment');
INSERT INTO categories
(cat_name, css_cl) VALUES ('Ботинки', 'promo__item--boots');
INSERT INTO categories
(cat_name, css_cl) VALUES ('Одежда', 'promo__item--clothing');
INSERT INTO categories
(cat_name, css_cl) VALUES ('Инструменты', 'promo__item--tools');
INSERT INTO categories
(cat_name, css_cl) VALUES ('Разное', 'promo__item--other');

/*Добавляем список пользователей*/

INSERT INTO users
(reg_date, email, user_name, user_pass, avatar_src, contacts)
VALUES ('2018-06-04 22:30:00', 'Vasya', 'vasya@mail.ru', '123456789', 'http://andrey-eltsov.ru/wp-content/uploads/2017/09/DsSd-Stim_hfhdY_jf-jfY-%D0%A1%D1%82%D0%B8%D0%BC-%D0%B4%D0%BB%D1%8F-%D0%BF%D0%B0%D1%86%D0%B0%D0%BD%D0%BE%D0%B2.jpg', '8-924-888-8855');
INSERT INTO users
(reg_date, email, user_name, user_pass, avatar_src, contacts)
VALUES ('2019-01-04 21:00:00', 'Petya', 'petya@mail.ru', '987654321', 'http://andrey-eltsov.ru/wp-content/uploads/2017/09/DopNaAvu1-300x270.jpg', '9-999-111-0000');
INSERT INTO users
(reg_date, email, user_name, user_pass, avatar_src, contacts)
VALUES ('2018-08-20 22:44:00', 'Gosha', 'gosha@mail.ru', 'qwerty', 'https://klyker.com/wp-content/uploads/2013/04/1910.jpg', '8-924-555-3333');
/*Добавляем список объявлений*/
INSERT INTO lots
(date_add, lot_name, descr, img_src, start_price, date_end, price_step, author_id, cat_id)
VALUES ('2019-01-20 21:00:00', '2014 Rossignol District Snowboard', 'Немного б/у', 'img/lot-1.jpg', '10999', '2019-01-30 21:00:00', '1000', '1', '1');
INSERT INTO lots
(date_add, lot_name, descr, img_src, start_price, date_end, price_step, author_id, cat_id)
VALUES ('2019-01-21 21:00:00', 'DC Ply Mens 2016/2017 Snowboard', 'Графитовый скользяк, кант не сточен', 'img/lot-2.jpg', '159999', '2019-02-26 21:00:00', '1000', '2', '1');
INSERT INTO lots
(date_add, lot_name, descr, img_src, start_price, date_end, price_step, author_id, cat_id)
VALUES ('2019-01-22 21:00:00', 'Крепления Union Contact Pro 2015 года размер L/XL', 'Стрепы работают отлично', 'img/lot-3.jpg', '8000', '2019-02-18 21:00:00', '500', '2', '2');
INSERT INTO lots
(date_add, lot_name, descr, img_src, start_price, date_end, price_step, author_id, cat_id)
VALUES ('2019-01-23 21:00:00', 'Ботинки для сноуборда DC Mutiny Charocal', 'Новые', 'img/lot-4.jpg', '10999', '2019-01-25 21:00:00', '10999', '1', '3');
INSERT INTO lots
(date_add, lot_name, descr, img_src, start_price, date_end, price_step, author_id, cat_id)
VALUES ('2019-02-02 21:00:00', 'Куртка для сноуборда DC Mutiny Charocal', 'В идеальном состоянии', 'img/lot-5.jpg', '7500', '2019-02-10 21:00:00', '800', '1', '4');
INSERT INTO lots
(date_add, lot_name, descr, img_src, start_price, date_end, price_step, author_id, cat_id)
VALUES ('2019-01-25 21:00:00', 'Маска Oakley Canopy', 'Есть царапины, не мешают обзору', 'img/lot-6.jpg', '5400', '2019-02-04 21:00:00', '500', '3', '6');
/*Добаляем ставки для объявления*/
INSERT INTO lot_rates
(date_add, rate, user_id, lot_id)
VALUES ('2019-01-26 10:00:00', '6000', '1', '6');
INSERT INTO lot_rates
(date_add, rate, user_id, lot_id)
VALUES ('2019-01-26 15:00:00', '6500', '2', '6');
/*ЗАПРОСЫ ДЛЯ ВЫБОРКИ из БД*/

/*получить все категории*/
SELECT * FROM categories;

/*получить самые новые, открытые лоты. Каждый лот должен включать название, стартовую цену, ссылку на изображение, цену, название категории*/
SELECT lot_name, start_price, img_src, cat_id FROM lots WHERE date_end > CURRENT_DATE() ORDER BY date_add DESC LIMIT 3;
/*показать лот по его id. Получите также название категории, к которой принадлежит лот*/
SELECT lots.*, categories.cat_name FROM lots JOIN categories ON lots.cat_id = categories.id WHERE lots.id = 3;
/*обновить название лота по его идентификатору*/
UPDATE lots SET lot_name = 'Брюки для сноуборда RIDE Charocal/LIME' WHERE id = 5;
/*получить список самых свежих ставок для лота по его идентификатору*/
SELECT * FROM lot_rates WHERE date_add < CURRENT_DATE() AND lot_id = 6 ORDER BY date_add DESC;

