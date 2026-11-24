<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261124025133 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Tabelas iniciais da agenda: usuario, categoria, evento (e a fila do Messenger)';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE categoria (id INT AUTO_INCREMENT NOT NULL, nome VARCHAR(60) NOT NULL, cor VARCHAR(7) NOT NULL, ativa TINYINT DEFAULT 1 NOT NULL, UNIQUE INDEX uniq_categoria_nome (nome), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE evento (id INT AUTO_INCREMENT NOT NULL, titulo VARCHAR(120) NOT NULL, slug VARCHAR(140) NOT NULL, resumo VARCHAR(255) DEFAULT NULL, descricao LONGTEXT DEFAULT NULL, inicio DATETIME NOT NULL, local VARCHAR(120) DEFAULT NULL, publicado TINYINT DEFAULT 0 NOT NULL, categoria_id INT NOT NULL, INDEX idx_evento_publicado_inicio (publicado, inicio), UNIQUE INDEX uniq_evento_slug (slug), INDEX IDX_47860B053397707A (categoria_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE usuario (id INT AUTO_INCREMENT NOT NULL, nome VARCHAR(120) NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, senha VARCHAR(255) NOT NULL, UNIQUE INDEX uniq_usuario_email (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 (queue_name, available_at, delivered_at, id), PRIMARY KEY (id))');
        $this->addSql('ALTER TABLE evento ADD CONSTRAINT FK_47860B053397707A FOREIGN KEY (categoria_id) REFERENCES categoria (id) ON DELETE RESTRICT');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE evento DROP FOREIGN KEY FK_47860B053397707A');
        $this->addSql('DROP TABLE categoria');
        $this->addSql('DROP TABLE evento');
        $this->addSql('DROP TABLE usuario');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
