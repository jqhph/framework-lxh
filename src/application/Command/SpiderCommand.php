<?php
/**
 * comment
 *
 * @author Jqh
 * @date   2017-08-14 02:27:31
 */

namespace Lxh\Command;

use Lxh\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Lxh\Contracts\Container\Container;

class SpiderCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'spider';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';


    /**
     * The help information description.
     *
     * @var string
     */
    protected $help;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Container $container)
    {
        parent::__construct($container);
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        header("Content-Type:text/html;charset=gb2312");

        $crawler = new \Lxh\Kernel\Spiders\Crawler();

        $sorts = $crawler->makeProdsData();

        $crawler->outputRequestResult();

    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
//            ['argument-name', InputArgument::REQUIRED, 'A description text.'],
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
//            ['option-name', 'The alias.', InputOption::VALUE_OPTIONAL, 'A description text.', 'The default value.'],
        ];
    }
}
