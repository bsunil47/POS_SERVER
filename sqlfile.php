<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2/25/2016
 * Time: 11:34 PM
 */
$sql = 'CREATE DATABASE `Tollr_POS_Db`';
$sql_toll_user = 'CREATE TABLE `tbl_toll_users` (
                                      `toll_user_id` int(11) NOT NULL,
                                      `toll_employee_id` varchar(512) DEFAULT NULL,
                                      `toll_password` varchar(512) DEFAULT NULL,
                                      `toll_password_hash` varchar(512) DEFAULT NULL,
                                      `toll_id` int(11) DEFAULT NULL,
                                      `toll_user_type_id` int(11) DEFAULT NULL,
                                      `access_token` varchar(512) DEFAULT NULL,
                                      `status` tinyint(4) DEFAULT NULL,
                                      `expiry_date` datetime DEFAULT NULL,
                                      `group_id` int(11) DEFAULT NULL,
                                      `language_id` int(11) DEFAULT NULL,
                                      PRIMARY KEY (`toll_user_id`)
                                    ) ENGINE=InnoDB DEFAULT CHARSET=latin1';
$sql_user_details = 'CREATE TABLE `tbl_user_details` (
  `user_id` int(11) NOT NULL,
  `firstname` varchar(45) DEFAULT NULL,
  `lastname` varchar(45) DEFAULT NULL,
  `user_email` varchar(256) DEFAULT NULL,
  `mobile_number` varchar(45) DEFAULT NULL,
  `address1` varchar(45) DEFAULT NULL,
  `address2` varchar(45) DEFAULT NULL,
  `zipcode` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1';
$sql_trip_details = 'CREATE TABLE `tbl_trip_details` (
  `trip_details_id` varchar(45) NOT NULL,
  `trip_id` varchar(45) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `updated_on` varchar(45) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `vechical_id` varchar(45) DEFAULT NULL,
  `vechical_type` tinyint(4) DEFAULT NULL,
  `assigned_booth_id` int(11) DEFAULT NULL,
  `allowed_booth_id` int(11) DEFAULT NULL,
  `toll_user_id` int(11) DEFAULT NULL,
  `boothside_id` varchar(45) DEFAULT NULL,
  `trip_type` tinyint(4) default 0,
  `status` tinyint(4) default 0,
  PRIMARY KEY (`trip_details_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1';
$sql_user_vehicals = 'CREATE TABLE `tbl_user_vehicals` (
  `vechical_id` varchar(45) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `registration_no` varchar(45) DEFAULT NULL,
  `owner_name` varchar(255) DEFAULT NULL,
  `vechical_type_id` int(11) DEFAULT NULL,
  `vehical_drive_type` tinyint(4) DEFAULT NULL COMMENT \'1 = own and 2 = temparory\',
  PRIMARY KEY (`vechical_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1';
$sql_vehical_types = 'CREATE TABLE `tbl_vehical_types` (
  `vehical_type_id` INT NOT NULL,
  `name` VARCHAR(45) NULL,
  `axel` INT NULL,
  PRIMARY KEY (`vehical_type_id`))';
$sql_report_table = 'CREATE TABLE `tbl_report` (
  `report_id` VARCHAR(45) NOT NULL,
  `trip_id` VARCHAR(45) NULL,
  `trip_type` TINYINT(4) DEFAULT 0,
  `trip_detail_id` VARCHAR (45) NULL,
  `report_type` TINYINT NULL COMMENT \'1=Allow , 2= VIP , 3= Tollred Report  and  4= Not Tollered\',
  `registration_no` VARCHAR(45) NULL,
  `vehical_type` TINYINT NULL,
  `pic` VARCHAR (256) NULL,
  `amount` INT(11) DEFAULT 0,
  `status` TINYINT NULL,
  PRIMARY KEY (`report_id`),
  UNIQUE INDEX `report_id_UNIQUE` (`report_id` ASC))';
