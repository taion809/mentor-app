<?php

use Phinx\Migration\AbstractMigration;

class Partnership extends AbstractMigration
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
        $this->query('
            CREATE TABLE `partnership` (
			    `id`            varchar(10) not null,
                `id_mentor`     varchar(10),  
                `id_apprentice` varchar(10),

                primary key(`id`),
                unique key(`id`, `id_mentor`, `id_apprentice`)
            )
        '); 		    
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->query('DROP TABLE `partnership`');
    }
}
