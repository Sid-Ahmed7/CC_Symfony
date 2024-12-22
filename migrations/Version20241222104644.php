<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;


final class Version20241222104644 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE session ADD created_at DATETIME  NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE session DROP created_at');
    }
}
