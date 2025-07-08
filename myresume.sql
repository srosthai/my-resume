/*
 Navicat Premium Data Transfer

 Source Server         : Localhost 3336
 Source Server Type    : MySQL
 Source Server Version : 80030
 Source Host           : localhost:3336
 Source Schema         : myresume

 Target Server Type    : MySQL
 Target Server Version : 80030
 File Encoding         : 65001

 Date: 08/07/2025 14:34:20
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for about_me
-- ----------------------------
DROP TABLE IF EXISTS `about_me`;
CREATE TABLE `about_me`  (
  `id` bigint(0) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `location` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `year_experience` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `fucus_on` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of about_me
-- ----------------------------
INSERT INTO `about_me` VALUES (1, 'About Me', 'Hi, I\'m SROS THAI, a passionate software developer with a focus on backend development using Laravel and PHP. I have a strong foundation in fullstack development, data structures, algorithms, and software engineering principles.\n\nMy goal is to create efficient and scalable applications that solve real-world problems. I am currently pursuing a Bachelor of Information Technology at BBU (Build Bright University) and have gained valuable experience working as a Backend Laravel Developer at EOT (eOcambo Technology).\n\nIn my free time, I enjoy exploring new technologies, contributing to open-source projects, and enhancing my skills in web development. I am always eager to learn and grow as a developer, and I believe in the power of collaboration and sharing knowledge with others.', 'Siem Reap, Cambodia', '2+ Years', 'Backend Development', '2025-07-08 03:46:40', '2025-07-08 03:46:40');

-- ----------------------------
-- Table structure for cache
-- ----------------------------
DROP TABLE IF EXISTS `cache`;
CREATE TABLE `cache`  (
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int(0) NOT NULL,
  PRIMARY KEY (`key`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of cache
-- ----------------------------
INSERT INTO `cache` VALUES ('laravel_cache_admin@gmail.com|127.0.0.1', 'i:1;', 1751868309);
INSERT INTO `cache` VALUES ('laravel_cache_admin@gmail.com|127.0.0.1:timer', 'i:1751868309;', 1751868309);

-- ----------------------------
-- Table structure for cache_locks
-- ----------------------------
DROP TABLE IF EXISTS `cache_locks`;
CREATE TABLE `cache_locks`  (
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int(0) NOT NULL,
  PRIMARY KEY (`key`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for educations
-- ----------------------------
DROP TABLE IF EXISTS `educations`;
CREATE TABLE `educations`  (
  `id` bigint(0) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `major` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `institution` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `from` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `to` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of educations
-- ----------------------------
INSERT INTO `educations` VALUES (1, 'Bachelor of Information Technology', 'Information Technology', 'BBU (Build Bright University)', 'Studying advanced concepts in Information Technology and software development. Focused on fullstack development, data structures, algorithms, and software engineering principles.', '2021', '2025', '2025-07-08 04:12:15', '2025-07-08 04:12:15');
INSERT INTO `educations` VALUES (2, 'High School Diploma', 'High School Diploma', 'KDHS (Kouk Doung High School)', 'Studied at High School for Specialized Studies, Siem Reap, Cambodia.', '2018', '2021', '2025-07-08 04:13:10', '2025-07-08 04:13:10');

-- ----------------------------
-- Table structure for failed_jobs
-- ----------------------------
DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE `failed_jobs`  (
  `id` bigint(0) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp(0) NOT NULL DEFAULT CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `failed_jobs_uuid_unique`(`uuid`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for job_batches
-- ----------------------------
DROP TABLE IF EXISTS `job_batches`;
CREATE TABLE `job_batches`  (
  `id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int(0) NOT NULL,
  `pending_jobs` int(0) NOT NULL,
  `failed_jobs` int(0) NOT NULL,
  `failed_job_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `cancelled_at` int(0) NULL DEFAULT NULL,
  `created_at` int(0) NOT NULL,
  `finished_at` int(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for jobs
-- ----------------------------
DROP TABLE IF EXISTS `jobs`;
CREATE TABLE `jobs`  (
  `id` bigint(0) UNSIGNED NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint(0) UNSIGNED NOT NULL,
  `reserved_at` int(0) UNSIGNED NULL DEFAULT NULL,
  `available_at` int(0) UNSIGNED NOT NULL,
  `created_at` int(0) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `jobs_queue_index`(`queue`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for migrations
-- ----------------------------
DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations`  (
  `id` int(0) UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(0) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 9 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of migrations
-- ----------------------------
INSERT INTO `migrations` VALUES (1, '0001_01_01_000000_create_users_table', 1);
INSERT INTO `migrations` VALUES (2, '0001_01_01_000001_create_cache_table', 1);
INSERT INTO `migrations` VALUES (3, '0001_01_01_000002_create_jobs_table', 1);
INSERT INTO `migrations` VALUES (4, '2025_07_04_125803_add_field_to_users_table', 2);
INSERT INTO `migrations` VALUES (5, '2025_07_08_024319_create_about_me_table', 3);
INSERT INTO `migrations` VALUES (6, '2025_07_08_024642_create_work_experiences_table', 4);
INSERT INTO `migrations` VALUES (7, '2025_07_08_024902_create_educations_table', 5);
INSERT INTO `migrations` VALUES (8, '2025_07_08_025125_create_tech_stacks_table', 6);
INSERT INTO `migrations` VALUES (9, '2025_07_08_051611_create_project_types_table', 7);
INSERT INTO `migrations` VALUES (10, '2025_07_08_051651_create_projects_table', 8);

-- ----------------------------
-- Table structure for password_reset_tokens
-- ----------------------------
DROP TABLE IF EXISTS `password_reset_tokens`;
CREATE TABLE `password_reset_tokens`  (
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`email`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for project_types
-- ----------------------------
DROP TABLE IF EXISTS `project_types`;
CREATE TABLE `project_types`  (
  `id` bigint(0) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of project_types
-- ----------------------------
INSERT INTO `project_types` VALUES (1, 'Frontend', '2025-07-08 05:38:35', '2025-07-08 05:38:35');
INSERT INTO `project_types` VALUES (2, 'Backend', '2025-07-08 05:38:41', '2025-07-08 05:38:41');
INSERT INTO `project_types` VALUES (3, 'Full Stack', '2025-07-08 06:35:56', '2025-07-08 06:35:56');

-- ----------------------------
-- Table structure for projects
-- ----------------------------
DROP TABLE IF EXISTS `projects`;
CREATE TABLE `projects`  (
  `id` bigint(0) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `project_type_id` bigint(0) UNSIGNED NULL DEFAULT NULL,
  `technologies` json NULL,
  `created_date` date NULL DEFAULT NULL,
  `status` enum('processing','completed') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'processing',
  `links` json NULL,
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of projects
-- ----------------------------
INSERT INTO `projects` VALUES (1, 'Der Tam ME', 'Discover Cambodia\'s ancient temples, pristine beaches, lush jungles, and vibrant culture. From the majestic Angkor Wat to hidden natural wonders, embark on an unforgettable journey.', 1, '[\"Next.js\", \"Tailwind\"]', '2025-07-08', 'completed', '[{\"Github\": \"https://github.com/Sovannthai/der-tamme\"}, {\"View\": \"https://der-tamme.vercel.app\"}]', '2025-07-08 05:58:29', '2025-07-08 07:18:55', 'uploads/projects/686cc32cb1f47_1751958316.jpg');
INSERT INTO `projects` VALUES (2, 'Online Image Compression', 'Compress JPG, PNG, WebP and GIF images while maintaining quality. Reduce file size by up to 90% for faster websites.', 1, '[\"HTML\", \"CSS\", \"JavaScript\", \"Jquery Libray Compression Tool\"]', '2025-07-08', 'completed', '[{\"Github\": \"https://images-compressor.vercel.app/\"}, {\"View\": \"https://github.com/Sovannthai/image-compressor\"}]', '2025-07-08 07:09:05', '2025-07-08 07:18:15', 'uploads/projects/686cc411852ed_1751958545.jpg');

-- ----------------------------
-- Table structure for sessions
-- ----------------------------
DROP TABLE IF EXISTS `sessions`;
CREATE TABLE `sessions`  (
  `id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint(0) UNSIGNED NULL DEFAULT NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int(0) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `sessions_user_id_index`(`user_id`) USING BTREE,
  INDEX `sessions_last_activity_index`(`last_activity`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of sessions
-- ----------------------------
INSERT INTO `sessions` VALUES ('2hoKB3cs8qiiV60AUGGedKzVivF9HrJBLLPF6OaY', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoib1puNXBKUmtnT1NBZFlvQUJsV1dJeWdzRmpsT2FweDZSM3p3dlY5dCI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjE7fQ==', 1751959394);

-- ----------------------------
-- Table structure for tech_stacks
-- ----------------------------
DROP TABLE IF EXISTS `tech_stacks`;
CREATE TABLE `tech_stacks`  (
  `id` bigint(0) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `logo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of tech_stacks
-- ----------------------------
INSERT INTO `tech_stacks` VALUES (1, 'HTML', 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/html5/html5-original.svg', 'Frontend', 'Standard markup language for creating web pages and applications.', '2025-07-08 04:26:57', '2025-07-08 04:26:57');
INSERT INTO `tech_stacks` VALUES (2, 'CSS', 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/css3/css3-original.svg', 'Frontend', 'Style sheet language used for describing the presentation of a document written in HTML.', '2025-07-08 04:27:41', '2025-07-08 04:27:41');
INSERT INTO `tech_stacks` VALUES (3, 'JavaScript', 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/javascript/javascript-original.svg', 'Frontend', 'High-level programming language for web development and interactive applications.', '2025-07-08 04:28:04', '2025-07-08 04:28:04');
INSERT INTO `tech_stacks` VALUES (4, 'Tailwind CSS', 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/tailwindcss/tailwindcss-original.svg', 'Frontend', 'Utility-first CSS framework for rapid UI development.', '2025-07-08 04:29:33', '2025-07-08 04:29:33');
INSERT INTO `tech_stacks` VALUES (5, 'Bootstrap', 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/bootstrap/bootstrap-original.svg', 'Frontend', 'Popular CSS framework for building responsive and mobile-first websites.', '2025-07-08 04:30:03', '2025-07-08 04:30:03');
INSERT INTO `tech_stacks` VALUES (6, 'PHP', 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/php/php-original.svg', 'Backend', 'Popular server-side scripting language for web development.', '2025-07-08 04:30:48', '2025-07-08 04:30:48');
INSERT INTO `tech_stacks` VALUES (7, 'Laravel', 'https://cdn.worldvectorlogo.com/logos/laravel-2.svg', 'Backend', 'PHP framework for building web applications with elegant syntax and powerful features.', '2025-07-08 04:31:34', '2025-07-08 04:31:34');
INSERT INTO `tech_stacks` VALUES (8, 'Vue.js', 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/vuejs/vuejs-original.svg', 'Frontend', 'Progressive JavaScript framework for building modern web applications.', '2025-07-08 04:32:12', '2025-07-08 04:32:12');
INSERT INTO `tech_stacks` VALUES (9, 'Next.js', 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/nextjs/nextjs-original.svg', 'Frontend', 'React framework for production with server-side rendering and static site generation.', '2025-07-08 04:32:50', '2025-07-08 04:32:50');
INSERT INTO `tech_stacks` VALUES (10, 'PostgreSQL', 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/postgresql/postgresql-original.svg', 'Database', 'Advanced open-source relational database with powerful features.', '2025-07-08 04:33:24', '2025-07-08 04:33:24');
INSERT INTO `tech_stacks` VALUES (11, 'MySQL', 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/mysql/mysql-original.svg', 'Database', 'Popular open-source relational database management system.', '2025-07-08 04:35:15', '2025-07-08 04:35:15');
INSERT INTO `tech_stacks` VALUES (12, 'Git', 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/git/git-original.svg', 'DevOps & Tools', 'Distributed version control system for tracking changes in source code.', '2025-07-08 04:35:43', '2025-07-08 04:35:43');
INSERT INTO `tech_stacks` VALUES (13, 'Linux (UBT)', 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/linux/linux-original.svg', 'DevOps & Tools', 'Open-source operating system kernel widely used in server environments.', '2025-07-08 04:36:12', '2025-07-08 04:36:12');
INSERT INTO `tech_stacks` VALUES (14, 'Figma', 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/figma/figma-original.svg', 'Design & Media', 'Collaborative design tool for creating user interfaces and prototypes.', '2025-07-08 04:36:38', '2025-07-08 04:36:38');
INSERT INTO `tech_stacks` VALUES (15, 'Canva', 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/canva/canva-original.svg', 'Design & Media', 'User-friendly graphic design tool for creating visual content.', '2025-07-08 04:37:02', '2025-07-08 04:37:02');
INSERT INTO `tech_stacks` VALUES (16, 'Vercel', 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/vercel/vercel-original.svg', 'Server & Hosting', 'Platform for deploying and hosting web applications with serverless functions.', '2025-07-08 04:37:29', '2025-07-08 04:37:29');
INSERT INTO `tech_stacks` VALUES (17, 'DigitalOcean', 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/digitalocean/digitalocean-original.svg', 'Server & Hosting', 'Cloud infrastructure provider offering scalable virtual servers and managed databases.', '2025-07-08 04:38:17', '2025-07-08 04:38:17');
INSERT INTO `tech_stacks` VALUES (18, 'cPanel', 'https://upload.wikimedia.org/wikipedia/commons/thumb/c/c2/CPanel_logo.svg/2560px-CPanel_logo.svg.png', 'Server & Hosting', 'Cloud infrastructure provider offering scalable virtual servers and managed databases.', '2025-07-08 04:38:52', '2025-07-08 04:38:52');

-- ----------------------------
-- Table structure for users
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users`  (
  `id` bigint(0) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `dob` date NULL DEFAULT NULL,
  `phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `position` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `email_verified_at` timestamp(0) NULL DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `users_email_unique`(`email`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 6 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of users
-- ----------------------------
INSERT INTO `users` VALUES (1, 'Sros Thai', 'srosthai00@gmail.com', '2025-07-04', '017670442', 'Siem Reap, Cambodia', 'Backend Laravel Dev', 'I focus on clean code, RESTful services, Spatie roles/permissions, and efficient backend logic. Passionate about solving problems and delivering user-friendly solutions.', 'uploads/users/686b8c54d82ce_1751878740.jpeg', NULL, '$2y$12$ZKaK3.1Z23tF5QLc5Ipi6OtUm0iXQohTRKpZ3dI2HQxpqXtPNcK1.', NULL, '2025-07-04 01:46:01', '2025-07-07 08:59:00');

-- ----------------------------
-- Table structure for work_experiences
-- ----------------------------
DROP TABLE IF EXISTS `work_experiences`;
CREATE TABLE `work_experiences`  (
  `id` bigint(0) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `position` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `company` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `from` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `to` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of work_experiences
-- ----------------------------
INSERT INTO `work_experiences` VALUES (1, 'Work Experience', 'Backend Laravel Dev', 'EOT (eOcambo Technology)', 'Developing and maintaining backend systems using Laravel and PHP. Focused on building scalable APIs, optimizing database queries, and ensuring application security. Collaborating with frontend developers to integrate user-facing elements.', '2024', 'Present', '2025-07-08 03:55:12', '2025-07-08 03:56:26');
INSERT INTO `work_experiences` VALUES (2, 'Junior Backend Laravel Dev', 'Junior Backend Laravel Dev', 'EOT (eOcambo Technology)', 'Started career in software development. Worked on backend systems using Laravel and PHP. Gained experience in RESTful APIs, database design, and application security.', '2023', '2024', '2025-07-08 03:56:20', '2025-07-08 03:56:20');

SET FOREIGN_KEY_CHECKS = 1;
