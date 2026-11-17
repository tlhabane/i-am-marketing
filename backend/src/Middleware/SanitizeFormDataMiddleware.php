<?php

namespace App\Middleware;

use App\Util\Utilities;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class SanitizeFormDataMiddleware implements MiddlewareInterface
{
    private Utilities $util;

    public function __construct(Utilities $util) {
        $this->util = $util;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $data = (object)$request->getParsedBody();

        $quotation = [];
        if (isset($data->quotation) && is_array($data->quotation)) {
            foreach ($data->quotation as $index => $quotationItem) {
                if (empty($quotationItem)) {
                    continue;
                }

                $quotationArray = [];
                if (is_array($quotationItem)) {
                    foreach ($quotationItem as $quotationArrayItemIndex => $quotationArrayItem) {
                        $quotationArray[$quotationArrayItemIndex] = $quotationArrayItem;
                    }

                    $quotation[$index] = $quotationArray;
                    continue;
                }
                $quotation[$index] = $this->util->sanitizeString($quotationItem);
            }
        }

        $services = [];
        if (isset($data->services) && is_array($data->services)) {
            foreach ($data->services as $service) {
                if (empty($service)) {
                    continue;
                }
                $sanitized = $this->util->sanitizeString($service);
                $formatted_service = $this->util->normalizeString(
                    implode(' ', explode('-', $sanitized))
                );
                $services[] = $this->util->sanitizeString($formatted_service);
            }
        }

        $sanitized = [
            'name'     => $this->util->sanitizeString($data->name ?? ''),
            'email'    => $this->util->sanitizeEmail($data->email ?? ''),
            'tel'      => $this->util->sanitizePhoneNumber($data->phone ?? $data->tel ?? ''),
            'altTel'   => $this->util->sanitizePhoneNumber($data->altPhone ?? $data->altTel ?? ''),
            'company'  => $this->util->sanitizeString($data->company ?? ''),
            'budget'   => $this->util->sanitizeString($data->budget ?? ''),
            'message'  => $this->util->sanitizeString($data->message ?? ''),
            'services' => $services ?? [],
            'quotation' => $quotation ?? [],
            'captcha'  => $this->util->sanitizeString($data->captcha ?? $data->captcha_challenge ?? ''),
            'day'      => $this->util->sanitizeString($data->day ?? ''),
            'time'     => $this->util->sanitizeString($data->time ?? '')
        ];

        // Pass sanitized data forward via Request Attributes
        $request = $request->withAttribute('formData', $sanitized);

        return $handler->handle($request);
    }
}