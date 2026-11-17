<?php

namespace App\Handlers;

use App\Contracts\IDtoContract;

readonly class SiteInfo implements IDtoContract
{
    public function __construct(
        public string $senderEmail,
        public string $siteName,
        public string $siteLogo,
        public string $siteAddress,
        public string $siteEmail,
        public string $siteUrl,
        public string $siteTelephone            = '',
        public string $supportEmail             = '',
        public string $contactPageUrl           = '',
        public string $privacyPolicyUrl         = '',
        public string $securityPolicyUrl        = '',
        public string $termsAndConditionsUrl    = '',
        public array $theme = []
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            senderEmail:            $data['senderEmail'],
            siteName:               $data['siteName'],
            siteLogo:               $data['siteLogo'],
            siteAddress:            $data['siteAddress'],
            siteEmail:              $data['siteEmail'],
            siteUrl:                $data['siteUrl'],
            siteTelephone:          $data['siteTelephone'] ?? '',
            supportEmail:           $data['supportEmail'] ?? '',
            contactPageUrl:         $data['contactPageUrl'] ?? '',
            privacyPolicyUrl:       $data['privacyPolicyUrl'] ?? '',
            securityPolicyUrl:      $data['securityPolicyUrl'] ?? '',
            termsAndConditionsUrl:  $data['termsAndConditionsUrl'] ?? '',
            theme:                  $data['theme'] ?? []
        );
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}