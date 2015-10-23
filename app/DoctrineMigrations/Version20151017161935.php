<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151017161935 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE payment_log (id INT AUTO_INCREMENT NOT NULL, creator_id INT DEFAULT NULL, user_id INT DEFAULT NULL, brand_id INT DEFAULT NULL, amount NUMERIC(10, 0) NOT NULL, comment LONGTEXT NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_B85CA9F161220EA6 (creator_id), INDEX IDX_B85CA9F1A76ED395 (user_id), INDEX IDX_B85CA9F144F5D008 (brand_id), INDEX created_at (brand_id, created_at), INDEX created_a_user_idt (brand_id, user_id, created_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE payment_log ADD CONSTRAINT FK_B85CA9F161220EA6 FOREIGN KEY (creator_id) REFERENCES fos_user (id)');
        $this->addSql('ALTER TABLE payment_log ADD CONSTRAINT FK_B85CA9F1A76ED395 FOREIGN KEY (user_id) REFERENCES fos_user (id)');
        $this->addSql('ALTER TABLE payment_log ADD CONSTRAINT FK_B85CA9F144F5D008 FOREIGN KEY (brand_id) REFERENCES brand (id)');
        $this->addSql('CREATE INDEX brand ON pixel_log (brand_id, user_id)');
        $this->addSql('ALTER TABLE fos_user ADD payment NUMERIC(5, 2) DEFAULT \'0\' NOT NULL, CHANGE balance payout NUMERIC(5, 2) DEFAULT \'0\' NOT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE payment_log');
        $this->addSql('ALTER TABLE fos_user ADD balance NUMERIC(5, 2) DEFAULT \'0.00\' NOT NULL, DROP payout, DROP payment');
        $this->addSql('DROP INDEX brand ON pixel_log');
    }
}
