-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 17, 2025 at 02:32 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_docutrack2`
--

-- --------------------------------------------------------

--
-- Table structure for table `ai_log_summaries`
--

DROP TABLE IF EXISTS `ai_log_summaries`;
CREATE TABLE `ai_log_summaries` (
  `id` int(11) NOT NULL,
  `summary_text` text NOT NULL,
  `error_count` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ai_log_summaries`
--

INSERT INTO `ai_log_summaries` (`id`, `summary_text`, `error_count`, `created_at`) VALUES
(1, 'AI Summary Inactive: API Key missing in .env', 7, '2025-12-14 17:49:43'),
(2, 'AI Summary Inactive: API Key missing in .env', 7, '2025-12-14 17:49:46'),
(3, 'AI Summary Inactive: API Key missing in .env', 7, '2025-12-14 18:23:55'),
(4, 'AI Summary Inactive: API Key missing in .env', 7, '2025-12-14 18:23:56'),
(5, 'AI Summary Inactive: API Key missing in .env', 7, '2025-12-14 18:23:57'),
(6, 'AI Summary Inactive: API Key missing in .env', 7, '2025-12-14 18:25:08'),
(7, 'AI Summary Inactive: API Key missing in .env', 7, '2025-12-14 18:25:09'),
(8, 'AI Summary Inactive: API Key missing in .env', 7, '2025-12-14 18:25:45'),
(9, 'AI Summary Inactive: API Key missing in .env', 7, '2025-12-14 18:25:45'),
(10, 'AI Summary Inactive: API Key missing in .env', 7, '2025-12-14 18:25:47'),
(11, 'AI Summary Inactive: API Key missing in .env', 7, '2025-12-14 18:27:53'),
(12, 'AI Summary Inactive: API Key missing in .env', 7, '2025-12-14 18:27:53'),
(13, 'AI Summary Inactive: API Key missing in .env', 7, '2025-12-14 18:27:54'),
(14, 'AI Summary Inactive: API Key missing in .env', 7, '2025-12-14 18:32:23'),
(15, 'AI Summary Inactive: API Key missing in .env', 7, '2025-12-14 18:32:24'),
(16, 'AI Summary Inactive: API Key missing in .env', 7, '2025-12-14 18:55:50'),
(17, 'AI Summary Inactive: API Key missing in .env', 7, '2025-12-14 18:55:51'),
(18, 'AI Summary Unavailable: Request failed: URL rejected: Malformed input to a URL function', 7, '2025-12-14 19:51:50'),
(19, 'AI Summary Unavailable: Request failed: URL rejected: Malformed input to a URL function', 7, '2025-12-14 19:54:35'),
(20, 'AI Summary Unavailable: Request failed: URL rejected: Malformed input to a URL function', 7, '2025-12-14 19:58:04'),
(21, 'AI Summary Unavailable: Request failed: URL rejected: Malformed input to a URL function', 7, '2025-12-14 19:58:05'),
(22, 'AI Summary Failed: Invalid response from API.', 7, '2025-12-14 19:59:25'),
(23, 'AI Summary Failed: Invalid response from API.', 7, '2025-12-14 19:59:26'),
(24, 'AI Error: models/gemini-pro is not found for API version v1beta, or is not supported for generateContent. Call ListModels to see the list of available models and their supported methods.', 7, '2025-12-14 20:00:24'),
(25, 'AI Error: models/gemini-pro is not found for API version v1beta, or is not supported for generateContent. Call ListModels to see the list of available models and their supported methods.', 8, '2025-12-14 20:00:25'),
(26, 'AI Error: models/gemini-1.5-flash is not found for API version v1beta, or is not supported for generateContent. Call ListModels to see the list of available models and their supported methods.', 8, '2025-12-14 20:01:40'),
(27, 'AI Error: Invalid JSON payload received. Unknown name \"\": Root element must be a message.', 8, '2025-12-14 20:03:07'),
(28, 'AI Error: You exceeded your current quota, please check your plan and billing details. For more information on this error, head to: https://ai.google.dev/gemini-api/docs/rate-limits. To monitor your current usage, head to: https://ai.dev/usage?tab=rate-limit. \n* Quota exceeded for metric: generativelanguage.googleapis.com/generate_content_free_tier_input_token_count, limit: 0, model: gemini-2.0-flash\n* Quota exceeded for metric: generativelanguage.googleapis.com/generate_content_free_tier_requests, limit: 0, model: gemini-2.0-flash\n* Quota exceeded for metric: generativelanguage.googleapis.com/generate_content_free_tier_requests, limit: 0, model: gemini-2.0-flash\nPlease retry in 53.987684844s.', 8, '2025-12-14 20:05:07'),
(29, 'AI Error: models/gemini-1.5-flash is not found for API version v1beta, or is not supported for generateContent. Call ListModels to see the list of available models and their supported methods.', 1, '2025-12-14 20:06:25'),
(30, 'Here are 3 critical errors summarized from the logs:\n\n*   The application frequently exceeded API rate limits for the `gemini-2.0-flash` model, specifically hitting free tier quotas for input token count and total requests per minute and per day.\n*   Attempts to use the `gemini-1.5-flash` model resulted in a 404 \"Not Found\" error, indicating it is either unavailable or unsupported for the `generateContent` method with the `v1beta` API version.\n*   These errors collectively highlight issues with current Gemini API integration, model selection, and/or the limitations of the free tier API plan.', 2, '2025-12-14 20:08:00'),
(31, 'Here is a summary of the most critical issues identified in the PHP error logs:\n\n*   **Persistent AI API Quota Exceeded:** The application is frequently exceeding multiple usage limits for the Gemini AI API\'s free tier, including input token volume and the number of requests per minute and day. This directly prevents successful communication with the AI service, causing disruptions and failures in features that depend on AI functionality.\n    *   **Suggested Investigation:** Review current API usage patterns, implement optimizations to reduce call frequency or data volume, and consider upgrading to a paid API tier to accommodate operational demands.\n\n*   **Invalid AI Model Reference:** The application is attempting to use an AI model (`gemini-1.5-flash`) that is either unavailable, incorrectly named, or not supported for the specified API version and method. This error explicitly prevents AI-powered features from functioning, leading to critical breakdowns in user experience or core application capabilities.\n    *   **Suggested Investigation:** Verify the application\'s configuration and code to ensure the correct and currently supported AI model names and API versions are being utilized, referencing the latest Gemini API documentation.\n\n*   **Overall AI API Integration Instability:** The combination of consistent quota breaches and misconfigured model calls points to deeper issues within the application\'s integration strategy with the AI API. This indicates a lack of robust error handling, usage monitoring, or appropriate capacity planning.\n    *   **Suggested Investigation:** Conduct a comprehensive audit of the AI API integration, focusing on error resilience, dynamic model selection, proactive usage monitoring, and a clear strategy for scaling AI service delivery.', 2, '2025-12-14 20:10:49'),
(32, 'Here\'s a summary of the most critical issues identified in the PHP error logs:\n\n*   **Frequent API Quota Exceedance:** The application is consistently hitting its free-tier rate limits for the Gemini AI API (specifically `gemini-2.0-flash` for input tokens and requests per minute/day). This prevents successful AI API calls, leading to service interruptions and degraded performance for features relying on AI. **Investigation Area:** Review API usage patterns, optimize request frequency, and consider upgrading to a higher-tier service plan.\n\n*   **Unsupported AI Model Configuration:** The system is attempting to utilize an AI model (`gemini-1.5-flash`) that is either no longer available or not supported for the specified API version and method. This causes complete failure for any application functionality reliant on this particular model. **Investigation Area:** Update the application\'s AI model configuration to a currently supported and available Gemini model.\n\n*   **Significant Performance Delays Due to Retries:** Following quota exceedance, the AI API is imposing substantial retry delays (e.g., 53 seconds) before allowing further attempts. While a mitigation, this directly translates into significant waiting times and sluggish performance for users whenever AI operations fail and are retried, severely impacting responsiveness. **Investigation Area:** Implement more robust error handling with smarter retry strategies (e.g., exponential backoff) and address the underlying causes of quota limits.', 2, '2025-12-14 20:10:59'),
(33, 'No logs to summarize.', 0, '2025-12-14 20:31:27'),
(34, 'Here\'s a summary of the most critical issues identified from the provided PHP error logs:\n\n*   **Repeated Super Admin Logins:** The logs show frequent successful logins for the `superadmin@gmail.com` account throughout the day. This pattern could indicate shared credentials, an automated process accessing the system, or an attacker attempting to maintain persistent access. It significantly increases security risk.\n    *   **Investigation Area:** Review super admin account usage patterns, implement Multi-Factor Authentication, and consider limiting access by IP address or time.\n*   **Debug-Level Logging in Production:** The entries are marked as `[AuthDebug]`, suggesting that debug-level logging is active for authentication events. While not an error itself, debug logs are generally too verbose for a production environment.\n    *   **Investigation Area:** Verify logging configurations to ensure that only essential information (warnings, errors, security events) is logged in production, reducing disk usage and potential exposure of sensitive data.\n*   **Insufficient Security Context in Logs:** The successful login entries lack critical details such as the source IP address or user agent string. This makes it difficult to verify the legitimacy of logins or trace suspicious activity effectively.\n    *   **Investigation Area:** Enhance the authentication logging mechanism to include more contextual information like the user\'s IP address and browser details, which are vital for security audits and incident response.', 0, '2025-12-15 03:15:10'),
(35, 'Here is a summary of the most critical issues identified from the PHP error logs:\n\n*   **Frequent Session Invalidation:** The system is frequently reporting \"user_id not set in session,\" indicating that active user sessions, particularly for the superadmin, are not being maintained consistently.\n    *   **Impact:** This leads to unexpected logouts, forcing users to repeatedly re-authenticate, which degrades the user experience and can interrupt critical administrative tasks.\n    *   **Action:** Investigate the application\'s session management configuration, including session expiry times, storage mechanisms (e.g., file-based vs. database), and any code that might inadvertently clear or fail to retrieve session data.\n\n*   **Excessive Debug Logging in Production:** The logs are predominantly filled with verbose debug messages (`[AuthDebug]`) for routine successful login attempts, rather than actual errors or warnings.\n    *   **Impact:** This volume of unnecessary information can quickly consume disk space, make it challenging to identify genuine critical issues or security events, and potentially impact application performance due to excessive I/O.\n    *   **Action:** Adjust the application\'s logging configuration to a more appropriate level for a production environment, focusing on warnings, errors, and critical events, rather than routine debug information.\n\n*   **Concentrated Activity and Instability for Superadmin Account:** All recorded login activities and the session failure specifically involve the `superadmin@gmail.com` account, often with repeated successful logins within short intervals.\n    *   **Impact:** This pattern could signify either unusual security activity against this high-privilege account or a severe usability problem where the superadmin is constantly being forced to log in due to underlying session instability. It also highlights a single point of failure for administrative access.\n    *   **Action:** Beyond resolving the general session issues, review the security practices surrounding the superadmin account, consider implementing multi-factor authentication, and investigate if the frequent logins are indicative of an automated process, a specific user workflow issue, or a potential security concern.', 0, '2025-12-15 05:01:12'),
(36, 'Here\'s a professional summary of the most critical issues identified in the PHP error logs:\n\n*   **Critical Access Control Flaw:** The system is explicitly granting access to resources (e.g., \"kegiatan ID: 3\") for a verifier user, even when its own access check logic clearly indicates a mismatch (\"Match: NO\") based on departmental criteria. This is a severe security vulnerability, potentially allowing unauthorized access to sensitive information or functions.\n    *   **Action Area:** Immediately review and debug the access control logic, especially the decision-making process after an access check fails, to ensure unauthorized access is properly denied.\n*   **Broken Notification Functionality:** The system is failing to generate rejection notifications due to a database error stating \"Unknown column \'judul\' in \'field list\'\". This indicates a mismatch between the application\'s code and the database schema.\n    *   **Action Area:** Investigate the database table(s) involved in creating notifications. Ensure the \'judul\' column exists and is correctly named, or update the application code to use the correct column name if it has changed.\n*   **Inconsistent User Department Data:** The verifier user attempting access has their \"User Jurusan\" (department) recorded as \'NULL\'. This data inconsistency, while currently bypassed by the access control flaw, indicates a problem with user profile management.\n    *   **Action Area:** Review the user creation, update, and management processes to ensure essential user attributes like \'Jurusan\' are properly captured and stored, as this data is crucial for correct authorization.', 0, '2025-12-17 08:41:12');

-- --------------------------------------------------------

--
-- Table structure for table `ai_security_alerts`
--

DROP TABLE IF EXISTS `ai_security_alerts`;
CREATE TABLE `ai_security_alerts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `ip_address` varchar(255) NOT NULL,
  `input_payload` text NOT NULL,
  `severity` enum('low','medium','high') NOT NULL,
  `detection_type` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_activity_logs`
--

DROP TABLE IF EXISTS `tbl_activity_logs`;
CREATE TABLE `tbl_activity_logs` (
  `logId` int(10) UNSIGNED NOT NULL,
  `userId` int(10) UNSIGNED NOT NULL COMMENT 'ID user yang melakukan aksi',
  `action` varchar(50) NOT NULL COMMENT 'Kode aksi (LOGIN_SUCCESS, PPK_APPROVE, dll)',
  `category` enum('authentication','workflow','document','financial','user_management','security') NOT NULL DEFAULT 'workflow' COMMENT 'Kategori aksi untuk grouping dan filtering',
  `entityType` varchar(50) DEFAULT NULL COMMENT 'Tipe entity (kegiatan, lpj, user, dll)',
  `entityId` int(10) UNSIGNED DEFAULT NULL COMMENT 'ID entity yang dimodifikasi',
  `description` text DEFAULT NULL COMMENT 'Deskripsi detail aksi',
  `oldValue` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Nilai sebelum perubahan' CHECK (json_valid(`oldValue`)),
  `newValue` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Nilai setelah perubahan' CHECK (json_valid(`newValue`)),
  `ipAddress` varchar(45) DEFAULT NULL COMMENT 'IP Address client (IPv6 support)',
  `userAgent` varchar(500) DEFAULT NULL COMMENT 'Browser/client user agent',
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Unified audit trail - semua aktivitas user tercatat di sini';

--
-- Dumping data for table `tbl_activity_logs`
--

INSERT INTO `tbl_activity_logs` (`logId`, `userId`, `action`, `category`, `entityType`, `entityId`, `description`, `oldValue`, `newValue`, `ipAddress`, `userAgent`, `createdAt`) VALUES
(1, 13, 'LOGOUT', 'authentication', 'user', 13, 'User melakukan logout', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-14 11:31:36'),
(2, 0, 'LOGIN_FAILED', 'authentication', 'user', NULL, 'Login gagal untuk email: agungellangoo@gmail.com. Alasan: Email tidak terdaftar.', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-14 12:02:18'),
(3, 0, 'LOGIN_FAILED', 'authentication', 'user', NULL, 'Login gagal untuk email: agungellangoo@gmail.com. Alasan: Email tidak terdaftar.', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-14 12:02:30'),
(4, 0, 'LOGIN_FAILED', 'authentication', 'user', NULL, 'Login gagal untuk email: hari.bernardo.tik24@stu.pnj.ac.id. Alasan: Email tidak terdaftar.', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-14 12:02:41'),
(5, 0, 'LOGIN_FAILED', 'authentication', 'user', NULL, 'Login gagal untuk email: sniperpride006@gmail.com. Alasan: Email tidak terdaftar.', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-14 12:03:18'),
(6, 0, 'LOGIN_FAILED', 'authentication', 'user', NULL, 'Login gagal untuk email: hari.bernardo.tik24@stu.pnj.ac.id. Alasan: Email tidak terdaftar.', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-14 17:11:53'),
(7, 0, 'LOGIN_FAILED', 'authentication', 'user', NULL, 'Login gagal untuk email: hari.bernardo.tik24@stu.pnj.ac.id. Alasan: Email tidak terdaftar.', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-14 17:11:58'),
(8, 0, 'LOGIN_FAILED', 'authentication', 'user', NULL, 'Login gagal untuk email: hari.bernardo.tik24@stu.pnj.ac.id. Alasan: Email tidak terdaftar.', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-14 17:12:22'),
(9, 0, 'LOGIN_FAILED', 'authentication', 'user', NULL, 'Login gagal untuk email: hari.bernardo.tik24@stu.pnj.ac.id. Alasan: Email tidak terdaftar.', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-14 17:12:38'),
(10, 1, 'LOGIN_SUCCESS', 'authentication', 'user', 1, 'User berhasil login dengan email: adminti@gmail.com', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-14 17:20:10'),
(11, 1, 'LOGIN_SUCCESS', 'authentication', 'user', 1, 'User berhasil login dengan email: adminti@gmail.com', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-14 17:23:11'),
(12, 1, 'LOGOUT', 'authentication', 'user', 1, 'User melakukan logout', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-14 17:30:50'),
(13, 9, 'LOGIN_SUCCESS', 'authentication', 'user', 9, 'User berhasil login dengan email: verifikator@gmail.com', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-14 17:31:06'),
(14, 10, 'LOGIN_SUCCESS', 'authentication', 'user', 10, 'User berhasil login dengan email: wadir@gmail.com', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-14 17:31:26'),
(15, 11, 'LOGIN_SUCCESS', 'authentication', 'user', 11, 'User berhasil login dengan email: ppk@gmail.com', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-14 17:31:38'),
(16, 11, 'LOGOUT', 'authentication', 'user', 11, 'User melakukan logout', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-14 17:31:46'),
(17, 12, 'LOGIN_SUCCESS', 'authentication', 'user', 12, 'User berhasil login dengan email: bendahara@gmail.com', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-14 17:31:50'),
(18, 13, 'LOGIN_SUCCESS', 'authentication', 'user', 13, 'User berhasil login dengan email: superadmin@gmail.com', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-14 17:32:07'),
(19, 2, 'LOGIN_SUCCESS', 'authentication', 'user', 2, 'User berhasil login dengan email: adminelektro@gmail.com', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-14 17:32:19'),
(20, 2, 'LOGOUT', 'authentication', 'user', 2, 'User melakukan logout', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-14 17:43:55'),
(21, 13, 'LOGIN_SUCCESS', 'authentication', 'user', 13, 'User berhasil login dengan email: superadmin@gmail.com', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-14 17:44:03'),
(22, 13, 'LOGIN_SUCCESS', 'authentication', 'user', 13, 'User berhasil login dengan email: superadmin@gmail.com', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-14 20:13:34'),
(23, 13, 'LOGIN_SUCCESS', 'authentication', 'user', 13, 'User berhasil login dengan email: superadmin@gmail.com', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-14 20:13:48'),
(24, 13, 'LOGIN_SUCCESS', 'authentication', 'user', 13, 'User berhasil login dengan email: superadmin@gmail.com', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-14 20:14:00'),
(25, 13, 'LOGIN_SUCCESS', 'authentication', 'user', 13, 'User berhasil login dengan email: superadmin@gmail.com', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-14 20:14:00'),
(26, 13, 'LOGOUT', 'authentication', 'user', 13, 'User melakukan logout', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-14 20:17:57'),
(27, 13, 'LOGIN_SUCCESS', 'authentication', 'user', 13, 'User berhasil login dengan email: superadmin@gmail.com', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-14 20:18:09'),
(28, 13, 'LOGIN_SUCCESS', 'authentication', 'user', 13, 'User berhasil login dengan email: superadmin@gmail.com', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-14 20:18:21'),
(29, 13, 'LOGIN_SUCCESS', 'authentication', 'user', 13, 'User berhasil login dengan email: superadmin@gmail.com', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-14 21:01:28'),
(30, 0, 'LOGIN_FAILED', 'authentication', 'user', NULL, 'Login gagal untuk email: hari.bernardo.tik24@stu.pnj.ac.id. Alasan: Email tidak terdaftar.', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-14 21:01:48'),
(31, 13, 'LOGIN_SUCCESS', 'authentication', 'user', 13, 'User berhasil login dengan email: superadmin@gmail.com', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-14 21:01:53'),
(32, 13, 'LOGIN_SUCCESS', 'authentication', 'user', 13, 'User berhasil login dengan email: superadmin@gmail.com', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-14 21:04:03'),
(33, 13, 'LOGIN_SUCCESS', 'authentication', 'user', 13, 'User berhasil login dengan email: superadmin@gmail.com', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-14 21:04:17'),
(34, 13, 'LOGIN_SUCCESS', 'authentication', 'user', 13, 'User berhasil login dengan email: superadmin@gmail.com', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-14 21:05:21'),
(35, 13, 'LOGIN_SUCCESS', 'authentication', 'user', 13, 'User berhasil login dengan email: superadmin@gmail.com', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-14 21:06:24'),
(36, 13, 'LOGIN_SUCCESS', 'authentication', 'user', 13, 'User berhasil login dengan email: superadmin@gmail.com', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-15 03:13:16'),
(37, 13, 'LOGOUT', 'authentication', 'user', 13, 'User melakukan logout', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-15 03:20:01'),
(38, 13, 'LOGIN_SUCCESS', 'authentication', 'user', 13, 'User berhasil login dengan email: superadmin@gmail.com', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-15 05:00:59'),
(39, 1, 'LOGIN_SUCCESS', 'authentication', 'user', 1, 'User berhasil login dengan email: adminti@gmail.com', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-17 06:52:19'),
(40, 1, 'LOGOUT', 'authentication', 'user', 1, 'User melakukan logout', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-17 06:52:27'),
(41, 0, 'LOGIN_FAILED', 'authentication', 'user', NULL, 'Login gagal untuk email: hari.bernardo.tik24@stu.pnj.ac.id. Alasan: Email tidak terdaftar.', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-17 06:53:22'),
(42, 0, 'LOGIN_FAILED', 'authentication', 'user', NULL, 'Login gagal untuk email: hari.bernardo.tik24@stu.pnj.ac.id. Alasan: Email tidak terdaftar.', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-17 06:53:25'),
(43, 0, 'LOGIN_FAILED', 'authentication', 'user', NULL, 'Login gagal untuk email: khalidfidel17@gmail.com. Alasan: Email tidak terdaftar.', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-17 06:53:47'),
(44, 0, 'LOGIN_FAILED', 'authentication', 'user', NULL, 'Login gagal untuk email: admin@gmail.com. Alasan: Email tidak terdaftar.', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-17 06:54:32'),
(45, 1, 'LOGIN_SUCCESS', 'authentication', 'user', 1, 'User berhasil login dengan email: adminti@gmail.com', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-17 06:54:46'),
(46, 1, 'LOGOUT', 'authentication', 'user', 1, 'User melakukan logout', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-17 06:57:09'),
(47, 9, 'LOGIN_SUCCESS', 'authentication', 'user', 9, 'User berhasil login dengan email: verifikator@gmail.com', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-17 06:57:22'),
(48, 9, 'LOGOUT', 'authentication', 'user', 9, 'User melakukan logout', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-17 06:57:52'),
(49, 1, 'LOGIN_SUCCESS', 'authentication', 'user', 1, 'User berhasil login dengan email: adminti@gmail.com', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-17 06:57:57'),
(50, 1, 'LOGOUT', 'authentication', 'user', 1, 'User melakukan logout', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-17 07:00:24'),
(51, 9, 'LOGIN_SUCCESS', 'authentication', 'user', 9, 'User berhasil login dengan email: verifikator@gmail.com', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-17 07:00:29'),
(52, 9, 'LOGOUT', 'authentication', 'user', 9, 'User melakukan logout', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-17 07:00:35'),
(53, 1, 'LOGIN_SUCCESS', 'authentication', 'user', 1, 'User berhasil login dengan email: adminti@gmail.com', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-17 07:00:41'),
(54, 1, 'LOGOUT', 'authentication', 'user', 1, 'User melakukan logout', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-17 07:01:52'),
(55, 9, 'LOGIN_SUCCESS', 'authentication', 'user', 9, 'User berhasil login dengan email: verifikator@gmail.com', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-17 07:01:58'),
(56, 9, 'LOGOUT', 'authentication', 'user', 9, 'User melakukan logout', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-17 07:02:11'),
(57, 1, 'LOGIN_SUCCESS', 'authentication', 'user', 1, 'User berhasil login dengan email: adminti@gmail.com', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-17 07:02:19'),
(58, 1, 'LOGOUT', 'authentication', 'user', 1, 'User melakukan logout', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-17 07:02:54'),
(59, 11, 'LOGIN_SUCCESS', 'authentication', 'user', 11, 'User berhasil login dengan email: ppk@gmail.com', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-17 07:03:00'),
(60, 11, 'LOGOUT', 'authentication', 'user', 11, 'User melakukan logout', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-17 07:03:25'),
(61, 10, 'LOGIN_SUCCESS', 'authentication', 'user', 10, 'User berhasil login dengan email: wadir@gmail.com', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-17 07:03:31'),
(62, 10, 'WADIR_APPROVE', 'workflow', 'kegiatan', 2, 'WADIR menyetujui kegiatan ID: 2. Catatan: Kegiatan: Pencitraan', '{\"statusUtamaId\":1}', '{\"statusUtamaId\":3}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-17 07:03:36'),
(63, 10, 'LOGOUT', 'authentication', 'user', 10, 'User melakukan logout', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-17 07:03:40'),
(64, 12, 'LOGIN_SUCCESS', 'authentication', 'user', 12, 'User berhasil login dengan email: bendahara@gmail.com', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-17 07:03:54'),
(65, 0, 'LOGIN_FAILED', 'authentication', 'user', NULL, 'Login gagal untuk email: khalidfidel17@gmail.com. Alasan: Email tidak terdaftar.', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-17 07:05:07'),
(66, 0, 'LOGIN_FAILED', 'authentication', 'user', NULL, 'Login gagal untuk email: khalidfidel17@gmail.com. Alasan: Email tidak terdaftar.', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-17 07:05:15'),
(67, 0, 'LOGIN_FAILED', 'authentication', 'user', NULL, 'Login gagal untuk email: khalidfidel17@gmail.com. Alasan: Email tidak terdaftar.', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-17 07:05:23'),
(68, 12, 'LOGIN_SUCCESS', 'authentication', 'user', 12, 'User berhasil login dengan email: bendahara@gmail.com', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-17 07:05:50'),
(69, 13, 'LOGIN_SUCCESS', 'authentication', 'user', 13, 'User berhasil login dengan email: superadmin@gmail.com', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-17 07:06:53'),
(70, 13, 'LOGIN_SUCCESS', 'authentication', 'user', 13, 'User berhasil login dengan email: superadmin@gmail.com', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-17 08:29:24'),
(71, 13, 'LOGOUT', 'authentication', 'user', 13, 'User melakukan logout', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-17 08:30:18'),
(72, 1, 'LOGIN_SUCCESS', 'authentication', 'user', 1, 'User berhasil login dengan email: adminti@gmail.com', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-17 08:30:24'),
(73, 1, 'LOGOUT', 'authentication', 'user', 1, 'User melakukan logout', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-17 08:31:50'),
(74, 9, 'LOGIN_SUCCESS', 'authentication', 'user', 9, 'User berhasil login dengan email: verifikator@gmail.com', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-17 08:32:35'),
(75, 9, 'LOGOUT', 'authentication', 'user', 9, 'User melakukan logout', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-17 08:33:17'),
(76, 1, 'LOGIN_SUCCESS', 'authentication', 'user', 1, 'User berhasil login dengan email: adminti@gmail.com', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-17 08:33:22'),
(77, 1, 'LOGOUT', 'authentication', 'user', 1, 'User melakukan logout', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-17 08:33:32'),
(78, 1, 'LOGIN_SUCCESS', 'authentication', 'user', 1, 'User berhasil login dengan email: adminti@gmail.com', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-17 08:33:38'),
(79, 1, 'LOGOUT', 'authentication', 'user', 1, 'User melakukan logout', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-17 08:34:37'),
(80, 9, 'LOGIN_SUCCESS', 'authentication', 'user', 9, 'User berhasil login dengan email: verifikator@gmail.com', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-17 08:34:48'),
(81, 9, 'LOGOUT', 'authentication', 'user', 9, 'User melakukan logout', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-17 08:36:02'),
(82, 13, 'LOGIN_SUCCESS', 'authentication', 'user', 13, 'User berhasil login dengan email: superadmin@gmail.com', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-17 08:41:02'),
(83, 13, 'LOGOUT', 'authentication', 'user', 13, 'User melakukan logout', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-17 08:44:03'),
(84, 12, 'LOGIN_SUCCESS', 'authentication', 'user', 12, 'User berhasil login dengan email: bendahara@gmail.com', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-17 08:44:07'),
(85, 13, 'LOGIN_SUCCESS', 'authentication', 'user', 13, 'User berhasil login dengan email: superadmin@gmail.com', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-17 08:48:07'),
(86, 1, 'LOGIN_SUCCESS', 'authentication', 'user', 1, 'User berhasil login dengan email: adminti@gmail.com', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-17 11:10:19'),
(87, 1, 'LOGOUT', 'authentication', 'user', 1, 'User melakukan logout', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-17 11:12:25'),
(88, 9, 'LOGIN_SUCCESS', 'authentication', 'user', 9, 'User berhasil login dengan email: verifikator@gmail.com', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-17 11:12:33'),
(89, 9, 'LOGOUT', 'authentication', 'user', 9, 'User melakukan logout', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-17 11:12:47'),
(90, 11, 'LOGIN_SUCCESS', 'authentication', 'user', 11, 'User berhasil login dengan email: ppk@gmail.com', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-17 11:12:54'),
(91, 1, 'LOGIN_SUCCESS', 'authentication', 'user', 1, 'User berhasil login dengan email: adminti@gmail.com', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-17 11:13:20'),
(92, 1, 'LOGOUT', 'authentication', 'user', 1, 'User melakukan logout', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-17 11:13:56'),
(93, 11, 'LOGIN_SUCCESS', 'authentication', 'user', 11, 'User berhasil login dengan email: ppk@gmail.com', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-17 11:14:07'),
(94, 11, 'LOGOUT', 'authentication', 'user', 11, 'User melakukan logout', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-17 11:14:14'),
(95, 10, 'LOGIN_SUCCESS', 'authentication', 'user', 10, 'User berhasil login dengan email: wadir@gmail.com', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-17 11:14:20'),
(96, 10, 'WADIR_APPROVE', 'workflow', 'kegiatan', 4, 'WADIR menyetujui kegiatan ID: 4. Catatan: Kegiatan: Pencitraan', '{\"statusUtamaId\":1}', '{\"statusUtamaId\":3}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-17 11:14:27'),
(97, 10, 'LOGOUT', 'authentication', 'user', 10, 'User melakukan logout', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-17 11:14:42'),
(98, 12, 'LOGIN_SUCCESS', 'authentication', 'user', 12, 'User berhasil login dengan email: bendahara@gmail.com', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-17 11:14:49'),
(99, 12, 'PENCAIRAN_SUCCESS', 'financial', 'kegiatan', 4, 'Pencairan dana Rp 10.000.000 untuk kegiatan ID: 4. Metode: bertahap. Catatan: Kontol', NULL, '{\"jumlahDicairkan\":10000000,\"metodePencairan\":\"bertahap\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-17 11:21:22'),
(100, 12, 'LOGOUT', 'authentication', 'user', 12, 'User melakukan logout', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-17 11:21:25'),
(101, 1, 'LOGIN_SUCCESS', 'authentication', 'user', 1, 'User berhasil login dengan email: adminti@gmail.com', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-17 11:21:30'),
(102, 1, 'LOGOUT', 'authentication', 'user', 1, 'User melakukan logout', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-17 11:59:24'),
(103, 1, 'LOGIN_SUCCESS', 'authentication', 'user', 1, 'User berhasil login dengan email: adminti@gmail.com', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-17 12:26:27'),
(104, 1, 'LOGIN_SUCCESS', 'authentication', 'user', 1, 'User berhasil login dengan email: adminti@gmail.com', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-17 12:34:51'),
(105, 1, 'LOGIN_SUCCESS', 'authentication', 'user', 1, 'User berhasil login dengan email: adminti@gmail.com', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-17 12:53:57'),
(106, 1, 'LOGIN_SUCCESS', 'authentication', 'user', 1, 'User berhasil login dengan email: adminti@gmail.com', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-17 12:54:09'),
(107, 1, 'LOGIN_SUCCESS', 'authentication', 'user', 1, 'User berhasil login dengan email: adminti@gmail.com', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-17 13:02:26');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_iku`
--

DROP TABLE IF EXISTS `tbl_iku`;
CREATE TABLE `tbl_iku` (
  `id` int(11) NOT NULL,
  `kode_iku` varchar(50) DEFAULT NULL,
  `indikator_kinerja` varchar(255) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `target` varchar(100) DEFAULT NULL,
  `realisasi` varchar(100) DEFAULT NULL,
  `tahun` year(4) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_indikator_kak`
--

DROP TABLE IF EXISTS `tbl_indikator_kak`;
CREATE TABLE `tbl_indikator_kak` (
  `indikatorId` int(11) NOT NULL,
  `kakId` int(11) NOT NULL,
  `bulan` tinyint(3) UNSIGNED DEFAULT NULL COMMENT 'Bulan pelaksanaan (1-12)',
  `indikatorKeberhasilan` varchar(250) DEFAULT NULL COMMENT 'Deskripsi indikator keberhasilan',
  `targetPersen` tinyint(3) UNSIGNED DEFAULT NULL COMMENT 'Target pencapaian (0-100)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Monthly success indicators for KAK';

--
-- Dumping data for table `tbl_indikator_kak`
--

INSERT INTO `tbl_indikator_kak` (`indikatorId`, `kakId`, `bulan`, `indikatorKeberhasilan`, `targetPersen`) VALUES
(1, 1, 2, 'asdfasdf', 255),
(2, 2, 4, 'asdf', 100),
(3, 3, 10, 'asfg2', 100),
(4, 4, 1, 'asfasfa', 100);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_jurusan`
--

DROP TABLE IF EXISTS `tbl_jurusan`;
CREATE TABLE `tbl_jurusan` (
  `namaJurusan` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Master table for academic departments';

--
-- Dumping data for table `tbl_jurusan`
--

INSERT INTO `tbl_jurusan` (`namaJurusan`) VALUES
('Administrasi Niaga'),
('Akuntansi'),
('Pascasarjana'),
('Teknik Elektro'),
('Teknik Grafika dan Penerbitan'),
('Teknik Informatika dan Komputer'),
('Teknik Mesin'),
('Teknik Sipil');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_kak`
--

DROP TABLE IF EXISTS `tbl_kak`;
CREATE TABLE `tbl_kak` (
  `kakId` int(11) NOT NULL,
  `kegiatanId` int(11) NOT NULL,
  `iku` varchar(200) DEFAULT NULL COMMENT 'Indikator Kinerja Utama',
  `penerimaManfaat` text DEFAULT NULL COMMENT 'Penerima manfaat kegiatan',
  `gambaranUmum` text DEFAULT NULL COMMENT 'Gambaran umum kegiatan',
  `metodePelaksanaan` text DEFAULT NULL COMMENT 'Metode pelaksanaan kegiatan',
  `tglPembuatan` date DEFAULT NULL COMMENT 'Tanggal pembuatan KAK'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Terms of Reference (Kerangka Acuan Kerja)';

--
-- Dumping data for table `tbl_kak`
--

INSERT INTO `tbl_kak` (`kakId`, `kegiatanId`, `iku`, `penerimaManfaat`, `gambaranUmum`, `metodePelaksanaan`, `tglPembuatan`) VALUES
(1, 1, 'Melanjutkan studi', 'kontol raka', 'kontol lu rak', 'raka kontol', '2025-12-17'),
(2, 2, 'Menjadi Wiraswasta', 'asdf', 'asdf', 'asdf', '2025-12-17'),
(3, 3, 'Prestasi,Kegiatan luar prodi', 'AsFf', 'wASFSAGD', 'qw', '2025-12-17'),
(4, 4, 'Kegiatan luar prodi,Prestasi', 'asfafa', 'asfafasf', 'asfaf', '2025-12-17');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_kategori_rab`
--

DROP TABLE IF EXISTS `tbl_kategori_rab`;
CREATE TABLE `tbl_kategori_rab` (
  `kategoriRabId` int(11) NOT NULL,
  `namaKategori` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Budget expense categories';

--
-- Dumping data for table `tbl_kategori_rab`
--

INSERT INTO `tbl_kategori_rab` (`kategoriRabId`, `namaKategori`) VALUES
(4, 'Belanja Barang'),
(5, 'Belanja Perjalanan'),
(6, 'Belanja Jasa');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_kegiatan`
--

DROP TABLE IF EXISTS `tbl_kegiatan`;
CREATE TABLE `tbl_kegiatan` (
  `kegiatanId` int(11) NOT NULL,
  `namaKegiatan` varchar(255) NOT NULL,
  `prodiPenyelenggara` varchar(50) DEFAULT NULL COMMENT 'FK ke tbl_prodi',
  `pemilikKegiatan` varchar(150) DEFAULT NULL COMMENT 'Nama pemilik/pelaksana kegiatan',
  `nimPelaksana` varchar(20) DEFAULT NULL COMMENT 'NIM pelaksana',
  `nip` varchar(30) DEFAULT NULL COMMENT 'NIP penanggung jawab',
  `namaPJ` varchar(100) DEFAULT NULL COMMENT 'Nama penanggung jawab',
  `buktiMAK` varchar(255) DEFAULT NULL COMMENT 'Kode MAK atau file bukti MAK',
  `userId` int(11) NOT NULL COMMENT 'User yang membuat kegiatan (Admin)',
  `jurusanPenyelenggara` varchar(50) DEFAULT NULL COMMENT 'FK ke tbl_jurusan',
  `statusUtamaId` int(11) NOT NULL DEFAULT 1 COMMENT 'Status usulan: 1=Menunggu, 2=Revisi, 3=Disetujui, 4=Ditolak, 5=Dana diberikan',
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `uploadAt` timestamp NULL DEFAULT NULL COMMENT 'Waktu upload dokumen',
  `wadirTujuan` int(11) NOT NULL COMMENT 'Wadir yang dituju untuk approval',
  `suratPengantar` varchar(255) DEFAULT NULL COMMENT 'Nama file surat pengantar',
  `tanggalMulai` date DEFAULT NULL COMMENT 'Tanggal mulai kegiatan',
  `tanggalSelesai` date DEFAULT NULL COMMENT 'Tanggal selesai kegiatan',
  `posisiId` int(11) NOT NULL DEFAULT 1 COMMENT 'Posisi workflow: 1=Admin, 2=Verifikator, 3=Wadir, 4=PPK, 5=Bendahara',
  `tanggalPencairan` datetime DEFAULT NULL COMMENT 'Tanggal dana dicairkan (full/first disbursement)',
  `jumlahDicairkan` decimal(15,2) DEFAULT NULL COMMENT 'Jumlah dana yang dicairkan',
  `danaDiSetujui` decimal(15,2) DEFAULT NULL COMMENT 'Total dana yang sudah disetujui verifikator atas grand total RAB pengusul',
  `metodePencairan` varchar(50) DEFAULT NULL COMMENT 'Metode: uang_muka, dana_penuh, bertahap',
  `catatanBendahara` text DEFAULT NULL COMMENT 'Catatan dari Bendahara saat pencairan',
  `pencairan_tahap_json` text DEFAULT NULL COMMENT 'JSON array untuk pencairan bertahap: [{tahap, tanggal, persentase, jumlah, status}]',
  `umpanBalikVerifikator` text DEFAULT NULL COMMENT 'Umpan balik dari Verifikator saat approval'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Main activity/proposal table with workflow tracking';

--
-- Dumping data for table `tbl_kegiatan`
--

INSERT INTO `tbl_kegiatan` (`kegiatanId`, `namaKegiatan`, `prodiPenyelenggara`, `pemilikKegiatan`, `nimPelaksana`, `nip`, `namaPJ`, `danaDiCairkan`, `buktiMAK`, `userId`, `jurusanPenyelenggara`, `statusUtamaId`, `createdAt`, `uploadAt`, `wadirTujuan`, `suratPengantar`, `tanggalMulai`, `tanggalSelesai`, `posisiId`, `tanggalPencairan`, `jumlahDicairkan`, `metodePencairan`, `catatanBendahara`, `pencairan_tahap_json`, `umpanBalikVerifikator`) VALUES
(1, 'Pencitraan', 'D4 Teknik Informatika', 'verrel', '2407411067', NULL, NULL, NULL, NULL, 1, 'Teknik Informatika dan Komputer', 1, '2025-12-17 06:56:16', NULL, 2, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL),
(2, 'Pencitraan', 'D3 Teknik Elektronika Industri', 'verrel', '2407411067', '2301421077', 'Rafi Akbar Prakasa', NULL, '1234567890', 1, 'Teknik Elektro', 1, '2025-12-17 07:01:51', NULL, 2, 'surat_pengantar_1765954955_6942558bb060e.doc', '2025-12-10', '2025-12-12', 5, NULL, NULL, NULL, NULL, NULL, NULL),
(3, 'Pencitraan', 'D4 Teknik Informatika', 'verrel', '2407411067', NULL, NULL, NULL, NULL, 1, 'Teknik Informatika dan Komputer', 4, '2025-12-17 08:31:46', NULL, 1, NULL, NULL, NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL),
(4, 'Pencitraan', 'D4 Teknik Informatika', 'verrel', '2407411067', '2301421077', 'Rafi Akbar Prakasa', NULL, '21234567890', 1, 'Teknik Informatika dan Komputer', 5, '2025-12-17 11:12:03', NULL, 1, 'surat_pengantar_1765970030_6942906eed621.doc', '2025-12-10', '2025-12-12', 1, '2026-01-13 00:00:00', 100000.00, 'bertahap', 'Kontol', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_log_status`
--

DROP TABLE IF EXISTS `tbl_log_status`;
CREATE TABLE `tbl_log_status` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `tipe_log` varchar(50) NOT NULL COMMENT 'Tipe: NOTIFIKASI_APPROVAL, REMINDER_LPJ, BOOKMARK',
  `id_referensi` int(11) DEFAULT NULL COMMENT 'ID kegiatan, ID LPJ, dll',
  `status` varchar(20) NOT NULL COMMENT 'Status: BELUM_DIBACA, DIBACA, AKTIF',
  `konten_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Konten notifikasi dalam format JSON' CHECK (json_valid(`konten_json`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Notification system and user-specific status tracking';

-- --------------------------------------------------------

--
-- Table structure for table `tbl_lpj`
--

DROP TABLE IF EXISTS `tbl_lpj`;
CREATE TABLE `tbl_lpj` (
  `lpjId` int(11) NOT NULL,
  `kegiatanId` int(11) NOT NULL,
  `grandTotalRealisasi` decimal(15,2) DEFAULT NULL COMMENT 'Total realisasi dari semua item LPJ',
  `submittedAt` timestamp NULL DEFAULT NULL COMMENT 'Tanggal submit LPJ',
  `approvedAt` timestamp NULL DEFAULT NULL COMMENT 'Tanggal approve LPJ',
  `tenggatLpj` date DEFAULT NULL COMMENT 'Batas waktu pengumpulan LPJ',
  `statusId` int(11) NOT NULL DEFAULT 1 COMMENT 'Status LPJ: 1=Menunggu, 2=Revisi, 3=Disetujui, 4=Ditolak',
  `komentarPenolakan` text DEFAULT NULL COMMENT 'Komentar jika LPJ ditolak',
  `komentarRevisi` text DEFAULT NULL COMMENT 'Komentar untuk revisi LPJ'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Accountability reports (Laporan Pertanggungjawaban)';

--
-- Dumping data for table `tbl_lpj`
--

INSERT INTO `tbl_lpj` (`lpjId`, `kegiatanId`, `grandTotalRealisasi`, `submittedAt`, `approvedAt`, `tenggatLpj`, `statusId`, `komentarPenolakan`, `komentarRevisi`) VALUES
(1, 4, NULL, NULL, NULL, '2026-02-05', 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_lpj_item`
--

DROP TABLE IF EXISTS `tbl_lpj_item`;
CREATE TABLE `tbl_lpj_item` (
  `lpjItemId` int(11) NOT NULL,
  `lpjId` int(11) NOT NULL,
  `kategoriId` int(11) DEFAULT NULL COMMENT 'FK to tbl_kategori_rab',
  `jenisBelanja` varchar(100) DEFAULT NULL COMMENT 'Jenis belanja/expense type',
  `uraian` text DEFAULT NULL COMMENT 'Deskripsi item',
  `rincian` text DEFAULT NULL COMMENT 'Rincian detail',
  `totalHarga` decimal(15,2) DEFAULT NULL COMMENT 'Total harga item',
  `realisasi` decimal(15,2) DEFAULT NULL COMMENT 'Nilai Realisasi',
  `subTotal` decimal(15,2) DEFAULT NULL COMMENT 'Subtotal',
  `fileBukti` varchar(255) DEFAULT NULL COMMENT 'Nama file bukti/evidence',
  `komentar` text DEFAULT NULL COMMENT 'Komentar untuk item',
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `sat1` varchar(50) DEFAULT NULL COMMENT 'Satuan 1',
  `sat2` varchar(50) DEFAULT NULL COMMENT 'Satuan 2',
  `vol1` decimal(10,2) DEFAULT NULL COMMENT 'Volume 1',
  `vol2` decimal(10,2) DEFAULT NULL COMMENT 'Volume 2',
  `harga` decimal(15,2) DEFAULT NULL COMMENT 'Harga Satuan (Plan)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Line items for accountability reports';

--
-- Dumping data for table `tbl_lpj_item`
--

INSERT INTO `tbl_lpj_item` (`lpjItemId`, `lpjId`, `kategoriId`, `jenisBelanja`, `uraian`, `rincian`, `totalHarga`, `realisasi`, `subTotal`, `fileBukti`, `komentar`, `createdAt`, `sat1`, `sat2`, `vol1`, `vol2`, `harga`) VALUES
(1, 1, 4, NULL, 'ada', 'adada', 100000.00, 100000.00, NULL, 'lpj_bukti_4_1765971768.png', NULL, '2025-12-17 11:42:48', '', '', 1.00, 1.00, 0.00);

--
-- Triggers `tbl_lpj_item`
--
DROP TRIGGER IF EXISTS `trg_lpj_item_calculate_total`;
DELIMITER $$
CREATE TRIGGER `trg_lpj_item_calculate_total` BEFORE INSERT ON `tbl_lpj_item` FOR EACH ROW BEGIN
    IF NEW.vol1 IS NOT NULL AND NEW.vol2 IS NOT NULL AND NEW.totalHarga IS NULL THEN
        SET NEW.totalHarga = NEW.vol1 * NEW.vol2;
    END IF;
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `trg_lpj_item_update_total`;
DELIMITER $$
CREATE TRIGGER `trg_lpj_item_update_total` BEFORE UPDATE ON `tbl_lpj_item` FOR EACH ROW BEGIN
    IF NEW.vol1 IS NOT NULL AND NEW.vol2 IS NOT NULL THEN
        SET NEW.totalHarga = NEW.vol1 * NEW.vol2;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_prodi`
--

DROP TABLE IF EXISTS `tbl_prodi`;
CREATE TABLE `tbl_prodi` (
  `namaProdi` varchar(50) NOT NULL,
  `namaJurusan` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Study programs under academic departments';

--
-- Dumping data for table `tbl_prodi`
--

INSERT INTO `tbl_prodi` (`namaProdi`, `namaJurusan`) VALUES
('D3 Administrasi Bisnis', 'Administrasi Niaga'),
('D4 Administrasi Bisnis Terapan', 'Administrasi Niaga'),
('D4 Bahasa Inggris untuk Komunikasi Bisnis dan Prof', 'Administrasi Niaga'),
('D4 Meeting, Incentive, Convention, and Exhibition ', 'Administrasi Niaga'),
('D3 Akuntansi', 'Akuntansi'),
('D3 Keuangan dan Perbankan', 'Akuntansi'),
('D4 Akuntansi Keuangan', 'Akuntansi'),
('D4 Keuangan dan Perbankan Syariah', 'Akuntansi'),
('D4 Manajemen Keuangan', 'Akuntansi'),
('S2 Magister Terapan Rekayasa Teknologi Manufaktur', 'Pascasarjana'),
('S2 Magister Terapan Teknik Elektro', 'Pascasarjana'),
('D3 Teknik Elektronika Industri', 'Teknik Elektro'),
('D3 Teknik Listrik', 'Teknik Elektro'),
('D3 Teknik Telekomunikasi', 'Teknik Elektro'),
('D4 Broadband Multimedia', 'Teknik Elektro'),
('D4 Teknik Instrumentasi dan Kontrol Industri', 'Teknik Elektro'),
('D4 Teknik Otomasi Listrik Industri', 'Teknik Elektro'),
('D3 Penerbitan (Jurnalistik)', 'Teknik Grafika dan Penerbitan'),
('D3 Teknik Grafika', 'Teknik Grafika dan Penerbitan'),
('D4 Desain Grafis', 'Teknik Grafika dan Penerbitan'),
('D4 Teknologi Industri Cetak Kemasan', 'Teknik Grafika dan Penerbitan'),
('D1 Teknik Komputer dan Jaringan', 'Teknik Informatika dan Komputer'),
('D4 Teknik Informatika', 'Teknik Informatika dan Komputer'),
('D4 Teknik Multimedia dan Jaringan', 'Teknik Informatika dan Komputer'),
('D4 Teknik Multimedia Digital', 'Teknik Informatika dan Komputer'),
('D3 Alat Berat', 'Teknik Mesin'),
('D3 Teknik Konversi Energi', 'Teknik Mesin'),
('D3 Teknik Mesin', 'Teknik Mesin'),
('D4 Pembangkit Tenaga Listrik', 'Teknik Mesin'),
('D4 Teknologi Rekayasa Konversi Energi', 'Teknik Mesin'),
('D4 Teknologi Rekayasa Manufaktur', 'Teknik Mesin'),
('D4 Teknologi Rekayasa Perawatan Alat Berat', 'Teknik Mesin'),
('D3 Konstruksi Gedung', 'Teknik Sipil'),
('D3 Konstruksi Sipil', 'Teknik Sipil'),
('D4 Manajemen Konstruksi', 'Teknik Sipil'),
('D4 Perancangan Jalan dan Jembatan', 'Teknik Sipil');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_progress_history`
--

DROP TABLE IF EXISTS `tbl_progress_history`;
CREATE TABLE `tbl_progress_history` (
  `progressHistoryId` int(11) NOT NULL,
  `kegiatanId` int(11) NOT NULL,
  `statusId` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `changedByUserId` int(11) DEFAULT NULL COMMENT 'User ID yang melakukan perubahan status'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='History of status changes for activities';

--
-- Dumping data for table `tbl_progress_history`
--

INSERT INTO `tbl_progress_history` (`progressHistoryId`, `kegiatanId`, `statusId`, `timestamp`, `changedByUserId`) VALUES
(1, 1, 2, '2025-12-17 06:57:49', 9),
(2, 2, 3, '2025-12-17 07:02:09', 9),
(3, 2, 1, '2025-12-17 07:03:23', 11),
(4, 2, 1, '2025-12-17 07:03:36', 10),
(5, 3, 2, '2025-12-17 08:32:50', 9),
(6, 3, 4, '2025-12-17 08:34:57', 9),
(7, 4, 3, '2025-12-17 11:12:45', 9),
(8, 4, 1, '2025-12-17 11:14:12', 11),
(9, 4, 1, '2025-12-17 11:14:27', 10),
(10, 4, 3, '2025-12-17 11:21:22', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_rab`
--

DROP TABLE IF EXISTS `tbl_rab`;
CREATE TABLE `tbl_rab` (
  `rabItemId` int(11) NOT NULL,
  `kakId` int(11) NOT NULL,
  `kategoriId` int(11) NOT NULL,
  `uraian` text DEFAULT NULL COMMENT 'Deskripsi item anggaran',
  `rincian` text DEFAULT NULL COMMENT 'Rincian detail item',
  `sat1` varchar(50) DEFAULT NULL COMMENT 'Satuan 1 (misal: orang, paket)',
  `sat2` varchar(50) DEFAULT NULL COMMENT 'Satuan 2 (misal: hari, bulan)',
  `vol1` decimal(10,2) NOT NULL COMMENT 'Volume 1',
  `vol2` decimal(10,2) NOT NULL COMMENT 'Volume 2',
  `harga` decimal(15,2) NOT NULL COMMENT 'Harga satuan',
  `totalHarga` decimal(15,2) DEFAULT NULL COMMENT 'Total harga item (vol1 * vol2 * harga)',
  `subtotal` decimal(15,2) DEFAULT NULL COMMENT 'Subtotal untuk kategori'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Budget plan items (Rencana Anggaran Biaya)';

--
-- Dumping data for table `tbl_rab`
--

INSERT INTO `tbl_rab` (`rabItemId`, `kakId`, `kategoriId`, `uraian`, `rincian`, `sat1`, `sat2`, `vol1`, `vol2`, `harga`, `totalHarga`, `subtotal`) VALUES
(1, 1, 4, 'asdfasdf', 'asdfasdf', '', '', 1.00, 1.00, 89999.00, 89999.00, NULL),
(2, 2, 4, 'asdf', 'asdf', '', '', 1.00, 1.00, 1000.00, 1000.00, NULL),
(3, 3, 4, 'weagheW', 'SDGG', 'bh', 'kali', 1.00, 1.00, 100000.00, 100000.00, NULL),
(4, 4, 4, 'ada', 'adada', '', '', 1.00, 1.00, 100000.00, 100000.00, NULL);

--
-- Triggers `tbl_rab`
--
DROP TRIGGER IF EXISTS `trg_rab_calculate_total`;
DELIMITER $$
CREATE TRIGGER `trg_rab_calculate_total` BEFORE INSERT ON `tbl_rab` FOR EACH ROW BEGIN
    SET NEW.totalHarga = NEW.vol1 * NEW.vol2 * NEW.harga;
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `trg_rab_update_total`;
DELIMITER $$
CREATE TRIGGER `trg_rab_update_total` BEFORE UPDATE ON `tbl_rab` FOR EACH ROW BEGIN
    SET NEW.totalHarga = NEW.vol1 * NEW.vol2 * NEW.harga;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_revisi_comment`
--

DROP TABLE IF EXISTS `tbl_revisi_comment`;
CREATE TABLE `tbl_revisi_comment` (
  `revisiCommentId` int(11) NOT NULL,
  `progressHistoryId` int(11) NOT NULL,
  `komentarRevisi` text DEFAULT NULL COMMENT 'Komentar revisi dari approver',
  `targetTabel` varchar(100) DEFAULT NULL COMMENT 'Target table yang perlu direvisi',
  `targetKolom` varchar(100) DEFAULT NULL COMMENT 'Target column yang perlu direvisi'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Revision comments for workflow feedback';

--
-- Dumping data for table `tbl_revisi_comment`
--

INSERT INTO `tbl_revisi_comment` (`revisiCommentId`, `progressHistoryId`, `komentarRevisi`, `targetTabel`, `targetKolom`) VALUES
(8, 6, 'kontolodon', 'tbl_kegiatan', 'statusUtamaId');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_role`
--

DROP TABLE IF EXISTS `tbl_role`;
CREATE TABLE `tbl_role` (
  `roleId` int(11) NOT NULL,
  `namaRole` varchar(50) NOT NULL,
  `urutan` tinyint(3) UNSIGNED DEFAULT NULL COMMENT 'Urutan dalam workflow (NULL jika bukan bagian workflow)',
  `deskripsi` varchar(200) DEFAULT NULL COMMENT 'Deskripsi peran dalam workflow'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Master table for user roles and workflow positions';

--
-- Dumping data for table `tbl_role`
--

INSERT INTO `tbl_role` (`roleId`, `namaRole`, `urutan`, `deskripsi`) VALUES
(1, 'Admin', 1, 'Posisi awal - Admin membuat/mengedit pengajuan'),
(2, 'Verifikator', 2, 'Verifikasi dokumen dan kelengkapan'),
(3, 'Wadir', 4, 'Wakil Direktur - approval tingkat direktur'),
(4, 'PPK', 3, 'Pejabat Pembuat Komitmen - approval anggaran'),
(5, 'Bendahara', 5, 'Pencairan dana'),
(6, 'Super Admin', NULL, 'Administrator sistem - tidak dalam workflow');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_status_utama`
--

DROP TABLE IF EXISTS `tbl_status_utama`;
CREATE TABLE `tbl_status_utama` (
  `statusId` int(11) NOT NULL,
  `namaStatusUsulan` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Master table for proposal status';

--
-- Dumping data for table `tbl_status_utama`
--

INSERT INTO `tbl_status_utama` (`statusId`, `namaStatusUsulan`) VALUES
(5, 'Dana diberikan'),
(3, 'Disetujui'),
(4, 'Ditolak'),
(1, 'Menunggu'),
(2, 'Revisi');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_tahapan_pelaksanaan`
--

DROP TABLE IF EXISTS `tbl_tahapan_pelaksanaan`;
CREATE TABLE `tbl_tahapan_pelaksanaan` (
  `tahapanId` int(11) NOT NULL,
  `kakId` int(11) NOT NULL,
  `namaTahapan` varchar(255) DEFAULT NULL COMMENT 'Nama tahapan pelaksanaan'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Implementation stages for activities';

--
-- Dumping data for table `tbl_tahapan_pelaksanaan`
--

INSERT INTO `tbl_tahapan_pelaksanaan` (`tahapanId`, `kakId`, `namaTahapan`) VALUES
(1, 1, 'raka '),
(2, 2, 'asdfa'),
(3, 3, 'asfwFWG'),
(4, 4, 'afafa');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_tahapan_pencairan`
--

DROP TABLE IF EXISTS `tbl_tahapan_pencairan`;
CREATE TABLE `tbl_tahapan_pencairan` (
  `tahapanId` int(11) NOT NULL,
  `idKegiatan` int(11) NOT NULL,
  `tglPencairan` date NOT NULL,
  `termin` varchar(100) NOT NULL COMMENT 'Nama termin (e.g. Termin 1, Tahap Awal)',
  `nominal` decimal(15,2) NOT NULL,
  `catatan` text DEFAULT NULL,
  `createdBy` int(11) DEFAULT NULL COMMENT 'User ID Bendahara',
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_tahapan_pencairan`
--

INSERT INTO `tbl_tahapan_pencairan` (`tahapanId`, `idKegiatan`, `tglPencairan`, `termin`, `nominal`, `catatan`, `createdBy`, `createdAt`) VALUES
(1, 4, '2026-01-13', '1', 50000.00, 'Kontol', 12, '2025-12-17 11:21:22'),
(2, 4, '2026-01-17', '2', 50000.00, 'Kontol', 12, '2025-12-17 11:21:22');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_user`
--

DROP TABLE IF EXISTS `tbl_user`;
CREATE TABLE `tbl_user` (
  `userId` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `roleId` int(11) NOT NULL,
  `namaJurusan` varchar(50) DEFAULT NULL COMMENT 'Departemen untuk Admin, NULL untuk peran lain',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('Aktif','Tidak Aktif') DEFAULT 'Aktif'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='System users with role-based access control';

--
-- Dumping data for table `tbl_user`
--

INSERT INTO `tbl_user` (`userId`, `nama`, `email`, `password`, `roleId`, `namaJurusan`, `created_at`, `status`) VALUES
(1, 'Admin TI', 'adminti@gmail.com', '$2y$10$IkVUO5T3qyIF3TO2f0amnOhvQSoVGXJfxOOs5iT5/Axz/Lzjy8ZBi', 1, 'Teknik Informatika dan Komputer', '2025-12-14 03:30:47', 'Aktif'),
(2, 'Admin Teknik Elektro', 'adminelektro@gmail.com', '$2y$10$IkVUO5T3qyIF3TO2f0amnOhvQSoVGXJfxOOs5iT5/Axz/Lzjy8ZBi', 1, 'Teknik Elektro', '2025-12-14 03:30:47', 'Aktif'),
(3, 'Admin Teknik Sipil', 'adminsipil@gmail.com', '$2y$10$IkVUO5T3qyIF3TO2f0amnOhvQSoVGXJfxOOs5iT5/Axz/Lzjy8ZBi', 1, 'Teknik Sipil', '2025-12-14 03:30:47', 'Aktif'),
(4, 'Admin Teknik Mesin', 'adminmesin@gmail.com', '$2y$10$IkVUO5T3qyIF3TO2f0amnOhvQSoVGXJfxOOs5iT5/Axz/Lzjy8ZBi', 1, 'Teknik Mesin', '2025-12-14 03:30:47', 'Aktif'),
(5, 'Admin Grafika dan Penerbitan', 'admintgp@gmail.com', '$2y$10$IkVUO5T3qyIF3TO2f0amnOhvQSoVGXJfxOOs5iT5/Axz/Lzjy8ZBi', 1, 'Teknik Grafika dan Penerbitan', '2025-12-14 03:30:47', 'Aktif'),
(6, 'Admin Akuntansi', 'adminakt@gmail.com', '$2y$10$IkVUO5T3qyIF3TO2f0amnOhvQSoVGXJfxOOs5iT5/Axz/Lzjy8ZBi', 1, 'Akuntansi', '2025-12-14 03:30:47', 'Aktif'),
(7, 'Admin Administrasi Niaga', 'adminan@gmail.com', '$2y$10$IkVUO5T3qyIF3TO2f0amnOhvQSoVGXJfxOOs5iT5/Axz/Lzjy8ZBi', 1, 'Administrasi Niaga', '2025-12-14 03:30:47', 'Aktif'),
(8, 'Admin Pascasarjana', 'adminpasca@gmail.com', '$2y$10$IkVUO5T3qyIF3TO2f0amnOhvQSoVGXJfxOOs5iT5/Axz/Lzjy8ZBi', 1, 'Pascasarjana', '2025-12-14 03:30:47', 'Aktif'),
(9, 'Verifikator', 'verifikator@gmail.com', '$2y$10$IkVUO5T3qyIF3TO2f0amnOhvQSoVGXJfxOOs5iT5/Axz/Lzjy8ZBi', 2, NULL, '2025-12-14 03:30:47', 'Aktif'),
(10, 'Wakil Direktur', 'wadir@gmail.com', '$2y$10$IkVUO5T3qyIF3TO2f0amnOhvQSoVGXJfxOOs5iT5/Axz/Lzjy8ZBi', 3, NULL, '2025-12-14 03:30:47', 'Aktif'),
(11, 'PPK', 'ppk@gmail.com', '$2y$10$IkVUO5T3qyIF3TO2f0amnOhvQSoVGXJfxOOs5iT5/Axz/Lzjy8ZBi', 4, NULL, '2025-12-14 03:30:47', 'Aktif'),
(12, 'Bendahara', 'bendahara@gmail.com', '$2y$10$IkVUO5T3qyIF3TO2f0amnOhvQSoVGXJfxOOs5iT5/Axz/Lzjy8ZBi', 5, NULL, '2025-12-14 03:30:47', 'Aktif'),
(13, 'Super Admin', 'superadmin@gmail.com', '$2y$10$IkVUO5T3qyIF3TO2f0amnOhvQSoVGXJfxOOs5iT5/Axz/Lzjy8ZBi', 6, NULL, '2025-12-14 03:30:47', 'Aktif');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_wadir`
--

DROP TABLE IF EXISTS `tbl_wadir`;
CREATE TABLE `tbl_wadir` (
  `wadirId` int(11) NOT NULL,
  `namaWadir` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Deputy directors for approval routing';

--
-- Dumping data for table `tbl_wadir`
--

INSERT INTO `tbl_wadir` (`wadirId`, `namaWadir`) VALUES
(1, 'Wadir 1'),
(2, 'Wadir 2'),
(3, 'Wadir 3'),
(4, 'Wadir 4');

-- --------------------------------------------------------

--
-- Stand-in structure for view `vw_kegiatan_detail`
-- (See below for the actual view)
--
DROP VIEW IF EXISTS `vw_kegiatan_detail`;
CREATE TABLE `vw_kegiatan_detail` (
`kegiatanId` int(11)
,`namaKegiatan` varchar(255)
,`prodiPenyelenggara` varchar(50)
,`pemilikKegiatan` varchar(150)
,`nimPelaksana` varchar(20)
,`nip` varchar(30)
,`namaPJ` varchar(100)
,`danaDiCairkan` decimal(15,2)
,`buktiMAK` varchar(255)
,`userId` int(11)
,`jurusanPenyelenggara` varchar(50)
,`statusUtamaId` int(11)
,`createdAt` timestamp
,`uploadAt` timestamp
,`wadirTujuan` int(11)
,`suratPengantar` varchar(255)
,`tanggalMulai` date
,`tanggalSelesai` date
,`posisiId` int(11)
,`tanggalPencairan` datetime
,`jumlahDicairkan` decimal(15,2)
,`metodePencairan` varchar(50)
,`catatanBendahara` text
,`pencairan_tahap_json` text
,`umpanBalikVerifikator` text
,`admin_nama` varchar(100)
,`admin_email` varchar(150)
,`status_nama` varchar(100)
,`posisi_nama` varchar(50)
,`wadir_nama` varchar(20)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `vw_lpj_status`
-- (See below for the actual view)
--
DROP VIEW IF EXISTS `vw_lpj_status`;
CREATE TABLE `vw_lpj_status` (
`lpjId` int(11)
,`kegiatanId` int(11)
,`grandTotalRealisasi` decimal(15,2)
,`submittedAt` timestamp
,`approvedAt` timestamp
,`tenggatLpj` date
,`statusId` int(11)
,`komentarPenolakan` text
,`komentarRevisi` text
,`namaKegiatan` varchar(255)
,`jurusanPenyelenggara` varchar(50)
,`prodiPenyelenggara` varchar(50)
,`status_lpj` varchar(100)
,`deadline_status` varchar(9)
);

-- --------------------------------------------------------

--
-- Structure for view `vw_kegiatan_detail`
--
DROP TABLE IF EXISTS `vw_kegiatan_detail`;

DROP VIEW IF EXISTS `vw_kegiatan_detail`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_kegiatan_detail`  AS SELECT `k`.`kegiatanId` AS `kegiatanId`, `k`.`namaKegiatan` AS `namaKegiatan`, `k`.`prodiPenyelenggara` AS `prodiPenyelenggara`, `k`.`pemilikKegiatan` AS `pemilikKegiatan`, `k`.`nimPelaksana` AS `nimPelaksana`, `k`.`nip` AS `nip`, `k`.`namaPJ` AS `namaPJ`, `k`.`danaDiCairkan` AS `danaDiCairkan`, `k`.`buktiMAK` AS `buktiMAK`, `k`.`userId` AS `userId`, `k`.`jurusanPenyelenggara` AS `jurusanPenyelenggara`, `k`.`statusUtamaId` AS `statusUtamaId`, `k`.`createdAt` AS `createdAt`, `k`.`uploadAt` AS `uploadAt`, `k`.`wadirTujuan` AS `wadirTujuan`, `k`.`suratPengantar` AS `suratPengantar`, `k`.`tanggalMulai` AS `tanggalMulai`, `k`.`tanggalSelesai` AS `tanggalSelesai`, `k`.`posisiId` AS `posisiId`, `k`.`tanggalPencairan` AS `tanggalPencairan`, `k`.`jumlahDicairkan` AS `jumlahDicairkan`, `k`.`metodePencairan` AS `metodePencairan`, `k`.`catatanBendahara` AS `catatanBendahara`, `k`.`pencairan_tahap_json` AS `pencairan_tahap_json`, `k`.`umpanBalikVerifikator` AS `umpanBalikVerifikator`, `u`.`nama` AS `admin_nama`, `u`.`email` AS `admin_email`, `s`.`namaStatusUsulan` AS `status_nama`, `r`.`namaRole` AS `posisi_nama`, `w`.`namaWadir` AS `wadir_nama` FROM ((((`tbl_kegiatan` `k` left join `tbl_user` `u` on(`k`.`userId` = `u`.`userId`)) left join `tbl_status_utama` `s` on(`k`.`statusUtamaId` = `s`.`statusId`)) left join `tbl_role` `r` on(`k`.`posisiId` = `r`.`roleId`)) left join `tbl_wadir` `w` on(`k`.`wadirTujuan` = `w`.`wadirId`)) ;

-- --------------------------------------------------------

--
-- Structure for view `vw_lpj_status`
--
DROP TABLE IF EXISTS `vw_lpj_status`;

DROP VIEW IF EXISTS `vw_lpj_status`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_lpj_status`  AS SELECT `l`.`lpjId` AS `lpjId`, `l`.`kegiatanId` AS `kegiatanId`, `l`.`grandTotalRealisasi` AS `grandTotalRealisasi`, `l`.`submittedAt` AS `submittedAt`, `l`.`approvedAt` AS `approvedAt`, `l`.`tenggatLpj` AS `tenggatLpj`, `l`.`statusId` AS `statusId`, `l`.`komentarPenolakan` AS `komentarPenolakan`, `l`.`komentarRevisi` AS `komentarRevisi`, `k`.`namaKegiatan` AS `namaKegiatan`, `k`.`jurusanPenyelenggara` AS `jurusanPenyelenggara`, `k`.`prodiPenyelenggara` AS `prodiPenyelenggara`, `s`.`namaStatusUsulan` AS `status_lpj`, CASE WHEN `l`.`tenggatLpj` < curdate() AND `l`.`statusId` = 1 THEN 'OVERDUE' WHEN `l`.`tenggatLpj` = curdate() AND `l`.`statusId` = 1 THEN 'DUE_TODAY' WHEN `l`.`tenggatLpj` > curdate() AND `l`.`statusId` = 1 THEN 'PENDING' ELSE 'COMPLETED' END AS `deadline_status` FROM ((`tbl_lpj` `l` join `tbl_kegiatan` `k` on(`l`.`kegiatanId` = `k`.`kegiatanId`)) left join `tbl_status_utama` `s` on(`l`.`statusId` = `s`.`statusId`)) ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `ai_log_summaries`
--
ALTER TABLE `ai_log_summaries`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ai_security_alerts`
--
ALTER TABLE `ai_security_alerts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_ai_security_alerts_created_at` (`created_at`);

--
-- Indexes for table `tbl_activity_logs`
--
ALTER TABLE `tbl_activity_logs`
  ADD PRIMARY KEY (`logId`),
  ADD KEY `idx_user_action` (`userId`,`action`),
  ADD KEY `idx_category` (`category`),
  ADD KEY `idx_entity` (`entityType`,`entityId`),
  ADD KEY `idx_created_at` (`createdAt`),
  ADD KEY `idx_action` (`action`);

--
-- Indexes for table `tbl_iku`
--
ALTER TABLE `tbl_iku`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_indikator_kak`
--
ALTER TABLE `tbl_indikator_kak`
  ADD PRIMARY KEY (`indikatorId`),
  ADD KEY `fk_indikator_kak` (`kakId`);

--
-- Indexes for table `tbl_jurusan`
--
ALTER TABLE `tbl_jurusan`
  ADD PRIMARY KEY (`namaJurusan`);

--
-- Indexes for table `tbl_kak`
--
ALTER TABLE `tbl_kak`
  ADD PRIMARY KEY (`kakId`),
  ADD KEY `fk_kak_kegiatan` (`kegiatanId`);

--
-- Indexes for table `tbl_kategori_rab`
--
ALTER TABLE `tbl_kategori_rab`
  ADD PRIMARY KEY (`kategoriRabId`);

--
-- Indexes for table `tbl_kegiatan`
--
ALTER TABLE `tbl_kegiatan`
  ADD PRIMARY KEY (`kegiatanId`),
  ADD KEY `idx_nimPelaksana` (`nimPelaksana`),
  ADD KEY `idx_nip` (`nip`),
  ADD KEY `fk_kegiatan_user` (`userId`),
  ADD KEY `fk_status_kegiatan` (`statusUtamaId`),
  ADD KEY `fk_kegiatan_jurusan` (`jurusanPenyelenggara`),
  ADD KEY `fk_wadir` (`wadirTujuan`),
  ADD KEY `idx_posisi` (`posisiId`),
  ADD KEY `idx_status` (`statusUtamaId`),
  ADD KEY `idx_created_at` (`createdAt`),
  ADD KEY `idx_tanggal_pencairan` (`tanggalPencairan`),
  ADD KEY `idx_workflow_position` (`posisiId`,`statusUtamaId`,`createdAt`),
  ADD KEY `idx_user_status` (`userId`,`statusUtamaId`),
  ADD KEY `idx_jurusan_status` (`jurusanPenyelenggara`,`statusUtamaId`);

--
-- Indexes for table `tbl_log_status`
--
ALTER TABLE `tbl_log_status`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_log_user` (`user_id`),
  ADD KEY `idx_user_status` (`user_id`,`status`),
  ADD KEY `idx_tipe_log` (`tipe_log`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `tbl_lpj`
--
ALTER TABLE `tbl_lpj`
  ADD PRIMARY KEY (`lpjId`),
  ADD UNIQUE KEY `idx_kegiatan_lpj` (`kegiatanId`),
  ADD KEY `fk_lpj_kegiatan` (`kegiatanId`),
  ADD KEY `fk_lpj_status` (`statusId`),
  ADD KEY `idx_status_tengat` (`statusId`,`tenggatLpj`);

--
-- Indexes for table `tbl_lpj_item`
--
ALTER TABLE `tbl_lpj_item`
  ADD PRIMARY KEY (`lpjItemId`),
  ADD KEY `fk_lpj_item_lpj` (`lpjId`),
  ADD KEY `fk_lpj_item_kategori` (`kategoriId`);

--
-- Indexes for table `tbl_prodi`
--
ALTER TABLE `tbl_prodi`
  ADD PRIMARY KEY (`namaProdi`),
  ADD KEY `fk_prodi_jurusan` (`namaJurusan`);

--
-- Indexes for table `tbl_progress_history`
--
ALTER TABLE `tbl_progress_history`
  ADD PRIMARY KEY (`progressHistoryId`),
  ADD KEY `fk_history_kegiatan` (`kegiatanId`),
  ADD KEY `fk_history_status` (`statusId`),
  ADD KEY `fk_history_user` (`changedByUserId`),
  ADD KEY `idx_timestamp` (`timestamp`),
  ADD KEY `idx_kegiatan_timestamp` (`kegiatanId`,`timestamp`);

--
-- Indexes for table `tbl_rab`
--
ALTER TABLE `tbl_rab`
  ADD PRIMARY KEY (`rabItemId`),
  ADD KEY `fk_rab_kak` (`kakId`),
  ADD KEY `fk_rab_kategori` (`kategoriId`);

--
-- Indexes for table `tbl_revisi_comment`
--
ALTER TABLE `tbl_revisi_comment`
  ADD PRIMARY KEY (`revisiCommentId`),
  ADD KEY `fk_comment_history` (`progressHistoryId`);

--
-- Indexes for table `tbl_role`
--
ALTER TABLE `tbl_role`
  ADD PRIMARY KEY (`roleId`),
  ADD UNIQUE KEY `idx_namaRole` (`namaRole`),
  ADD KEY `idx_urutan` (`urutan`);

--
-- Indexes for table `tbl_status_utama`
--
ALTER TABLE `tbl_status_utama`
  ADD PRIMARY KEY (`statusId`),
  ADD UNIQUE KEY `idx_namaStatus` (`namaStatusUsulan`);

--
-- Indexes for table `tbl_tahapan_pelaksanaan`
--
ALTER TABLE `tbl_tahapan_pelaksanaan`
  ADD PRIMARY KEY (`tahapanId`),
  ADD KEY `fk_tahapan_kak` (`kakId`);

--
-- Indexes for table `tbl_tahapan_pencairan`
--
ALTER TABLE `tbl_tahapan_pencairan`
  ADD PRIMARY KEY (`tahapanId`),
  ADD KEY `fk_tahapan_kegiatan` (`idKegiatan`);

--
-- Indexes for table `tbl_user`
--
ALTER TABLE `tbl_user`
  ADD PRIMARY KEY (`userId`),
  ADD UNIQUE KEY `idx_email` (`email`),
  ADD KEY `fk_user_role` (`roleId`),
  ADD KEY `fk_user_jurusan` (`namaJurusan`);

--
-- Indexes for table `tbl_wadir`
--
ALTER TABLE `tbl_wadir`
  ADD PRIMARY KEY (`wadirId`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `ai_log_summaries`
--
ALTER TABLE `ai_log_summaries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `ai_security_alerts`
--
ALTER TABLE `ai_security_alerts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_activity_logs`
--
ALTER TABLE `tbl_activity_logs`
  MODIFY `logId` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=108;

--
-- AUTO_INCREMENT for table `tbl_iku`
--
ALTER TABLE `tbl_iku`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_indikator_kak`
--
ALTER TABLE `tbl_indikator_kak`
  MODIFY `indikatorId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tbl_kak`
--
ALTER TABLE `tbl_kak`
  MODIFY `kakId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tbl_kategori_rab`
--
ALTER TABLE `tbl_kategori_rab`
  MODIFY `kategoriRabId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `tbl_kegiatan`
--
ALTER TABLE `tbl_kegiatan`
  MODIFY `kegiatanId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tbl_log_status`
--
ALTER TABLE `tbl_log_status`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_lpj`
--
ALTER TABLE `tbl_lpj`
  MODIFY `lpjId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tbl_lpj_item`
--
ALTER TABLE `tbl_lpj_item`
  MODIFY `lpjItemId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tbl_progress_history`
--
ALTER TABLE `tbl_progress_history`
  MODIFY `progressHistoryId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `tbl_rab`
--
ALTER TABLE `tbl_rab`
  MODIFY `rabItemId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tbl_revisi_comment`
--
ALTER TABLE `tbl_revisi_comment`
  MODIFY `revisiCommentId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `tbl_role`
--
ALTER TABLE `tbl_role`
  MODIFY `roleId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `tbl_tahapan_pelaksanaan`
--
ALTER TABLE `tbl_tahapan_pelaksanaan`
  MODIFY `tahapanId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tbl_tahapan_pencairan`
--
ALTER TABLE `tbl_tahapan_pencairan`
  MODIFY `tahapanId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tbl_user`
--
ALTER TABLE `tbl_user`
  MODIFY `userId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tbl_indikator_kak`
--
ALTER TABLE `tbl_indikator_kak`
  ADD CONSTRAINT `fk_indikator_kak` FOREIGN KEY (`kakId`) REFERENCES `tbl_kak` (`kakId`) ON DELETE CASCADE;

--
-- Constraints for table `tbl_kak`
--
ALTER TABLE `tbl_kak`
  ADD CONSTRAINT `fk_kak_kegiatan` FOREIGN KEY (`kegiatanId`) REFERENCES `tbl_kegiatan` (`kegiatanId`) ON DELETE CASCADE;

--
-- Constraints for table `tbl_kegiatan`
--
ALTER TABLE `tbl_kegiatan`
  ADD CONSTRAINT `fk_kegiatan_jurusan` FOREIGN KEY (`jurusanPenyelenggara`) REFERENCES `tbl_jurusan` (`namaJurusan`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_kegiatan_user` FOREIGN KEY (`userId`) REFERENCES `tbl_user` (`userId`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_status_kegiatan` FOREIGN KEY (`statusUtamaId`) REFERENCES `tbl_status_utama` (`statusId`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_wadir` FOREIGN KEY (`wadirTujuan`) REFERENCES `tbl_wadir` (`wadirId`) ON UPDATE CASCADE;

--
-- Constraints for table `tbl_log_status`
--
ALTER TABLE `tbl_log_status`
  ADD CONSTRAINT `fk_log_user` FOREIGN KEY (`user_id`) REFERENCES `tbl_user` (`userId`) ON DELETE CASCADE;

--
-- Constraints for table `tbl_lpj`
--
ALTER TABLE `tbl_lpj`
  ADD CONSTRAINT `fk_lpj_kegiatan` FOREIGN KEY (`kegiatanId`) REFERENCES `tbl_kegiatan` (`kegiatanId`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_lpj_status` FOREIGN KEY (`statusId`) REFERENCES `tbl_status_utama` (`statusId`) ON UPDATE CASCADE;

--
-- Constraints for table `tbl_lpj_item`
--
ALTER TABLE `tbl_lpj_item`
  ADD CONSTRAINT `fk_lpj_item_kategori` FOREIGN KEY (`kategoriId`) REFERENCES `tbl_kategori_rab` (`kategoriRabId`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_lpj_item_lpj` FOREIGN KEY (`lpjId`) REFERENCES `tbl_lpj` (`lpjId`) ON DELETE CASCADE;

--
-- Constraints for table `tbl_prodi`
--
ALTER TABLE `tbl_prodi`
  ADD CONSTRAINT `fk_prodi_jurusan` FOREIGN KEY (`namaJurusan`) REFERENCES `tbl_jurusan` (`namaJurusan`) ON UPDATE CASCADE;

--
-- Constraints for table `tbl_progress_history`
--
ALTER TABLE `tbl_progress_history`
  ADD CONSTRAINT `fk_history_kegiatan` FOREIGN KEY (`kegiatanId`) REFERENCES `tbl_kegiatan` (`kegiatanId`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_history_status` FOREIGN KEY (`statusId`) REFERENCES `tbl_status_utama` (`statusId`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_history_user` FOREIGN KEY (`changedByUserId`) REFERENCES `tbl_user` (`userId`) ON UPDATE CASCADE;

--
-- Constraints for table `tbl_rab`
--
ALTER TABLE `tbl_rab`
  ADD CONSTRAINT `fk_rab_kak` FOREIGN KEY (`kakId`) REFERENCES `tbl_kak` (`kakId`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_rab_kategori` FOREIGN KEY (`kategoriId`) REFERENCES `tbl_kategori_rab` (`kategoriRabId`) ON UPDATE CASCADE;

--
-- Constraints for table `tbl_revisi_comment`
--
ALTER TABLE `tbl_revisi_comment`
  ADD CONSTRAINT `fk_comment_history` FOREIGN KEY (`progressHistoryId`) REFERENCES `tbl_progress_history` (`progressHistoryId`) ON DELETE CASCADE;

--
-- Constraints for table `tbl_tahapan_pelaksanaan`
--
ALTER TABLE `tbl_tahapan_pelaksanaan`
  ADD CONSTRAINT `fk_tahapan_kak` FOREIGN KEY (`kakId`) REFERENCES `tbl_kak` (`kakId`) ON DELETE CASCADE;

--
-- Constraints for table `tbl_tahapan_pencairan`
--
ALTER TABLE `tbl_tahapan_pencairan`
  ADD CONSTRAINT `fk_tahapan_kegiatan` FOREIGN KEY (`idKegiatan`) REFERENCES `tbl_kegiatan` (`kegiatanId`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tbl_user`
--
ALTER TABLE `tbl_user`
  ADD CONSTRAINT `fk_user_jurusan` FOREIGN KEY (`namaJurusan`) REFERENCES `tbl_jurusan` (`namaJurusan`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_user_role` FOREIGN KEY (`roleId`) REFERENCES `tbl_role` (`roleId`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
