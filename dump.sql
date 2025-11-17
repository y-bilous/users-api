-- Version DoctrineMigrations\Version20251114190528
CREATE TABLE users (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, login VARCHAR(32) NOT NULL, phone VARCHAR(15) NOT NULL, password VARCHAR(255) NOT NULL);
CREATE UNIQUE INDEX UNIQ_1483A5E9AA08CB10 ON users (login);
CREATE INDEX idx_user_login_phone ON users (login, phone);
CREATE TABLE messenger_messages (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, body CLOB NOT NULL, headers CLOB NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , available_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , delivered_at DATETIME DEFAULT NULL --(DC2Type:datetime_immutable)
        );
CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name);
CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at);
CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at);

-- Version DoctrineMigrations\Version20251115143858
ALTER TABLE users ADD COLUMN roles CLOB NOT NULL;
