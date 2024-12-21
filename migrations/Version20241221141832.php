<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;


final class Version20241221141832 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE program ADD rating DOUBLE PRECISION NOT NULL');
        $this->addSql('ALTER TABLE session DROP is_chosen');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE session ADD is_chosen TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE program DROP rating');
    }
}
