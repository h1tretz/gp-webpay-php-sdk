<?php

namespace AdamStipak\Webpay\PaymentRequest;

use AdamStipak\Webpay\ArrayToXml;

class AddInfo
{

    /**
     * @var array
     */
    private $values;

    /**
     * @var string
     */
    private $schema;

    public function __construct($schema, $values)
    {
        $this->schema = $schema;
        $this->values = $values;
        $this->validate();
    }

    private function validate()
    {
        $dom = new \DOMDocument;
        $dom->loadXML($this->toXml());

        libxml_use_internal_errors(true);
        if (!$dom->schemaValidateSource($this->schema)) {
            $errors = [];
            foreach (libxml_get_errors() as $xmlError) {
                $errors[] = $xmlError->message;
            }
            throw new AddInfoException("Validation errors: " . implode(' | ', $errors));
        }
        libxml_use_internal_errors(false);
    }

    public function toXml()
    {
        return trim(ArrayToXml::convert(
            $this->values, [
                'rootElementName' => 'additionalInfoRequest',
                '_attributes' => [
                    'version' => '4.0',
                    'xmlns' => "http://gpe.cz/gpwebpay/additionalInfo/request",
                ],
            ]
        ));
    }

    public static function createMinimalValues($version = null)
    {
        return [
            '_attributes' => [
                'version' => $version ? $version : '4.0',
            ],
        ];
    }
}
