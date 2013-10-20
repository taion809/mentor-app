<?php

use Phinx\Migration\AbstractMigration;

class CreateUser extends AbstractMigration
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
            create table `user` (
                `id`                   varchar(255) not null,
                `first_name`           varchar(255),
                `last_name`            varchar(255),
                `email`                varchar(255),
                `irc_nick`             varchar(255),
                `twitter_handle`       varchar(255),
                `mentor_available`     tinyint(2),
                `apprentice_available` tinyint(1),
                `timezone`             varchar(255),

                primary key (`id`)
            )
        ');

        $this->query('
            create table `teaching_skills` (
                `id_user` varchar(255) not null,
                `id_tag`  varchar(255) not null,

                primary key (`id_user`, `id_tag`)
            )
        ');

        $this->query('
            create table `learning_skills` (
                `id_user` varchar(255) not null,
                `id_tag`  varchar(255) not null,

                primary key (`id_user`, `id_tag`)
            )
        ');
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->query('drop table `teaching_skills`');
        $this->query('drop table `learning_skills`');
        $this->query('drop table `user`');
    }
}