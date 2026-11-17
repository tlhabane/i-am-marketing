<?php

namespace App\Handlers;

class PrepareTemplatePayloadHandler
{
    public function __construct(public SiteInfo $siteInfo)
    {}

    public function prepare(): array
    {
        return [
            'organization_name'             => $this->siteInfo->siteName,
            'organization_logo'             => $this->siteInfo->siteLogo,
            'organization_phone'            => $this->siteInfo->siteTelephone,
            'organization_email'            => $this->siteInfo->siteEmail,
            'organization_support_email'    => $this->siteInfo->supportEmail,
            'website_address'               => $this->siteInfo->siteUrl,
            'organization_address'          => $this->siteInfo->siteAddress,
            'contact_page_url'              => $this->siteInfo->contactPageUrl,
            'privacy_policy_url'            => $this->siteInfo->privacyPolicyUrl,
            'security_policy_url'           => $this->siteInfo->securityPolicyUrl,
            'terms_and_conditions_url'      => $this->siteInfo->termsAndConditionsUrl,
            'theme'                         => $this->siteInfo->theme
        ];
    }
}