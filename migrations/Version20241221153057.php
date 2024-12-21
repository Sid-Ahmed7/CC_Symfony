<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;


final class Version20241221153057 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE program DROP rating');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE program ADD rating DOUBLE PRECISION NOT NULL');
    }
}
