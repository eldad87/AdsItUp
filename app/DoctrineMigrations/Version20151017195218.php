<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151017195218 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX updatedAt ON brand_record');
        $this->addSql('ALTER TABLE brand_record CHANGE is_commission_granted is_processed TINYINT(1) NOT NULL');
        $this->addSql('CREATE INDEX updatedAt ON brand_record (is_processed, updated_at)');
        $this->addSql('ALTER TABLE payment_log ADD is_processed TINYINT(1) NOT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX updatedAt ON brand_record');
        $this->addSql('ALTER TABLE brand_record CHANGE is_processed is_commission_granted TINYINT(1) NOT NULL');
        $this->addSql('CREATE INDEX updatedAt ON brand_record (is_commission_granted, updated_at)');
        $this->addSql('ALTER TABLE payment_log DROP is_processed');
    }
}
