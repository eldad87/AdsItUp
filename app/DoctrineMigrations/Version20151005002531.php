<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151005002531 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE brand_record (id INT AUTO_INCREMENT NOT NULL, offer_id INT DEFAULT NULL, user_id INT DEFAULT NULL, offer_banner_id INT DEFAULT NULL, offer_click_id INT DEFAULT NULL, brand_id INT DEFAULT NULL, external_id INT NOT NULL, type INT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, record LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', INDEX IDX_18BCE39F53C674EE (offer_id), INDEX IDX_18BCE39FA76ED395 (user_id), INDEX IDX_18BCE39FCB8BB5F7 (offer_banner_id), INDEX IDX_18BCE39F8A63D1ED (offer_click_id), INDEX IDX_18BCE39F44F5D008 (brand_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE brand_record ADD CONSTRAINT FK_18BCE39F53C674EE FOREIGN KEY (offer_id) REFERENCES offer (id)');
        $this->addSql('ALTER TABLE brand_record ADD CONSTRAINT FK_18BCE39FA76ED395 FOREIGN KEY (user_id) REFERENCES fos_user (id)');
        $this->addSql('ALTER TABLE brand_record ADD CONSTRAINT FK_18BCE39FCB8BB5F7 FOREIGN KEY (offer_banner_id) REFERENCES offer_banner (id)');
        $this->addSql('ALTER TABLE brand_record ADD CONSTRAINT FK_18BCE39F8A63D1ED FOREIGN KEY (offer_click_id) REFERENCES offer_click (id)');
        $this->addSql('ALTER TABLE brand_record ADD CONSTRAINT FK_18BCE39F44F5D008 FOREIGN KEY (brand_id) REFERENCES brand (id)');
        $this->addSql('ALTER TABLE offer_banner ADD name VARCHAR(255) NOT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE brand_record');
        $this->addSql('ALTER TABLE offer_banner DROP name');
    }
}
