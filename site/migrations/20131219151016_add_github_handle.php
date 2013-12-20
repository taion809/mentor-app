<?php

use Phinx\Migration\AbstractMigration;

class AddGithubHandle extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $this->query('
            ALTER TABLE `user` ADD COLUMN `github_handle` VARCHAR(255) AFTER `twitter_handle` 
        ');
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->query('ALTER TABLE `user` DROP COLUMN `github_handle`');
    }
}
