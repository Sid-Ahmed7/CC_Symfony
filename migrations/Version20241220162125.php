<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;


final class Version20241220162125 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE session ADD status VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE user ADD account_status VARCHAR(255) NOT NULL, CHANGE gender gender VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE `user` DROP account_status, CHANGE gender gender VARCHAR(50) NOT NULL');
        $this->addSql('ALTER TABLE session DROP status');
    }
}
