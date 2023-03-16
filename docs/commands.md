# Console commands

## Create
Use Laravel Scout import command to create and update indices.
```
php artisan scout:import <model>
```
For example, if your model is "App\Models\Post" then command would be like this:
```
php artisan scout:import "App\Models\Post"
```

If you want to recreate an index, first make sure it's deleted and then create it.
Follow up with a scout import to refill the index as well.

## Update Aliased Indexes
If you are using Aliased Indexes, you should use this command instead of `scout:import`
```
php artisan elastic:update <index?>
```
You can specify an index or choose to omit it and the command will update all your indexes.
For example, if your model is "App\Model\Post" and the index is "posts":
```
php artisan elastic:update posts
```

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
