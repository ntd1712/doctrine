<?php

namespace Chaos\Support\Doctrine\ORM;

use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Cache\ApcuCache;
use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\Cache\CacheProvider;
use Doctrine\Common\Cache\FilesystemCache;
use Doctrine\Common\Cache\MemcachedCache;
use Doctrine\Common\Cache\PhpFileCache;
use Doctrine\Common\Cache\RedisCache;
use Doctrine\Common\EventManager;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Events;
use Doctrine\ORM\Mapping\Driver\SimplifiedXmlDriver;
use Doctrine\ORM\Mapping\Driver\SimplifiedYamlDriver;
use Doctrine\ORM\Mapping\Driver\XmlDriver;
use Doctrine\ORM\Mapping\Driver\YamlDriver;
use Doctrine\Persistence\Mapping\Driver\MappingDriverChain;
use Doctrine\Persistence\Mapping\Driver\PHPDriver;
use Doctrine\Persistence\Mapping\Driver\StaticPHPDriver;

/**
 * Class EntityManagerFactory.
 *
 * <code>
 * $manager = (new EntityManagerFactory())($container, 'mysql', $config['doctrine'])
 * $container['mysql_entity_manager'] = $manager;
 * $container['mysql_connection'] = $manager->getConnection();
 * </code>
 *
 * @author t(-.-t) <ntd1712@mail.com>
 */
class EntityManagerFactory
{
    /**
     * Creates a <tt>EntityManager</tt> object.
     *
     * @param \Psr\Container\ContainerInterface $container The Container instance.
     * @param string $requestedName The ID of the object being instantiated.
     * @param null|array $options An array of configuration relevant to the object.
     *
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Doctrine\ORM\ORMException
     *
     * @return EntityManager|\Doctrine\ORM\EntityManagerInterface|\Doctrine\Persistence\ObjectManager
     */
    public function __invoke($container, $requestedName, array $options = null)
    {
        $manager = $options['entity_managers'][$requestedName ?: $options['default_entity_manager']];
        $entityManager = EntityManager::create(
            $connection = $options['connections'][$manager['connection']],
            $configuration = $this->createConfiguration($connection, $manager, $options),
            $eventManager = new EventManager()
        );

        /* @noinspection PhpUndefinedMethodInspection */
        $configuration->getEntityListenerResolver()->setContainer($container);
        $configuration->setDefaultQueryHint('options', $options);

        if (!empty($connection['schema_filter'])) {
            $eventManager->addEventListener(
                Events::loadClassMetadata,
                new Tools\TablePrefixEntityListener($connection['schema_filter'])
            );
            // or: $eventManager->addEventSubscriber(new Tools\TablePrefixEntityListener($connection['schema_filter']));
        }

        if (!empty($connection['mapping_types'])) {
            $this->registerMappingTypes(
                $connection['mapping_types'],
                $entityManager->getConnection()->getDatabasePlatform()
            );
        }

        if (!empty($options['types'])) {
            $this->registerTypes($options['types']);
        }

        // ensure an attempt to autoload an annotation class is made
        AnnotationRegistry::registerUniqueLoader('class_exists');

        return $entityManager;
    }

    /**
     * @param array $connection CONNECTION options.
     * @param array $manager MANAGER options.
     * @param array $options Options.
     *
     * @throws \Doctrine\ORM\ORMException
     *
     * @return Configuration
     */
    private function createConfiguration($connection, $manager, $options)
    {
        $configuration = new Configuration();
        $configuration->setProxyNamespace($options['proxy_namespace']);
        $configuration->setProxyDir($proxyDir = $options['proxy_dir'] ?: sys_get_temp_dir());
        $configuration->setAutoGenerateProxyClasses($options['auto_generate_proxy_classes']);
        $configuration->setMetadataDriverImpl($this->createMappingDriver($manager['mappings'], $configuration));

        if (isset($connection['auto_commit'])) {
            $configuration->setAutoCommit($connection['auto_commit']);
        }

        if (isset($connection['logging'])) {
            $configuration->setSQLLogger(new $connection['logging']());
        }

        /*if (!empty($manager['hydration_cache_driver'])) {
            $configuration->setHydrationCacheImpl($this->getCacheDriver($manager['hydration_cache_driver'], $cacheNs));
        }*/

        if (isset($manager['metadata_cache_driver'])) {
            $configuration->setMetadataCacheImpl(
                $this->createCacheDriver($proxyDir, $manager['metadata_cache_driver'])
            );
        }

        if (isset($manager['query_cache_driver'])) {
            $configuration->setQueryCacheImpl($this->createCacheDriver($proxyDir, $manager['query_cache_driver']));
        }

        if (isset($manager['result_cache_driver'])) {
            $configuration->setResultCacheImpl($this->createCacheDriver($proxyDir, $manager['result_cache_driver']));
        }

        if (isset($manager['class_metadata_factory_name'])) {
            $configuration->setClassMetadataFactoryName($manager['class_metadata_factory_name']);
        }

        if (isset($manager['default_repository_class'])) {
            $configuration->setDefaultRepositoryClassName($manager['default_repository_class']);
        }

        if (isset($manager['entity_listener_resolver'])) {
            $configuration->setEntityListenerResolver(new $manager['entity_listener_resolver']());
        } else {
            $configuration->setEntityListenerResolver(new DefaultEntityListenerResolver());
        }

        if (isset($manager['repository_factory'])) {
            $configuration->setRepositoryFactory(new $manager['repository_factory']());
        }

        if (isset($manager['ast'])) {
            $configuration->setDefaultQueryHints($manager['ast']);
        }

        if (isset($manager['dql']['datetime_functions'])) {
            $configuration->setCustomDatetimeFunctions($manager['dql']['datetime_functions']);
        }

        if (isset($manager['dql']['numeric_functions'])) {
            $configuration->setCustomNumericFunctions($manager['dql']['numeric_functions']);
        }

        if (isset($manager['dql']['string_functions'])) {
            $configuration->setCustomStringFunctions($manager['dql']['string_functions']);
        }

        return $configuration;
    }

    /**
     * @param string $proxyDir Proxy directory.
     * @param array $options CACHE_DRIVER options.
     *
     * @return \Doctrine\Common\Cache\Cache
     */
    private function createCacheDriver($proxyDir, $options)
    {
        switch ($options['type']) {
            case 'apcu':
                if (extension_loaded('apcu')) {
                    $cache = new ApcuCache();
                }
                break;
            case 'filesystem':
                $cache = new FilesystemCache(
                    $options['directory'],
                    isset($options['extension']) ? $options['extension'] : FilesystemCache::EXTENSION,
                    isset($options['umask']) ? $options['umask'] : 0002
                );
                break;
            case 'memcached':
                if (extension_loaded('memcached')) {
                    $memcache = new \Memcached(
                        isset($options['persistent_id']) ? $options['persistent_id'] : '',
                        isset($options['on_new_object_cb']) ? $options['on_new_object_cb'] : null
                    );
                    $memcache->addServer(
                        $options['host'],
                        $options['port'],
                        isset($options['weight']) ? $options['weight'] : 0
                    );

                    $cache = new MemcachedCache();
                    $cache->setMemcached($memcache);
                }
                break;
            case 'php':
                $cache = new PhpFileCache(
                    $options['directory'],
                    isset($options['extension']) ? $options['extension'] : FilesystemCache::EXTENSION,
                    isset($options['umask']) ? $options['umask'] : 0002
                );
                break;
            case 'redis':
                if (extension_loaded('redis')) {
                    $redis = new \Redis();
                    $redis->connect(
                        $options['host'],
                        isset($options['port']) ? $options['port'] : 6379,
                        isset($options['timeout']) ? $options['timeout'] : 0.0,
                        isset($options['reserved']) ? $options['reserved'] : null,
                        isset($options['retry_interval']) ? $options['retry_interval'] : 0
                        // isset($options['read_timeout']) ? $options['read_timeout'] : 0.0
                    );
                    $redis->select(isset($options['dbIndex']) ? $options['dbIndex'] : 0);

                    $cache = new RedisCache();
                    $cache->setRedis($redis);
                }
                break;
            default:
                $cache = new ArrayCache();
        }

        if (empty($cache)) {
            throw new \RuntimeException(sprintf('Unsupported cache driver: %s', $options['type']));
        }

        if ($cache instanceof CacheProvider) {
            $namespace = $cache->getNamespace();

            if ('' !== $namespace) {
                $namespace .= ':';
            }

            $cache->setNamespace($namespace . 'dc2_' . md5($proxyDir) . '_'); // to avoid collisions
        }

        return $cache;
    }

    /**
     * @param array $mappings Array of mappings.
     * @param Configuration $configuration The Configuration instance.
     *
     * @return null|mixed|\Doctrine\Persistence\Mapping\Driver\MappingDriver
     */
    private function createMappingDriver(array $mappings, Configuration $configuration)
    {
        $mappingDriverChain = new MappingDriverChain();

        foreach ($mappings as $namespace => $mapping) {
            if (true !== $mapping['mapping']) {
                continue;
            }

            switch ($mapping['type']) {
                case 'annotation':
                    $mappingDriverChain->addDriver(
                        $configuration->newDefaultAnnotationDriver(
                            $mapping['dir'],
                            isset($mapping['use_simple_annotation_reader'])
                                ? $mapping['use_simple_annotation_reader']
                                : true
                        ),
                        $ns = isset($mapping['prefix']) ? $mapping['prefix'] : $namespace
                    );

                    if (isset($mapping['alias'])) {
                        $configuration->addEntityNamespace($mapping['alias'], $ns);
                    }
                    break;
                case 'php':
                    $mappingDriverChain->addDriver(new PHPDriver($mapping['dir']), $namespace);
                    break;
                case 'staticphp':
                    $mappingDriverChain->addDriver(new StaticPHPDriver($mapping['dir']), $namespace);
                    break;
                case 'simplifiedxml':
                    $mappingDriverChain->addDriver(
                        new SimplifiedXmlDriver(
                            $mapping['dir'],
                            isset($mapping['fileExtension'])
                                ? $mapping['fileExtension']
                                : SimplifiedXmlDriver::DEFAULT_FILE_EXTENSION
                        ),
                        $namespace
                    );
                    break;
                case 'simplifiedyml':
                    $mappingDriverChain->addDriver(
                        new SimplifiedYamlDriver(
                            $mapping['dir'],
                            isset($mapping['fileExtension'])
                                ? $mapping['fileExtension']
                                : SimplifiedYamlDriver::DEFAULT_FILE_EXTENSION
                        ),
                        $namespace
                    );
                    break;
                case 'xml':
                    $mappingDriverChain->addDriver(
                        new XmlDriver(
                            $mapping['dir'],
                            isset($mapping['fileExtension'])
                                ? $mapping['fileExtension']
                                : XmlDriver::DEFAULT_FILE_EXTENSION
                        ),
                        $namespace
                    );
                    break;
                case 'yaml':
                    $mappingDriverChain->addDriver(
                        new YamlDriver(
                            $mapping['dir'],
                            isset($mapping['fileExtension'])
                                ? $mapping['fileExtension']
                                : YamlDriver::DEFAULT_FILE_EXTENSION
                        ),
                        $namespace
                    );
                    break;
                default:
            }
        }

        return $mappingDriverChain;
    }

    /**
     * @param array $types Array of custom types.
     *
     * @throws \Doctrine\DBAL\DBALException
     *
     * @return void
     */
    private function registerTypes($types)
    {
        foreach ($types as $doctrineType => $args) {
            Type::hasType($doctrineType)
                ? Type::overrideType($doctrineType, $args['class'])
                : Type::addType($doctrineType, $args['class']);
        }
    }

    /**
     * @param array $types Array of type mappings.
     * @param \Doctrine\DBAL\Platforms\AbstractPlatform $platform The Platform instance.
     *
     * @throws \Doctrine\DBAL\DBALException
     *
     * @return void
     */
    private function registerMappingTypes($types, $platform)
    {
        foreach ($types as $dbType => $doctrineType) {
            $platform->registerDoctrineTypeMapping($dbType, $doctrineType);
        }
    }
}
