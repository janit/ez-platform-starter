<?php


namespace Ez\ZipCodeCheckerBundle\eZ\Publish\FieldType\ZipCodeChecker;

use eZ\Publish\Core\FieldType\Value as BaseValue;

/**
 * Value for ZipCodeChecker field type.
 */
class Value extends BaseValue
{
    /**
     * zip content.
     *
     * @var string
     */
    public $zip;

    /**
     * Construct a new Value object and initialize it $zip.
     *
     * @param string $text
     */
    public function __construct($zip = '')
    {
        $this->zip = $zip;
    }

    /**
     * @see \eZ\Publish\Core\FieldType\Value
     */
    public function __toString()
    {
        return (string)$this->zip;
    }
}
