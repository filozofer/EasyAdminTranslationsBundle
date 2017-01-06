<?php

/*
 * This file is part of the EasyAdminTranslationBundle.
 *
 * (c) Maxime Tual <maxime.tual@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace EasyAdminTranslationsBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use EasyAdminTranslationsBundle\DependencyInjection\Compiler\EasyAdminTranslationsTwigPaths;

/**
 * @author Maxime Tual <maxime.tual@gmail.com>
 */
class EasyAdminTranslationsBundle extends Bundle
{
    const VERSION = '0.0.1';
}
