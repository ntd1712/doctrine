# Doctrine Registry service

This supports multiple Doctrine Entity Managers (ORM), Document Managers (ODM) or Connections.

## Installation

#### Library

```bash
git clone https://github.com/ntd1712/doctrine.git
```

#### Composer

This can be installed with [Composer](https://getcomposer.org/doc/00-intro.md)

Define the following requirement in your `composer.json` file.

```json
{
    "require": {
        "chaos/doctrine": "*"
    },

    "repositories": [
      {
        "type": "vcs",
        "url": "https://github.com/ntd1712/doctrine"
      }
    ]
}
```

#### Usage

```php
<?php // For example, in Laravel

use Chaos\Support\Doctrine\ManagerRegistry;
use Chaos\Support\Doctrine\ODM\DocumentManagerFactory;
use Chaos\Support\Doctrine\ORM\EntityManagerFactory;

try {
    $options = config('doctrine');
    $connections = $managers = [];

    foreach ($options['connections'] as $id => $name) {
        $connections[$id] = $id . '_connection'; // e.g. ['default' => 'default_connection']
    }

    if (isset($options['entity_managers'])) {
        foreach ($options['entity_managers'] as $id => $name) {
            /* @var \Doctrine\ODM\MongoDB\DocumentManager|\Doctrine\ORM\EntityManager $manager */
            $manager = (new EntityManagerFactory())($container, $id, $options);
            $container[$managers[$id] = $id . '_entity_manager'] = $manager;
            $container[$connections[$name['connection']]] = $manager->getConnection();
        }
    }

    if (isset($options['document_managers'])) {
        foreach ($options['document_managers'] as $id => $name) {
            $manager = (new DocumentManagerFactory())($container, $id, $options);
            $container[$managers[$id] = $id . '_document_manager'] = $manager;
            $container[$connections[$name['connection']]] = $manager->getClient();
        }
    }

    $doctrine = new ManagerRegistry(
        'anonymous',
        $connections,
        $managers,
        'default',
        'default',
        $options['proxy_interface_name']
    );
    $doctrine->setContainer($container);

    $container['doctrine'] = $doctrine;
} catch (\Exception $exception) {
    header('HTTP/1.1 503 Service Unavailable.', true, 503);
    echo 'The application environment is not set correctly.';
    exit(1);
}

$doctrine = $container->get('doctrine');
$entityManager   = $doctrine->getManager();
$entityManager2  = $doctrine->getManager('mysql');
$documentManager = $doctrine->getManager('mongodb');
```

#### Commandline Tool

To run database migrations, validate class annotations and so on you will need a `cli-config.php` file at the root
of the project. For example, in Laravel:

```php
<?php

// define('LARAVEL_START', microtime(true));
// $loader = require __DIR__ . '/vendor/autoload.php';
// Doctrine\Common\Annotations\AnnotationRegistry::registerUniqueLoader([$loader, 'loadClass']);

/* @var Illuminate\Contracts\Http\Kernel $kernel */
$app = include_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

$controller = new App\Http\Controllers\Api\Controller();
$doctrine = $controller->getContainer()->get('doctrine');

if (empty($argv[1]) || false !== strpos($argv[1], 'orm')) {
    foreach ($doctrine->getManagerNames() as $name => $id) {
        if (false === strpos($id, '_entity_manager')) {
            continue;
        }

        $helperSet = Doctrine\ORM\Tools\Console\ConsoleRunner::createHelperSet(
            $doctrine->getManager($name)
        );

        $cli = Doctrine\ORM\Tools\Console\ConsoleRunner::createApplication($helperSet, []);
        $cli->setAutoExit(false);
        /* @noinspection PhpUnhandledExceptionInspection */
        $cli->run();
    }
} else if (false !== strpos($argv[1], 'odm')) {
    foreach ($doctrine->getManagerNames() as $name => $id) {
        if (false === strpos($id, '_document_manager')) {
            continue;
        }

        $helperSet = Chaos\Support\Doctrine\ODM\Tools\Console\ConsoleRunner::createHelperSet(
            $doctrine->getManager($name)
        );

        $cli = Chaos\Support\Doctrine\ODM\Tools\Console\ConsoleRunner::createApplication($helperSet, []);
        $cli->setAutoExit(false);
        /* @noinspection PhpUnhandledExceptionInspection */
        $cli->run();
    }
}

exit(0);
```

```bash
$> php vendor/bin/doctrine odm
$> php vendor/bin/doctrine orm
```

#### Sample Configuration

In Laravel you can create your own configuration in config\doctrine.php, for example:

```php
<?php

return [
    'entity_managers' => [
        'default' => [
            'connection' => 'default',
            'result_cache_driver' => [
                'type' => 'array',
            ],
            'query_cache_driver' => [
                'type' => 'array',
            ],
            'metadata_cache_driver' => [
                'type' => 'array',
            ],
            'mappings' => [
                'Chaos\\Modules\\Account\\Entity' => [
                    'mapping' => true,
                    'type' => 'annotation',
                    'dir' => base_path('modules/core/Account/src/Entity'),
                    'alias' => 'Account',
                    'use_simple_annotation_reader' => false,
                ],
                'Chaos\\Modules\\Lookup\\Entity' => [
                    'mapping' => true,
                    'type' => 'annotation',
                    'dir' => base_path('modules/core/Lookup/src/Entity'),
                    'alias' => 'Lookup',
                    'use_simple_annotation_reader' => false,
                ],
            ],
        ],
        'mysql' => [
            'connection' => 'mysql',
            'metadata_cache_driver' => [
                'type' => 'array',
            ],
            'mappings' => [
                'Chaos\\Modules\\Bank\\Entity' => [
                    'mapping' => true,
                    'type' => 'annotation',
                    'dir' => base_path('modules/app/Bank/Entity'),
                    'alias' => 'Bank',
                    'use_simple_annotation_reader' => false,
                ],
                'Chaos\\Modules\\Booking\\Entity' => [
                    'mapping' => true,
                    'type' => 'annotation',
                    'dir' => base_path('modules/app/Booking/Entity'),
                    'alias' => 'Booking',
                    'use_simple_annotation_reader' => false,
                ],
            ],
        ],
    ],
    'document_managers' => [
        'mongodb' => [
            'connection' => 'mongodb',
            'database' => 'default',
            'metadata_cache_driver' => [
                'type' => 'array',
            ],
            'mappings' => [
                'Chaos\\Modules\\Mongo\\Document' => [
                    'mapping' => true,
                    'type' => 'annotation',
                    'dir' => base_path('modules/app/Mongo/Document'),
                    'alias' => 'Mongo',
                ],
            ],
        ],
    ],
    'connections' => [
        'default' => [
            'dbname' => env('DB_DATABASE', 'forge'),
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'user' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => 'utf8mb4',
            'unix_socket' => env('DB_SOCKET', ''),
            'driver' => 'pdo_mysql',
            'schema_filter' => '',
        ],
        'mysql' => [
            'dbname' => env('DB2_DATABASE', 'forge'),
            'host' => env('DB2_HOST', '127.0.0.1'),
            'port' => env('DB2_PORT', '3306'),
            'user' => env('DB2_USERNAME', 'forge'),
            'password' => env('DB2_PASSWORD', ''),
            'charset' => 'utf8mb4',
            'unix_socket' => null,
            'driver' => 'mysqli',
            'schema_filter' => null,
            'driverOptions' => [],
        ],
        'mongodb' => [
            'server' => 'mongodb://localhost:27017',
            'options' => [],
            'driver_options' => [
                'typeMap' => [
                    'root' => 'array',
                    'document' => 'array',
                ],
            ],
        ],
    ],
    'proxy_interface_name' => 'Doctrine\\Persistence\\Proxy',
    'proxy_namespace' => 'DoctrineProxies',
    'proxy_dir' => base_path('storage/framework/cache/proxies'),
    'auto_generate_proxy_classes' => 2,
    'hydrator_namespace' => 'DoctrineHydrators',
    'hydrator_dir' => base_path('storage/framework/cache/hydrators'),
    'auto_generate_hydrator_classes' => 1,
    'default_entity_manager' => 'default',
    'default_document_manager' => 'default',
    'default_connection' => 'default',
];
```
