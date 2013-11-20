<?php

use Phinx\Migration\AbstractMigration;

class CreateTags extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-change-method
     *
     * Uncomment this method if you would like to use it.
     *
    public function change()
    {
    }
    */
    
    /**
     * Migrate Up.
     */
    public function up()
    {
        $this->execute('
            create table `tag` (
                `name` varchar(255) not null unique,
                `authorized` tinyint(1) not null default 0,
                `added` datetime not null,

                PRIMARY KEY (`name`)
            )
        ');
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->execute('drop table tag');
    }
}