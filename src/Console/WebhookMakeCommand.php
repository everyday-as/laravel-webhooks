<?php

namespace GmodStore\LaravelWebhooks\Console;

use Illuminate\Console\GeneratorCommand;

class WebhookMakeCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:webhook';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new webhook class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Webhook';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/stubs/webhook.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param string $rootNamespace
     *
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\Http\Webhooks';
    }
}
