<?php

namespace Chaos\Support\Doctrine\ODM;

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
use Doctrine\ODM\MongoDB\Configuration;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Mapping\Driver\SimplifiedXmlDriver;
use Doctrine\ODM\MongoDB\Mapping\Driver\XmlDriver;
use Doctrine\Persistence\Mapping\Driver\MappingDriverChain;
use MongoDB\Client;

/**
 * Class DocumentManagerFactory.
 *
 * <code>
 * $manager = (new DocumentManagerFactory())($container, 'mongodb', $config['doctrine'])
 * $container['mongodb_document_manager'] = $manager;
 * $container['mongodb_connection'] = $manager->getClient();
 * </code>
 *
 * @author t(-.-t) <ntd1712@mail.com>
 */
class DocumentManagerFactory
{
    /**
     * Creates a <tt>DocumentManager</tt> object.
     *
     * @param \Psr\Container\ContainerInterface $container The Container instance.
     * @param string $requestedName The ID of the object being instantiated.
     * @param null|array $options An array of configuration relevant to the object.
     *
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Doctrine\ODM\MongoDB\MongoDBException
     *
     * @return DocumentManager|\Doctrine\Persistence\ObjectManager
     */
    public function __invoke($container, $requestedName, array $options = null)
    {
        $manager = $options['document_managers'][$requestedName ?: $options['default_document_manager']];
        $documentManager = DocumentManager::create(
            $this->createClient($options['connections'][$manager['connection']]),
            $configuration = $this->createConfiguration($manager, $options),
            new EventManager()
        );

        if (!empty($options['types'])) {
            $this->registerTypes($options['types']);
        }

        // ensure an attempt to autoload an annotation class is made
        AnnotationRegistry::registerUniqueLoader('class_exists');
        spl_autoload_register($configuration->getProxyManagerConfiguration()->getProxyAutoloader());

        return $documentManager;
    }

    /**
     * @param array $options Options.
     *
     * @return Client
     */
    private function createClient($options)
    {
        return new Client($options['server'], $options['options'], $options['driver_options']);
    }

    /**
     * @param array $manager MANAGER options.
     * @param array $options Options.
     *
     * @throws \Doctrine\ODM\MongoDB\MongoDBException
     *
     * @return Configuration
     */
    private function createConfiguration($manager, $options)
    {
        $configuration = new Configuration();
        $configuration->setDefaultDB(
            isset($options['default_database']) ? $options['default_database'] : $manager['database']
        );
        $configuration->setProxyNamespace($options['proxy_namespace']);
        $configuration->setProxyDir($proxyDir = $options['proxy_dir'] ?: sys_get_temp_dir());
        $configuration->setHydratorNamespace($options['hydrator_namespace']);
        $configuration->setHydratorDir($options['hydrator_dir']);
        $configuration->setAutoGenerateHydratorClasses($options['auto_generate_hydrator_classes']);
        $configuration->setMetadataDriverImpl($this->createMappingDriver($manager['mappings'], $configuration));

        if (isset($manager['metadata_cache_driver'])) {
            $configuration->setMetadataCacheImpl(
                $this->createCacheDriver($proxyDir, $manager['metadata_cache_driver'])
            );
        }

        if (isset($options['auto_generate_proxy_classes'])) {
            $configuration->setAutoGenerateProxyClasses($options['auto_generate_proxy_classes']);
        }

        if (isset($options['persistent_collection_namespace'])) {
            $configuration->setPersistentCollectionNamespace($options['persistent_collection_namespace']);
        }

        if (isset($options['persistent_collection_dir'])) {
            $configuration->setPersistentCollectionDir($options['persistent_collection_dir']);
        }

        if (isset($options['auto_generate_persistent_collection_classes'])) {
            $configuration->setAutoGeneratePersistentCollectionClasses(
                $options['auto_generate_persistent_collection_classes']
            );
        }

        if (isset($manager['class_metadata_factory_name'])) {
            $configuration->setClassMetadataFactoryName($manager['class_metadata_factory_name']);
        }

        if (isset($manager['default_commit_options'])) {
            $configuration->setDefaultCommitOptions($manager['default_commit_options']);
        }

        if (!empty($manager['filters'])) {
            foreach ($manager['filters'] as $name => $args) {
                if (true !== $args['enabled']) {
                    continue;
                }

                $configuration->addFilter($name, $args['class'], isset($args['parameters']) ? $args['parameters'] : []);
            }
        }

        if (isset($manager['default_document_repository_class'])) {
            $configuration->setDefaultDocumentRepositoryClassName($manager['default_document_repository_class']);
        }

        if (isset($manager['default_gridfs_repository_class'])) {
            $configuration->setDefaultGridFSRepositoryClassName($manager['default_gridfs_repository_class']);
        }

        if (isset($manager['repository_factory'])) {
            $configuration->setRepositoryFactory(new $manager['repository_factory']());
        }

        if (isset($manager['persistent_collection_factory'])) {
            $configuration->setPersistentCollectionFactory(new $manager['persistent_collection_factory']());
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
                        $configuration->newDefaultAnnotationDriver((array) $mapping['dir']),
                        $ns = isset($mapping['prefix']) ? $mapping['prefix'] : $namespace
                    );

                    if (isset($mapping['alias'])) {
                        $configuration->addDocumentNamespace($mapping['alias'], $ns);
                    }
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
}
