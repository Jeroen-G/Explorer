# Console commands

## Create
```
php artisan elastic:create
```
Creates all indices.
Throws an exception if one already exists.

If you want to recreate an index, first make sure it's deleted and then create it.
Follow up with a scout import to refill the index as well.

## Delete
```
php artisan elastic:delete
```
Deletes all indices.

## Search
```
php artisan elastic:search <model> <term> --fields=<fields>
```
Rudimentary command to test a basic search query.
