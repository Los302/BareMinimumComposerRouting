<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170725181749 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $table = $schema->createTable('users');
        $table->addColumn('id', 'integer', ['length' => 11, 'autoincrement' => true]);
        $table->addColumn('first_name', 'string', ['length' => 20]);
        $table->addColumn('last_name', 'string', ['length' => 20]);
        $table->addColumn('username', 'string', ['length' => 20, 'notnull' => false]);
        $table->addColumn('password', 'string', ['length' => 40, 'notnull' => false]);
        $table->addColumn('email', 'string', ['length' => 100, 'notnull' => false]);
        $table->addColumn('role', 'string', ['length' => 20, 'notnull' => false]);
        $table->addColumn('active', 'boolean');
        $table->setPrimaryKey(['id']);
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $schema->dropTable('users');
    }
}
