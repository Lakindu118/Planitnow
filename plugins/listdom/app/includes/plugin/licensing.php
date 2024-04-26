<?php

use Webilia\WP\Plugin\Licensing;

/**
 * Listdom Plugin Licensing Class.
 *
 * @class LSD_Plugin_Licensing
 * @version	1.0.0
 */
class LSD_Plugin_Licensing
{
    /**
     * @var Licensing
     */
    private $handler;

    /**
     * Constructor
     */
    function __construct(array $args = [])
    {
        $license_key_option = $args['license_key_option'] ?? $args['prefix'].'_purchase_code';
        $activation_id_option = $args['activation_id_option'] ?? $args['prefix'].'_activation_id';
        $basename = $args['basename'] ?? LSD_BASENAME;

        // Webilia Licensing Server
        $this->handler = new Licensing(
            $license_key_option,
            $activation_id_option,
            $basename,
            LSD_LICENSING_SERVER
        );
    }

    /**
     * @return mixed
     */
    public function getLicenseKey()
    {
        return $this->handler->getLicenseKey();
    }

    /**
     * @return mixed
     */
    public function getActivationId()
    {
        return $this->handler->getActivationId();
    }

    /**
     * @return string
     */
    public function getBasename(): string
    {
        return $this->handler->getBasename();
    }

    /**
     * Check to See if License is Valid
     *
     * @return bool
     */
    public function isLicenseValid(): bool
    {
        return $this->handler->isValid();
    }

    /**
     * @param string $license_key
     * @return array
     */
    public function activate(string $license_key): array
    {
        list($status, $response, $activation_id) = $this->handler->activate($license_key);

        if($response === Licensing::STATUS_VALID) $message = esc_html__('License key is valid and your website activated successfully!', 'listdom');
        else if($response === Licensing::STATUS_INVALID) $message = esc_html__('License key is invalid or not for this product or its activation limit reached!', 'listdom');
        else if($response === Licensing::ERROR_UNKNOWN) $message = esc_html__('Something went wrong!', 'listdom');
        else if($response === Licensing::ERROR_CONNECTION) $message = esc_html__('It seems your website cannot webilia.com server for validating the license key! Please consult with your host provider.', 'listdom');
        else $message = $response;

        return [$status, $message];
    }
}
