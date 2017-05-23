<?php
/**
 * NOT NEEDED FOR NOW ! see ZipCodeCheckerFormMapper
 */

namespace Ez\ZipCodeCheckerBundle\eZ\Publish\FieldType\FormMapper;


use Ez\ZipCodeCheckerBundle\eZ\Publish\FieldType\ZipCodeChecker\Value;
use Symfony\Component\Form\DataTransformerInterface;


class ZipCodeCheckerValueTransformer  implements DataTransformerInterface
{
    public function transform($value)
    {
        if (!$value instanceof Value) {
            return null;
        }

        return $value->zip;
    }

    public function reverseTransform($value)
    {
        if (!$value) {
            return null;
        }

        return new Value($value);
    }




}