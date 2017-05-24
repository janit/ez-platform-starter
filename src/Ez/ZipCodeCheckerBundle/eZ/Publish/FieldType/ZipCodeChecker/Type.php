<?php


namespace Ez\ZipCodeCheckerBundle\eZ\Publish\FieldType\ZipCodeChecker;

use eZ\Publish\Core\FieldType\FieldType;
use eZ\Publish\Core\FieldType\ValidationError;
use eZ\Publish\API\Repository\Values\ContentType\FieldDefinition;
use eZ\Publish\Core\Base\Exceptions\InvalidArgumentType;
use eZ\Publish\Core\FieldType\Validator\StringLengthValidator;
use eZ\Publish\SPI\FieldType\Value as SPIValue;
use eZ\Publish\Core\FieldType\Value as BaseValue;
use VasilDakov\Postcode\Postcode;


class Type extends FieldType
{
    protected $validatorConfigurationSchema = array(
        'postalCodeValidator' => array(
            'postalCodeCountry' => array(
                'type' => 'text',
                'default' => 'England',
            )
        ),
    );

    /*protected $validCountryPostalCode = array (
        'England' => array(
            'MK7 6AJ',
            'RM10 5RL',

        ),
        'Germany' => array(
            '50969',
            '50777',

        )
    );
    */

    protected $validCountryPostalCode = array (

        'Germany' => array(
            '50969',
            '50777',

        ),
        'England' => ''   // this will use a Postal code API
    );


    /**
     * Validates the validatorConfiguration of a FieldDefinitionCreateStruct or FieldDefinitionUpdateStruct.
     *
     * @param mixed $validatorConfiguration
     *
     * @return \eZ\Publish\SPI\FieldType\ValidationError[]
     */
    public function validateValidatorConfiguration($validatorConfiguration)
    {

        $validationErrors = array();

        foreach ($validatorConfiguration as $validatorIdentifier => $constraints) {
            if ($validatorIdentifier !== 'postalCodeValidator') {
                $validationErrors[] = new ValidationError(
                    "Validator '%validator%' is unknown",
                    null,
                    array(
                        '%validator%' => $validatorIdentifier,
                    )
                );
                continue;
            }
            foreach ($constraints as $name => $value) {

                switch ($name) {
                    case 'postalCodeCountry':
                        if ($value !== false  &&
                            !(null === $value) &&
                            !array_key_exists($value, $this->validCountryPostalCode )
                        )
                        {
                            $validationErrors[] = new ValidationError(
                                "Validator parameter '%parameter%' not included in the supported country list",
                                null,
                                array(
                                    '%parameter%' => $value,
                                )
                            );
                        }
                        break;
                    default:
                        $validationErrors[] = new ValidationError(
                            "Validator parameter '%parameter%' is unknown",
                            null,
                            array(
                                '%parameter%' => $name,
                            )
                        );
                }
            }
        }

        return $validationErrors;
    }

    /**
     * Validates a field based on the validators in the field definition.
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     *
     * @param \eZ\Publish\API\Repository\Values\ContentType\FieldDefinition $fieldDefinition The field definition of the field
     * @param \Ez\ZipCodeCheckerBundle\eZ\Publish\FieldType\ZipCodeChecker\Value $fieldValue The field value for which an action is performed
     *
     * @return \eZ\Publish\SPI\FieldType\ValidationError[]
     */
    public function validate(FieldDefinition $fieldDefinition, SPIValue $fieldValue)
    {


        $validationErrors = array();

        if ($this->isEmptyValue($fieldValue)) {
            return $validationErrors;
        }

        $validatorConfiguration = $fieldDefinition->getValidatorConfiguration();
        $constraints = isset($validatorConfiguration['postalCodeValidator'])
            ? $validatorConfiguration['postalCodeValidator']
            : array();



        $fieldDefinitionCountry = $constraints['postalCodeCountry']; //e.g England  (saved in the FieldType definition)

        $countryNotFound = false;
        if (isset($fieldDefinitionCountry) &&
            $fieldDefinitionCountry !== false &&
            $fieldDefinitionCountry !== '')
        {
            //next could be a nice interface
            switch($fieldDefinitionCountry)
            {
                case'England':
                    try {
                        $postcode = new Postcode($fieldValue->zip);

                    }catch(\Exception $e) {
                        $countryNotFound = $fieldDefinitionCountry ;

                    }
                    break;
                case'Germany':
                    if (array_search($fieldValue->zip, $this->validCountryPostalCode[$fieldDefinitionCountry]) === false) {

                        $countryNotFound = $fieldDefinitionCountry ;
                    }
                    break;
                default:
                    $countryNotFound = true ;
                    break;
            }

            if ($countryNotFound)
            {
                $validationErrors[] = new ValidationError(
                    '%zip_code% : in %country%  not found.',
                    '%zip_code% : in %country%  not found.', //not implemented , we select only one country yet
                    array(
                        '%zip_code%' => $fieldValue->zip,
                        '%country%' => $fieldDefinitionCountry,
                    ),
                    'zip'
                );
            }
        }

        //working with multidimensional country array , check first $validCountryPostalCode
        /*
        if (isset($fieldDefinitionCountry) &&
            $fieldDefinitionCountry !== false &&
            $fieldDefinitionCountry !== '') &&
        array_search($fieldValue->zip, $this->validCountryPostalCode[$fieldDefinitionCountry]) === false)
        {
            $validationErrors[] = new ValidationError(
                '%zip_code% : in %country%  not found.',
                '%zip_code% : in %country%  not found.', //not implemented , we select only one country yet
                array(
                    '%zip_code%' => $fieldValue->zip,
                    '%country%' => $fieldDefinitionCountry,
                ),
                'zip'
            );
        }
        */



    //print_r($validationErrors);exit;

        return $validationErrors;
    }

    /**
     * Returns the field type identifier for this field type.
     *
     * @return string
     */
    public function getFieldTypeIdentifier()
    {
        return 'ezzip';
    }

    /**
     * Returns the name of the given field value.
     *
     * It will be used to generate content name and url alias if current field is designated
     * to be used in the content name/urlAlias pattern.
     *
     * @param \Ez\ZipCodeCheckerBundle\eZ\Publish\FieldType\ZipCodeChecker\Value $value
     *
     * @return string
     */
    public function getName(SPIValue $value)
    {
        return (string)$value->zip;
    }

    /**
     * Returns the fallback default value of field type when no such default
     * value is provided in the field definition in content types.
     *
     * @return \Ez\ZipCodeCheckerBundle\eZ\Publish\FieldType\ZipCodeChecker\Value
     */
    public function getEmptyValue()
    {
        return new Value();
    }

    /**
     * Returns if the given $value is considered empty by the field type.
     *
     * @param mixed $value
     *
     * @return bool
     */
    public function isEmptyValue(SPIValue $value)
    {
        return $value->zip === null || trim($value->zip) === '';
    }

    /**
     * Inspects given $inputValue and potentially converts it into a dedicated value object.
     *
     * @param string|\Ez\ZipCodeCheckerBundle\eZ\Publish\FieldType\ZipCodeChecker\Value $inputValue
     *
     * @return \Ez\ZipCodeCheckerBundle\eZ\Publish\FieldType\ZipCodeChecker\Value The potentially converted and structurally plausible value.
     */
    protected function createValueFromInput($inputValue)
    {
        if (is_string($inputValue)) {
            $inputValue = new Value($inputValue);
        }

        return $inputValue;
    }

    /**
     * Throws an exception if value structure is not of expected format.
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException If the value does not match the expected structure.
     *
     * @param \Ez\ZipCodeCheckerBundle\eZ\Publish\FieldType\ZipCodeChecker\Value $value
     */
    protected function checkValueStructure(BaseValue $value)
    {
        if (!is_string($value->zip)) {
            throw new InvalidArgumentType(
                '$value->zip',
                'string',
                $value->zip
            );
        }
    }

    /**
     * Returns information for FieldValue->$sortKey relevant to the field type.
     *
     * @param \eZ\Publish\Core\FieldType\TextLine\Value $value
     *
     * @return array
     */
    protected function getSortInfo(BaseValue $value)
    {
        return $value->zip;
        //return $this->getName($value);
    }

    /**
     * Converts an $hash to the Value defined by the field type.
     *
     * @param mixed $hash
     *
     * @return \Ez\ZipCodeCheckerBundle\eZ\Publish\FieldType\ZipCodeChecker\Value $value
     */
    public function fromHash($hash)
    {
        if ($hash === null) {
            return $this->getEmptyValue();
        }

        return new Value($hash);
    }

    /**
     * Converts a $Value to a hash.
     *
     * @param \Ez\ZipCodeCheckerBundle\eZ\Publish\FieldType\ZipCodeChecker\Value $value
     *
     * @return mixed
     */
    public function toHash(SPIValue $value)
    {
        if ($this->isEmptyValue($value)) {
            return null;
        }

        return $value->zip;
    }

    /**
     * Returns whether the field type is searchable.
     *
     * @return bool
     */
    public function isSearchable()
    {
        return false;
    }
}
