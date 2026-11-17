<?php

// Parse the .env file in the current directory into an array
$env = Dotenv\Dotenv::createArrayBacked(__DIR__ . '/..')->load();
$constants = require __DIR__ . "/constants.php";

$settings = [];

$settings['site'] = [
    'organization_name'             => $constants['APP']['NAME'],
    'organization_email'            => $constants['APP']['EMAIL'],
    'organization_support_email'    => $constants['APP']['SUPPORT_EMAIL'],
    'organization_phone'            => $constants['APP']['PHONE'],
    'organization_address'          => $constants['APP']['ADDRESS'],
    'website_address'               => $constants['APP']['URL'],
    'organization_logo'             => $constants['APP']['LOGO'],
    'contact_page_url'              => $constants['APP']['CONTACT_PAGE_URL'],
    'privacy_policy_url'            => $constants['APP']['PRIVACY_POLICY_URL'],
    'security_policy_url'           => $constants['APP']['SECURITY_POLICY_URL'],
    'terms_and_conditions_url'      => $constants['APP']['TERMS_URL']
];

$settings['smtp'] = [
    'host'      => $env['SMTP_HOST'],
    'port'      => intval($env['SMTP_PORT']),
    'username'  => $env['SMTP_USERNAME'],
    'password'  => $env['SMTP_PASSWORD'],
    'smtp_options'  => [
        'ssl' => [
            'verify_peer'       => false,
            'verify_peer_name'  => false,
            'allow_self_signed' => true
        ]
    ]
];

$settings['theme'] = [
    'primary_color'             => $constants['MAIL_THEME']['PRIMARY_COLOR'],
    'text_color'                => $constants['MAIL_THEME']['BODY_FONT_COLOR'],
    'body_bg'                   => $constants['MAIL_THEME']['BODY_BG_COLOR'],
    'footer_bg'                 => $constants['MAIL_THEME']['FOOTER_BG_COLOR'],
    'footer_text_color'         => $constants['MAIL_THEME']['FOOTER_FONT_COLOR'],
];

return $settings;
