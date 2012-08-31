<?php

namespace Fuel\Migrations;

class Remove_page_root
{
    public function up()
    {
        \DB::query("ALTER TABLE `nos_page` DROP `page_root_id`;")->execute();
   }

    public function down()
    {

    }
}
