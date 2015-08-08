<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150808001010 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE offer (id INT AUTO_INCREMENT NOT NULL, offer_category_id INT DEFAULT NULL, brand_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, destination VARCHAR(500) NOT NULL, description LONGTEXT NOT NULL, is_active TINYINT(1) NOT NULL, INDEX IDX_29D6873E936421EC (offer_category_id), INDEX IDX_29D6873E44F5D008 (brand_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE offer_category (id INT AUTO_INCREMENT NOT NULL, brand_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, is_active TINYINT(1) NOT NULL, INDEX IDX_7F31A9A344F5D008 (brand_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE offer ADD CONSTRAINT FK_29D6873E936421EC FOREIGN KEY (offer_category_id) REFERENCES offer_category (id)');
        $this->addSql('ALTER TABLE offer ADD CONSTRAINT FK_29D6873E44F5D008 FOREIGN KEY (brand_id) REFERENCES brand (id)');
        $this->addSql('ALTER TABLE offer_category ADD CONSTRAINT FK_7F31A9A344F5D008 FOREIGN KEY (brand_id) REFERENCES brand (id)');
        $this->addSql('ALTER TABLE fos_user DROP FOREIGN KEY FK_957A647944F5D008');
        $this->addSql('ALTER TABLE fos_user ADD CONSTRAINT FK_957A647944F5D008 FOREIGN KEY (brand_id) REFERENCES brand (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE offer DROP FOREIGN KEY FK_29D6873E936421EC');
        $this->addSql('DROP TABLE offer');
        $this->addSql('DROP TABLE offer_category');
        $this->addSql('ALTER TABLE fos_user DROP FOREIGN KEY FK_957A647944F5D008');
        $this->addSql('ALTER TABLE fos_user ADD CONSTRAINT FK_957A647944F5D008 FOREIGN KEY (brand_id) REFERENCES fos_user (id)');
    }
}
