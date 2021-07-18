<?php

namespace Chaos\Support\Doctrine\ORM\Tools;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\Mapping\ClassMetadataInfo;

/**
 * Class TablePrefixEntityListener.
 *
 * @see http://doctrine-project.org/projects/doctrine-orm/en/latest/cookbook/sql-table-prefixes.html
 */
class TablePrefixEntityListener implements EventSubscriber
{
    /**
     * @var string
     */
    private $prefix;

    /**
     * Constructor.
     *
     * @param string $prefix Table prefix.
     */
    public function __construct($prefix)
    {
        $this->prefix = $prefix;
    }

    /**
     * {@inheritDoc}
     *
     * @return string[]
     */
    public function getSubscribedEvents()
    {
        return [
            Events::loadClassMetadata
        ];
    }

    /**
     * @param LoadClassMetadataEventArgs $eventArgs Event arguments.
     *
     * @return void
     */
    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs)
    {
        $metadata = $eventArgs->getClassMetadata();
        $mappings = $metadata->getAssociationMappings();

        if (!$metadata->isInheritanceTypeSingleTable() || $metadata->rootEntityName === $metadata->getName()) {
            $metadata->setPrimaryTable(['name' => $this->prefix . $metadata->getTableName()]);
        }

        foreach ($mappings as $fieldName => $mapping) {
            if (ClassMetadataInfo::MANY_TO_MANY == $mapping['type'] && $mapping['isOwningSide']) {
                $metadata->associationMappings[$fieldName]['joinTable']['name']
                    = $this->prefix . $mapping['joinTable']['name'];
            }
        }
    }
}
