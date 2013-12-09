<?php

/*
 * This file is part of the Sonata project.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 * (c) Kévin Dunglas <dunglas@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bangpound\Bundle\DoctrineCouchDBAdminBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Sonata\AdminBundle\DependencyInjection\AbstractSonataAdminExtension;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Definition\Processor;

/**
 * SonataAdminBundleExtension
 *
 * @author      Thomas Rabaix <thomas.rabaix@sonata-project.org>
 * @author      Michael Williams <michael.williams@funsational.com>
 */
class BangpoundDoctrineCouchDBAdminExtension extends AbstractSonataAdminExtension
{
    /**
     *
     * @param array            $configs   An array of configuration settings
     * @param ContainerBuilder $container A ContainerBuilder instance
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configs = $this->fixTemplatesConfiguration($configs, $container);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('doctrine_couchdb.xml');
        $loader->load('doctrine_couchdb_filter_types.xml');
        $loader->load('security.xml');

        $configuration = new Configuration();
        $processor = new Processor();
        $config = $processor->processConfiguration($configuration, $configs);

        $pool = $container->getDefinition('sonata.admin.manager.doctrine_couchdb');

        $container->setParameter('sonata_doctrine_couchdb_admin.templates', $config['templates']);

        // define the templates
        $container->getDefinition('sonata.admin.builder.doctrine_couchdb_list')
            ->replaceArgument(1, $config['templates']['types']['list']);

        $container->getDefinition('sonata.admin.builder.doctrine_couchdb_show')
            ->replaceArgument(1, $config['templates']['types']['show']);
    }
}
