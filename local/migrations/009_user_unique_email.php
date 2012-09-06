<?php

namespace Fuel\Migrations;

class User_unique_email
{
    public function up()
    {
        \DBUtil::create_index('nos_user', 'user_email', 'user_email', 'unique');
    }

    public function down()
    {

    }
}