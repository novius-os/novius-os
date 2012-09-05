<?php

namespace Fuel\Migrations;

class Move_config_metadata
{
    public function up()
    {
        try {
            \Fuel\Core\File::create_dir(APPPATH, 'metadata');
        } catch (\Exception $e) {}
        
        foreach (array('app_installed', 'app_dependencies', 'app_namespaces', 'data_catchers', 'enhancers', 'launchers', 'templates') as $section) {
            \Fuel\Core\File::rename(APPPATH.'data/config/'.$section.'.php', APPPATH.'metadata/'.$section.'.php');
        }
    }

    public function down()
    {

    }
}