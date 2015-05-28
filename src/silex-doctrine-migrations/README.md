# Silex Migration Provider

This is a wrapper for the [Doctrine Migrations project](http://docs.doctrine-project.org/projects/doctrine-migrations/en/latest/reference/introduction.html).

For more information on how to use the schema manager, please see [Doctrine's Schema Manager documentation](http://readthedocs.org/docs/doctrine-dbal/en/latest/reference/schema-manager.html). 


## Install via Composer

```
composer.phar require dbtlr/silex-doctrine-migrations
```

## Add service provider

```php
$app->register(new \Dbtlr\MigrationProvider\MigrationServiceProvider(), array(
    'db.migrations.path' => __DIR__ . '/../app/migrations',
));
```

### Config options

- `db.migrations.path` (required): The full path where you want to store your migration classes.
- `db.migrations.table_name` (optional): The name of the table that we store meta information about the state of migrations. Defaults to: migration_versions.
- `db.migrations.namespace` (optional): The namespace for the migration classes (defaults to: DoctrineMigration).
- `db.migrations.name` (optional): TThe name of the migrations to use.


## Available commands

- migrations:execute    Execute a single migration version up or down manually.
- migrations:generate   Generate a blank migration class.
- migrations:migrate    Execute a migration to a specified version or the latest available version.
- migrations:status     View the status of a set of migrations.
- migrations:version    Manually add and delete migration versions from the version table.





