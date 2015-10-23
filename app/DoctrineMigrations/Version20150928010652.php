<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150928010652 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE commission_plan_user (commission_plan_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_5014313C414CE44B (commission_plan_id), INDEX IDX_5014313CA76ED395 (user_id), PRIMARY KEY(commission_plan_id, user_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_commission_plan (user_id INT NOT NULL, commission_plan_id INT NOT NULL, INDEX IDX_8A17F010A76ED395 (user_id), INDEX IDX_8A17F010414CE44B (commission_plan_id), PRIMARY KEY(user_id, commission_plan_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE commission_plan_user ADD CONSTRAINT FK_5014313C414CE44B FOREIGN KEY (commission_plan_id) REFERENCES commission_plan (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE commission_plan_user ADD CONSTRAINT FK_5014313CA76ED395 FOREIGN KEY (user_id) REFERENCES fos_user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_commission_plan ADD CONSTRAINT FK_8A17F010A76ED395 FOREIGN KEY (user_id) REFERENCES fos_user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_commission_plan ADD CONSTRAINT FK_8A17F010414CE44B FOREIGN KEY (commission_plan_id) REFERENCES commission_plan (id) ON DELETE CASCADE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE commission_plan_user');
        $this->addSql('DROP TABLE user_commission_plan');
    }
}
