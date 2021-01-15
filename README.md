# Drupal and Pa11y-CI

```shell
composer create-project drupal/recommended-project drupa11y
cd drupa11y/
composer require --dev drupal/core-dev-pinned
composer require --dev phpunit/phpunit:^8.5 squizlabs/php_codesniffer
composer require drush/drush
```

Create web/sites/settings.local.php:

```php
<?php
$databases['default']['default'] = [
  'database' => sys_get_temp_dir() . '/drupa11y.sqlite',
  'prefix' => '',
  'namespace' => 'Drupal\\Core\\Database\\Driver\\sqlite',
  'driver' => 'sqlite',
];
```

Edit web/sites/settings.php to include settings.local.php

```shell
vendor/bin/drush -y site:install standard --no-interaction --site-name "NJ Meetup" \
 --site-mail admin@example.com --account-name admin --account-mail admin@example.com --account-pass admin
```
