## **Laravel Packager**

#### **About**
Developer support library.
#### **Install**
As Composer package

`Composer require olckerstech/packager`
#### **Configure**

Add `olckerstech\core\src\CoreServiceProvider::class,` to the providers array in `config/app`.

Run `php artisan vendor:publish --provider="olckerstech/packager" --tag="config"` to publish the config file.

Run `php artisan vendor:publish --provider="olckerstech/packager" --tag="stubs"` to publish the stubs file.
#### **Usage**
Create entity scaffolding within application: 

`php artisan make:scaffold <entity name>`

Create new package framework: 

`php artisan package:create <vendor/package>`

Create entity scaffolding within package created by Packager: 

`php artisan package:scaffold <entity name>`
