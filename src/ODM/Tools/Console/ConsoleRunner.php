<?php

namespace Chaos\Support\Doctrine\ODM\Tools\Console;

use Doctrine\DBAL\Tools\Console as DBALConsole;
use Doctrine\ODM\MongoDB\Tools\Console\Command as Command;
use Doctrine\ODM\MongoDB\Tools\Console\Helper\DocumentManagerHelper;
use PackageVersions\Versions;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Helper\HelperSet;

/**
 * Class ConsoleRunner.
 *
 * <code>
 * $doctrine = $container->get('doctrine');
 * foreach ($doctrine->getManagerNames() as $name => $id) {
 *   $helperSet = ConsoleRunner::createHelperSet(
 *     $doctrine->getManager($name)
 *   );
 *   $cli = ConsoleRunner::createApplication($helperSet, []);
 *   $cli->setAutoExit(false);
 *   $cli->run();
 * }
 * exit(0);
 * </code>
 *
 * @author t(-.-t) <ntd1712@mail.com>
 */
final class ConsoleRunner
{
    /**
     * Creates a Symfony Console HelperSet.
     *
     * @param \Doctrine\ODM\MongoDB\DocumentManager $documentManager The DocumentManager instance.
     *
     * @return HelperSet
     */
    public static function createHelperSet($documentManager)
    {
        return new HelperSet(
            [
                'dm' => new DocumentManagerHelper($documentManager),
            ]
        );
    }

    /**
     * Runs console with the given helper set.
     *
     * @param HelperSet $helperSet A set of helpers.
     * @param array $commands Optional.
     *
     * @throws \Exception
     *
     * @return void
     */
    public static function run(HelperSet $helperSet, array $commands = [])
    {
        $cli = self::createApplication($helperSet, $commands);
        $cli->run();
    }

    /**
     * Creates a console application with the given helper set and optional commands.
     *
     * @param HelperSet $helperSet A set of helpers.
     * @param array $commands Optional.
     *
     * @return Application
     */
    public static function createApplication(HelperSet $helperSet, array $commands = [])
    {
        $cli = new Application('Doctrine Command Line Interface', Versions::getVersion('doctrine/mongodb-odm'));
        $cli->setCatchExceptions(true);
        $cli->setHelperSet($helperSet);
        self::addCommands($cli);
        $cli->addCommands($commands);

        return $cli;
    }

    /**
     * Adds an array of command objects.
     *
     * @param Application $cli CLI application.
     *
     * @return void
     */
    public static function addCommands(Application $cli): void
    {
        $cli->addCommands(
            [
                // DBAL Commands
                new DBALConsole\Command\ImportCommand(),
                new DBALConsole\Command\ReservedWordsCommand(),
                new DBALConsole\Command\RunSqlCommand(),

                // ORM Commands
                new Command\ClearCache\MetadataCommand(),
                new Command\Schema\CreateCommand(),
                new Command\Schema\DropCommand(),
                new Command\Schema\ShardCommand(),
                new Command\Schema\UpdateCommand(),
                new Command\Schema\ValidateCommand(),
                new Command\GenerateHydratorsCommand(),
                new Command\GeneratePersistentCollectionsCommand(),
                new Command\GenerateProxiesCommand(),
                new Command\QueryCommand()
            ]
        );
    }

    /**
     * Prints the instructions to create a configuration file.
     *
     * @return void
     */
    public static function printCliConfigTemplate()
    {
        echo <<<'HELP'
You are missing a "cli-config.php" or "config/cli-config.php" file in your
project, which is required to get the Doctrine Console working. You can use the
following sample as a template:

<?php
use Chaos\Support\Doctrine\ODM\Tools\Console\ConsoleRunner;

// replace with file to your own project bootstrap
require_once 'bootstrap.php';

// replace with mechanism to retrieve DocumentManager in your app
$documentManager = GetDocumentManager();

return ConsoleRunner::createHelperSet($documentManager);

HELP;
    }
}
