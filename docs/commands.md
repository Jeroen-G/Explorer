# Console commands

## Create
Use Laravel Scout import command to create and update indices.
```
php artisan scout:import <model>
```
For example, if your model is "App\Models\Post.php" then command would be like this:
```php
php artisan scout:import "App\Models\Post.php"
```

If you want to recreate an index, first make sure it's deleted and then create it.
Follow up with a scout import to refill the index as well.

## Delete
```
php artisan scout:delete-index <model>
```
Use Laravel Scount delete-index command to delete the indices.

## Search
```
php artisan elastic:search <model> <term> --fields=<fields>
```
Rudimentary command to test a basic search query.
