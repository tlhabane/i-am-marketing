<?php

use App\Handlers\SiteInfo;
use App\Middleware\JsonBodyParserMiddleware;
use App\Util\BasePathDetector;
use PHPMailer\PHPMailer\PHPMailer;
use Psr\Container\ContainerInterface;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

return [
    'settings' => function () {
        return require __DIR__ . '/settings.php';
    },

    PHPMailer::class => function (ContainerInterface $container): PHPMailer {
        $settings   = $container->get('settings')['smtp'];

        $mail = new PHPMailer(true);
        $mail->CharSet = 'UTF-8';
        $mail->Encoding = 'base64';
        $mail->SMTPOptions = $settings['smtp_options'];
        $mail->isSMTP();
        $mail->Host = $settings['host'];
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Username = $settings['username'];
        $mail->Password = $settings['password'];
        $mail->Port = $settings['port'] ?? 465;
        $mail->isHTML();

        return $mail;
    },

    SiteInfo::class => function (ContainerInterface $container) {
        $smtp = $container->get('settings')['smtp'];
        $site = $container->get('settings')['site'];

        return SiteInfo::fromArray([
            'senderEmail'           => $smtp['username'],
            'siteName'              => $site['organization_name'],
            'siteLogo'              => $site['organization_logo'],
            'siteAddress'           => $site['organization_address'],
            'siteEmail'             => $site['organization_email'],
            'supportEmail'          => $site['organization_support_email'] ?? '',
            'siteTelephone'         => $site['organization_phone'] ?? '',
            'siteUrl'               => $site['website_address'],
            'contactPageUrl'        => $site['contact_page_url'],
            'privacyPolicyUrl'      => $site['privacy_policy_url'],
            'securityPolicyUrl'     => $site['security_policy_url'],
            'termsAndConditionsUrl' => $site['terms_and_conditions_url'],
            'theme'                 => $container->get('settings')['theme']
        ]);
    },

    BasePathDetector::class => function () {
        return new BasePathDetector($_SERVER);
    },

    Environment::class => function (ContainerInterface $container) {
        $loader = new FilesystemLoader(__DIR__ . '/../templates');
        $twig = new Environment($loader, [
            'cache' => false, // Set to a path in production
        ]);
        // Add global theme variables to all templates
        $twig->addGlobal('theme', $container->get('settings')['theme']);
        return $twig;
    },

    JsonBodyParserMiddleware::class => function () {
        return new JsonBodyParserMiddleware();
    },
];
