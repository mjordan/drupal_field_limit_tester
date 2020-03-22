## Overview

PHP script that generates a simple Drupal 8 module that installs however many fields you want.

The resulting module tests the limits of a Drupal instance and is NOT intended to be installed on production instances. In fact, it very well might bork your Drupal instance. It's probably a good idea to spin up a sacrificial instance just to use this on, especially if you set the number of fields to some crazy number like 1000.

This script comes with absolutely no warranty. By reading this sentence you give up all rights to blame its author if your Drupal instance [blows up real good](https://www.youtube.com/watch?v=uHkvD7-u7y8).

## Usage

Open the `drupal_field_limit_tester.php` file and edit the following variables:

```php
/**
 * You may want to adjust these variables.
 */
$module_directory = 'maxfieldtest';
$module_name = 'Max Field Test';
$module_description = 'A module that generates a content type and adds a bunch of fields.';
$num_fields = 20;
$path_to_word_file = './LICENSE';
$num_csv_records = 10;
```

Then, run the script:

`php drupal_field_limit_tester.php`

With the above variables, the following Drupal module will be generated:

```
maxfieldtest
├── config
│   └── install
│       ├── core.entity_form_display.node.maxfieldtest.default.yml
│       ├── core.entity_view_display.node.maxfieldtest.default.yml
│       ├── field.field.node.maxfieldtest.field_maxtest00001.yml
│       ├── field.field.node.maxfieldtest.field_maxtest00002.yml
│       ├── field.field.node.maxfieldtest.field_maxtest00003.yml
│       ├── field.field.node.maxfieldtest.field_maxtest00004.yml
│       ├── field.field.node.maxfieldtest.field_maxtest00005.yml
│       ├── field.field.node.maxfieldtest.field_maxtest00006.yml
│       ├── field.field.node.maxfieldtest.field_maxtest00007.yml
│       ├── field.field.node.maxfieldtest.field_maxtest00008.yml
│       ├── field.field.node.maxfieldtest.field_maxtest00009.yml
│       ├── field.field.node.maxfieldtest.field_maxtest00010.yml
│       ├── field.field.node.maxfieldtest.field_maxtest00011.yml
│       ├── field.field.node.maxfieldtest.field_maxtest00012.yml
│       ├── field.field.node.maxfieldtest.field_maxtest00013.yml
│       ├── field.field.node.maxfieldtest.field_maxtest00014.yml
│       ├── field.field.node.maxfieldtest.field_maxtest00015.yml
│       ├── field.field.node.maxfieldtest.field_maxtest00016.yml
│       ├── field.field.node.maxfieldtest.field_maxtest00017.yml
│       ├── field.field.node.maxfieldtest.field_maxtest00018.yml
│       ├── field.field.node.maxfieldtest.field_maxtest00019.yml
│       ├── field.field.node.maxfieldtest.field_maxtest00020.yml
│       ├── field.storage.node.field_maxtest00001.yml
│       ├── field.storage.node.field_maxtest00002.yml
│       ├── field.storage.node.field_maxtest00003.yml
│       ├── field.storage.node.field_maxtest00004.yml
│       ├── field.storage.node.field_maxtest00005.yml
│       ├── field.storage.node.field_maxtest00006.yml
│       ├── field.storage.node.field_maxtest00007.yml
│       ├── field.storage.node.field_maxtest00008.yml
│       ├── field.storage.node.field_maxtest00009.yml
│       ├── field.storage.node.field_maxtest00010.yml
│       ├── field.storage.node.field_maxtest00011.yml
│       ├── field.storage.node.field_maxtest00012.yml
│       ├── field.storage.node.field_maxtest00013.yml
│       ├── field.storage.node.field_maxtest00014.yml
│       ├── field.storage.node.field_maxtest00015.yml
│       ├── field.storage.node.field_maxtest00016.yml
│       ├── field.storage.node.field_maxtest00017.yml
│       ├── field.storage.node.field_maxtest00018.yml
│       ├── field.storage.node.field_maxtest00019.yml
│       ├── field.storage.node.field_maxtest00020.yml
│       ├── migrate_plus.migration_group.maxfieldtest.yml
│       └── node.type.maxfieldtest.yml
├── maxfieldtest.csv
├── maxfieldtest.migration_config.yml
└── maxfieldtest.info.yml
```

Installing the module in your Drupal instance will create a new content type and attach 20 test fields to it.

## Importing sample content

Within the module directory, you will find a CSV file containing sample field data (with the number of records specified in the `$num_csv_records` variable at the top of the script), and within the `config/install` directory, the configuration files for an accompanying migration. By default, the sample content is randomized words from the module's LICENSE file but you can use any text file you want. To use this migration, you will need to have the [Migrate Tools](https://www.drupal.org/project/migrate_tools) and [Migrate Source CSV](https://www.drupal.org/project/migrate_source_csv) modules installed.

To import the sample content:

1. Go to Structure > Migrations. You should see the migration configuration in the list.
1. Click on the "List migrations" button in the row for your module's migration group.
1. Click on "Execute".

When you uninstall your module, all of the content type and migration configuration is removed autmoatically, but the nodes created by the migration are not. You will need to delete those yourself.

## Author

Mark Jordan (https://github.com/mjordan)

## License

The Unlicense.

