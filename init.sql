-- Tables

CREATE TABLE IF NOT EXISTS games (
    id          SERIAL PRIMARY KEY,
    name        VARCHAR(255) NOT NULL,
    type        VARCHAR(100) NOT NULL,
    description TEXT,
    release_date DATE,
    studio      VARCHAR(255),
    image_url   VARCHAR(255),
    created_at  TIMESTAMP DEFAULT NOW(),
    updated_at  TIMESTAMP DEFAULT NOW()
);

CREATE TABLE IF NOT EXISTS users (
    id          SERIAL PRIMARY KEY,
    username    VARCHAR(100) NOT NULL UNIQUE,
    email       VARCHAR(255) NOT NULL UNIQUE,
    password    VARCHAR(255) NOT NULL,
    role        VARCHAR(50)  NOT NULL DEFAULT 'user',
    description TEXT         DEFAULT '',
    created_at  TIMESTAMP    DEFAULT NOW()
);

CREATE TABLE IF NOT EXISTS levels (
    id          SERIAL PRIMARY KEY,
    game_id     INT NOT NULL REFERENCES games(id) ON DELETE CASCADE,
    difficulty  VARCHAR(50) NOT NULL,
    description TEXT DEFAULT ''
);

CREATE TABLE IF NOT EXISTS achievements (
    id          SERIAL PRIMARY KEY,
    game_id     INT NOT NULL REFERENCES games(id) ON DELETE CASCADE,
    title       VARCHAR(255) NOT NULL,
    description TEXT
);

CREATE TABLE IF NOT EXISTS user_games (
    user_id     INT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    game_id     INT NOT NULL REFERENCES games(id) ON DELETE CASCADE,
    date_added  TIMESTAMP DEFAULT NOW(),
    play_time   INT DEFAULT 0,
    PRIMARY KEY (user_id, game_id)
);

CREATE TABLE IF NOT EXISTS user_achievements (
    user_id         INT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    achievement_id  INT NOT NULL REFERENCES achievements(id) ON DELETE CASCADE,
    unlocked_at     TIMESTAMP DEFAULT NOW(),
    PRIMARY KEY (user_id, achievement_id)
);

-- Données de test

INSERT INTO users (username, email, password, role) VALUES
    ('admin', 'admin@aetheria.com', '$2y$10$bccNnOlzy0tGczCUlJ3DxO/.MYDU.dxu7N4cPK4F9EQFgIF1il/aC', 'admin');

INSERT INTO games (name, type, description, release_date, studio, image_url) VALUES
    ('Final Fantasy I',   'RPG', 'Le jeu qui a lancé la saga légendaire.',        '1987-12-18', 'Square', 'final_fantasy1.jpg'),
    ('Final Fantasy II',  'RPG', 'Une aventure épique avec un système unique.',   '1988-12-17', 'Square', 'final_fantasy2.jpg'),
    ('Final Fantasy III', 'RPG', 'Découvrez le système des Jobs pour la première fois.', '1990-04-27', 'Square', 'final_fantasy3.jpg'),
    ('Final Fantasy IV',  'RPG', 'Une histoire de rédemption et de courage.',     '1991-07-19', 'Square', 'final_fantasy4.jpg'),
    ('Final Fantasy V',   'RPG', 'Maîtrisez des centaines de capacités de Jobs.', '1992-12-06', 'Square', 'final_fantasy5.jpg');
