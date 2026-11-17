<?php

$constants = [];
const APP_URL             = "http://localhost:3000";

$constants['APP'] = [
    'URL'                   => APP_URL,
    'NAME'                  => "I Am Markeing",
    'LOGO'                  => APP_URL . "/assets/Logo_1213de0d-7lKfiguU.png",
    'EMAIL'                 => "hello@iam.za.bz",
    'SUPPORT_EMAIL'         => "",
    'PHONE'                 => "+27 83 277 9549",
    'ADDRESS'               => "Kameeldrift, Pretoria, South Africa",
    'CONTACT_PAGE_URL'      => APP_URL,
    'PRIVACY_POLICY_URL'    => "",
    'SECURITY_POLICY_URL'   => "",
    'TERMS_URL'             => ""
];

# Directories
const BASE_URL  = "http://localhost/i-am-marketing/backend";
const BASE_DIR  = __DIR__ . "/..";

# Mail template colors
$constants['MAIL_THEME'] = [
    'PRIMARY_COLOR'        => "#007be8",
    'BODY_FONT_COLOR'      => "#333333",
    'BODY_BG_COLOR'        => "#fefefe",
    'FOOTER_BG_COLOR'      => "#e6e7e8",
    'FOOTER_FONT_COLOR'    => "#414042"
];

return $constants;