<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180523140011 extends AbstractMigration
{

    const NAME = 'plg_sns_login_config';

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        if ($schema->hasTable(self::NAME)) {
            return true;
        }
        $table = $schema->createTable(self::NAME);
        $table->addColumn('sns_login_id', 'integer', array(
            'unsigned' => true
        ));
        $table->addColumn('name', 'text', array(
            'notnull' => false
        ));
        $table->addColumn('public_key', 'text', array(
            'notnull' => false
        ));
        $table->addColumn('secret_key', 'text', array(
            'notnull' => false
        ));
        $table->setPrimaryKey(array('sns_login_id'));
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        if (!$schema->hasTable(self::NAME)) {
            return true;
        }
        $schema->dropTable(self::NAME);
    }
}
