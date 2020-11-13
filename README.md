This is my personal starter with some custom stubs inspired by laraveldaily vuejs example


## How to use

- Clone the repository with `git clone`
- Copy `.env.example` file to `.env` and edit database credentials there
- Run `composer install`
- Run `php artisan key:generate`
- Run `php artisan migrate --seed` (it has some seeded data for your testing)
- Run `php artisan passport:install`
- Run `npm install`
- Run `npm run dev`
- That's it: launch the main URL. 

### Create Module

- Run `php artisan make:tkr-module name`
This will create 
1. Model in `app/Models` 
2. Controller in `app/Http/Controllers/Api/V1/Admin` 
3. Resource in `app/Http/Resources` 
4. Store and Update Requests in `app/Http/Requests` 
5. Vue cruds in `resources/adminapp/js/cruds` 
6. Vue store requests in `resources/adminapp/js/store/cruds`



### Todo
- [ ] Change router to dynamically load everything from routes folder   
- [ ] Change store to dynamically load everything from store folder  
- [ ] Change to inertia.js when intertia.js is at v2 atleast 
