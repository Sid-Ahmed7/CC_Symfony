<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;


final class Version20241220234722 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {

        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE program_category (program_id INT NOT NULL, category_id INT NOT NULL, INDEX IDX_8779E5F43EB8070A (program_id), INDEX IDX_8779E5F412469DE2 (category_id), PRIMARY KEY(program_id, category_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE review (id INT AUTO_INCREMENT NOT NULL, program_id INT DEFAULT NULL, author_id INT DEFAULT NULL, rating INT NOT NULL, comment LONGTEXT NOT NULL, create_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_794381C63EB8070A (program_id), INDEX IDX_794381C6F675F31B (author_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE program_category ADD CONSTRAINT FK_8779E5F43EB8070A FOREIGN KEY (program_id) REFERENCES program (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE program_category ADD CONSTRAINT FK_8779E5F412469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE review ADD CONSTRAINT FK_794381C63EB8070A FOREIGN KEY (program_id) REFERENCES program (id)');
        $this->addSql('ALTER TABLE review ADD CONSTRAINT FK_794381C6F675F31B FOREIGN KEY (author_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE program ADD difficulty VARCHAR(255) NOT NULL, ADD price NUMERIC(10, 2) NOT NULL');
    }

    public function down(Schema $schema): void
    {
   
        $this->addSql('ALTER TABLE program_category DROP FOREIGN KEY FK_8779E5F43EB8070A');
        $this->addSql('ALTER TABLE program_category DROP FOREIGN KEY FK_8779E5F412469DE2');
        $this->addSql('ALTER TABLE review DROP FOREIGN KEY FK_794381C63EB8070A');
        $this->addSql('ALTER TABLE review DROP FOREIGN KEY FK_794381C6F675F31B');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE program_category');
        $this->addSql('DROP TABLE review');
        $this->addSql('ALTER TABLE program DROP difficulty, DROP price');
    }
}
