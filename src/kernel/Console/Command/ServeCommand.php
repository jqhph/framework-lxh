<?php

namespace Lxh\Console\Command;

use Lxh\Console\Command;
use Symfony\Component\Console\Input\InputOption;

class ServeCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'serve';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Runs PHP built-in web server.';

    protected $host = 'localhost';

    protected $port = 8082;

    protected $router;

    public function fire()
    {
        $host = $this->option('host');
        $port = $this->option('port');
        $route = $this->option('route');

        $address = "{$host}:{$port}";

        $documentRoot = rtrim(resolve('app')->getPublicPath(), '/');

        if ($route && !file_exists($route)) {;
            return $this->error("Router [$route] is not exists");
        }

        $this->line("Server started on http://{$address}/\n");
        $this->line("Document root is \"{$documentRoot}\"\n");
        if ($route) {
            $this->line("Routing file is \"$route\"\n");
        }
        $this->line("Quit the server with CTRL-C or COMMAND-C.\n");

        passthru('"' . PHP_BINARY . '"' . " -S {$address} -t \"{$documentRoot}\" $route");
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['host', '', InputOption::VALUE_OPTIONAL, 'Web server host.', $this->host],
            ['port', 'p', InputOption::VALUE_OPTIONAL, 'Web server port.', $this->port],
            ['route', 'r', InputOption::VALUE_OPTIONAL, 'Web server route.', $this->router],
        ];
    }
}
