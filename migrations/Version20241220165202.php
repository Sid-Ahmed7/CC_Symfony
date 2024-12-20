<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;


final class Version20241220165202 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        
        $this->addSql('CREATE TABLE coach_speciality (coach_id INT NOT NULL, speciality_id INT NOT NULL, INDEX IDX_9D2AD77D3C105691 (coach_id), INDEX IDX_9D2AD77D3B5A08D7 (speciality_id), PRIMARY KEY(coach_id, speciality_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE speciality (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE coach_speciality ADD CONSTRAINT FK_9D2AD77D3C105691 FOREIGN KEY (coach_id) REFERENCES coach (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE coach_speciality ADD CONSTRAINT FK_9D2AD77D3B5A08D7 FOREIGN KEY (speciality_id) REFERENCES speciality (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE coach DROP speciality');
    }

    public function down(Schema $schema): void
    {
        
        $this->addSql('ALTER TABLE coach_speciality DROP FOREIGN KEY FK_9D2AD77D3C105691');
        $this->addSql('ALTER TABLE coach_speciality DROP FOREIGN KEY FK_9D2AD77D3B5A08D7');
        $this->addSql('DROP TABLE coach_speciality');
        $this->addSql('DROP TABLE speciality');
        $this->addSql('ALTER TABLE coach ADD speciality VARCHAR(255) NOT NULL');
    }
}
