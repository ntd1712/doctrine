<?php

namespace Chaos\Support\Doctrine\ORM;

use Doctrine\ORM\Mapping\DefaultEntityListenerResolver as BaseDefaultEntityListenerResolver;

/**
 * Class DefaultEntityListenerResolver.
 *
 * <code>
 * $entityManager = $container->get('doctrine')->getManager();
 * $entityListenerResolver = $entityManager->getConfiguration()->getEntityListenerResolver();
 * $entityListenerResolver->setContainer($container);
 * </code>
 *
 * @author t(-.-t) <ntd1712@mail.com>
 */
class DefaultEntityListenerResolver extends BaseDefaultEntityListenerResolver
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
     * @param string $className Fully-qualified listener class name. e.g. MyApplication\Entity\Listener
     *
     * @return object
     */
    public function resolve($className)
    {
        $listener = parent::resolve($className);

        if (method_exists($listener, 'setContainer')) {
            $listener->setContainer($this->container);
        }

        return $listener;
    }
}
