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

namespace Bangpound\Bundle\DoctrineCouchDBAdminBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Bangpound\Bundle\DoctrineCouchDBAdminBundle\DependencyInjection\Compiler\AddGuesserCompilerPass;
use Bangpound\Bundle\DoctrineCouchDBAdminBundle\DependencyInjection\Compiler\AddTemplatesCompilerPass;

class BangpoundDoctrineCouchDBAdminBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new AddGuesserCompilerPass());
        $container->addCompilerPass(new AddTemplatesCompilerPass());
    }

}
