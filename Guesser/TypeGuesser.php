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

class TypeGuesser extends AbstractTypeGuesser
{
    /**
     * {@inheritdoc}
     */
    public function guessType($class, $property, ModelManagerInterface $modelManager)
    {
        if (!$ret = $this->getParentMetadataForProperty($class, $property, $modelManager)) {
            return new TypeGuess('text', array(), Guess::LOW_CONFIDENCE);
        }

        list($metadata, $propertyName, $parentAssociationMappings) = $ret;

        if ($metadata->hasAssociation($propertyName)) {
            $mapping = $metadata->fieldMappings[$propertyName];

            switch ($mapping['type']) {
                case ClassMetadata::TO_ONE:
                    return new TypeGuess('couch_one', array(), Guess::HIGH_CONFIDENCE);

                case ClassMetadata::TO_MANY:
                    return new TypeGuess('couch_many', array(), Guess::HIGH_CONFIDENCE);
            }
        }
        switch ($metadata->getTypeOfField($propertyName)) {
            case 'mixed':
              return new TypeGuess('array', array(), Guess::HIGH_CONFIDENCE);
            case 'boolean':
                return new TypeGuess('boolean', array(), Guess::HIGH_CONFIDENCE);
            case 'datetime':
                return new TypeGuess('datetime', array(), Guess::HIGH_CONFIDENCE);
            case 'integer':
                return new TypeGuess('integer', array(), Guess::MEDIUM_CONFIDENCE);
            case 'string':
                return new TypeGuess('text', array(), Guess::MEDIUM_CONFIDENCE);
            default:
                return new TypeGuess('text', array(), Guess::LOW_CONFIDENCE);
        }
    }
}
