<?php
require __DIR__ . '/vendor/autoload.php';

$config_file = __DIR__ . '/config.ini';

if (!file_exists($config_file)){
    die("Config file {$config_file} not exists!!");
}

$config = parse_ini_file($config_file);

// set Cacti path
if (isset($config['cacti_path'])) {
    Cacti::$cacti_cli_path = $config['cacti_path'];
}

$work = new Work(['username' => $config['username'], 'password' => $config['password'], 'company' => $config['company']]);

// get Root info
$root_info = $work->searchNode($config['root_ip']);
$root_id = $root_info['items'][0]['id'];
$root_name = $root_info['items'][0]['dev_name'];

$tree = function ($parent, $parent_cacti_id) use (&$tree, $work, $config) {

    $parent_title = str_replace('&rarr;', '→', strip_tags($parent['dev_name']));
    $title =  trim(preg_replace('#\[(.+)\]#', '', $parent_title));

    $parent_cacti_id = Cacti::addHeader2Tree($parent_title, $parent_cacti_id, $config['tree_id']);

    $cacti_host_id = Cacti::addHost($config['host_template'], $parent['ip'], $config['community_read'], $title);

    if ($cacti_host_id) {
        print "{$cacti_host_id} {$title} done\n";
        Cacti::addHostGraphs2Tree($cacti_host_id, $parent_cacti_id, $config['tree_id']);
    }

    foreach ($work->getChildrenNodes($parent['id']) as $child) {
        $tree($child, $parent_cacti_id);
    }
};

// building tree in Cacti
$tree(['id' => $root_id, 'dev_name' => $root_name, 'ip' => $config['root_ip']], null);
