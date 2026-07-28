-- ============================================================
--  Arunella Database — Dummy Data
--  Database: arunella (MySQL)
--  Generated for: CSC 313 Service Oriented Computing Project
--  Insert order respects FK constraints:
--    admin → farmer / buyer / transporter → crop → `order` → has → delivery
-- ============================================================

USE arunella;

-- ─────────────────────────────────────────────
--  1. ADMIN
-- ─────────────────────────────────────────────
INSERT INTO admin (admin_id, name, email, password) VALUES
(1, 'Saman Perera',   'saman@arunella.lk',   'admin@123'),
(2, 'Nirosha Fernando','nirosha@arunella.lk', 'admin@456');


-- ─────────────────────────────────────────────
--  2. FARMER
-- ─────────────────────────────────────────────
INSERT INTO farmer (user_id, admin_id, role, name, email, password, nic, contact_no, district, rating, location, wallet, bank_account_no) VALUES
(1,  1, 'FARMER', 'Kamal Bandara',     'kamal@gmail.com',    'pass1234', '198812345678', '0771234567', 'Kandy',        4.5, 'Peradeniya, Kandy',    12500.00, '7890123456789'),
(2,  1, 'FARMER', 'Sunil Rathnayake',  'sunil@gmail.com',    'pass1234', '197523456789', '0712345678', 'Matale',       4.2, 'Ukuwela, Matale',       8750.00, '1234567890123'),
(3,  1, 'FARMER', 'Priyantha Herath',  'priyantha@gmail.com','pass1234', '199034567890', '0723456789', 'Nuwara Eliya', 4.8, 'Nuwara Eliya Town',    22000.00, '2345678901234'),
(4,  2, 'FARMER', 'Chaminda Jayasena', 'chaminda@gmail.com', 'pass1234', '198645678901', '0734567890', 'Badulla',      3.9, 'Bandarawela, Badulla',  5300.00, '3456789012345'),
(5,  2, 'FARMER', 'Dilrukshi Wijesinghe','dilrukshi@gmail.com','pass1234','200056789012','0745678901', 'Kurunegala',   4.6, 'Narammala, Kurunegala',17800.00, '4567890123456'),
(6,  2, 'FARMER', 'Roshan Kumara',     'roshan@gmail.com',   'pass1234', '199167890123', '0756789012', 'Polonnaruwa',  4.1, 'Minneriya, Polonnaruwa',9600.00, '5678901234567');


-- ─────────────────────────────────────────────
--  3. BUYER
-- ─────────────────────────────────────────────
INSERT INTO buyer (user_id, admin_id, name, email, password, nic, contact_no, district, rating, role, business_reg_no, market_location) VALUES
(1, 1, 'Nimal Gunawardena',  'nimal@supermart.lk',  'buypass1', '197812345679', '0761234567', 'Colombo',    4.7, 'BUYER', 'BR-2018-COL-0042', 'Colombo 07 Market'),
(2, 1, 'Sithara Dissanayake','sithara@freshveg.lk', 'buypass2', '198923456780', '0772345678', 'Gampaha',    4.3, 'BUYER', 'BR-2019-GAM-0118', 'Kiribathgoda Market'),
(3, 1, 'Mahesh Gunasekara',  'mahesh@organicco.lk', 'buypass3', '199034567891', '0783456789', 'Kalutara',   4.5, 'BUYER', 'BR-2020-KAL-0203', 'Panadura Market'),
(4, 2, 'Anoma Wickramasinghe','anoma@bestbuys.lk',  'buypass4', '197645678902', '0794567890', 'Matara',     4.0, 'BUYER', 'BR-2017-MAT-0055', 'Matara Main Market'),
(5, 2, 'Thilina Ranawaka',   'thilina@agrotrade.lk','buypass5', '200056789013', '0705678901', 'Ratnapura',  4.6, 'BUYER', 'BR-2021-RAT-0311', 'Ratnapura Pola');


-- ─────────────────────────────────────────────
--  4. TRANSPORTER
-- ─────────────────────────────────────────────
INSERT INTO transporter (user_id, admin_id, name, email, password, nic, contact_no, district, rating, role, vehicle_plate_no, max_capacity) VALUES
(1, 1, 'Asanka Liyanage',   'asanka@transport.lk',  'trans123', '198834567890', '0768901234', 'Colombo',      4.5, 'TRANSPORTER', 'CAB-1234', 3000.0),
(2, 1, 'Lakmal Senanayake', 'lakmal@transport.lk',  'trans123', '197945678901', '0779012345', 'Kandy',        4.2, 'TRANSPORTER', 'WP-5678',  5000.0),
(3, 2, 'Buddhika Madhushanka','budhi@transport.lk', 'trans123', '199056789012', '0720123456', 'Gampaha',      4.7, 'TRANSPORTER', 'NW-9012',  2000.0),
(4, 2, 'Sampath Jayawardena','sampath@transport.lk','trans123', '198867890123', '0731234567', 'Ratnapura',    3.9, 'TRANSPORTER', 'SB-3456',  8000.0);


-- ─────────────────────────────────────────────
--  5. CROP  (image left NULL — binary data)
-- ─────────────────────────────────────────────
INSERT INTO crop (product_id, product_name, user_id, price_per_kg, stock, status, uploaded_date, exp_date, min_price, description, image) VALUES
( 1, 'Tomato',        1, 120.00,  500, 'AVAILABLE',     '2026-07-01', '2026-07-20', 100.00, 'Fresh red tomatoes from Kandy highlands. Pesticide-free.',        NULL),
( 2, 'Carrot',        3, 85.00,   300, 'AVAILABLE',     '2026-07-05', '2026-08-05', 70.00,  'Organic Nuwara Eliya carrots, Grade A quality.',                  NULL),
( 3, 'Leeks',         3, 95.00,   200, 'AVAILABLE',     '2026-07-08', '2026-07-25', 80.00,  'Highland leeks, freshly harvested from Nuwara Eliya.',            NULL),
( 4, 'Beans',         2, 75.00,   400, 'AVAILABLE',     '2026-07-10', '2026-07-28', 60.00,  'Green beans from Matale, ideal for export quality.',              NULL),
( 5, 'Cabbage',       1, 55.00,   150, 'AVAILABLE',     '2026-07-12', '2026-08-12', 45.00,  'Large heads of cabbage, harvested from Peradeniya farms.',        NULL),
( 6, 'Capsicum',      3, 180.00,  100, 'AVAILABLE',     '2026-07-14', '2026-07-30', 150.00, 'Red, yellow, and green bell peppers from NE district.',           NULL),
( 7, 'Bitter Gourd',  5, 65.00,   250, 'AVAILABLE',     '2026-07-15', '2026-07-29', 55.00,  'Farm-fresh bitter gourd from Kurunegala.',                        NULL),
( 8, 'Pumpkin',       6, 45.00,   600, 'AVAILABLE',     '2026-07-16', '2026-09-01', 35.00,  'Large pumpkins from Minneriya, long shelf life.',                 NULL),
( 9, 'Brinjal',       4, 70.00,   320, 'SOLD',          '2026-06-20', '2026-07-10', 60.00,  'Purple brinjals from Bandarawela.',                               NULL),
(10, 'Potato',        3, 110.00, 1000, 'AVAILABLE',     '2026-07-18', '2026-09-18', 90.00,  'High-altitude Nuwara Eliya potatoes, low moisture.',              NULL),
(11, 'Radish',        2, 40.00,   180, 'AVAILABLE',     '2026-07-20', '2026-07-31', 30.00,  'White radish, suitable for pickling and cooking.',                NULL),
(12, 'Ginger',        5, 250.00,  80,  'AVAILABLE',     '2026-07-10', '2026-10-10', 220.00, 'Organic ginger roots from Kurunegala, strong aroma.',            NULL),
(13, 'Garlic',        6, 300.00,  60,  'LOW_STOCK',     '2026-07-05', '2026-10-05', 270.00, 'Dry garlic bulbs from Polonnaruwa, excellent flavour.',          NULL),
(14, 'Okra',          4, 80.00,   220, 'AVAILABLE',     '2026-07-21', '2026-08-04', 65.00,  'Tender lady fingers from Badulla, harvested twice weekly.',      NULL),
(15, 'Mushroom',      1, 350.00,  50,  'LOW_STOCK',     '2026-07-25', '2026-08-01', 300.00, 'Oyster mushrooms grown in controlled environment, Kandy.',       NULL);


-- ─────────────────────────────────────────────
--  6. ORDER  (backtick-quoted reserved word)
-- ─────────────────────────────────────────────
INSERT INTO `order` (order_id, user_id, price, quantity, date, status) VALUES
( 1, 1,  6000.00, 50,  '2026-07-10', 'COMPLETED'),
( 2, 2,  2550.00, 30,  '2026-07-11', 'COMPLETED'),
( 3, 3, 11000.00, 100, '2026-07-12', 'PROCESSING'),
( 4, 4,  3200.00, 40,  '2026-07-13', 'COMPLETED'),
( 5, 1,  1800.00, 20,  '2026-07-14', 'CANCELLED'),
( 6, 5,  8750.00, 125, '2026-07-15', 'PROCESSING'),
( 7, 2,  5500.00, 60,  '2026-07-16', 'SHIPPED'),
( 8, 3,  4400.00, 40,  '2026-07-17', 'PROCESSING'),
( 9, 4,  9000.00, 80,  '2026-07-18', 'SHIPPED'),
(10, 5,  2100.00, 25,  '2026-07-20', 'PROCESSING');


-- ─────────────────────────────────────────────
--  7. HAS  (order ↔ crop link table)
-- ─────────────────────────────────────────────
INSERT INTO has (order_id, product_id) VALUES
-- Order 1 : Tomato + Capsicum
(1,  1),
(1,  6),
-- Order 2 : Carrot + Beans
(2,  2),
(2,  4),
-- Order 3 : Potato + Leeks + Cabbage
(3,  10),
(3,  3),
(3,  5),
-- Order 4 : Bitter Gourd + Okra
(4,  7),
(4, 14),
-- Order 5 : Mushroom
(5, 15),
-- Order 6 : Carrot + Beans + Radish
(6,  2),
(6,  4),
(6, 11),
-- Order 7 : Ginger + Garlic
(7, 12),
(7, 13),
-- Order 8 : Pumpkin + Brinjal
(8,  8),
(8,  9),
-- Order 9 : Tomato + Potato + Leeks
(9,  1),
(9, 10),
(9,  3),
-- Order 10 : Capsicum + Okra
(10,  6),
(10, 14);


-- ─────────────────────────────────────────────
--  8. DELIVERY  (confirmation_img left NULL)
-- ─────────────────────────────────────────────
INSERT INTO delivery (delivery_id, user_id, order_id, pickup_location, delivery_location, status, confirmation_img, date) VALUES
(1, 1, 1, 'Peradeniya, Kandy',       'Colombo 07 Market',       'DELIVERED',   NULL, '2026-07-12'),
(2, 2, 2, 'Kiribathgoda, Gampaha',   'Kiribathgoda Market',     'DELIVERED',   NULL, '2026-07-13'),
(3, 3, 3, 'Nuwara Eliya Town',       'Panadura Market',         'IN_TRANSIT',  NULL, '2026-07-14'),
(4, 4, 4, 'Bandarawela, Badulla',    'Matara Main Market',      'DELIVERED',   NULL, '2026-07-15'),
(5, 1, 6, 'Narammala, Kurunegala',   'Ratnapura Pola',          'IN_TRANSIT',  NULL, '2026-07-16'),
(6, 2, 7, 'Ukuwela, Matale',         'Kiribathgoda Market',     'SHIPPED',     NULL, '2026-07-17'),
(7, 3, 8, 'Nuwara Eliya Town',       'Panadura Market',         'PENDING',     NULL, '2026-07-18'),
(8, 4, 9, 'Minneriya, Polonnaruwa',  'Matara Main Market',      'SHIPPED',     NULL, '2026-07-19');

-- ============================================================
--  END OF DUMMY DATA
-- ============================================================
