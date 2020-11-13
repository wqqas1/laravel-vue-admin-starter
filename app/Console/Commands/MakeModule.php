<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class MakeModule extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:tkr-module {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new module model, class, controller, requests and vue cruds';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->ucArgument = ucfirst($this->argument('name'));
        $this->loArgument = strtolower($this->argument('name'));

        foreach ($this->getFiles() as $file => $path) {
            $this->createStub($file, $path);
        }

        $this->showOutput($this->ucArgument);
    }

    /**
     * Get file to stub.
     *
     * @return array
     */
    protected function getFiles()
    {
        $ModulePlural =  Str::plural($this->loArgument);
        $lowModulePlural = strtolower($ModulePlural);
        $ucfModulePlural = ucfirst($ModulePlural);
        $camelModulePlural = (Str::camel($ModulePlural));

        $lowModule = $this->loArgument;
        $ucfModule = ucfirst($lowModule);
        $camelModule = (Str::camel($lowModule));
        mkdir(base_path("resources/adminapp/js/cruds/{$ucfModulePlural}"), 0755);
        mkdir(base_path("resources/adminapp/js/store/cruds/{$ucfModulePlural}"), 0755);
        $files = [
            'app.Models.Model.php.stub' => "app/Models/{$ucfModule}.php",
            'app.Http.Controllers.Api.V1.Admin.ApiController.php.stub' => "app/Http/Controllers/Api/V1/Admin/{$ucfModulePlural}ApiController.php",
            'app.Http.Requests.StoreRequest.php.stub' => "app/Http/Requests/Store{$ucfModule}Request.php",
            'app.Http.Requests.UpdateRequest.php.stub' => "app/Http/Requests/Update{$ucfModule}Request.php",
            'app.Http.Resources.Admin.Resource.php.stub' => "app/Http/Resources/Admin/{$ucfModule}Resource.php",
            'resources.adminapp.js.cruds.Create.vue.stub' => "resources/adminapp/js/cruds/{$ucfModulePlural}/Create.vue",
            'resources.adminapp.js.cruds.Edit.vue.stub' => "resources/adminapp/js/cruds/{$ucfModulePlural}/Edit.vue",
            'resources.adminapp.js.cruds.Index.vue.stub' => "resources/adminapp/js/cruds/{$ucfModulePlural}/Index.vue",
            'resources.adminapp.js.cruds.Show.vue.stub' => "resources/adminapp/js/cruds/{$ucfModulePlural}/Show.vue",
            'resources.adminapp.js.store.cruds.index.js.stub' => "resources/adminapp/js/store/cruds/{$ucfModulePlural}/index.js",
            'resources.adminapp.js.store.cruds.single.js.stub' => "resources/adminapp/js/store/cruds/{$ucfModulePlural}/single.js",

        ];

        return $files;
    }

    /**
     * Create files form stubs.
     *
     * @param  string  $file
     * @param  string  $path
     * @return void
     */
    protected function createStub($file, $path)
    {
        $fileContent = file_get_contents(base_path("stubs/tekrow/{$file}"));
        $stub = str_replace(
            ['{{ DummyText }}', '{{ dummyText }}', '{{ DummyTextPlu }}', '{{ dummyTextPlu }}'],
            [
                ucfirst($this->argument('name')),
                strtolower($this->argument('name')),
                Str::plural(ucfirst($this->argument('name'))),
                Str::plural(strtolower($this->argument('name'))),
            ],
            $fileContent
        );

        $file = fopen(base_path("{$path}"), 'w+');
        fwrite($file, $stub);
        fclose($file);
    }

    /**
     * Generate info.
     *
     * @return void
     */
    protected function showOutput()
    {
        $ArgumentPlu = Str::plural(ucfirst($this->argument('name')));
        $argumentPlu = Str::plural(strtolower($this->argument('name')));
        $argument = strtolower($this->argument('name'));
        $Argument = ucfirst($this->argument('name'));

        $this->info('Please add following routes to: resources/adminapp/js/routes/routes.js');

        $this->info("\n
        {
            path: '{$argumentPlu}',
            name: '{$argumentPlu}.index',
            component: () => import('@cruds/{$ArgumentPlu}/Index.vue'),
            meta: { title: '{$ArgumentPlu}' }
          },
          {
            path: '{$argumentPlu}/create',
            name: '{$argumentPlu}.create',
            component: () => import('@cruds/{$ArgumentPlu}/Create.vue'),
            meta: { title: '{$ArgumentPlu}' }
          },
          {
            path: '{$argumentPlu}/:id',
            name: '{$argumentPlu}.show',
            component: () => import('@cruds/{$ArgumentPlu}/Show.vue'),
            meta: { title: '{$ArgumentPlu}' }
          },
          {
            path: '{$argumentPlu}/:id/edit',
            name: '{$argumentPlu}.edit',
            component: () => import('@cruds/{$ArgumentPlu}/Edit.vue'),
            meta: { title: '{$ArgumentPlu}' 
          }
        }
      ");

        $this->info("Please add following to: resources/adminapp/js/store/store.js\n
        before \n
        Vue.use(Vuex)
        ");

        $this->info("\n
        import {$ArgumentPlu}Index from './cruds/{$ArgumentPlu}'\n
        import {$ArgumentPlu}Single from './cruds/{$ArgumentPlu}/single'\n
      ");

        $this->info("Please add following to: resources/adminapp/js/store/store.js\n
        after \n
        export default new Vuex.Store({
            modules: {
        ");

        $this->info("\n
        {$ArgumentPlu}Index,\n
        {$ArgumentPlu}Single,\n
      ");

    }
}
