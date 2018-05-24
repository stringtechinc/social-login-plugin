<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180523181422 extends AbstractMigration
{

    const NAME = 'plg_sns_login_customer';

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
        $table->addColumn('id', 'integer', array(
            'autoincrement' => true
        ));
        $table->addColumn('customer_id', 'integer', array(
            'unsigned' => true,
            'notnull' => false
        ));
        $table->addColumn('union_id', 'text', array(
            'notnull' => false
        ));
        $table->addColumn('info', 'text', array(
            'notnull' => false
        ));
        $table->setPrimaryKey(array('id'));
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
