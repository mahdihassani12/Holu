-- FlowBook clean database - 03 October 2026
-- Import into an EMPTY database. This file does not erase an existing database.
-- All 28 table definitions and indexes are preserved.
-- Only Mahdi Hassani (user ID 2) and his active system permissions are retained.
-- His password hash is unchanged. Last activity and references to removed creators are reset.
-- Old province/category/cash permissions and all other records are removed.
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET NAMES utf8mb4;
SET time_zone = "+00:00";


CREATE TABLE `accessibilities` (
  `id` int(11) NOT NULL,
  `insertion_date` varchar(45) NOT NULL,
  `insertion_time` varchar(45) NOT NULL,
  `users_id` int(11) NOT NULL,
  `deleted` int(11) NOT NULL DEFAULT 0,
  `system_users_id` int(11) NOT NULL,
  `access_point` varchar(300) NOT NULL,
  `is_accessed` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

CREATE TABLE `additional_informations` (
  `id` int(11) NOT NULL,
  `insertion_date` varchar(45) NOT NULL,
  `insertion_time` varchar(45) NOT NULL,
  `users_id` int(11) NOT NULL,
  `deleted` int(11) NOT NULL DEFAULT 0,
  `reference_type` varchar(45) NOT NULL,
  `reference_id` int(11) NOT NULL,
  `key_info` varchar(45) NOT NULL,
  `value_info` varchar(90) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

CREATE TABLE `additional_information_items` (
  `id` int(11) NOT NULL,
  `deleted` int(11) NOT NULL DEFAULT 0,
  `name` varchar(45) NOT NULL DEFAULT '',
  `label` varchar(45) NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

CREATE TABLE `attachments` (
  `id` int(11) NOT NULL,
  `insertion_date` varchar(45) NOT NULL,
  `insertion_time` varchar(45) NOT NULL,
  `users_id` int(11) NOT NULL,
  `deleted` int(11) DEFAULT 0,
  `type` varchar(45) NOT NULL,
  `reference_id` int(11) NOT NULL,
  `path` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

CREATE TABLE `backups` (
  `id` int(11) NOT NULL,
  `type` varchar(15) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `path` varchar(500) NOT NULL,
  `size` bigint(20) NOT NULL,
  `status` varchar(15) NOT NULL DEFAULT 'completed',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

CREATE TABLE `branches` (
  `id` int(11) NOT NULL,
  `name` text NOT NULL,
  `abbreviation` varchar(10) DEFAULT NULL,
  `province_id` int(11) NOT NULL,
  `users_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `cash_reservations` (
  `id` int(11) NOT NULL,
  `insertion_date` varchar(45) NOT NULL,
  `insertion_time` varchar(45) NOT NULL,
  `users_id` int(11) NOT NULL,
  `deleted` int(11) NOT NULL DEFAULT 0,
  `logistic_cashes_id` int(11) NOT NULL,
  `reservation_date` varchar(45) NOT NULL,
  `reservation_amount` int(11) NOT NULL,
  `currency` varchar(45) NOT NULL,
  `description` text NOT NULL,
  `is_approved` int(11) NOT NULL DEFAULT 0,
  `approve_date` varchar(45) DEFAULT NULL,
  `approve_time` varchar(45) DEFAULT NULL,
  `approved_by` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `insertion_date` varchar(45) NOT NULL,
  `insertion_time` varchar(45) NOT NULL,
  `users_id` int(11) NOT NULL,
  `deleted` int(11) NOT NULL DEFAULT 0,
  `category_type` varchar(45) NOT NULL,
  `category_name` varchar(45) NOT NULL,
  `description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

CREATE TABLE `exchanges` (
  `id` int(11) NOT NULL,
  `insertion_date` varchar(45) NOT NULL,
  `insertion_time` varchar(45) NOT NULL,
  `users_id` int(11) NOT NULL,
  `deleted` int(11) NOT NULL DEFAULT 0,
  `province` varchar(45) NOT NULL,
  `branch` varchar(100) DEFAULT NULL,
  `exchange_date` varchar(45) NOT NULL,
  `from_amount` decimal(11,2) NOT NULL,
  `from_currency` varchar(45) NOT NULL,
  `to_amount` decimal(11,2) NOT NULL,
  `to_currency` varchar(45) NOT NULL,
  `description` text NOT NULL,
  `tms_markup` int(11) NOT NULL DEFAULT 0,
  `qb_markup` int(11) NOT NULL DEFAULT 0,
  `sib_markup` int(11) NOT NULL DEFAULT 0,
  `ad_markup` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

CREATE TABLE `expenses` (
  `id` int(11) NOT NULL,
  `insertion_date` varchar(45) NOT NULL,
  `insertion_time` varchar(45) NOT NULL,
  `users_id` int(11) NOT NULL,
  `deleted` int(11) NOT NULL DEFAULT 0,
  `province` varchar(45) NOT NULL,
  `branch` varchar(100) DEFAULT NULL,
  `sub_categories_id` int(11) NOT NULL,
  `expense_date` varchar(45) NOT NULL,
  `expense_amount` decimal(11,2) NOT NULL,
  `currency` varchar(45) NOT NULL,
  `check_number` varchar(45) NOT NULL,
  `description` text NOT NULL,
  `additional_informations` text DEFAULT NULL,
  `tms_markup` int(11) NOT NULL DEFAULT 0,
  `qb_markup` int(11) NOT NULL DEFAULT 0,
  `sib_markup` int(11) NOT NULL DEFAULT 0,
  `ad_markup` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

CREATE TABLE `hr_employees` (
  `id` int(11) NOT NULL,
  `insertion_date` varchar(45) NOT NULL DEFAULT '0000-00-00',
  `insertion_time` varchar(45) NOT NULL DEFAULT '00:00:00',
  `users_id` int(11) NOT NULL DEFAULT 0,
  `deleted` int(11) NOT NULL DEFAULT 0,
  `id_number` varchar(45) NOT NULL DEFAULT '',
  `picture_address` varchar(255) NOT NULL DEFAULT '',
  `first_name` varchar(45) NOT NULL DEFAULT '',
  `last_name` varchar(45) NOT NULL DEFAULT '',
  `father_name` varchar(45) NOT NULL DEFAULT '',
  `birth_date` varchar(45) NOT NULL DEFAULT '',
  `tazkira_number` varchar(45) NOT NULL DEFAULT '',
  `tazkira_safe` varchar(45) NOT NULL DEFAULT '',
  `tazkira_book` varchar(45) NOT NULL DEFAULT '',
  `phone_number` varchar(100) NOT NULL DEFAULT '',
  `family_phone_number` varchar(45) NOT NULL DEFAULT '',
  `email` varchar(45) NOT NULL DEFAULT '',
  `main_location_province` varchar(45) NOT NULL DEFAULT '',
  `main_location_address` varchar(45) NOT NULL DEFAULT '',
  `current_location_province` varchar(45) NOT NULL DEFAULT '',
  `current_location_address` varchar(45) NOT NULL DEFAULT '',
  `marital_status` varchar(45) NOT NULL DEFAULT '',
  `gender` varchar(45) NOT NULL DEFAULT '',
  `education` varchar(45) NOT NULL DEFAULT '',
  `experience` varchar(45) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

CREATE TABLE `incomes` (
  `id` int(11) NOT NULL,
  `insertion_date` varchar(45) NOT NULL,
  `insertion_time` varchar(45) NOT NULL,
  `users_id` int(11) NOT NULL,
  `deleted` int(11) NOT NULL DEFAULT 0,
  `province` varchar(45) NOT NULL,
  `branch` varchar(100) DEFAULT NULL,
  `sub_categories_id` int(11) NOT NULL,
  `income_date` varchar(45) NOT NULL,
  `income_amount` decimal(11,2) NOT NULL,
  `currency` varchar(45) NOT NULL,
  `check_number` varchar(45) NOT NULL,
  `description` text NOT NULL,
  `sib_number` varchar(45) DEFAULT NULL,
  `accounting_note` text DEFAULT NULL,
  `additional_informations` text DEFAULT NULL,
  `tms_markup` int(11) NOT NULL DEFAULT 0,
  `qb_markup` int(11) NOT NULL DEFAULT 0,
  `sib_markup` int(11) NOT NULL DEFAULT 0,
  `ad_markup` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

CREATE TABLE `income_request_approvals` (
  `id` int(11) NOT NULL,
  `insertion_date` varchar(45) NOT NULL,
  `insertion_time` varchar(45) NOT NULL,
  `users_id` int(11) NOT NULL,
  `deleted` int(11) NOT NULL DEFAULT 0,
  `tms_customers_id` int(11) NOT NULL DEFAULT 0,
  `request_type` varchar(45) NOT NULL DEFAULT '',
  `incomes_id` int(11) NOT NULL DEFAULT 0,
  `is_approved_s` int(11) NOT NULL DEFAULT 0,
  `approve_date_s` varchar(45) NOT NULL DEFAULT '',
  `approved_by_s` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

CREATE TABLE `invoices` (
  `id` int(11) NOT NULL,
  `insertion_date` varchar(45) NOT NULL,
  `insertion_time` varchar(45) NOT NULL,
  `users_id` int(11) NOT NULL,
  `deleted` int(11) DEFAULT 0,
  `reference_type` varchar(45) NOT NULL,
  `reference_id` int(11) NOT NULL,
  `province` varchar(45) NOT NULL,
  `branch` varchar(100) DEFAULT NULL,
  `is_printed` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

CREATE TABLE `logistic_cashes` (
  `id` int(11) NOT NULL,
  `insertion_date` varchar(45) NOT NULL,
  `insertion_time` varchar(45) NOT NULL,
  `users_id` int(11) NOT NULL,
  `deleted` int(11) NOT NULL DEFAULT 0,
  `name` varchar(45) NOT NULL,
  `description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

CREATE TABLE `markups` (
  `id` int(11) NOT NULL,
  `insertion_date` varchar(45) NOT NULL,
  `insertion_time` varchar(45) NOT NULL,
  `users_id` int(11) NOT NULL,
  `deleted` int(11) DEFAULT 0,
  `reference_type` varchar(45) NOT NULL,
  `reference_id` int(11) NOT NULL,
  `markup_type` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

CREATE TABLE `provinces` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `abbreviation` varchar(10) DEFAULT NULL,
  `users_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `purchases` (
  `id` int(11) NOT NULL,
  `insertion_date` varchar(45) NOT NULL,
  `insertion_time` varchar(45) NOT NULL,
  `users_id` int(11) NOT NULL,
  `deleted` int(11) DEFAULT 0,
  `logistic_cashes_id` int(11) NOT NULL DEFAULT 0,
  `province` varchar(45) NOT NULL,
  `branch` varchar(100) DEFAULT NULL,
  `sub_categories_id` int(11) NOT NULL DEFAULT 0,
  `purchase_date` varchar(45) NOT NULL,
  `purchase_amount` decimal(11,2) NOT NULL,
  `currency` varchar(45) NOT NULL,
  `check_number` varchar(45) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `tms_markup` int(11) NOT NULL DEFAULT 0,
  `qb_markup` int(11) NOT NULL DEFAULT 0,
  `sib_markup` int(11) NOT NULL DEFAULT 0,
  `ad_markup` int(11) NOT NULL DEFAULT 0,
  `is_approved` int(11) NOT NULL DEFAULT 0,
  `approve_date` varchar(45) DEFAULT NULL,
  `approve_time` varchar(45) DEFAULT NULL,
  `approve_description` text DEFAULT NULL,
  `approved_by` int(11) NOT NULL DEFAULT 0,
  `is_included` int(11) NOT NULL DEFAULT 0,
  `include_date` varchar(45) DEFAULT NULL,
  `include_time` varchar(45) DEFAULT NULL,
  `include_description` text DEFAULT NULL,
  `included_by` int(11) NOT NULL DEFAULT 0,
  `additional_informations` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

CREATE TABLE `sub_categories` (
  `id` int(11) NOT NULL,
  `insertion_date` varchar(45) NOT NULL,
  `insertion_time` varchar(45) NOT NULL,
  `users_id` int(11) NOT NULL,
  `deleted` int(11) NOT NULL DEFAULT 0,
  `categories_id` int(11) NOT NULL,
  `sub_category_name` varchar(45) NOT NULL,
  `description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

CREATE TABLE `sub_category_aiis` (
  `id` int(11) NOT NULL,
  `insertion_date` varchar(45) NOT NULL,
  `insertion_time` varchar(45) NOT NULL,
  `users_id` int(11) NOT NULL,
  `deleted` int(11) DEFAULT 0,
  `sub_categories_id` int(11) NOT NULL,
  `additional_information_items_id` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

CREATE TABLE `tms_customers` (
  `id` int(11) NOT NULL,
  `insert_date` date NOT NULL,
  `customer_type` varchar(45) NOT NULL,
  `province` varchar(45) NOT NULL,
  `branch` varchar(100) DEFAULT NULL,
  `customer_name` varchar(45) NOT NULL,
  `customer_id` varchar(45) NOT NULL,
  `is_customer_id_free` int(11) NOT NULL COMMENT 'if  1 free  if 0 not free',
  `contract_start_date` date NOT NULL,
  `contract_end_date` date NOT NULL,
  `demo_start_time` varchar(45) NOT NULL,
  `demo_end_time` varchar(45) NOT NULL,
  `has_nightly_bandwidth` varchar(45) NOT NULL,
  `nightly_bandwidth_d` decimal(11,1) DEFAULT NULL,
  `nightly_bandwidth_u` decimal(11,1) DEFAULT NULL,
  `bandwidth` varchar(45) NOT NULL,
  `bandwidth_d` decimal(11,1) NOT NULL,
  `bandwidth_u` decimal(11,1) NOT NULL,
  `service_type` varchar(45) NOT NULL,
  `bandwidth_type` varchar(45) NOT NULL,
  `has_demo` varchar(20) NOT NULL,
  `package_id` int(11) NOT NULL,
  `payment_policy` varchar(45) NOT NULL DEFAULT '',
  `cost` int(11) NOT NULL,
  `currency` varchar(45) NOT NULL,
  `payment_period` int(11) NOT NULL DEFAULT 1,
  `poc_name` varchar(45) NOT NULL,
  `poc_number` varchar(45) NOT NULL,
  `poc_email` varchar(45) NOT NULL,
  `reseller_id` int(11) NOT NULL,
  `provider_id` int(11) NOT NULL,
  `commission` varchar(45) NOT NULL,
  `location` varchar(45) NOT NULL,
  `address` varchar(200) NOT NULL,
  `comment` varchar(200) NOT NULL,
  `device_type_id` varchar(45) NOT NULL,
  `router_type_id` int(11) NOT NULL DEFAULT 0,
  `device_possession_type` varchar(45) NOT NULL,
  `ip_public` int(11) NOT NULL,
  `equipment` varchar(45) NOT NULL,
  `activation_date` date NOT NULL,
  `state` varchar(45) NOT NULL COMMENT '1 = new; 2=installation; 3=Demo; 4=active, 5=Inactive;6= terminated;',
  `last_state` int(11) NOT NULL,
  `finance_requests` int(11) NOT NULL COMMENT '0= defult; 1= requested; 2= confirmed;',
  `terminate_waiting` int(11) NOT NULL COMMENT '0=nothing; 1=waiting; 2=terminated',
  `installation_date` varchar(45) NOT NULL,
  `insertion_date` varchar(45) NOT NULL,
  `service_start_date` date DEFAULT NULL,
  `step` int(11) NOT NULL,
  `is_termination_read` int(11) NOT NULL,
  `is_installation_read` int(11) NOT NULL,
  `is_suspend_read` int(11) NOT NULL,
  `is_activation_read` int(11) NOT NULL,
  `is_activation_approved` int(11) NOT NULL,
  `is_activation_approved_ff` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL,
  `contract_at` varchar(45) NOT NULL,
  `changed_at` timestamp NOT NULL,
  `deleted` int(11) NOT NULL DEFAULT 0,
  `document_type` varchar(45) NOT NULL,
  `document_number` varchar(45) NOT NULL,
  `job` varchar(45) NOT NULL,
  `education` varchar(45) NOT NULL,
  `gender` varchar(45) NOT NULL,
  `contract_duration` varchar(45) NOT NULL,
  `receiver_cost` int(11) NOT NULL,
  `receiver_currency` varchar(45) NOT NULL,
  `router_cost` int(11) NOT NULL,
  `router_currency` varchar(45) NOT NULL,
  `familiarity_type` varchar(45) NOT NULL,
  `activation_approved_by_a` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_persian_ci;

CREATE TABLE `tms_device_types` (
  `id` int(11) NOT NULL,
  `insert_date` date NOT NULL,
  `type` varchar(100) NOT NULL,
  `model` varchar(120) NOT NULL,
  `info` text NOT NULL,
  `user_id` int(11) NOT NULL,
  `deleted` smallint(6) NOT NULL DEFAULT 0,
  `province` varchar(40) NOT NULL,
  `change_by` int(11) NOT NULL,
  `change_type` smallint(6) DEFAULT 0,
  `change_on` date NOT NULL,
  `branch` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `changed_at` timestamp NOT NULL,
  `created_at` timestamp NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

CREATE TABLE `tms_packages` (
  `id` int(11) NOT NULL,
  `insert_date` date NOT NULL,
  `package` varchar(45) NOT NULL,
  `speed` varchar(45) NOT NULL,
  `period` int(11) NOT NULL,
  `cost` int(11) NOT NULL,
  `created_at` varchar(100) NOT NULL,
  `changed_at` varchar(100) NOT NULL,
  `province` varchar(40) NOT NULL,
  `user_id` int(11) NOT NULL,
  `deleted` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_persian_ci;

CREATE TABLE `tms_router_types` (
  `id` int(11) NOT NULL,
  `insert_date` date NOT NULL,
  `user_id` int(11) NOT NULL,
  `deleted` smallint(6) NOT NULL DEFAULT 0,
  `name` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

CREATE TABLE `transaction_deletions` (
  `id` int(11) NOT NULL,
  `insertion_date` varchar(45) NOT NULL,
  `insertion_time` varchar(45) NOT NULL,
  `users_id` int(11) NOT NULL,
  `deleted` int(11) NOT NULL DEFAULT 0,
  `reference_type` varchar(45) NOT NULL,
  `reference_id` int(11) NOT NULL,
  `tms_markup` int(11) NOT NULL DEFAULT 0,
  `qb_markup` int(11) NOT NULL DEFAULT 0,
  `sib_markup` int(11) NOT NULL DEFAULT 0,
  `ad_markup` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

CREATE TABLE `transaction_editions` (
  `id` int(11) NOT NULL,
  `insertion_date` varchar(45) NOT NULL,
  `insertion_time` varchar(45) NOT NULL,
  `users_id` int(11) NOT NULL,
  `deleted` int(11) NOT NULL DEFAULT 0,
  `reference_type` varchar(45) NOT NULL,
  `reference_id` int(11) NOT NULL,
  `old_data` varchar(1000) NOT NULL DEFAULT '',
  `new_data` varchar(1000) NOT NULL DEFAULT '',
  `tms_markup` int(11) NOT NULL DEFAULT 0,
  `qb_markup` int(11) NOT NULL DEFAULT 0,
  `sib_markup` int(11) NOT NULL DEFAULT 0,
  `ad_markup` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

CREATE TABLE `transfers` (
  `id` int(11) NOT NULL,
  `insertion_date` varchar(45) NOT NULL,
  `insertion_time` varchar(45) NOT NULL,
  `users_id` int(11) NOT NULL,
  `deleted` int(11) NOT NULL DEFAULT 0,
  `from_province` varchar(45) NOT NULL,
  `from_branch` varchar(100) DEFAULT NULL,
  `to_province` varchar(45) NOT NULL,
  `to_branch` varchar(100) DEFAULT NULL,
  `transfer_date` varchar(45) NOT NULL,
  `transfer_amount` decimal(11,2) NOT NULL,
  `check_number` varchar(45) NOT NULL DEFAULT '',
  `currency` varchar(45) NOT NULL,
  `description` text NOT NULL,
  `tms_markup` int(11) NOT NULL DEFAULT 0,
  `qb_markup` int(11) NOT NULL DEFAULT 0,
  `rqb_markup` int(11) NOT NULL DEFAULT 0,
  `sib_markup` int(11) NOT NULL DEFAULT 0,
  `ad_markup` int(11) NOT NULL DEFAULT 0,
  `is_approved` int(11) NOT NULL DEFAULT 0,
  `approve_date` varchar(45) DEFAULT NULL,
  `approve_time` varchar(45) DEFAULT NULL,
  `approved_by` int(11) NOT NULL DEFAULT 0,
  `approve_description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `insertion_date` varchar(45) NOT NULL,
  `insertion_time` varchar(45) NOT NULL,
  `users_id` int(11) NOT NULL,
  `deleted` int(11) DEFAULT 0,
  `first_name` varchar(45) NOT NULL,
  `last_name` varchar(45) NOT NULL,
  `username` varchar(45) NOT NULL,
  `email` varchar(45) NOT NULL DEFAULT '',
  `password` varchar(100) NOT NULL,
  `last_activity` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

ALTER TABLE `accessibilities`
  ADD PRIMARY KEY (`id`),
  ADD KEY `access_point` (`access_point`) USING BTREE;

ALTER TABLE `additional_informations`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `additional_information_items`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `attachments`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `backups`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `branches`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `cash_reservations`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `exchanges`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `expenses`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `hr_employees`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `incomes`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `income_request_approvals`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `invoices`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `logistic_cashes`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `markups`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `provinces`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `purchases`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `sub_categories`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `sub_category_aiis`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `tms_customers`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `tms_device_types`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `tms_packages`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `tms_router_types`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `transaction_deletions`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `transaction_editions`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `transfers`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `accessibilities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=88;

ALTER TABLE `additional_informations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

ALTER TABLE `additional_information_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

ALTER TABLE `attachments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

ALTER TABLE `backups`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

ALTER TABLE `branches`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

ALTER TABLE `cash_reservations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

ALTER TABLE `exchanges`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

ALTER TABLE `expenses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

ALTER TABLE `hr_employees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

ALTER TABLE `incomes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

ALTER TABLE `income_request_approvals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

ALTER TABLE `invoices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

ALTER TABLE `logistic_cashes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

ALTER TABLE `markups`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

ALTER TABLE `provinces`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

ALTER TABLE `purchases`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

ALTER TABLE `sub_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

ALTER TABLE `sub_category_aiis`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

ALTER TABLE `tms_customers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

ALTER TABLE `tms_device_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

ALTER TABLE `tms_packages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

ALTER TABLE `tms_router_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

ALTER TABLE `transaction_deletions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

ALTER TABLE `transaction_editions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

ALTER TABLE `transfers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

START TRANSACTION;

INSERT INTO `users` (`id`, `insertion_date`, `insertion_time`, `users_id`, `deleted`, `first_name`, `last_name`, `username`, `email`, `password`, `last_activity`) VALUES
(2, '2021-09-22', '10:04:33', 2, 0, 'Mahdi', 'Hassani', 'mahdi', 'hassanim430@gmail.com', 'd46da0f3edbae09f55e369be636951de', NULL);

INSERT INTO `accessibilities` (`id`, `insertion_date`, `insertion_time`, `users_id`, `deleted`, `system_users_id`, `access_point`, `is_accessed`) VALUES
(1, '2021-09-22', '16:44:28', 2, 0, 2, 'system_accessibility/home/default/', 1),
(2, '2021-09-22', '16:44:28', 2, 0, 2, 'system_accessibility/home/closing_balance/', 1),
(3, '2021-09-22', '16:44:28', 2, 0, 2, 'system_accessibility/home/ten_highest_expenses/', 1),
(4, '2021-09-22', '16:44:28', 2, 0, 2, 'system_accessibility/home/num_transaction/', 1),
(5, '2021-09-22', '16:44:28', 2, 0, 2, 'system_accessibility/home/monthly_income/', 1),
(6, '2021-09-22', '16:44:28', 2, 0, 2, 'system_accessibility/home/monthly_expense/', 1),
(7, '2021-09-22', '16:44:28', 2, 0, 2, 'system_accessibility/transaction/income/view_income', 1),
(8, '2021-09-22', '16:44:28', 2, 0, 2, 'system_accessibility/transaction/income/add_income', 1),
(9, '2021-09-22', '16:44:28', 2, 0, 2, 'system_accessibility/transaction/income/edit_income', 1),
(10, '2021-09-22', '16:44:28', 2, 0, 2, 'system_accessibility/transaction/income/delete_income', 1),
(11, '2021-09-22', '16:44:28', 2, 0, 2, 'system_accessibility/transaction/income/print_receipt/', 1),
(12, '2021-09-22', '16:44:28', 2, 0, 2, 'system_accessibility/transaction/income/add_attachment/', 1),
(13, '2021-09-22', '16:44:28', 2, 0, 2, 'system_accessibility/transaction/income/edit_attachment', 1),
(14, '2021-09-22', '16:44:28', 2, 0, 2, 'system_accessibility/transaction/income/filter_table', 1),
(15, '2021-09-22', '16:44:28', 2, 0, 2, 'system_accessibility/transaction/income/qb_markup', 1),
(16, '2021-09-22', '16:44:28', 2, 0, 2, 'system_accessibility/transaction/income/sib_markup', 1),
(17, '2021-09-22', '16:44:28', 2, 0, 2, 'system_accessibility/transaction/income/ad_markup', 1),
(18, '2021-09-22', '16:44:28', 2, 0, 2, 'system_accessibility/transaction/expense/view_expense', 1),
(19, '2021-09-22', '16:44:28', 2, 0, 2, 'system_accessibility/transaction/expense/add_expense', 1),
(20, '2021-09-22', '16:44:28', 2, 0, 2, 'system_accessibility/transaction/expense/edit_expense', 1),
(21, '2021-09-22', '16:44:28', 2, 0, 2, 'system_accessibility/transaction/expense/delete_expense', 1),
(22, '2021-09-22', '16:44:28', 2, 0, 2, 'system_accessibility/transaction/expense/print_voucher/', 1),
(23, '2021-09-22', '16:44:28', 2, 0, 2, 'system_accessibility/transaction/expense/add_attachment/', 1),
(24, '2021-09-22', '16:44:28', 2, 0, 2, 'system_accessibility/transaction/expense/edit_attachment', 1),
(25, '2021-09-22', '16:44:28', 2, 0, 2, 'system_accessibility/transaction/expense/filter_table', 1),
(26, '2021-09-22', '16:44:28', 2, 0, 2, 'system_accessibility/transaction/expense/qb_markup', 1),
(27, '2021-09-22', '16:44:28', 2, 0, 2, 'system_accessibility/transaction/expense/sib_markup', 1),
(28, '2021-09-22', '16:44:28', 2, 0, 2, 'system_accessibility/transaction/expense/ad_markup', 1),
(29, '2021-09-22', '16:44:28', 2, 0, 2, 'system_accessibility/transaction/exchange/view_exchange', 1),
(30, '2021-09-22', '16:44:28', 2, 0, 2, 'system_accessibility/transaction/exchange/add_exchange', 1),
(31, '2021-09-22', '16:44:28', 2, 0, 2, 'system_accessibility/transaction/exchange/edit_exchange', 1),
(32, '2021-09-22', '16:44:28', 2, 0, 2, 'system_accessibility/transaction/exchange/delete_exchange', 1),
(33, '2021-09-22', '16:44:28', 2, 0, 2, 'system_accessibility/transaction/exchange/filter_table', 1),
(34, '2021-09-22', '16:44:28', 2, 0, 2, 'system_accessibility/transaction/exchange/qb_markup', 1),
(35, '2021-09-22', '16:44:28', 2, 0, 2, 'system_accessibility/transaction/exchange/sib_markup', 1),
(36, '2021-09-22', '16:44:28', 2, 0, 2, 'system_accessibility/transaction/exchange/ad_markup', 1),
(37, '2021-09-22', '16:44:28', 2, 0, 2, 'system_accessibility/transaction/transfer/view_transfer', 1),
(38, '2021-09-22', '16:44:28', 2, 0, 2, 'system_accessibility/transaction/transfer/add_transfer', 1),
(39, '2021-09-22', '16:44:28', 2, 0, 2, 'system_accessibility/transaction/transfer/edit_transfer', 1),
(40, '2021-09-22', '16:44:28', 2, 0, 2, 'system_accessibility/transaction/transfer/delete_transfer', 1),
(41, '2021-09-22', '16:44:28', 2, 0, 2, 'system_accessibility/transaction/transfer/filter_table', 1),
(42, '2021-09-22', '16:44:28', 2, 0, 2, 'system_accessibility/transaction/transfer/qb_markup', 1),
(43, '2021-09-22', '16:44:28', 2, 0, 2, 'system_accessibility/transaction/transfer/sib_markup', 1),
(44, '2021-09-22', '16:44:28', 2, 0, 2, 'system_accessibility/transaction/transfer/ad_markup', 1),
(45, '2021-09-22', '16:44:28', 2, 0, 2, 'system_accessibility/transaction/transfer/print_voucher', 1),
(46, '2021-09-22', '16:44:28', 2, 0, 2, 'system_accessibility/transaction/transfer/view_attachment', 1),
(47, '2021-09-22', '16:44:28', 2, 0, 2, 'system_accessibility/request/report_transfer/index/', 1),
(48, '2021-09-22', '16:44:28', 2, 0, 2, 'system_accessibility/request/report_transfer/filter_table', 1),
(49, '2021-09-22', '16:44:28', 2, 0, 2, 'system_accessibility/report/report_transaction/view_transaction/', 1),
(50, '2021-09-22', '16:44:28', 2, 0, 2, 'system_accessibility/report/report_transaction/qb_markup/', 1),
(51, '2021-09-22', '16:44:28', 2, 0, 2, 'system_accessibility/report/report_transaction/sib_markup/', 1),
(52, '2021-09-22', '16:44:28', 2, 0, 2, 'system_accessibility/report/report_transaction/ad_markup/', 1),
(53, '2021-09-22', '16:44:28', 2, 0, 2, 'system_accessibility/report/report_transaction/edit_sib_number/', 1),
(54, '2021-09-22', '16:44:28', 2, 0, 2, 'system_accessibility/report/report_transaction/view_commnet/', 1),
(55, '2021-09-22', '16:44:28', 2, 0, 2, 'system_accessibility/report/report_transaction/view_attachment/', 1),
(56, '2021-09-22', '16:44:28', 2, 0, 2, 'system_accessibility/report/report_transaction/edit_transaction/', 1),
(57, '2021-09-22', '16:44:28', 2, 0, 2, 'system_accessibility/report/report_transaction/export_excel/', 1),
(58, '2021-09-22', '16:44:28', 2, 0, 2, 'system_accessibility/report/report_transaction/print_receipt/', 1),
(59, '2021-09-22', '16:44:28', 2, 0, 2, 'system_accessibility/report/report_transaction/print_voucher/', 1),
(60, '2021-09-22', '16:44:28', 2, 0, 2, 'system_accessibility/report/report_transaction_edition/index/', 1),
(61, '2021-09-22', '16:44:28', 2, 0, 2, 'system_accessibility/report/report_transaction_edition/view_full_info/', 1),
(62, '2021-09-22', '16:44:28', 2, 0, 2, 'system_accessibility/report/report_transaction_edition/qb_markup/', 1),
(63, '2021-09-22', '16:44:28', 2, 0, 2, 'system_accessibility/report/report_transaction_edition/sib_markup/', 1),
(64, '2021-09-22', '16:44:28', 2, 0, 2, 'system_accessibility/report/report_transaction_edition/ad_markup/', 1),
(65, '2021-09-22', '16:44:28', 2, 0, 2, 'system_accessibility/report/report_transaction_deletion/index/', 1),
(66, '2021-09-22', '16:44:28', 2, 0, 2, 'system_accessibility/report/report_transaction_deletion/view_full_info/', 1),
(67, '2021-09-22', '16:44:28', 2, 0, 2, 'system_accessibility/report/report_transaction_deletion/qb_markup/', 1),
(68, '2021-09-22', '16:44:28', 2, 0, 2, 'system_accessibility/report/report_transaction_deletion/sib_markup/', 1),
(69, '2021-09-22', '16:44:28', 2, 0, 2, 'system_accessibility/report/report_transaction_deletion/ad_markup/', 1),
(70, '2021-09-22', '16:44:28', 2, 0, 2, 'system_accessibility/management/list_category/add_category', 1),
(71, '2021-09-22', '16:44:28', 2, 0, 2, 'system_accessibility/management/list_category/edit_category', 1),
(72, '2021-09-22', '16:44:28', 2, 0, 2, 'system_accessibility/management/list_category/delete_category', 1),
(73, '2021-09-22', '16:44:28', 2, 0, 2, 'system_accessibility/management/list_sub_category/add_sub_category', 1),
(74, '2021-09-22', '16:44:28', 2, 0, 2, 'system_accessibility/management/list_sub_category/edit_sub_category', 1),
(75, '2021-09-22', '16:44:28', 2, 0, 2, 'system_accessibility/management/list_sub_category/delete_sub_category', 1),
(76, '2021-09-22', '16:44:28', 2, 0, 2, 'system_accessibility/management/list_user/add_user', 1),
(77, '2021-09-22', '16:44:28', 2, 0, 2, 'system_accessibility/management/list_user/edit_user', 1),
(78, '2021-09-22', '16:44:28', 2, 0, 2, 'system_accessibility/management/list_user/delete_user', 1),
(79, '2021-09-22', '16:44:28', 2, 0, 2, 'system_accessibility/management/list_user/set_accessibility', 1),
(80, '2026-03-29', '15:15:59', 2, 0, 2, 'system_accessibility/management/list_province/add_province', 1),
(81, '2026-03-29', '15:15:59', 2, 0, 2, 'system_accessibility/management/list_province/edit_province', 1),
(82, '2026-03-29', '15:15:59', 2, 0, 2, 'system_accessibility/management/list_province/delete_province', 1),
(83, '2026-03-29', '15:41:27', 2, 0, 2, 'system_accessibility/management/list_branch/add_branch', 1),
(84, '2026-03-29', '15:41:27', 2, 0, 2, 'system_accessibility/management/list_branch/edit_branch', 1),
(85, '2026-03-29', '15:41:27', 2, 0, 2, 'system_accessibility/management/list_branch/delete_branch', 1),
(86, '2026-05-20', '19:45:49', 2, 0, 2, 'system_accessibility/management/list_backup/download_backup', 1),
(87, '2026-05-20', '19:45:49', 2, 0, 2, 'system_accessibility/management/list_backup/delete_backup', 1);

COMMIT;
