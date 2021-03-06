<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bangpound\Bundle\DoctrineCouchDBAdminBundle\Util;

use Doctrine\ODM\CouchDB\DocumentManager;
use Sonata\AdminBundle\Exception\ModelManagerException;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
use Symfony\Component\Console\Output\OutputInterface;

use Sonata\AdminBundle\Security\Handler\AclSecurityHandlerInterface;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Util\ObjectAclManipulator as BaseObjectAclManipulator;

class ObjectAclManipulator extends BaseObjectAclManipulator
{
    /**
     * {@inheritDoc}
     */
    public function batchConfigureAcls(OutputInterface $output, AdminInterface $admin, UserSecurityIdentity $securityIdentity = null)
    {
        $securityHandler = $admin->getSecurityHandler();
        if (!$securityHandler instanceof AclSecurityHandlerInterface) {
            $output->writeln('Admin class is not configured to use ACL : <info>ignoring</info>');

            return;
        }

        $output->writeln(sprintf(' > generate ACLs for %s', $admin->getCode()));
        $objectOwnersMsg = is_null($securityIdentity) ? '' : ' and set the object owner';

        /** @var DocumentManager $om */
        $om = $admin->getModelManager()->getDocumentManager();
        $qb = $om->createQueryBuilder($admin->getClass());

        $count = 0;
        $countUpdated = 0;
        $countAdded = 0;

        try {
            $batchSize = 20;
            $batchSizeOutput = 200;
            $objectIds = array();

            foreach ($qb->getQuery()->iterate() as $row) {
                $objectIds[]      = ObjectIdentity::fromDomainObject($row);
                $objectIdIterator = new \ArrayIterator($objectIds);

                // detach from Doctrine, so that it can be Garbage-Collected immediately
                $om->detach($row);

                $count++;

                if (($count % $batchSize) == 0) {
                    list($batchAdded, $batchUpdated) = $this->configureAcls($output, $admin, $objectIdIterator, $securityIdentity);
                    $countAdded += $batchAdded;
                    $countUpdated += $batchUpdated;
                    $objectIds = array();
                }

                if (($count % $batchSizeOutput) == 0) {
                    $output->writeln(sprintf('   - generated class ACEs%s for %s objects (added %s, updated %s)', $objectOwnersMsg, $count, $countAdded, $countUpdated));
                }
            }

            if (count($objectIds) > 0) {
                list($batchAdded, $batchUpdated) = $this->configureAcls($output, $admin, $objectIdIterator, $securityIdentity);
                $countAdded += $batchAdded;
                $countUpdated += $batchUpdated;
            }
        } catch (\BadMethodCallException $e) {
            throw new ModelManagerException('', 0, $e);
        }

        $output->writeln(sprintf('   - [TOTAL] generated class ACEs%s for %s objects (added %s, updated %s)', $objectOwnersMsg, $count, $countAdded, $countUpdated));
    }
}
