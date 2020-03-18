# Overview

PHP script that generates a simple Drupal 8 module that installs however many fields you want.

The resulting module tests the limits of a Drupal instance and is NOT intended to be installed on production instances. In fact, it might bork your Drupal instance. It might be a good idea to spin up a sacrificial instance just to use this on, especially if you set the number of fields to some crazy number like 1000.

This script comes with absolutely no warranty. By reading this sentence you give up all rights to blame its author if your Drupal instance [blows up real good](https://www.youtube.com/watch?v=uHkvD7-u7y8).

# Usage

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
│       └── node.type.maxfieldtest.yml
├── maxfieldtest.csv
└── maxfieldtest.info.yml
```

Installing the module in your Drupal instance will create a new content type and attach 20 test fields to it. A CSV file containing faked up data (by default, from randomized words drawn from the LICENSE file) is also generated so you can populate some nodes using the Migrate framework.

# Author

Mark Jordan (https://github.com/mjordan)

