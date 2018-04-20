<?php

namespace Lxh\Migration\Console;

use Lxh\Migration\Console\Command;
use Phinx\Console\PhinxApplication;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class Application extends PhinxApplication
{
    /**
     * Class Constructor.
     *
     * Initialize the Phinx console application.
     *
     * @param string $version The Application Version
     */
    public function __construct()
    {
        $composerConfig = json_decode(file_get_contents(__DIR__ . '/../../../composer.json'));
        $version = $composerConfig->version;

        parent::__construct('Phinx by CakePHP - https://phinx.org.', $version);

        $this->addCommands([
            new Command\Init(),
            new Command\Create(),
            new Command\Migrate(),
            new Command\Rollback(),
            new Command\Status(),
            new Command\Breakpoint(),
            new Command\Test(),
            new Command\SeedCreate(),
            new Command\SeedRun(),
        ]);
    }

    /**
     * Runs the current application.
     *
     * @param \Symfony\Component\Console\Input\InputInterface $input An Input instance
     * @param \Symfony\Component\Console\Output\OutputInterface $output An Output instance
     * @return int 0 if everything went fine, or an error code
     */
    public function doRun(InputInterface $input, OutputInterface $output)
    {
        // always show the version information except when the user invokes the help
        // command as that already does it
        if ($input->hasParameterOption(['--help', '-h']) === false && $input->getFirstArgument() !== null) {
            $output->writeln($this->getLongVersion());
            $output->writeln('');
        }

        return parent::doRun($input, $output);
    }
}
