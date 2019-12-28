<?php

if (PHP_SAPI !== 'cli') {
    die('This script supports command line usage only. Please check your command.');
}

$finder = \PhpCsFixer\Finder::create()
    ->exclude([
        'vendor', 'build'
    ])
    ->in(__DIR__);

return \PhpCsFixer\Config::create()
    ->setRules([
        '@PSR2' => true,
        'array_syntax' => ['syntax' => 'short'],
        'escape_implicit_backslashes' => [
            'single_quoted' => true
        ]
    ])
    ->setFinder($finder);
