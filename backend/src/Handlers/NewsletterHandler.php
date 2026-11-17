<?php

namespace App\Handlers;

use RuntimeException;
use Nyholm\Psr7\Response;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class NewsletterHandler implements RequestHandlerInterface
{
    private string $template = 'newsletter';


    public function __construct(
        private readonly PHPMailer $mailer,
        private readonly Environment $twig,
        private readonly SiteInfo $siteInfo,
        private readonly PrepareTemplatePayloadHandler $payloadHandler
    )
    {}

    /**
     * @throws Exception
     * @throws RuntimeError
     * @throws LoaderError
     * @throws SyntaxError
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        // Data comes from the SanitizeFormDataMiddleware via attributes
        $data               = $request->getAttribute('formData');
        $subscriber_email   = $data['email'] ?? '';
        $source             = $data['source'] ?? '';

        if (empty($subscriber_email)) {
            return new Response(422, ['Content-Type' => 'application/json'], json_encode([
                'status' => 'error',
                'message' => 'Invalid email address provided.'
            ]));
        }

        try {
            // Send admin Notification
            $this->mailer->setFrom($this->siteInfo->senderEmail, $this->siteInfo->siteName);
            $this->mailer->addAddress($this->siteInfo->siteEmail, $this->siteInfo->siteName);

            $this->mailer->Subject = 'New Mailing List Subscription';
            $alternate_message = "You have a new subscriber: <b>{$subscriber_email}</b>";

            $notification_payload = [
                'subscriber_email'  => $subscriber_email,
                'organization_name' => $this->siteInfo->siteName,
                'source_name'       => $source
            ];
            $newsletter_notification_template = "messages/{$this->template}/{$this->template}_notification.twig";
            $message_body = $this->twig->render($newsletter_notification_template, $notification_payload);

            $common_template_payload = $this->payloadHandler->prepare();
            $template_payload = array_merge(
                $common_template_payload,
                [
                    'email_title'       => 'Notification',
                    'message_body'      => $message_body,
                    'alternate_message' => $alternate_message,
                ]
            );

            $mail_template = "default.twig";
            $this->mailer->Body = $this->twig->render($mail_template, $template_payload);

            if ($this->mailer->send()) {
                $this->mailer->clearAddresses();
                $this->mailer->clearReplyTos();

                $this->mailer->setFrom($this->siteInfo->senderEmail, $this->siteInfo->siteName);
                $this->mailer->addReplyTo($this->siteInfo->siteEmail, $this->siteInfo->siteName);
                $this->mailer->addAddress($subscriber_email);

                $alternate_message = "Thanks for subscribing to our mailing list!";

                $confirmation_payload = [
                    'organization_name' => $this->siteInfo->siteName,
                ];
                $newsletter_confirmation_template = "messages/{$this->template}/{$this->template}_confirmation.twig";
                $message_body = $this->twig->render($newsletter_confirmation_template, $confirmation_payload);

                $template_payload = array_merge(
                    $common_template_payload,
                    [
                        'email_title'       => 'Confirmation',
                        'message_body'      => $message_body,
                        'alternate_message' => $alternate_message,
                    ]
                );

                $this->mailer->Body = $this->twig->render($mail_template, $template_payload);

                if ($this->mailer->send()) {
                    return new Response(200, ['Content-Type' => 'application/json'], json_encode([
                        'status' => 'success',
                        'message' => 'Thank you for subscribing!'
                    ]));
                }
            }

            throw new RuntimeException(
                'An error occurred while processing your subscription request, please try again later.'
            );

        } catch (\Exception $e) {
            return new Response(500, ['Content-Type' => 'application/json'], json_encode([
                'status' => 'error',
                'message' => 'Could not process subscription: ' . $e->getMessage()
            ]));
        }
    }
}