## **Laravel Packager**

#### **About**
Developer support library. v0.9.0-alpha

Semantic Versioning 2.0.0 ( see https://semver.org/ )

#### **Install**
As Composer package

`Composer require olckerstech/packager`
#### **Configure**

Add `olckerstech\core\src\PackagerServiceProvider::class,` to the providers array in `config/app` ONLY if you do not want to rely on package auto discovery.

Run `php artisan vendor:publish --provider="olckerstech/packager" --tag="config"` to publish the config file.

#### **Usage**
Create entity scaffolding within application: 

`php artisan make:scaffold <entity name>`

Create new package framework: 

`php artisan package:create <vendor/package>`

Create entity scaffolding within package created by Packager: 

`php artisan package:scaffold <entity name>`
