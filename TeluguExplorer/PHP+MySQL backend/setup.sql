-- ═══════════════════════════════════════════════════════════
--  Mana Telugu Pune — Database Setup
--  HOW TO RUN:
--  1. hPanel → Databases → phpMyAdmin
--  2. Select your database from the left panel
--  3. Click "SQL" tab → paste this entire file → click "Go"
-- ═══════════════════════════════════════════════════════════

-- ── USERS TABLE ──────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `users` (
  `id`               INT          AUTO_INCREMENT PRIMARY KEY,
  `name`             VARCHAR(100) NOT NULL,
  `email`            VARCHAR(150) NOT NULL UNIQUE,
  `mobile`           VARCHAR(15),
  `password_hash`    VARCHAR(255) NOT NULL,
  `is_admin`         TINYINT(1)   NOT NULL DEFAULT 0,
  `is_paid`          TINYINT(1)   NOT NULL DEFAULT 0,
  `paid_at`          DATETIME     NULL,
  `payment_ref`      VARCHAR(100) NULL,
  `payment_method`   VARCHAR(20)  NULL,
  `created_at`       DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── PLACES TABLE ─────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `places` (
  `id`          INT            AUTO_INCREMENT PRIMARY KEY,
  `place_code`  VARCHAR(20)    NOT NULL,
  `name`        VARCHAR(200)   NOT NULL,
  `category`    VARCHAR(50),
  `subcat`      VARCHAR(50),
  `location`    VARCHAR(200),
  `district`    VARCHAR(50),
  `description` TEXT,
  `rating`      DECIMAL(3,1)   NOT NULL DEFAULT 0.0,
  `reviews`     INT            NOT NULL DEFAULT 0,
  `distance`    VARCHAR(50),
  `best_time`   VARCHAR(60),
  `img`         TEXT,
  `status`      VARCHAR(20)    NOT NULL DEFAULT 'Active',
  `created_at`  DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── SEED DEFAULT PLACES ───────────────────────────────────
INSERT INTO `places`
  (`place_code`, `name`, `category`, `subcat`, `location`, `district`, `description`, `rating`, `reviews`, `distance`, `best_time`, `img`, `status`)
VALUES
(
  'PLC-001',
  'Shreemant Dagdusheth Halwai Ganpati',
  'Devotional', 'Temple',
  'Budhwar Peth, Pune', 'Pune',
  'One of the most revered Ganesh temples in India, adorned with a gold-plated idol and visited by millions during Ganesh Chaturthi. The trust also runs multiple charitable initiatives across Pune.',
  4.8, 1200, 'City Center', 'Year Round',
  'https://images.unsplash.com/photo-1621996659490-3275b4d0d951?w=700&q=80',
  'Active'
),
(
  'PLC-002',
  'Shaniwar Wada',
  'Historical', 'Fort',
  'Shaniwar Peth, Pune', 'Pune',
  'A magnificent 17th-century fortification that served as the seat of the Peshwa rulers of the Maratha Empire. Famous for its grand gates, fountains and the legendary ghost story of Narayanrao.',
  4.6, 980, 'City Center', 'Oct – Mar',
  'https://images.unsplash.com/photo-1544551763-46a013bb70d5?w=700&q=80',
  'Active'
),
(
  'PLC-003',
  'Lonavala Hill Station',
  'Hill Station', 'Hill Station',
  'Lonavala', 'Pune',
  'A beloved hill station just 65 km from Pune, famous for Bhushi Dam, Rajmachi Fort, Tiger Point, Imagica theme park and the iconic chikki sweet. A perfect monsoon escape.',
  4.5, 860, '65 km', 'Jun – Sep',
  'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=700&q=80',
  'Active'
),
(
  'PLC-004',
  'Sinhagad Fort Trek',
  'Trekking', 'Trek',
  'Sinhagad, Pune', 'Pune',
  'An iconic Maratha fort and popular trekking destination, famous for the heroic Battle of Sinhagad by Tanaji Malusare in 1670. Enjoy the sunrise trek and authentic local food at the top.',
  4.7, 1500, '30 km', 'Oct – Feb',
  'https://images.unsplash.com/photo-1592496001020-d31bd830651f?w=700&q=80',
  'Active'
),
(
  'PLC-005',
  'Ganpatipule Beach',
  'Beach', 'Beach',
  'Ganpatipule, Ratnagiri', 'Ratnagiri',
  'One of Maharashtra''s cleanest beaches with the famous Swayambhu Ganesh temple facing the Arabian Sea. Perfect blend of spirituality and natural beauty. Ideal for family trips.',
  4.6, 720, '330 km', 'Nov – Feb',
  'https://images.unsplash.com/photo-1536768139911-e290a59011e4?w=700&q=80',
  'Active'
),
(
  'PLC-006',
  'Pawna Lake Camping',
  'Camping', 'Camping',
  'Pawna, Pune', 'Pune',
  'An unforgettable overnight tent camping experience by the stunning Pawna Lake, surrounded by the silhouettes of Lohagad, Tikona and Tung forts. Best campfire destination near Pune.',
  4.8, 940, '50 km', 'Oct – Mar',
  'https://images.unsplash.com/photo-1518709268805-4e9042af9f23?w=700&q=80',
  'Active'
),
(
  'PLC-007',
  'Aga Khan Palace',
  'Historical', 'Museum',
  'Nagar Road, Pune', 'Pune',
  'A stately palace-turned-memorial where Mahatma Gandhi, Kasturba Gandhi and Mahadev Desai were held during the Quit India Movement in 1942. Now a revered museum preserving their memories.',
  4.5, 650, '5 km', 'Year Round',
  'https://images.unsplash.com/photo-1553913861-c0fddf2619ee?w=700&q=80',
  'Active'
),
(
  'PLC-008',
  'Mahabaleshwar',
  'Hill Station', 'Hill Station',
  'Mahabaleshwar', 'Satara',
  'Maharashtra''s most popular hill station known for its famous strawberries, Venna Lake boating, Arthur Seat viewpoint, Wilson Point and dense forests. A paradise for nature lovers.',
  4.7, 1100, '120 km', 'Mar – Jun',
  'https://images.unsplash.com/photo-1441974231531-c6227db76b6e?w=700&q=80',
  'Active'
),
(
  'PLC-009',
  'Rajiv Gandhi Zoological Park',
  'Wildlife', 'Zoo & Wildlife',
  'Katraj, Pune', 'Pune',
  'A well-maintained zoo in Katraj housing tigers, lions, leopards, crocodiles, anacondas and exotic birds. Also features a dedicated snake park and deer park. Great outing for families with children.',
  4.3, 820, '10 km', 'Year Round',
  'https://images.unsplash.com/photo-1534567110243-8875d62b3b8d?w=700&q=80',
  'Active'
);

-- ── VERIFY ───────────────────────────────────────────────
SELECT 'Tables created and seeded successfully!' AS status;
SELECT COUNT(*) AS total_places FROM places;
