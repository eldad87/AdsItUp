<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151016032102 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE commission_plan ADD referrer_payout NUMERIC(10, 0) NOT NULL');
        $this->addSql('ALTER TABLE brand_record ADD referrer_payout NUMERIC(10, 0) NOT NULL');
        $this->addSql('CREATE INDEX updatedAt ON brand_record (is_commission_granted, updated_at)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX updatedAt ON brand_record');
        $this->addSql('ALTER TABLE brand_record DROP referrer_payout');
        $this->addSql('ALTER TABLE commission_plan DROP referrer_payout');
    }
}
