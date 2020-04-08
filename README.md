## **Core components for Saas Laravel application**

#### **About**

#### **Install**
As Composer package

`Composer require olckerstech/core`
#### **Configure**

Most of the install features are automated. 

Add `olckerstech\core\src\CoreServiceProvider::class,` to the providers array in `config/app`.

Run the following commands:

`php artisan core:init`

`php artisan core:install`

`composer dump-autoload`


**Manual Framework Configurations**

Run `php artisan key:generate`

Then, we need to ensure that all references to `app/User` is changed to `app/Models/User.`

Change the following in the `config/auth.php` file:

    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => App\Models\User::class, //Changed from App\User::class
        ],
        
If you have packages referencing the `app/User` model directly, they will have to be updated.
Example: Change the following in the `config/services.php` file (If required!):

    'stripe' => [
        'model' => App\Models\User::class, // changed
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
    ],
    


**Package Assets**

By default the configuration will be copied to the `config/core.php` directory.

If you need to customize components, you can publish the various components by using the following Artisan commands:

###### Publish All Resources

###### Publish Migrations

###### Publish Routes

###### Publish Translations
#### **Usage**
