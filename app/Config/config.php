<?php

/*
* Common configuration options
*
*/

$siteConfigurationArr = array();

/* Database configuration dev1.in */
$siteConfigurationArr['DATABASE_DATASOURCE'] = "Database/Mysql";
$siteConfigurationArr['DATABASE_PERSISTENT'] = false;
$siteConfigurationArr['DATABASE_HOST'] = "localhost";
$siteConfigurationArr['DATABASE_USER'] = "prj_wlp";
$siteConfigurationArr['DATABASE_PASSWORD'] = "5xIOZhcXu3tx5Cxv";
$siteConfigurationArr['DATABASE_NAME'] = "prj_wlp";
$siteConfigurationArr['DATABASE_PREFIX'] = "";
$siteConfigurationArr['DATABASE_ENCODING'] = "utf8";
$siteConfigurationArr['SANDBOXMAIL'] = false;

/* Debug Level */
$siteConfigurationArr['DEBUG_LEVEL'] = 2;

$siteConfigurationArr['CONFIG_ADMIN_IMAGE_PATH'] = WWW_ROOT . 'files' . DS . 'admin';
$siteConfigurationArr['CONFIG_SLIDER_IMAGE_PATH'] = WWW_ROOT . 'files' . DS . 'slider';
$siteConfigurationArr['CONFIG_TEMP_UPLOAD_DIR'] = WWW_ROOT . "files" . DS . "temp";
$siteConfigurationArr['CONFIG_USER_BIG_UPLOAD_DIR'] = WWW_ROOT . "files" . DS . "users";
$siteConfigurationArr['CONFIG_USER_SMALL_UPLOAD_DIR'] = WWW_ROOT . "files" . DS . "users" . DS . "thumbs";
$siteConfigurationArr['CONFIG_ADMIN_IMAGE_PREFIX'] = 'large_';
$siteConfigurationArr['CONFIG_ADMIN_IMAGE_THUMB_PREFIX'] = 'thumb_';
$siteConfigurationArr['CONFIG_ADMIN_IMAGE_HEIGHT'] = 300;
$siteConfigurationArr['CONFIG_ADMIN_IMAGE_WIDTH'] = 300;
$siteConfigurationArr['CONFIG_ADMIN_IMAGE_THUMB_HEIGHT'] = 150;
$siteConfigurationArr['CONFIG_ADMIN_IMAGE_THUMB_WIDTH'] = 150;

/* API */
$siteConfigurationArr['API_PUBLIC_KEY'] = "fg32w_0-b48e-4d19-108c-51df4Ew2!2";
$siteConfigurationArr['API_UUID_KEY'] = "f81d4fae-7dec-11d0-a765-00a0c91e6bf6";
/* google api key */
$siteConfigurationArr['ANDROID_API_KEY'] = "AIzaSyANRIdlPz8cz-3j9F5kNkc_Tqrd1796iDU";


/* Date Format */
$siteConfigurationArr['CONFIG_DATE_FORMAT'] = "d-m-Y";

/* email setting */
define('FROM_EMAIL', 'nikhil.parmar@multidots.com');
define('FROM_EMAIL_NAME', 'Wallpaper');

define('REPLY_EMAIL', 'nikhil.parmar@multidots.com');
define('REPLY_EMAIL_NAME', 'Wallpaper');

define('SUBJECT_EMAIL', 'Wallpaper');

define('SEND_MAIL', true);

define('DATEFORMATE', 'dd-mm-yyyy');

/* Site title */
$siteConfigurationArr['CONFIG_SITE_TITLE_POSTFIX'] = "Wallpaper";
$siteConfigurationArr['CONFIG_SITE_TITLE_SEPARATOR'] = " &#124; ";

/* encript/decript key */
$siteConfigurationArr['ENCRYPT_DECRYPT_KEY'] = "mltdots";


/* Site Constants */
$siteConfigurationArr['QUICK_DEFAULT_POLL_DATA_TYPE'] = 9;
$siteConfigurationArr['USERNAME_MAX_LENGTH'] = 20;
$siteConfigurationArr['USERNAME_MIN_LENGTH'] = 4;
$siteConfigurationArr['PAGE_LIMIT'] = 20;
$siteConfigurationArr['NORMAL_PAGINATION_LIMIT'] = 20;
$siteConfigurationArr['HIEGHER_PAGINATION_LIMIT'] = 50;
$siteConfigurationArr['HUGE_PAGINATION_LIMIT'] = 100;
$siteConfigurationArr['LESS_PAGINATION_LIMIT'] = 10;
$siteConfigurationArr['POLL_INVITE_EMAIL_LIMIT'] = 100;
$siteConfigurationArr['ACTIVITY_FEED_CREATE_WALP'] = 'create_wallpaper';
$siteConfigurationArr['ACTIVITY_FEED_RATE_WALP'] = 'rate_wallpaper';
$siteConfigurationArr['ACTIVITY_FEED_LIKE_WALP'] = 'like_wallpaper';
$siteConfigurationArr['ACTIVITY_FEED_COMMENT_WALP'] = 'comment_wallpaper';
$siteConfigurationArr['POLL_INVITE_EMAIL_LIMIT'] = 100;
$siteConfigurationArr['CONFIG_AVATAR_PATH'] = WWW_ROOT . 'files' . DS . 'avatar';

/* disable error(just show one common error) actions amd it's setting */
$siteConfigurationArr['ERROR_DISABLE'] = 0; /*-- 1:show only one message , 0: show original message */

/* Loads configurations for different hosts */
require "hosts_config.php";

$siteConfigurationArr['WALP_THUMB_URL'] = $siteConfigurationArr['SITE_URL']  . 'files/wallpaperes/thumbs/';
$siteConfigurationArr['WALP_VIDEO_URL'] = $siteConfigurationArr['SITE_URL']  . 'files/wallpaperes/';
$siteConfigurationArr['USER_AVATAR_URL'] = $siteConfigurationArr['SITE_URL']  . 'files/users/';
$siteConfigurationArr['USER_THUMB_IMAGE_URL'] = $siteConfigurationArr['SITE_URL'] . 'files/users/thumbs/';

/* set system default timezone */
$siteConfigurationArr['SYSTEM_TIME_ZONE'] = 'UTC';
date_default_timezone_set('UTC');


$siteConfigurationArr['CONFIG_AVATAR_URL'] = $siteConfigurationArr['SITE_URL'] . 'files/avatar/';
/* Now define all available config options */
if (!empty($siteConfigurationArr)) {
	foreach ($siteConfigurationArr as $key => $value) {
		define($key, $value);
	}
}


/* API Response Codes */
require 'api_response_codes.php';

Configure::write('debug', DEBUG_LEVEL);
set_time_limit(0);
