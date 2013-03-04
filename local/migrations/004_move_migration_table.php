<?php

namespace Fuel\Migrations;

class Move_migration_table
{
    public function up()
    {
        \Config::load('migrations', true);
		$table = \Config::get('migrations.table');
        if ($table == 'migration') {
            \DB::query('RENAME TABLE `novius_os`.`migration` TO  `novius_os`.`nos_migration`;')->execute();
            \Migrate::set_table('nos_migration');
            \Config::set('migrations.table', 'nos_migration');
        }
    }

    public function down()
    {

    }
}