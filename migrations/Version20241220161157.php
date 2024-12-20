<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;


final class Version20241220161157 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE user_coach (user_id INT NOT NULL, coach_id INT NOT NULL, INDEX IDX_DD9B9694A76ED395 (user_id), INDEX IDX_DD9B96943C105691 (coach_id), PRIMARY KEY(user_id, coach_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_coach ADD CONSTRAINT FK_DD9B9694A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_coach ADD CONSTRAINT FK_DD9B96943C105691 FOREIGN KEY (coach_id) REFERENCES coach (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user_coach DROP FOREIGN KEY FK_DD9B9694A76ED395');
        $this->addSql('ALTER TABLE user_coach DROP FOREIGN KEY FK_DD9B96943C105691');
        $this->addSql('DROP TABLE user_coach');
    }
}
