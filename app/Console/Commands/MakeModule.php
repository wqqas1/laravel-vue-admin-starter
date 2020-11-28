<?php

namespace App\Console\Commands;

use App\Models\Role;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use App\Models\Permission;

class MakeModule extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tkr:module {name}';

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
        $this->ArgumentPlu = Str::plural(Str::studly($this->argument('name')));
        $this->argumentPlu = Str::plural(Str::camel($this->argument('name')));
        $this->argument_plu = Str::plural(Str::snake($this->argument('name')));
        $this->argumentKebabPlu = Str::plural(Str::kebab($this->argument('name')));
        $this->argument = Str::singular(Str::camel($this->argument('name')));
        $this->argument_snake = Str::singular(Str::snake($this->argument('name')));
        $this->argument_kebab = Str::singular(Str::kebab($this->argument('name')));
        $this->Argument = Str::singular(Str::studly($this->argument('name')));

        foreach ($this->getFiles() as $file => $path) {
            $this->createStub($file, $path);
        }

        $this->addRoutes();
        $this->addApiRoutes();
        $this->addStore();
        $this->addSidebar();


        $this->createModulePermissions();
        $this->call('make:migration', [
            'name' => 'create_' . $this->argument_plu . '_table',
            '--create' => $this->argument_plu
        ]);
        $this->call('migrate');
        $this->showOutput();

    }
    /**
     * Get file to stub.
     *
     * @return array
     */
    protected function createModulePermissions(){
        Permission::insert([
            ['title' =>$this->argument_snake . '_create'],
            ['title' =>$this->argument_snake . '_edit'],
            ['title' =>$this->argument_snake . '_show'],
            ['title' =>$this->argument_snake . '_delete'],
            ['title' =>$this->argument_snake . '_access'],
        ]);
        $superAdmin = Role::where('title','=','SuperAdmin')->first();
        $superAdmin->permissions()->sync(Permission::all()->pluck('id'));
    }
    /**
     * Get file to stub.
     *
     * @return array
     */
    protected function getFiles()
    {
        if(!file_exists(base_path("resources/adminapp/js/cruds/{$this->ArgumentPlu}")))
            mkdir(base_path("resources/adminapp/js/cruds/{$this->ArgumentPlu}"), 0755);
        if(!file_exists(base_path("resources/adminapp/js/store/cruds/{$this->ArgumentPlu}")))
            mkdir(base_path("resources/adminapp/js/store/cruds/{$this->ArgumentPlu}"), 0755);
        $files = [
            'app.Models.Model.php.stub' => "app/Models/{$this->Argument}.php",
            'app.Http.Controllers.Api.V1.Admin.ApiController.php.stub' => "app/Http/Controllers/Api/V1/Admin/{$this->ArgumentPlu}ApiController.php",
            'app.Http.Requests.StoreRequest.php.stub' => "app/Http/Requests/Store{$this->Argument}Request.php",
            'app.Http.Requests.UpdateRequest.php.stub' => "app/Http/Requests/Update{$this->Argument}Request.php",
            'app.Http.Resources.Admin.Resource.php.stub' => "app/Http/Resources/Admin/{$this->Argument}Resource.php",
            'resources.adminapp.js.cruds.Create.vue.stub' => "resources/adminapp/js/cruds/{$this->ArgumentPlu}/Create.vue",
            'resources.adminapp.js.cruds.Edit.vue.stub' => "resources/adminapp/js/cruds/{$this->ArgumentPlu}/Edit.vue",
            'resources.adminapp.js.cruds.Index.vue.stub' => "resources/adminapp/js/cruds/{$this->ArgumentPlu}/Index.vue",
            'resources.adminapp.js.cruds.Show.vue.stub' => "resources/adminapp/js/cruds/{$this->ArgumentPlu}/Show.vue",
            'resources.adminapp.js.store.cruds.index.js.stub' => "resources/adminapp/js/store/cruds/{$this->ArgumentPlu}/index.js",
            'resources.adminapp.js.store.cruds.single.js.stub' => "resources/adminapp/js/store/cruds/{$this->ArgumentPlu}/single.js",

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
            ['{{ dummy-text-plu }}','{{ dummy-text }}','{{ dummy_text_plu }}', '{{ dummy_text }}', '{{ DummyText }}', '{{ dummyText }}', '{{ DummyTextPlu }}', '{{ dummyTextPlu }}'],
            [
                $this->argumentKebabPlu,
                $this->argument_kebab,
                $this->argument_plu,
                $this->argument_snake,
                $this->Argument,
                $this->argument,
                $this->ArgumentPlu,
                $this->argumentPlu,
            ],
            $fileContent
        );

        $file = fopen(base_path("{$path}"), 'w+');
        fwrite($file, $stub);
        fclose($file);
    }
    /**
     * Add sidebar link for module.
     *
     */
    protected function addSidebar(){
        $fileContent = file_get_contents(base_path("resources/adminapp/js/pages/Layout/DashboardLayout.vue"));
        $fileContent = str_replace('sidebarLinks: [',$this->getModuleSideBarCode(), $fileContent);
        $file = fopen(base_path("resources/adminapp/js/pages/Layout/DashboardLayout.vue"), 'w+');
        fwrite($file, $fileContent);
        fclose($file);
    }
    /**
     *
     * Return sidebar code.
     * @return String
     */
    protected function getModuleSideBarCode(){
        return "
        sidebarLinks: [
            {
              title: '{$this->ArgumentPlu}',
              icon: 'table_view',
              path: { name: '{$this->argumentPlu}.index' },
              gate: '{$this->argument_snake}_access'
            },";
    }
    /**
     *
     * Return module routes to vue router.
     * @return String
     */
    protected function getModuleRoutes(){
        return"{
        path: 'dashboard',
        name: 'dashboard',
        component: () => import('@pages/Dashboard.vue'),
        meta: { title: 'Dashboard' }
      },
      {
        path: '{$this->argumentPlu}',
        name: '{$this->argumentPlu}.index',
        component: () => import('@cruds/{$this->ArgumentPlu}/Index.vue'),
        meta: { title: '{$this->ArgumentPlu}' }
      },
      {
        path: '{$this->argumentPlu}/create',
        name: '{$this->argumentPlu}.create',
        component: () => import('@cruds/{$this->ArgumentPlu}/Create.vue'),
        meta: { title: '{$this->ArgumentPlu}' }
      },
      {
        path: '{$this->argumentPlu}/:id',
        name: '{$this->argumentPlu}.show',
        component: () => import('@cruds/{$this->ArgumentPlu}/Show.vue'),
        meta: { title: '{$this->ArgumentPlu}' }
      },
      {
        path: '{$this->argumentPlu}/:id/edit',
        name: '{$this->argumentPlu}.edit',
        component: () => import('@cruds/{$this->ArgumentPlu}/Edit.vue'),
        meta: { title: '{$this->ArgumentPlu}' 
      }
    },            
      ";
    }
    /**
     * Add module routes to vue router.
     *
     */
    protected function addRoutes(){
        $fileContent = file_get_contents(base_path("resources/adminapp/js/routes/routes.js"));
        $fileContent = str_replace("{
        path: 'dashboard',
        name: 'dashboard',
        component: () => import('@pages/Dashboard.vue'),
        meta: { title: 'Dashboard' }
      },",$this->getModuleRoutes(), $fileContent);
        $file = fopen(base_path("resources/adminapp/js/routes/routes.js"), 'w+');
        fwrite($file, $fileContent);
        fclose($file);
    }
    protected function getModuleApiRoutes(){
        return"
        Route::resource('users', 'UsersApiController');
        
        // {$this->ArgumentPlu}
        Route::resource('{$this->argumentPlu}', '{$this->ArgumentPlu}ApiController');
      ";
    }
    /**
     * Add module Api routes to vue router.
     *
     */
    protected function addApiRoutes(){
        $fileContent = file_get_contents(base_path("routes/api.php"));
        $fileContent = str_replace("Route::resource('users', 'UsersApiController');",$this->getModuleApiRoutes(), $fileContent);
        $file = fopen(base_path("routes/api.php"), 'w+');
        fwrite($file, $fileContent);
        fclose($file);
    }



    /**
     * Add module store requests to vuex store.
     *
     */
    protected function addStore(){
        $fileContent = file_get_contents(base_path("resources/adminapp/js/store/store.js"));
        $fileContent = str_replace('Vue.use(Vuex)',$this->getModuleStoreImports(),$fileContent);
        $fileContent = str_replace('modules: {',$this->getModuleStoreModules(),$fileContent);
        $file = fopen(base_path("resources/adminapp/js/store/store.js"), 'w+');
        fwrite($file, $fileContent);
        fclose($file);
    }

    /**
     *
     * Return module store imports to vue router.
     * @return String
     */
    protected function getModuleStoreImports(){
        return "\n
    import {$this->ArgumentPlu}Index from './cruds/{$this->ArgumentPlu}'
    import {$this->ArgumentPlu}Single from './cruds/{$this->ArgumentPlu}/single'\n
    Vue.use(Vuex)
      ";
    }
    /**
     *
     * Return module store imports to vue router.
     * @return String
     */
    protected function getModuleStoreModules(){
        return "
      modules: {
        {$this->ArgumentPlu}Index,
        {$this->ArgumentPlu}Single,
      ";
    }

    /**
     * Generate info.
     *
     * @return void
     */
    protected function showOutput()
    {
        $this->info("Module {$this->Argument} created successfully!");
    }
}
