<?php

namespace Ez\ZipCodeCheckerBundle\eZ\Publish\FieldType\FormMapper;

use eZ\Publish\API\Repository\FieldTypeService;
use EzSystems\RepositoryForms\Data\Content\FieldData;
use EzSystems\RepositoryForms\Data\FieldDefinitionData;
use EzSystems\RepositoryForms\FieldType\DataTransformer\FieldValueTransformer;
use EzSystems\RepositoryForms\FieldType\FieldDefinitionFormMapperInterface;
use EzSystems\RepositoryForms\FieldType\FieldValueFormMapperInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ZipCodeCheckerFormMapper implements FieldDefinitionFormMapperInterface, FieldValueFormMapperInterface
{
    /**
     * @var \eZ\Publish\API\Repository\FieldTypeService
     */
    private $fieldTypeService;

    public function __construct(FieldTypeService $fieldTypeService)
    {
        $this->fieldTypeService = $fieldTypeService;
    }

    //backend
    public function mapFieldDefinitionForm(FormInterface $fieldDefinitionForm, FieldDefinitionData $data)
    {

        $fieldDefinitionForm
            ->add('postalCodeCountry', TextType::class, [
                'required' => true,
                'property_path' => 'validatorConfiguration[postalCodeValidator][postalCodeCountry]',
                'label' => 'field_definition.ezzip.postal_code_country'
            ])
            /*
             //We don't need a default value
            ->add(
            // Creating from FormBuilder as we need to add a DataTransformer.
                $fieldDefinitionForm->getConfig()->getFormFactory()->createBuilder()
                    ->create('defaultValue', TextType::class, [
                        'required' => false,
                        'label' => 'field_definition.ezzip.default_value',
                    ])
                    ->addModelTransformer(new ZipCodeCheckerValueTransformer())
                    // Deactivate auto-initialize as we're not on the root form.
                    ->setAutoInitialize(false)->getForm()
            )
            */
            ;
    }

    //Frontend
    public function mapFieldValueForm(FormInterface $fieldForm, FieldData $data)
    {
        $fieldDefinition = $data->fieldDefinition;

        $formConfig = $fieldForm->getConfig();
        $names = $fieldDefinition->getNames();
        $label = $fieldDefinition->getName($formConfig->getOption('languageCode')) ?: reset($names);

        $fieldForm
            ->add(
                $formConfig->getFormFactory()->createBuilder()
                    ->create(
                        'value',
                        TextType::class,
                        ['required' => $fieldDefinition->isRequired, 'label' => $label]
                    )
                    ->addModelTransformer(new FieldValueTransformer($this->fieldTypeService->getFieldType($fieldDefinition->fieldTypeIdentifier)))
                    // Deactivate auto-initialize as we're not on the root form.
                    ->setAutoInitialize(false)
                    ->getForm()
            );
    }

    /**
     * Fake method to set the translation domain for the extractor.
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'translation_domain' => 'ezrepoforms_content_type',
            ]);
    }
}
