<?php

namespace Chaos\Support\Doctrine;

use Doctrine\Persistence\AbstractManagerRegistry;
use Exception;
use RuntimeException;

/**
 * Class ManagerRegistry.
 *
 * <code>
 * $doctrine = new ManagerRegistry(
 *   'anonymous',
 *   ['default' => 'default_connection', 'mysql' => 'mysql_connection', 'mongodb' => 'mongodb_connection'],
 *   ['default' => 'default_entity_manager', 'mysql' => 'mysql_entity_manager', 'mongo' => 'mongodb_document_manager'],
 *   'default',
 *   'default',
 *   \Doctrine\Persistence\Proxy::class
 * );
 * $doctrine->setContainer($container);
 * $container['doctrine'] = $doctrine;
 * </code>
 *
 * <code>
 * $entityManager   = $doctrine->getManager();
 * $entityManager2  = $doctrine->getManager('mysql');
 * $documentManager = $doctrine->getManager('mongodb');
 * </code>
 */
class ManagerRegistry extends AbstractManagerRegistry
{
    // <editor-fold defaultstate="collapsed" desc="Default properties">

    /**
     * @var \Psr\Container\ContainerInterface
     */
    protected $container;

    /**
     * {@inheritDoc}
     *
     * @param \Psr\Container\ContainerInterface $container The Container instance.
     *
     * @return $this
     */
    public function setContainer($container)
    {
        $this->container = $container;

        return $this;
    }

    // </editor-fold>

    /**
     * {@inheritDoc}
     *
     * @param string $name The name of the service.
     *
     * @return mixed
     */
    protected function getService($name)
    {
        return $this->container->get($name);
    }

    /**
     * {@inheritDoc}
     *
     * @param string $name The name of the service.
     *
     * @return void
     */
    protected function resetService($name)
    {
        if (method_exists($this->container, 'set')) {
            $this->container->set($name, null);
        }
    }

    /**
     * {@inheritDoc}
     *
     * @param string $alias The alias.
     *
     * @return string The full namespace.
     */
    public function getAliasNamespace($alias)
    {
        foreach ($this->getManagerNames() as $id) {
            /* @var \Doctrine\ODM\MongoDB\DocumentManager|\Doctrine\ORM\EntityManager $manager */
            $manager = $this->getService($id);

            if (is_a($manager, 'Doctrine\ORM\EntityManager')) {
                try {
                    return $manager->getConfiguration()->getEntityNamespace($alias);
                } catch (Exception $e) {
                    // Probably mapped by another entity manager, or invalid, just ignore this here.
                }
            } elseif (is_a($manager, 'Doctrine\ODM\MongoDB\DocumentManager')) {
                try {
                    return $manager->getConfiguration()->getDocumentNamespace($alias);
                } catch (Exception $e) {
                    //
                }
            }
        }

        throw new RuntimeException(sprintf('The namespace alias "%s" is not known to any manager.', $alias));
    }
}
