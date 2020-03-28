<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200314164123 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE patient (id INT AUTO_INCREMENT NOT NULL, fname VARCHAR(255) NOT NULL, lname VARCHAR(255) NOT NULL, dob DATE NOT NULL, address1 VARCHAR(255) NOT NULL, address2 VARCHAR(255) DEFAULT NULL, town VARCHAR(255) NOT NULL, county VARCHAR(255) NOT NULL, sex VARCHAR(255) NOT NULL, nationality VARCHAR(255) NOT NULL, doctor VARCHAR(255) DEFAULT NULL, scheme VARCHAR(255) NOT NULL, allergies VARCHAR(255) DEFAULT NULL, username VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, phone VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE patient');
    }
}
