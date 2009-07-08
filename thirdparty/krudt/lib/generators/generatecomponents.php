<?php
require_once 'baselib.inc.php';

class generators_GenerateComponents {
  protected $dir_resources;

  function __construct($dir_resources) {
    $this->dir_resources = $dir_resources;
    if (!filesys()->is_dir($this->dir_resources)) {
      throw new Exception("Can't locate resouces dir for krudt");
    }
  }

  function run() {
    $dir_generator_templates = $this->dir_resources;
    $destination_root = getcwd();
    if (console()->count_arguments() != 1) {
      echo "USAGE: ".console()->script_filename()." [OPTIONS] model_plural_name\n";
      echo "OPTIONS:\n";
      echo "  --dry  Simulate all changes.\n";
      exit;
    }

    if (console()->option('dry')) {
      echo "Dry mode. No changes are actual.\n";
      filesys(new baselib_ReadonlyFilesys());
    }
    filesys()->enable_debug();

    $model_plural_name = strtolower(console()->argument(0));
    if (!filesys()->is_file($destination_root."/lib/".$model_plural_name.".inc.php")) {
      throw new Exception("Can't find model in lib/".$model_plural_name.".inc.php\n");
    }
    $php = filesys()->get_contents($destination_root."/lib/".$model_plural_name.".inc.php");
    list($model_name, $model_fields) = $this->reflect_model($model_plural_name, $php);
    $slug_name = 'slug';
    $file_name = $model_plural_name;

    echo "Generating: model_name => ".$model_name.", model_plural_name => ".$model_plural_name."\n";
    filesys()->mkdir_p($destination_root."/lib/components/".$file_name);

    $content = filesys()->get_contents($dir_generator_templates."/lib/components/contacts/list.php");
    $content = $this->replace_names($content, $model_name, $model_plural_name);
    $content = $this->replace_fields($content, $model_fields);
    filesys()->put_contents($destination_root."/lib/components/".$file_name."/list.php", $content);

    $content = filesys()->get_contents($dir_generator_templates."/lib/components/contacts/entry.php");
    $content = $this->replace_names($content, $model_name, $model_plural_name);
    $content = $this->replace_fields($content, $model_fields);
    $content = $this->replace_slug($content, $slug_name);
    filesys()->put_contents($destination_root."/lib/components/".$file_name."/entry.php", $content);
    filesys()->mkdir_p($destination_root."/templates/".$file_name);

    foreach (filesys()->scandir($dir_generator_templates."/templates/contacts") as $entry) {
      $filename = $dir_generator_templates."/templates/contacts/".$entry;
      if (filesys()->is_file($filename)) {
        $content = filesys()->get_contents($filename);
        $content = $this->replace_names($content, $model_name, $model_plural_name);
        filesys()->put_contents($destination_root."/templates/".$file_name."/".$entry, $content);
      }
    }
  }

  function replace_names($php, $model_name, $model_plural_name) {
    $php = str_replace('contacts', $model_plural_name, $php);
    $php = str_replace('contact', $model_name, $php);
    $php = str_replace('Contacts', ucfirst($model_plural_name), $php);
    $php = str_replace('Contact', ucfirst($model_name), $php);
    return $php;
  }

  function replace_fields($php, $fields = array()) {
    $all = array();
    foreach ($fields as $field) {
      $all[] = "        '".$field."' => \$this->body('".$field."')";
    }
    return str_replace("        'slug' => \$this->body('slug')", implode(",\n", $all), $php);
  }

  function replace_slug($php, $slug_name) {
    return str_replace("'slug' => \$this->name()", "'".$slug_name."' => \$this->name()", $php);
  }

  function reflect_model($model_plural_name, $php) {
    if (!preg_match('/class ([\w]*) \{/', $php, $matches)) {
      throw new Exception("Can't reflect model.");
    }
    $singluar_name = strtolower($matches[1]);
    if (!preg_match("/function __construct\\(\\\$row = array\\((.+)\\)\\) \\{/", $php, $matches)) {
      throw new Exception("Can't reflect model.");
    }
    if (!preg_match_all("/'(\\w+)' => null/", $matches[1], $matches2)) {
      throw new Exception("Can't reflect model.");
    }
    return array($singluar_name, array_diff($matches2[1], array('id')));
  }

}