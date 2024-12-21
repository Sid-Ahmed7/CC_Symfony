<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;


final class Version20241220182803 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user DROP gender');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE `user` ADD gender VARCHAR(255) NOT NULL');
    }
}
