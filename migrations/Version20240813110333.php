<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240813110333 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE answer (id BLOB NOT NULL, title VARCHAR(255) NOT NULL, next_question_id BLOB DEFAULT NULL, product_id_restrictions CLOB NOT NULL, question_id BLOB NOT NULL, PRIMARY KEY(id), CONSTRAINT FK_DADD4A251E27F6BF FOREIGN KEY (question_id) REFERENCES question (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_DADD4A251E27F6BF ON answer (question_id)');
        $this->addSql('CREATE TABLE product (id BLOB NOT NULL, name VARCHAR(255) NOT NULL, product_category_id BLOB NOT NULL, PRIMARY KEY(id), CONSTRAINT FK_D34A04ADBE6903FD FOREIGN KEY (product_category_id) REFERENCES product_category (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_D34A04ADBE6903FD ON product (product_category_id)');
        $this->addSql('CREATE TABLE product_category (name VARCHAR(255) NOT NULL, id BLOB NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE question (id BLOB NOT NULL, title VARCHAR(255) NOT NULL, questionnaire_id BLOB NOT NULL, PRIMARY KEY(id), CONSTRAINT FK_B6F7494ECE07E8FF FOREIGN KEY (questionnaire_id) REFERENCES questionnaire (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_B6F7494ECE07E8FF ON question (questionnaire_id)');
        $this->addSql('CREATE TABLE questionnaire (product_category_id BLOB NOT NULL, title VARCHAR(255) NOT NULL, id BLOB NOT NULL, PRIMARY KEY(id))');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE answer');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE product_category');
        $this->addSql('DROP TABLE question');
        $this->addSql('DROP TABLE questionnaire');
    }
}
