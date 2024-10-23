<?php

namespace YahnisElsts\PluginUpdateChecker\v5p5;

// Load Autoloaders for v4p10 and v5p5.
require_once __DIR__ . '/Puc/v4p10/Autoloader.php';
require_once __DIR__ . '/Puc/v5p5/Autoloader.php';

// Initialize the autoloaders.
new \YahnisElsts\PluginUpdateChecker\v4p10\Puc_v4p10_Autoloader();
new \YahnisElsts\PluginUpdateChecker\v5p5\Puc_v5p5_Autoloader();

// Load Factory classes for v4p10 and v5p5.
require_once __DIR__ . '/Puc/v4p10/Factory.php';
require_once __DIR__ . '/Puc/v5p5/Factory.php';

// Load major and minor factory classes.
use YahnisElsts\PluginUpdateChecker\v5\PucFactory as MajorFactory;
use YahnisElsts\PluginUpdateChecker\v5p5\PucFactory as MinorFactory;

// Register classes defined in this version with the factory.
foreach (
    array(
        'Plugin\\UpdateChecker'     => Plugin\UpdateChecker::class,
        'Theme\\UpdateChecker'      => Theme\UpdateChecker::class,
        'Vcs\\PluginUpdateChecker'  => Vcs\PluginUpdateChecker::class,
        'Vcs\\ThemeUpdateChecker'   => Vcs\ThemeUpdateChecker::class,
        'GitHubApi'                 => Vcs\GitHubApi::class,
        'BitBucketApi'              => Vcs\BitBucketApi::class,
        'GitLabApi'                 => Vcs\GitLabApi::class,
    ) as $pucGeneralClass => $pucVersionedClass
) {
    MajorFactory::addVersion($pucGeneralClass, $pucVersionedClass, '5.5');
    MinorFactory::addVersion($pucGeneralClass, $pucVersionedClass, '5.5');
}
