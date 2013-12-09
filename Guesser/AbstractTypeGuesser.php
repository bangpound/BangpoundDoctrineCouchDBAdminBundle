<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bangpound\Bundle\DoctrineCouchDBAdminBundle\Guesser;

use Sonata\AdminBundle\Guesser\TypeGuesserInterface;
use Doctrine\ORM\Mapping\MappingException;
use Bangpound\Bundle\DoctrineCouchDBAdminBundle\Model\ModelManager;

abstract class AbstractTypeGuesser implements TypeGuesserInterface
{
    /**
     * @param string                                                          $baseClass
     * @param string                                                          $propertyFullName
     * @param \Bangpound\Bundle\DoctrineCouchDBAdminBundle\Model\ModelManager $modelManager
     *
     * @return array|null
     */
    protected function getParentMetadataForProperty($baseClass, $propertyFullName, ModelManager $modelManager)
    {
        try {
            return $modelManager->getParentMetadataForProperty($baseClass, $propertyFullName);
        } catch (MappingException $e) {
            // no metadata not found.
            return null;
        }
    }
}
