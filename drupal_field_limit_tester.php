<?php

/**
 * Script to generate a Drupal 8 module that creates a content type and
 * a specified number of fields on that content type. Also generates a CSV
 * file thta can be used to populate nodes. Useful for scalability
 * testing.
 *
 * This script comes with absolutely no warranty. By reading this
 * sentence you give up all rights to blame its author if your Drupal
 * instance blows up real good.
 *
 * This script is in the public domain.
 */

/**
 * You may want to adjust these variables.
 */
$module_directory = 'maxfieldtest';
$module_name = 'Max Field Test';
$module_description = 'A module that generates a content type and adds a bunch of fields.';
$num_fields = 20;
$path_to_word_file = './LICENSE';
$num_csv_records = 10;

/**
 * You don't need to touch anything below this line. But you can if you want.
 */

$module_machine_name = basename($module_directory);

/**
 * Generate the .info.yml file.
 */
$info_yaml = <<< INFO
name: "$module_name"
description: "$module_description"
type: module
core: 8.x
INFO;

$content_type_yaml = <<<CTYPE
langcode: en
status: true
dependencies:
  enforced:
    module:
      - $module_machine_name
  module:
    - menu_ui
third_party_settings:
  menu_ui:
    available_menus:
      - main
    parent: 'main:'
name: "Content type created by $module_name module"
type: $module_machine_name
description: 'Content type for testing maximum number of fields.'
help: ''
new_revision: true
preview_mode: 1
display_submitted: true
CTYPE;

/**
 * Create the module directory and write out the .info.yml file.
 */
if (file_exists($module_directory)) {
    exit("Sorry, the module directory $module_directory already exists\n");
}

mkdir($module_directory);
$info_file_path = $module_directory .
    DIRECTORY_SEPARATOR . $module_machine_name . '.info.yml';
file_put_contents($info_file_path, $info_yaml);

mkdir($module_directory . DIRECTORY_SEPARATOR . 'config');
$install_files_directory = $module_directory . 
    DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'install';
mkdir($install_files_directory);

$content_type_file_path = $install_files_directory . 
    DIRECTORY_SEPARATOR . 'node.type.' . $module_machine_name . '.yml';
file_put_contents($content_type_file_path, $content_type_yaml);

for ($i = 1; $i <= $num_fields; $i++) {
    $field_suffix = str_pad($i, 5, "0", STR_PAD_LEFT);
    $field_machine_name = 'field_maxtest' . $field_suffix;

/**
 * Generate the field definition and storage .yml files,
 * one of each per field.
 */
$field_definition_yml = <<<FIELD
langcode: en
status: true
dependencies:
  enforced:
    module:
      - $module_machine_name
  config:
    - field.storage.node.$field_machine_name
    - node.type.$module_machine_name
id: node.$module_machine_name.$field_machine_name
field_name: $field_machine_name
entity_type: node
bundle: $module_machine_name
label: "Test field $field_suffix" 
description: ''
required: false
translatable: false
default_value: {  }
default_value_callback: ''
settings: {  }
field_type: string
FIELD;

    $field_definition_file_path = $install_files_directory . 
        DIRECTORY_SEPARATOR . 'field.field.node.' . $module_machine_name . 
        '.' . $field_machine_name . '.yml';
    file_put_contents($field_definition_file_path, $field_definition_yml);

$field_storage_yml = <<<STORAGE
langcode: en
status: true
dependencies:
  enforced:
    module:
      - node
      - $module_machine_name
id: node.$field_machine_name
field_name: $field_machine_name
entity_type: node
type: string
settings:
  max_length: 255
  is_ascii: false
  case_sensitive: false
module: core
locked: false
cardinality: -1
translatable: true
indexes: {  }
persist_with_no_fields: false
custom_storage: false
STORAGE;

    $field_storage_file_path = $install_files_directory . 
        DIRECTORY_SEPARATOR . 'field.storage.node.' . $field_machine_name . '.yml';
    file_put_contents($field_storage_file_path, $field_storage_yml);
}

/**
 * Generate the form display .yml file.
 */
$node_form_display_yml = <<<FORMDISPLAY1
langcode: en
status: true
dependencies:
  config:
FORMDISPLAY1;

$node_form_display_yml .= "\n";
for ($i = 1; $i <= $num_fields; $i++) {
    $field_machine_name = get_node_field_name($i, $field_suffix);
    $node_form_display_yml .= "     - field.field.node.$module_machine_name.$field_machine_name\n";
}

$node_form_display_yml .= <<<FORMDISPLAY2
     - node.type.$module_machine_name
  enforced:
    module:
      - $module_machine_name
  module:
    - path
third_party_settings: { }
id: node.$module_machine_name.default
targetEntityType: node
bundle: $module_machine_name
mode: default
content:
  created:
    type: datetime_timestamp
    weight: 8
    region: content
    settings: {  }
    third_party_settings: {  }
FORMDISPLAY2;

for ($i = 1; $i <= $num_fields; $i++) {
    $field_machine_name = get_node_field_name($i, $field_suffix);

$node_form_display_yml .= "\n" . <<<FORMDISPLAY3
  $field_machine_name:
    type: string_textfield
    weight: 1
    region: content
    settings:
      size: 60
      placeholder: ''
    third_party_settings: {  }
FORMDISPLAY3;
}

$node_form_display_yml .= "\n" . <<<FORMDISPLAY4
  langcode:
    type: language_select
    weight: 5
    region: content
    settings:
      include_locked: true
    third_party_settings: {  }
  path:
    type: path
    weight: 4
    region: content
    settings: {  }
    third_party_settings: {  }
  promote:
    type: boolean_checkbox
    weight: 6
    region: content
    settings:
      display_label: true
    third_party_settings: {  }
  status:
    type: boolean_checkbox
    settings:
      display_label: true
    weight: 5
    region: content
    third_party_settings: {  }
  sticky:
    type: boolean_checkbox
    weight: 9
    region: content
    settings:
      display_label: true
    third_party_settings: {  }
  title:
    type: string_textfield
    weight: 0
    region: content
    settings:
      size: 60
      placeholder: ''
    third_party_settings: {  }
  translation:
    weight: 3
    region: content
    settings: {  }
    third_party_settings: {  }
  uid:
    type: entity_reference_autocomplete
    weight: 7
    region: content
    settings:
      match_operator: CONTAINS
      size: 60
      placeholder: ''
    third_party_settings: {  }
hidden: {  }
FORMDISPLAY4;

    $node_form_display_file_path = $install_files_directory . 
        DIRECTORY_SEPARATOR . 'core.entity_form_display.node.' . $module_machine_name . '.default.yml';
    file_put_contents($node_form_display_file_path, $node_form_display_yml);

/**
 * Generate the view display config file.
 */
$node_view_display_yml = <<<VIEWDISPLAY
langcode: en
status: true
dependencies:
  config:
VIEWDISPLAY;
$node_view_display_yml .= "\n";
for ($i = 1; $i <= $num_fields; $i++) {
    $field_machine_name = get_node_field_name($i, $field_suffix);
    $node_view_display_yml .= "     - field.field.node.$module_machine_name.$field_machine_name\n";
}

$node_view_display_yml .= <<<VIEWDISPLAY2
     - node.type.$module_machine_name
  enforced:
    module:
      - $module_machine_name
  module: { }
id: node.$module_machine_name.default
targetEntityType: node
bundle: $module_machine_name
mode: default
content:
VIEWDISPLAY2;

for ($i = 1; $i <= $num_fields; $i++) {
    $field_machine_name = get_node_field_name($i, $field_suffix);

$node_view_display_yml .= "\n" . <<<VIEWDISPLAY3
  $field_machine_name:
    type: string
    region: content
    label: above
    settings:
      link_to_entity: false
    third_party_settings: {  }
VIEWDISPLAY3;
}

$node_view_display_yml .= "\nhidden: { }\n";

    $node_view_display_file_path = $install_files_directory . 
        DIRECTORY_SEPARATOR . 'core.entity_view_display.node.' . $module_machine_name . '.default.yml';
    file_put_contents($node_view_display_file_path, $node_view_display_yml);

/**
 * Generate the CSV file.
 */
$word_file_contents = file_get_contents($path_to_word_file);
$words = preg_split('/\s+/', $word_file_contents);
$words = array_unique($words);
$csv_data = [];
$csv_header = ['row_id', 'title'];

for ($i = 1; $i <= $num_fields; $i++) {
    $field_machine_name = get_node_field_name($i, $field_suffix);
    $csv_header[] = $field_machine_name;
}
$csv_data[] = $csv_header;
for ($r = 1; $r <= $num_csv_records; $r++) {
    $record = [];
    $record[] =  $r;
    $record[] =  ucfirst($module_machine_name) . ' sample node ' . $r;
    for ($f = 1; $f <= $num_fields; $f++) {
        $csv_value = get_random_sentence($words);
        $record[] =  $csv_value;
    }
    $csv_data[] = $record;
}

$fp = fopen($module_directory . DIRECTORY_SEPARATOR . $module_machine_name . '.csv', 'w');
foreach ($csv_data as $fields) {
    fputcsv($fp, $fields);
}

/**
 * Generate the migration configuration files.
 */
$migration_config_yml = <<<MIGRATIONCONFIG1
langcode: en
status: true
dependencies:
  enforced:
    module:
      - $module_machine_name
id: $module_machine_name
class: null
field_plugin_method: null
cck_plugin_method: null
migration_tags: null
migration_group: $module_machine_name 
label: "Import $module_machine_name test nodes from CSV"
source:
  plugin: csv
  path: modules/contrib/$module_machine_name/$module_machine_name.csv
  header_row_count: 1
  keys:
    - row_id
  constants:
    uid: 1
process:
  title: title
MIGRATIONCONFIG1;

$migration_config_yml .= "\n";
for ($i = 1; $i <= $num_fields; $i++) {
    $field_machine_name = get_node_field_name($i, $field_suffix);
    $migration_config_yml .= '  ' . $field_machine_name . ': ' . $field_machine_name . "\n";
}

$migration_config_yml .= <<<MIGRATIONCONFIG2
destination:
  plugin: 'entity:node'
  default_bundle: $module_machine_name
migration_dependencies: null
MIGRATIONCONFIG2;

    $migration_config_file_path = $module_directory . 
        DIRECTORY_SEPARATOR . $module_machine_name . '.migration_config.yml';
    file_put_contents($migration_config_file_path, $migration_config_yml);

$migration_group_config_file_yml = <<<MIGRATIONGROUPCONFIG
langcode: en
status: true
dependencies:
  enforced:
    module:
      - $module_machine_name
id: $module_machine_name
label: '$module_machine_name Content'
description: 'Migration configuration for importing test content generated by the $module_machine_name module.'
source_type: csv
module: null
shared_configuration: null
MIGRATIONGROUPCONFIG;

    $migration_group_config_file_path = $install_files_directory . 
        DIRECTORY_SEPARATOR . 'migrate_plus.migration_group.' . $module_machine_name . '.yml';
    file_put_contents($migration_group_config_file_path, $migration_group_config_file_yml);

/**
 * Functions.
 */
function get_random_sentence($words) {
    foreach ($words as &$word) {
        $word = str_replace(array("\n", "\r", '.', ',', '<', '>', '"', "'"), '', $word);
        $word = strtolower($word);
    }
    shuffle($words);
    $random_words = array_slice($words, 0, rand(0,30));
    $sentence = implode(' ', $random_words);
    $sentence = ucfirst($sentence) . '.';
    if (strlen($sentence) > 250) {
        $sentence = substr($sentence, 0, 250) . '!';
    }
    // We don't want any sentences that are just a '.'.
    if ($sentence == '.') {
        $sentence = get_random_sentence($words);
    }
    return $sentence;
}

function get_node_field_name($field_sequence, $field_suffix) {
    $field_suffix = str_pad($field_sequence, 5, "0", STR_PAD_LEFT);
    $field_machine_name = 'field_maxtest' . $field_suffix;
    return $field_machine_name;
}

/**
 * Greet the user then exit.
 */
print "Your Drupal module is in $module_directory. Have a nice day!\n";
