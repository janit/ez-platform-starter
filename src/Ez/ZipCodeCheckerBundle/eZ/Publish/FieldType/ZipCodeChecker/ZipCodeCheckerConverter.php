<?php
/**
 * Created by PhpStorm.
 * User: ez
 * Date: 21/05/17
 * Time: 11:26 AM
 */

namespace Ez\ZipCodeCheckerBundle\eZ\Publish\FieldType\ZipCodeChecker;


use eZ\Publish\Core\Persistence\Legacy\Content\FieldValue\Converter;
use eZ\Publish\Core\Persistence\Legacy\Content\StorageFieldDefinition;
use eZ\Publish\Core\Persistence\Legacy\Content\StorageFieldValue;
use eZ\Publish\SPI\Persistence\Content\FieldValue;
use eZ\Publish\SPI\Persistence\Content\Type\FieldDefinition;

class ZipCodeCheckerConverter implements Converter
{
    const POSTAL_CODE_VALIDATOR_IDENTIFIER = 'postalCodeValidator';
    const POSTAL_CODE_COUNTRY = 'postalCodeCountry';

    public function toStorageValue(FieldValue $value, StorageFieldValue $storageFieldValue)
    {
        $storageFieldValue->dataText = $value->data;
        $storageFieldValue->sortKeyString = $value->sortKey;
    }

    public function toFieldValue(StorageFieldValue $value, FieldValue $fieldValue)
    {
        $fieldValue->data = $value->dataText;
        $fieldValue->sortKey = $value->sortKeyString;
    }

    //when you add the "Postal Code" from the drop-down menu and editing the fieldType definition inside a Contenttype (backend) and you give a new country as postalCodeValidator, then we store the new country in "dataText1". A default Value is what will be displayed in the input field on object level. example : see the ezstring(title) for Article (Minimum length, Maximum length & Default Value))
    public function toStorageFieldDefinition(FieldDefinition $fieldDef, StorageFieldDefinition $storageDef)
    {
        if (isset($fieldDef->fieldTypeConstraints->validators[self::POSTAL_CODE_VALIDATOR_IDENTIFIER][self::POSTAL_CODE_COUNTRY])) {
            $storageDef->dataText1 = $fieldDef->fieldTypeConstraints->validators[self::POSTAL_CODE_VALIDATOR_IDENTIFIER][self::POSTAL_CODE_COUNTRY];
        } else {
            $storageDef->dataText1 = null;
        }


        //Not needed for now
        //$storageDef->dataText2 = $fieldDef->defaultValue->data;
    }

    public function toFieldDefinition(StorageFieldDefinition $storageDef, FieldDefinition $fieldDef)
    {
        $validatorConstraints = array();

        if (isset($storageDef->dataText1)) {
            $validatorConstraints[self::POSTAL_CODE_VALIDATOR_IDENTIFIER][self::POSTAL_CODE_COUNTRY] =
                $storageDef->dataText1 != null ?
                    (string)$storageDef->dataText1 :
                    null;
        }


        $fieldDef->fieldTypeConstraints->validators = $validatorConstraints;
        //Not needed for now
        /*
        $fieldDef->defaultValue->data = $storageDef->dataText2 ?: null;
        $fieldDef->defaultValue->sortKey = $storageDef->dataText2 ?: '';
        */
    }

    public function getIndexColumn()
    {
        return 'sort_key_string';
    }
}