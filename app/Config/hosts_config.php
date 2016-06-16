<?php

/*
 * Configuration options for different hosts
 */

$serverName = filter_input(INPUT_SERVER, 'SERVER_NAME', FILTER_SANITIZE_STRING);
if ($serverName == "wallpaper.local") {
    $siteConfigurationArr['SITE_URL'] = "http://wallpaper.local/";
    $siteConfigurationArr['ADMIN_URL'] = "http://wallpaper.local/";
    $siteConfigurationArr['WEB_URL'] = "http://wallpaper.local/";
    $siteConfigurationArr['DATABASE_USER'] = "root";
    $siteConfigurationArr['DATABASE_PASSWORD'] = "";
    $siteConfigurationArr['DATABASE_NAME'] = "prj_wallpaper";
    $siteConfigurationArr['DEBUG_LEVEL'] = 2;
    $siteConfigurationArr['SANDBOXMAIL'] = true;
    $siteConfigurationArr['UR_SITE_TITLE_NOTIFICATION'] = '';
    $siteConfigurationArr['UR_APP_MODE'] = "local";
    $siteConfigurationArr['ERROR_DISABLE'] = 0;
    $siteConfigurationArr['S3_BUCKET_FOLDER_NAME'] = 'local';
} else {
    throw new NotFoundException();
}