-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Waktu pembuatan: 03 Jul 2025 pada 14.24
-- Versi server: 10.4.27-MariaDB
-- Versi PHP: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `absensigps`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `attendances`
--

CREATE TABLE `attendances` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `schedule_id` bigint(20) UNSIGNED NOT NULL,
  `barcode_id` bigint(20) UNSIGNED DEFAULT NULL,
  `date` date DEFAULT NULL,
  `time_in` time DEFAULT NULL,
  `time_out` time DEFAULT NULL,
  `shift_id` bigint(20) UNSIGNED DEFAULT NULL,
  `latitude` double DEFAULT NULL,
  `longitude` double DEFAULT NULL,
  `status` enum('present','late','excused','sick','absent') NOT NULL DEFAULT 'absent',
  `work_mode` varchar(255) DEFAULT NULL,
  `note` varchar(255) DEFAULT NULL,
  `attachment` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `user_id` char(26) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `attendances`
--

INSERT INTO `attendances` (`id`, `schedule_id`, `barcode_id`, `date`, `time_in`, `time_out`, `shift_id`, `latitude`, `longitude`, `status`, `work_mode`, `note`, `attachment`, `created_at`, `updated_at`, `user_id`) VALUES
(74, 622, NULL, '2025-04-21', '00:33:23', '00:33:35', 2, -7.7885321, 110.3920163, 'late', 'wfh', NULL, NULL, '2025-04-20 17:33:23', '2025-04-20 17:33:35', '01jrqfk4af8vk7b2wr08f0zdzd'),
(78, 623, NULL, '2025-04-22', '21:04:00', NULL, 3, -7.7884623, 110.3920594, 'present', 'wfh', NULL, NULL, '2025-04-22 14:04:00', '2025-04-22 14:04:00', '01jrqfk4af8vk7b2wr08f0zdzd'),
(79, 628, NULL, '2025-04-29', NULL, NULL, 1, NULL, NULL, 'excused', NULL, 'Otomatis diberikan izin karena tidak hadir lebih dari 4 jam.', NULL, '2025-04-29 14:08:56', '2025-04-29 14:08:56', '01jrqfk4af8vk7b2wr08f0zdzd');

-- --------------------------------------------------------

--
-- Struktur dari tabel `barcodes`
--

CREATE TABLE `barcodes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `value` varchar(255) NOT NULL,
  `latitude` double NOT NULL DEFAULT 0,
  `longitude` double NOT NULL DEFAULT 0,
  `radius` double NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `barcodes`
--

INSERT INTO `barcodes` (`id`, `name`, `value`, `latitude`, `longitude`, `radius`, `created_at`, `updated_at`) VALUES
(4, 'kos', '8949029430842', -7.7885033, 110.3920286, 100, '2025-04-16 13:37:37', '2025-04-16 13:37:37'),
(5, 'jogja kita', '3245913429326', -7.7928500543639, 110.38830591435, 2000, '2025-04-18 12:38:42', '2025-04-18 12:38:42');

-- --------------------------------------------------------

--
-- Struktur dari tabel `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('pt_solusi_247_cache_580071baad7b227a20e85017933acecb', 'i:1;', 1745935881),
('pt_solusi_247_cache_580071baad7b227a20e85017933acecb:timer', 'i:1745935881;', 1745935881),
('pt_solusi_247_cache_7e646f8b417e1d0d0d0946a032554630', 'i:2;', 1747992679),
('pt_solusi_247_cache_7e646f8b417e1d0d0d0946a032554630:timer', 'i:1747992679;', 1747992679),
('pt_solusi_247_cache_agung@solusi.com|127.0.0.1', 'i:2;', 1747992679),
('pt_solusi_247_cache_agung@solusi.com|127.0.0.1:timer', 'i:1747992679;', 1747992679),
('pt_solusi_247_cache_attendance-01jrqfk4af8vk7b2wr08f0zdzd-2025-04-01', 'a:0:{}', 1745418055),
('pt_solusi_247_cache_attendance-01jrqfk4af8vk7b2wr08f0zdzd-2025-04-16', 'a:0:{}', 1745418036),
('pt_solusi_247_cache_attendance-01jrqfk4af8vk7b2wr08f0zdzd-2025-04-17', 'a:0:{}', 1745418041),
('pt_solusi_247_cache_attendance-01jrqfk4af8vk7b2wr08f0zdzd-2025-04-18', 'a:0:{}', 1745418048),
('pt_solusi_247_cache_attendance-01jrqfk4af8vk7b2wr08f0zdzd-2025-04-19', 'a:0:{}', 1745418045),
('pt_solusi_247_cache_attendance-01jrqfk4af8vk7b2wr08f0zdzd-2025-04-22', 'a:1:{i:0;a:20:{s:2:\"id\";i:78;s:11:\"schedule_id\";i:623;s:10:\"barcode_id\";N;s:4:\"date\";s:10:\"2025-04-22\";s:7:\"time_in\";s:8:\"21:04:00\";s:8:\"time_out\";N;s:8:\"shift_id\";i:3;s:8:\"latitude\";d:-7.7884623;s:9:\"longitude\";d:110.3920594;s:6:\"status\";s:7:\"present\";s:9:\"work_mode\";s:3:\"wfh\";s:4:\"note\";N;s:10:\"attachment\";N;s:10:\"created_at\";s:19:\"2025-04-22 21:04:00\";s:10:\"updated_at\";s:19:\"2025-04-22 21:04:00\";s:7:\"user_id\";s:26:\"01jrqfk4af8vk7b2wr08f0zdzd\";s:11:\"coordinates\";a:2:{s:3:\"lat\";d:-7.7884623;s:3:\"lng\";d:110.3920594;}s:3:\"lat\";d:-7.7884623;s:3:\"lng\";d:110.3920594;s:5:\"shift\";s:7:\"Shift 3\";}}', 1745417852),
('pt_solusi_247_cache_attendance-01jrqfk4af8vk7b2wr08f0zdzd-2025-04-23', 'a:0:{}', 1745418052),
('pt_solusi_247_cache_attendance-01jrqfk4af8vk7b2wr08f0zdzd-2025-04-25', 'a:0:{}', 1745418058),
('pt_solusi_247_cache_attendance-01jrqfk4af8vk7b2wr08f0zdzd-2025-04-29', 'a:1:{i:0;a:20:{s:2:\"id\";i:79;s:11:\"schedule_id\";i:628;s:10:\"barcode_id\";N;s:4:\"date\";s:10:\"2025-04-29\";s:7:\"time_in\";N;s:8:\"time_out\";N;s:8:\"shift_id\";i:1;s:8:\"latitude\";N;s:9:\"longitude\";N;s:6:\"status\";s:7:\"excused\";s:9:\"work_mode\";N;s:4:\"note\";s:60:\"Otomatis diberikan izin karena tidak hadir lebih dari 4 jam.\";s:10:\"attachment\";N;s:10:\"created_at\";s:19:\"2025-04-29 21:08:56\";s:10:\"updated_at\";s:19:\"2025-04-29 21:08:56\";s:7:\"user_id\";s:26:\"01jrqfk4af8vk7b2wr08f0zdzd\";s:11:\"coordinates\";N;s:3:\"lat\";N;s:3:\"lng\";N;s:5:\"shift\";s:7:\"Shift 2\";}}', 1746022591),
('pt_solusi_247_cache_attendance-01jrqfk4af8vk7b2wr08f0zdzd-2025-05-03', 'a:0:{}', 1745417855),
('pt_solusi_247_cache_attendance-01jrqfk4af8vk7b2wr08f0zdzd-4-2025', 'a:3:{i:0;a:10:{s:2:\"id\";i:74;s:6:\"status\";s:4:\"late\";s:4:\"date\";s:10:\"2025-04-21\";s:8:\"latitude\";d:-7.7885321;s:9:\"longitude\";d:110.3920163;s:10:\"attachment\";N;s:4:\"note\";N;s:11:\"coordinates\";a:2:{s:3:\"lat\";d:-7.7885321;s:3:\"lng\";d:110.3920163;}s:3:\"lat\";d:-7.7885321;s:3:\"lng\";d:110.3920163;}i:1;a:10:{s:2:\"id\";i:78;s:6:\"status\";s:7:\"present\";s:4:\"date\";s:10:\"2025-04-22\";s:8:\"latitude\";d:-7.7884623;s:9:\"longitude\";d:110.3920594;s:10:\"attachment\";N;s:4:\"note\";N;s:11:\"coordinates\";a:2:{s:3:\"lat\";d:-7.7884623;s:3:\"lng\";d:110.3920594;}s:3:\"lat\";d:-7.7884623;s:3:\"lng\";d:110.3920594;}i:2;a:10:{s:2:\"id\";i:79;s:6:\"status\";s:7:\"excused\";s:4:\"date\";s:10:\"2025-04-29\";s:8:\"latitude\";N;s:9:\"longitude\";N;s:10:\"attachment\";N;s:4:\"note\";s:60:\"Otomatis diberikan izin karena tidak hadir lebih dari 4 jam.\";s:11:\"coordinates\";N;s:3:\"lat\";N;s:3:\"lng\";N;}}', 1748079143),
('pt_solusi_247_cache_attendance-01jrqfk4af8vk7b2wr08f0zdzd-5-2025', 'a:0:{}', 1748079133),
('pt_solusi_247_cache_bf856c935775f37db4d5bdfbf4653289', 'i:1;', 1747724133),
('pt_solusi_247_cache_bf856c935775f37db4d5bdfbf4653289:timer', 'i:1747724132;', 1747724132),
('pt_solusi_247_cache_superadmin@example.com|127.0.0.1', 'i:1;', 1745935881),
('pt_solusi_247_cache_superadmin@example.com|127.0.0.1:timer', 'i:1745935881;', 1745935881);

-- --------------------------------------------------------

--
-- Struktur dari tabel `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `divisions`
--

CREATE TABLE `divisions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `divisions`
--

INSERT INTO `divisions` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'Divisi 1', '2024-10-31 02:15:36', '2024-10-31 02:15:36'),
(2, 'Divisi 2', '2024-10-31 02:15:36', '2024-10-31 02:15:36'),
(3, 'Divisi 3', '2024-10-31 02:15:36', '2024-10-31 02:15:36'),
(4, 'Divisi 4', '2024-10-31 02:15:36', '2024-10-31 02:15:36'),
(5, 'Divisi 5', '2024-10-31 06:37:37', '2024-10-31 06:37:37');

-- --------------------------------------------------------

--
-- Struktur dari tabel `educations`
--

CREATE TABLE `educations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `educations`
--

INSERT INTO `educations` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'SD', '2024-10-31 02:15:37', '2024-10-31 02:15:37'),
(2, 'SMP', '2024-10-31 02:15:37', '2024-10-31 02:15:37'),
(3, 'SMA', '2024-10-31 02:15:37', '2024-10-31 02:15:37'),
(4, 'SMK', '2024-10-31 02:15:37', '2024-10-31 02:15:37'),
(5, 'D1', '2024-10-31 02:15:37', '2024-10-31 02:15:37'),
(6, 'D2', '2024-10-31 02:15:37', '2024-10-31 02:15:37'),
(7, 'D3', '2024-10-31 02:15:37', '2024-10-31 02:15:37'),
(8, 'D4', '2024-10-31 02:15:37', '2024-10-31 02:15:37'),
(9, 'S1', '2024-10-31 02:15:37', '2024-10-31 02:15:37'),
(10, 'S2', '2024-10-31 02:15:37', '2024-10-31 02:15:37'),
(11, 'S3', '2024-10-31 02:15:37', '2024-10-31 02:15:37');

-- --------------------------------------------------------

--
-- Struktur dari tabel `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `job_titles`
--

CREATE TABLE `job_titles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `job_titles`
--

INSERT INTO `job_titles` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'Manager', '2024-10-31 02:15:38', '2024-10-31 02:15:38'),
(2, 'Staff', '2024-10-31 02:15:38', '2024-10-31 02:15:38'),
(3, 'Accounting', '2024-10-31 02:15:38', '2024-10-31 02:15:38'),
(4, 'HRD', '2024-10-31 02:15:38', '2024-10-31 02:15:38'),
(5, 'IT', '2024-10-31 02:15:38', '2024-10-31 02:15:38');

-- --------------------------------------------------------

--
-- Struktur dari tabel `leave_requests`
--

CREATE TABLE `leave_requests` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` char(26) NOT NULL,
  `from` date NOT NULL,
  `to` date DEFAULT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `type` enum('excused','sick','on_time') NOT NULL,
  `note` text DEFAULT NULL,
  `attachment` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_divisions_table', 1),
(2, '0001_01_01_000000_create_educations_table', 1),
(3, '0001_01_01_000000_create_job_titles_table', 1),
(4, '0001_01_01_000000_create_users_table', 1),
(5, '0001_01_01_000001_add_two_factor_columns_to_users_table', 1),
(6, '0001_01_01_000001_create_cache_table', 1),
(7, '0001_01_01_000002_create_jobs_table', 1),
(8, '2024_06_08_023152_create_personal_access_tokens_table', 1),
(9, '2024_06_09_113236_create_barcodes_table', 1),
(10, '2024_06_16_092112_create_shifts_table', 1),
(11, '2024_06_17_113800_create_schedules_table', 1),
(12, '2024_06_17_113814_create_attendances_table', 1),
(13, '2025_04_11_204754_add_work_mode_to_attendances_table', 1),
(14, '2025_04_12_070621_update_work_mode_column_in_attendances_table', 1),
(15, '2025_04_12_164615_change_work_mode_column_in_attendances', 1),
(16, '2025_04_13_183008_update_schedules_table_add_month_year_columns', 2),
(17, '2025_04_13_190755_add_foreign_key_to_attendances_table', 3),
(18, '2025_04_13_223119_add_day_to_schedules_table', 4),
(19, '2025_04_14_193416_add_date_to_schedules_table', 5),
(20, '2025_04_19_133136_create_leave_requests_table', 6);

-- --------------------------------------------------------

--
-- Struktur dari tabel `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` char(36) NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `schedules`
--

CREATE TABLE `schedules` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` char(26) NOT NULL,
  `date` date NOT NULL,
  `shift_id` bigint(20) UNSIGNED NOT NULL,
  `day` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `month` tinyint(3) UNSIGNED NOT NULL,
  `year` smallint(5) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `schedules`
--

INSERT INTO `schedules` (`id`, `user_id`, `date`, `shift_id`, `day`, `created_at`, `updated_at`, `month`, `year`) VALUES
(608, '01jrqfk4af8vk7b2wr08f0zdzd', '2025-04-01', 1, 'Tuesday', '2025-04-18 05:26:45', '2025-04-18 05:26:45', 4, 2025),
(609, '01jrqfk4af8vk7b2wr08f0zdzd', '2025-04-02', 1, 'Wednesday', '2025-04-18 05:26:45', '2025-04-18 05:26:45', 4, 2025),
(610, '01jrqfk4af8vk7b2wr08f0zdzd', '2025-04-03', 1, 'Thursday', '2025-04-18 05:26:45', '2025-04-18 05:26:45', 4, 2025),
(611, '01jrqfk4af8vk7b2wr08f0zdzd', '2025-04-04', 1, 'Friday', '2025-04-18 05:26:45', '2025-04-18 05:26:45', 4, 2025),
(612, '01jrqfk4af8vk7b2wr08f0zdzd', '2025-04-07', 1, 'Monday', '2025-04-18 05:26:45', '2025-04-18 05:26:45', 4, 2025),
(613, '01jrqfk4af8vk7b2wr08f0zdzd', '2025-04-08', 1, 'Tuesday', '2025-04-18 05:26:45', '2025-04-18 05:26:45', 4, 2025),
(614, '01jrqfk4af8vk7b2wr08f0zdzd', '2025-04-09', 1, 'Wednesday', '2025-04-18 05:26:45', '2025-04-18 05:26:45', 4, 2025),
(615, '01jrqfk4af8vk7b2wr08f0zdzd', '2025-04-10', 1, 'Thursday', '2025-04-18 05:26:45', '2025-04-18 05:26:45', 4, 2025),
(616, '01jrqfk4af8vk7b2wr08f0zdzd', '2025-04-11', 1, 'Friday', '2025-04-18 05:26:45', '2025-04-18 05:26:45', 4, 2025),
(617, '01jrqfk4af8vk7b2wr08f0zdzd', '2025-04-14', 1, 'Monday', '2025-04-18 05:26:45', '2025-04-18 05:26:45', 4, 2025),
(618, '01jrqfk4af8vk7b2wr08f0zdzd', '2025-04-15', 1, 'Tuesday', '2025-04-18 05:26:45', '2025-04-18 05:26:45', 4, 2025),
(619, '01jrqfk4af8vk7b2wr08f0zdzd', '2025-04-16', 1, 'Wednesday', '2025-04-18 05:26:45', '2025-04-18 05:26:45', 4, 2025),
(620, '01jrqfk4af8vk7b2wr08f0zdzd', '2025-04-20', 3, 'sunday', '2025-04-18 05:26:45', '2025-04-18 05:26:45', 4, 2025),
(622, '01jrqfk4af8vk7b2wr08f0zdzd', '2025-04-21', 2, 'Monday', '2025-04-18 05:26:45', '2025-04-18 05:26:45', 4, 2025),
(623, '01jrqfk4af8vk7b2wr08f0zdzd', '2025-04-22', 3, 'Tuesday', '2025-04-18 05:26:45', '2025-04-18 05:26:45', 4, 2025),
(624, '01jrqfk4af8vk7b2wr08f0zdzd', '2025-04-23', 1, 'Wednesday', '2025-04-18 05:26:45', '2025-04-18 05:26:45', 4, 2025),
(625, '01jrqfk4af8vk7b2wr08f0zdzd', '2025-04-24', 1, 'Thursday', '2025-04-18 05:26:45', '2025-04-18 05:26:45', 4, 2025),
(626, '01jrqfk4af8vk7b2wr08f0zdzd', '2025-04-25', 1, 'Friday', '2025-04-18 05:26:45', '2025-04-18 05:26:45', 4, 2025),
(627, '01jrqfk4af8vk7b2wr08f0zdzd', '2025-04-28', 1, 'Monday', '2025-04-18 05:26:45', '2025-04-18 05:26:45', 4, 2025),
(628, '01jrqfk4af8vk7b2wr08f0zdzd', '2025-04-29', 1, 'Tuesday', '2025-04-18 05:26:45', '2025-04-18 05:26:45', 4, 2025),
(629, '01jrqfk4af8vk7b2wr08f0zdzd', '2025-04-30', 1, 'Wednesday', '2025-04-18 05:26:45', '2025-04-18 05:26:45', 4, 2025),
(630, '01jrqfk4af8vk7b2wr08f0zdzd', '2025-05-01', 3, 'Thursday', '2025-05-03 13:41:11', '2025-05-03 13:41:11', 5, 2025),
(631, '01jrqfk4af8vk7b2wr08f0zdzd', '2025-05-03', 3, 'saturday', '2025-05-03 13:41:11', '2025-05-03 13:41:11', 5, 2025),
(632, '01jrqfk4af8vk7b2wr08f0zdzd', '2025-05-05', 3, 'Monday', '2025-05-03 13:41:11', '2025-05-03 13:41:11', 5, 2025),
(633, '01jrqfk4af8vk7b2wr08f0zdzd', '2025-05-06', 3, 'Tuesday', '2025-05-03 13:41:11', '2025-05-03 13:41:11', 5, 2025),
(634, '01jrqfk4af8vk7b2wr08f0zdzd', '2025-05-07', 3, 'Wednesday', '2025-05-03 13:41:11', '2025-05-03 13:41:11', 5, 2025),
(635, '01jrqfk4af8vk7b2wr08f0zdzd', '2025-05-08', 3, 'Thursday', '2025-05-03 13:41:11', '2025-05-03 13:41:11', 5, 2025),
(636, '01jrqfk4af8vk7b2wr08f0zdzd', '2025-05-09', 3, 'Friday', '2025-05-03 13:41:11', '2025-05-03 13:41:11', 5, 2025),
(637, '01jrqfk4af8vk7b2wr08f0zdzd', '2025-05-12', 3, 'Monday', '2025-05-03 13:41:11', '2025-05-03 13:41:11', 5, 2025),
(638, '01jrqfk4af8vk7b2wr08f0zdzd', '2025-05-13', 3, 'Tuesday', '2025-05-03 13:41:11', '2025-05-03 13:41:11', 5, 2025),
(639, '01jrqfk4af8vk7b2wr08f0zdzd', '2025-05-14', 3, 'Wednesday', '2025-05-03 13:41:11', '2025-05-03 13:41:11', 5, 2025),
(640, '01jrqfk4af8vk7b2wr08f0zdzd', '2025-05-15', 3, 'Thursday', '2025-05-03 13:41:11', '2025-05-03 13:41:11', 5, 2025),
(641, '01jrqfk4af8vk7b2wr08f0zdzd', '2025-05-16', 3, 'Friday', '2025-05-03 13:41:11', '2025-05-03 13:41:11', 5, 2025),
(642, '01jrqfk4af8vk7b2wr08f0zdzd', '2025-05-19', 3, 'Monday', '2025-05-03 13:41:11', '2025-05-03 13:41:11', 5, 2025),
(643, '01jrqfk4af8vk7b2wr08f0zdzd', '2025-05-20', 3, 'Tuesday', '2025-05-03 13:41:11', '2025-05-03 13:41:11', 5, 2025),
(644, '01jrqfk4af8vk7b2wr08f0zdzd', '2025-05-21', 3, 'Wednesday', '2025-05-03 13:41:11', '2025-05-03 13:41:11', 5, 2025),
(645, '01jrqfk4af8vk7b2wr08f0zdzd', '2025-05-22', 3, 'Thursday', '2025-05-03 13:41:11', '2025-05-03 13:41:11', 5, 2025),
(646, '01jrqfk4af8vk7b2wr08f0zdzd', '2025-05-23', 3, 'Friday', '2025-05-03 13:41:11', '2025-05-03 13:41:11', 5, 2025),
(647, '01jrqfk4af8vk7b2wr08f0zdzd', '2025-05-26', 3, 'Monday', '2025-05-03 13:41:11', '2025-05-03 13:41:11', 5, 2025),
(648, '01jrqfk4af8vk7b2wr08f0zdzd', '2025-05-27', 3, 'Tuesday', '2025-05-03 13:41:11', '2025-05-03 13:41:11', 5, 2025),
(649, '01jrqfk4af8vk7b2wr08f0zdzd', '2025-05-28', 3, 'Wednesday', '2025-05-03 13:41:11', '2025-05-03 13:41:11', 5, 2025),
(650, '01jrqfk4af8vk7b2wr08f0zdzd', '2025-05-29', 3, 'Thursday', '2025-05-03 13:41:11', '2025-05-03 13:41:11', 5, 2025),
(651, '01jrqfk4af8vk7b2wr08f0zdzd', '2025-05-30', 3, 'Friday', '2025-05-03 13:41:11', '2025-05-03 13:41:11', 5, 2025);

-- --------------------------------------------------------

--
-- Struktur dari tabel `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` char(26) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `shifts`
--

CREATE TABLE `shifts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `shifts`
--

INSERT INTO `shifts` (`id`, `name`, `start_time`, `end_time`, `created_at`, `updated_at`) VALUES
(1, 'Shift 2', '08:00:00', '16:00:00', '2024-10-31 02:15:39', '2025-04-16 13:17:00'),
(2, 'Shift 1', '00:00:00', '08:00:00', '2024-10-31 02:15:39', '2025-04-16 13:17:15'),
(3, 'Shift 3', '21:01:00', '23:59:00', '2025-04-16 13:17:36', '2025-04-22 14:03:30');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` char(26) NOT NULL,
  `nip` varchar(255) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `gender` enum('male','female') NOT NULL,
  `birth_date` date DEFAULT NULL,
  `birth_place` varchar(255) DEFAULT NULL,
  `address` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `education_id` bigint(20) UNSIGNED DEFAULT NULL,
  `division_id` bigint(20) UNSIGNED DEFAULT NULL,
  `job_title_id` bigint(20) UNSIGNED DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `two_factor_secret` text DEFAULT NULL,
  `two_factor_recovery_codes` text DEFAULT NULL,
  `two_factor_confirmed_at` timestamp NULL DEFAULT NULL,
  `raw_password` varchar(255) DEFAULT NULL,
  `group` enum('user','admin','superadmin') NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `profile_photo_path` varchar(2048) DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `nip`, `name`, `email`, `phone`, `gender`, `birth_date`, `birth_place`, `address`, `city`, `education_id`, `division_id`, `job_title_id`, `password`, `two_factor_secret`, `two_factor_recovery_codes`, `two_factor_confirmed_at`, `raw_password`, `group`, `email_verified_at`, `profile_photo_path`, `remember_token`, `created_at`, `updated_at`) VALUES
('01jbg5pp37cyc84vapny7nbn80', '0000000000000001', 'Super Admin', 'superadmin@solusi.com', '081790000001', 'male', '2024-10-31', 'jogja', 'Jl.  demangan', 'jogja', 11, 1, 5, '$2y$12$9c2rg8Z1rs5fNH4199fzLO89TZuKvNGA6fdgoiJY3LdbYb/dxpQGS', NULL, NULL, NULL, 'superadmin', 'superadmin', '2024-10-31 02:15:35', 'profile-photos/YVsvutx40cDD9agEPKuXmgngkvjHqPDPLHoyVxL1.png', 'ripxaT9jgYcAtv0arBiYgb3usohr3wds6JkGfqCtS8PgZOzLcHWKMazJ916X', '2024-10-31 02:15:35', '2025-04-22 14:11:21'),
('01jbg5pph9vdpwzkkbj0v3wb84', '0000000000000000', 'Admin', 'admin@solusi.com', '081790000002', 'male', '1988-10-17', 'Bandung', 'Jl Raya No. 4', 'jogja', 8, 2, 4, '$2y$12$lXLlBDZB1FZKYYsDQfA.ieWzgVheDfhT.i5ahmhQWcXzaL7KMZAKS', NULL, NULL, NULL, 'admin', 'admin', '2024-10-31 02:15:36', 'profile-photos/t6KjwwVgSohrStg92tFLzsGsCMurOmEkUuVxa9TD.png', 'nc9LsnBBJh2y3WnwVaZhrn6XqyV7BSZJ9X2oiLizbPH8DeGdi4dC9Hy2qKOV', '2024-10-31 02:15:36', '2025-04-22 14:05:10'),
('01jrqfk4af8vk7b2wr08f0zdzd', '23350452', 'agung', 'agung@solusi.com', '088888', 'male', '2025-04-01', 'jakata', 'saro', 'jogja', 9, 1, 1, '$2y$12$zysWpWRdyaDceZ8JQNmRrudllvdAV/zMhIoLBeNcUm044GSIdtrte', NULL, NULL, NULL, 'agungadi04', 'user', NULL, 'profile-photos/6xXP2H4CN90GsFdYSvq1gAWm8gQ8GJn6UFkwftqQ.jpg', 'mDcIS6xUQCAYRY742TRamTEUi8mDwcjIjzXgScPI8VngKodXJBYF2fhiOAXa', '2025-04-13 11:47:09', '2025-04-19 11:44:16');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `attendances`
--
ALTER TABLE `attendances`
  ADD PRIMARY KEY (`id`),
  ADD KEY `attendances_schedule_id_foreign` (`schedule_id`),
  ADD KEY `attendances_shift_id_foreign` (`shift_id`),
  ADD KEY `attendances_user_id_foreign` (`user_id`),
  ADD KEY `attendances_barcode_id_foreign` (`barcode_id`);

--
-- Indeks untuk tabel `barcodes`
--
ALTER TABLE `barcodes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `barcodes_value_unique` (`value`);

--
-- Indeks untuk tabel `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indeks untuk tabel `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indeks untuk tabel `divisions`
--
ALTER TABLE `divisions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `divisions_name_unique` (`name`);

--
-- Indeks untuk tabel `educations`
--
ALTER TABLE `educations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `educations_name_unique` (`name`);

--
-- Indeks untuk tabel `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indeks untuk tabel `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indeks untuk tabel `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `job_titles`
--
ALTER TABLE `job_titles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `job_titles_name_unique` (`name`);

--
-- Indeks untuk tabel `leave_requests`
--
ALTER TABLE `leave_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `leave_requests_user_id_foreign` (`user_id`);

--
-- Indeks untuk tabel `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indeks untuk tabel `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indeks untuk tabel `schedules`
--
ALTER TABLE `schedules`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_date` (`user_id`,`date`),
  ADD KEY `schedules_shift_id_foreign` (`shift_id`);

--
-- Indeks untuk tabel `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indeks untuk tabel `shifts`
--
ALTER TABLE `shifts`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `users_education_id_foreign` (`education_id`),
  ADD KEY `users_division_id_foreign` (`division_id`),
  ADD KEY `users_job_title_id_foreign` (`job_title_id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `attendances`
--
ALTER TABLE `attendances`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=80;

--
-- AUTO_INCREMENT untuk tabel `barcodes`
--
ALTER TABLE `barcodes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `divisions`
--
ALTER TABLE `divisions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `educations`
--
ALTER TABLE `educations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT untuk tabel `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `job_titles`
--
ALTER TABLE `job_titles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `leave_requests`
--
ALTER TABLE `leave_requests`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT untuk tabel `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT untuk tabel `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `schedules`
--
ALTER TABLE `schedules`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=652;

--
-- AUTO_INCREMENT untuk tabel `shifts`
--
ALTER TABLE `shifts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `attendances`
--
ALTER TABLE `attendances`
  ADD CONSTRAINT `attendances_barcode_id_foreign` FOREIGN KEY (`barcode_id`) REFERENCES `barcodes` (`id`),
  ADD CONSTRAINT `attendances_schedule_id_foreign` FOREIGN KEY (`schedule_id`) REFERENCES `schedules` (`id`),
  ADD CONSTRAINT `attendances_shift_id_foreign` FOREIGN KEY (`shift_id`) REFERENCES `shifts` (`id`),
  ADD CONSTRAINT `attendances_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `leave_requests`
--
ALTER TABLE `leave_requests`
  ADD CONSTRAINT `leave_requests_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `schedules`
--
ALTER TABLE `schedules`
  ADD CONSTRAINT `schedules_shift_id_foreign` FOREIGN KEY (`shift_id`) REFERENCES `shifts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `schedules_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_division_id_foreign` FOREIGN KEY (`division_id`) REFERENCES `divisions` (`id`),
  ADD CONSTRAINT `users_education_id_foreign` FOREIGN KEY (`education_id`) REFERENCES `educations` (`id`),
  ADD CONSTRAINT `users_job_title_id_foreign` FOREIGN KEY (`job_title_id`) REFERENCES `job_titles` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
