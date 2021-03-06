<?php

namespace Laravel\Ui;

use InvalidArgumentException;
use Illuminate\Console\Command;

class UiCommand extends Command
{
    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'ui
                    { type : The preset type (bootstrap, tailwind, vue, react) }
                    { --auth : Install authentication UI scaffolding }
                    { --option=* : Pass an option to the preset command }';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Swap the front-end scaffolding for the application';

    /**
     * Execute the console command.
     *
     * @return void
     *
     * @throws \InvalidArgumentException
     */
    public function handle()
    {
        if (static::hasMacro($this->argument('type'))) {
            return call_user_func(static::$macros[$this->argument('type')], $this);
        }

        if (! in_array($this->argument('type'), ['bootstrap', 'tailwind', 'vue', 'react'])) {
            throw new InvalidArgumentException('Invalid preset.');
        }

        $this->{$this->argument('type')}();

        if ($this->option('auth')) {
            $this->call('ui:auth', [
                'type' => Presets\Tailwind::installed() ? 'tailwind' : 'bootstrap',
            ]);
        }
    }

    /**
     * Install the "bootstrap" preset.
     *
     * @return void
     */
    protected function bootstrap()
    {
        Presets\Bootstrap::install();

        $this->reinstallJsFramework();

        $this->info('Bootstrap scaffolding installed successfully.');
        $this->comment('Please run "npm install && npm run dev" to compile your fresh scaffolding.');
    }

    /**
     * Install the "tailwind" preset.
     *
     * @return void
     */
    protected function tailwind()
    {
        Presets\Tailwind::install();

        $this->reinstallJsFramework();

        $this->info('Tailwind scaffolding installed successfully.');
        $this->comment('Please run "npm install && npm run dev" to compile your fresh scaffolding.');
    }

    /**
     * Install the "vue" preset.
     *
     * @return void
     */
    protected function vue()
    {
        if (! Presets\Tailwind::installed()) {
            Presets\Bootstrap::install();
        }

        Presets\Vue::install();

        $this->info('Vue scaffolding installed successfully.');
        $this->comment('Please run "npm install && npm run dev" to compile your fresh scaffolding.');
    }

    /**
     * Install the "react" preset.
     *
     * @return void
     */
    protected function react()
    {
        if (! Presets\Tailwind::installed()) {
            Presets\Bootstrap::install();
        }

        Presets\React::install();

        $this->info('React scaffolding installed successfully.');
        $this->comment('Please run "npm install && npm run dev" to compile your fresh scaffolding.');
    }

    /**
     * Reinstalls Vue or React if installed.
     *
     * @return void
     */
    protected function reinstallJsFramework()
    {
        if (Presets\React::installed()) {
            Presets\React::install();
        } elseif (Presets\Vue::installed()) {
            Presets\Vue::install();
        }
    }
}
