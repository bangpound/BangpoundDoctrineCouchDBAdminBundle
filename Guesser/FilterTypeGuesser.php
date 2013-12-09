<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 * (c) Kévin Dunglas <dunglas@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bangpound\Bundle\DoctrineCouchDBAdminBundle\Guesser;

use Sonata\AdminBundle\Model\ModelManagerInterface;
use Symfony\Component\Form\Guess\Guess;
use Symfony\Component\Form\Guess\TypeGuess;

use Doctrine\ODM\CouchDB\Mapping\ClassMetadata;

class FilterTypeGuesser extends AbstractTypeGuesser
{
    /**
     * {@inheritdoc}
     */
    public function guessType($class, $property, ModelManagerInterface $modelManager)
    {
        if (!$ret = $this->getParentMetadataForProperty($class, $property, $modelManager)) {
            return false;
        }

        $options = array(
            'field_type'     => null,
            'field_options' => array(),
            'options' => array(),
        );

        list($metadata, $propertyName, $parentAssociationMappings) = $ret;

        $options['parent_association_mappings'] = $parentAssociationMappings;

        if ($metadata->hasAssociation($propertyName)) {
            $mapping = $metadata->fieldMappings[$propertyName];

            switch ($mapping['type']) {
                case ClassMetadata::TO_ONE:
                case ClassMetadata::TO_MANY:
                    $options['operator_type']    = 'sonata_type_equal';
                    $options['operator_options'] = array();

                    $options['field_type'] = 'document';
                    $options['field_options'] = array(
                        'class' => $mapping['targetDocument']
                    );

                    $options['field_name'] = $mapping['fieldName'];
                    $options['mapping_type'] = $mapping['type'];

                    return new TypeGuess('doctrine_couch_model', $options, Guess::HIGH_CONFIDENCE);
            }
        }

        $options['field_name'] = $metadata->fieldMappings[$propertyName]['fieldName'];

        switch ($metadata->getTypeOfField($propertyName)) {
            case 'mixed':
                return new TypeGuess('doctrine_couch_model', $options, Guess::MEDIUM_CONFIDENCE);
            case 'boolean':
                $options['field_type'] = 'sonata_type_boolean';
                $options['field_options'] = array();

                return new TypeGuess('doctrine_couch_boolean', $options, Guess::HIGH_CONFIDENCE);
            case 'datetime':
                return new TypeGuess('doctrine_couch_datetime', $options, Guess::HIGH_CONFIDENCE);
            case 'integer':
                $options['field_type'] = 'number';

                return new TypeGuess('doctrine_couch_number', $options, Guess::MEDIUM_CONFIDENCE);
            case 'id':
            case 'string':
                $options['field_type'] = 'text';

                return new TypeGuess('doctrine_couch_string', $options, Guess::MEDIUM_CONFIDENCE);
            default:
                return new TypeGuess('doctrine_couch_string', $options, Guess::LOW_CONFIDENCE);
        }
    }
}
