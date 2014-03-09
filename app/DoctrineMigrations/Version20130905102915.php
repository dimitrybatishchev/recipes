<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20130905102915 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("CREATE TABLE Measure_Unit (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(128) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE Cuisine (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(128) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE User (id INT NOT NULL, firstname VARCHAR(128) NOT NULL, lastname VARCHAR(128) NOT NULL, avatar VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE Ingredient (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(128) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE Comment (id INT AUTO_INCREMENT NOT NULL, creator_id INT DEFAULT NULL, recipe_id INT DEFAULT NULL, text LONGTEXT NOT NULL, created DATETIME NOT NULL, INDEX IDX_5BC96BF061220EA6 (creator_id), INDEX IDX_5BC96BF059D8A214 (recipe_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE Recipe_Ingredient (recipe_id INT NOT NULL, ingredient_id INT NOT NULL, measure_unit_id INT DEFAULT NULL, count VARCHAR(64) NOT NULL, INDEX IDX_432FCF3959D8A214 (recipe_id), INDEX IDX_432FCF39933FE08C (ingredient_id), INDEX IDX_432FCF3963C6A475 (measure_unit_id), PRIMARY KEY(recipe_id, ingredient_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE Recipe (id INT AUTO_INCREMENT NOT NULL, category_id INT DEFAULT NULL, creator_id INT DEFAULT NULL, cuisine_id INT DEFAULT NULL, name VARCHAR(128) NOT NULL, description LONGTEXT NOT NULL, image VARCHAR(255) DEFAULT NULL, INDEX IDX_DD24B40112469DE2 (category_id), INDEX IDX_DD24B40161220EA6 (creator_id), INDEX IDX_DD24B401ED4BAC14 (cuisine_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE User_Liked_Recipes (recipe_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_167AC6F659D8A214 (recipe_id), INDEX IDX_167AC6F6A76ED395 (user_id), PRIMARY KEY(recipe_id, user_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE Category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(128) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("ALTER TABLE Comment ADD CONSTRAINT FK_5BC96BF061220EA6 FOREIGN KEY (creator_id) REFERENCES User (id)");
        $this->addSql("ALTER TABLE Comment ADD CONSTRAINT FK_5BC96BF059D8A214 FOREIGN KEY (recipe_id) REFERENCES Recipe (id)");
        $this->addSql("ALTER TABLE Recipe_Ingredient ADD CONSTRAINT FK_432FCF3959D8A214 FOREIGN KEY (recipe_id) REFERENCES Recipe (id)");
        $this->addSql("ALTER TABLE Recipe_Ingredient ADD CONSTRAINT FK_432FCF39933FE08C FOREIGN KEY (ingredient_id) REFERENCES Ingredient (id)");
        $this->addSql("ALTER TABLE Recipe_Ingredient ADD CONSTRAINT FK_432FCF3963C6A475 FOREIGN KEY (measure_unit_id) REFERENCES Measure_Unit (id)");
        $this->addSql("ALTER TABLE Recipe ADD CONSTRAINT FK_DD24B40112469DE2 FOREIGN KEY (category_id) REFERENCES Category (id)");
        $this->addSql("ALTER TABLE Recipe ADD CONSTRAINT FK_DD24B40161220EA6 FOREIGN KEY (creator_id) REFERENCES User (id)");
        $this->addSql("ALTER TABLE Recipe ADD CONSTRAINT FK_DD24B401ED4BAC14 FOREIGN KEY (cuisine_id) REFERENCES Cuisine (id)");
        $this->addSql("ALTER TABLE User_Liked_Recipes ADD CONSTRAINT FK_167AC6F659D8A214 FOREIGN KEY (recipe_id) REFERENCES Recipe (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE User_Liked_Recipes ADD CONSTRAINT FK_167AC6F6A76ED395 FOREIGN KEY (user_id) REFERENCES User (id) ON DELETE CASCADE");
        $this->addSql("INSERT INTO Measure_Unit (id, name) VALUES
            (1, 'банка'),
            (2, 'головка'),
            (3, 'грамм'),
            (4, 'зубчик'),
            (5, 'килограмм'),
            (6, 'кусок'),
            (7, 'литр'),
            (8, 'миллилитр'),
            (9, 'на кончике ножа'),
            (10, 'по вкусу'),
            (11, 'пучок'),
            (12, 'стакан'),
            (13, 'столовая ложка'),
            (14, 'чайная ложка'),
            (15, 'штука'),
            (16, 'щепотка'),
            (17, 'стебель')");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE Recipe_Ingredient DROP FOREIGN KEY FK_432FCF3963C6A475");
        $this->addSql("ALTER TABLE Recipe DROP FOREIGN KEY FK_DD24B401ED4BAC14");
        $this->addSql("ALTER TABLE Comment DROP FOREIGN KEY FK_5BC96BF061220EA6");
        $this->addSql("ALTER TABLE Recipe DROP FOREIGN KEY FK_DD24B40161220EA6");
        $this->addSql("ALTER TABLE User_Liked_Recipes DROP FOREIGN KEY FK_167AC6F6A76ED395");
        $this->addSql("ALTER TABLE Recipe_Ingredient DROP FOREIGN KEY FK_432FCF39933FE08C");
        $this->addSql("ALTER TABLE Comment DROP FOREIGN KEY FK_5BC96BF059D8A214");
        $this->addSql("ALTER TABLE Recipe_Ingredient DROP FOREIGN KEY FK_432FCF3959D8A214");
        $this->addSql("ALTER TABLE User_Liked_Recipes DROP FOREIGN KEY FK_167AC6F659D8A214");
        $this->addSql("ALTER TABLE Recipe DROP FOREIGN KEY FK_DD24B40112469DE2");
        $this->addSql("DROP TABLE Measure_Unit");
        $this->addSql("DROP TABLE Cuisine");
        $this->addSql("DROP TABLE User");
        $this->addSql("DROP TABLE Ingredient");
        $this->addSql("DROP TABLE Comment");
        $this->addSql("DROP TABLE Recipe_Ingredient");
        $this->addSql("DROP TABLE Recipe");
        $this->addSql("DROP TABLE User_Liked_Recipes");
        $this->addSql("DROP TABLE Category");
    }
}
