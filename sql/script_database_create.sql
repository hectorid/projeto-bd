CREATE DATABASE projeto_bd WITH
    OWNER = admin
    TEMPLATE = template0 -- Foi utilizado esse template para acessar o encoding UTF-8
    ENCODING = 'UTF8';


-- SCHEMA config ------------------------------------------------------------------------

CREATE SCHEMA config;

CREATE TABLE config.reaction_types (
    code int PRIMARY KEY,
    name varchar NOT NULL UNIQUE
);

CREATE TABLE config.notification_types (
    code          int PRIMARY KEY,
    name          varchar NOT NULL UNIQUE,
    text_template text    NOT NULL UNIQUE
);

-- END SCHEMA config --------------------------------------------------------------------


-- SCHEMA data --------------------------------------------------------------------------
CREATE SCHEMA data;

CREATE TABLE data.users (
    id              serial PRIMARY KEY,
    username        varchar UNIQUE NOT NULL,
    visible_name    varchar        NOT NULL,
    email           varchar UNIQUE NOT NULL,
    birthdate       date           NOT NULL,
    profile_picture bytea,
    password_hash   varchar        NOT NULL,
    created_at      timestamp      NOT NULL DEFAULT now()
);

CREATE TABLE data.user_follows (
    follower int REFERENCES data.users ON DELETE CASCADE,
    followed int REFERENCES data.users ON DELETE CASCADE,

    PRIMARY KEY (follower, followed)
);

CREATE TABLE data.messages (
    id             serial PRIMARY KEY,
    sent_by        int NOT NULL REFERENCES data.users,
    sent_to        int NOT NULL REFERENCES data.users,
    text           text NOT NULL,
    created_at     timestamp NOT NULL DEFAULT now(),
    last_edited_at timestamp
);

CREATE TABLE data.posts (
    id              serial PRIMARY KEY,
    text            text      NOT NULL,
    created_by      int       NOT NULL REFERENCES data.users,
    created_at      timestamp NOT NULL DEFAULT now(),
    last_edited_at  timestamp,
    posted_in_group int REFERENCES data.groups
);

CREATE TABLE data.comments (
    id             serial PRIMARY KEY,
    post           int       NOT NULL REFERENCES data.posts ON DELETE CASCADE,
    text           text      NOT NULL,
    created_by     int       NOT NULL REFERENCES data.users,
    created_at     timestamp NOT NULL DEFAULT now(),
    last_edited_at timestamp
);

CREATE TABLE data.reactions (
    post   int REFERENCES data.posts ON DELETE CASCADE,
    "user" int REFERENCES data.users,
    type   int NOT NULL REFERENCES config.reaction_types,

    PRIMARY KEY (post, "user")
);

CREATE TABLE data.images (
    post    int REFERENCES data.posts ON DELETE CASCADE,
    index   int   NOT NULL,
    content bytea NOT NULL,
    caption varchar,

    PRIMARY KEY (post, index)
);

CREATE TABLE data.groups (
    id         serial PRIMARY KEY,
    name       varchar   NOT NULL,
    created_by int       REFERENCES data.users ON DELETE SET NULL,
    created_at timestamp NOT NULL DEFAULT now()
);

CREATE TABLE data.group_members (
    "group"      int REFERENCES data.groups,
    "user"       int REFERENCES data.users,
    joined_at    timestamp NOT NULL DEFAULT now(),
    is_moderator bool      NOT NULL DEFAULT FALSE,

    PRIMARY KEY ("group", "user")
);

CREATE TABLE data.folders (
    id         serial PRIMARY KEY,
    name       varchar   NOT NULL,
    created_by int       NOT NULL REFERENCES data.users ON DELETE CASCADE,
    created_at timestamp NOT NULL DEFAULT now(),

    UNIQUE (created_by, "name")
);

CREATE TABLE data.folder_posts (
    folder   int REFERENCES data.folders ON DELETE CASCADE,
    post     int REFERENCES data.posts ON DELETE CASCADE,
    saved_at timestamp NOT NULL DEFAULT now(),

    PRIMARY KEY (folder, post)
);

CREATE TABLE data.notifications (
    id        serial PRIMARY KEY,
    type      int       NOT NULL REFERENCES config.notification_types,
    "user"    int       NOT NULL REFERENCES data.users ON DELETE CASCADE,
    issued_at timestamp NOT NULL DEFAULT now(),
    agent     int REFERENCES data.users ON DELETE CASCADE,
    message   int REFERENCES data.messages ON DELETE CASCADE,
    "group"   int REFERENCES data.groups ON DELETE CASCADE,
    post      int REFERENCES data.posts ON DELETE CASCADE,
    comment   int REFERENCES data.comments ON DELETE CASCADE
);


CREATE OR REPLACE VIEW data.user_profiles
AS
    WITH user_following_counts AS (
            SELECT follower AS "user",
                   count(1) AS following
            FROM data.user_follows
            GROUP BY follower
        ),
         user_followers_counts AS (
             SELECT followed AS "user",
                    count(1) AS followers
             FROM data.user_follows
             GROUP BY followed
         )
    SELECT users.id,
           users.username,
           users.visible_name,
           encode(users.profile_picture, 'base64') as profile_picture,
           coalesce(USER_FOLLOWING_COUNTS.FOLLOWING, 0) as following,
           coalesce(USER_FOLLOWERS_COUNTS.FOLLOWERS, 0) as followers,
           users.created_at
    FROM data.users
             LEFT JOIN USER_FOLLOWING_COUNTS
                       ON USER_FOLLOWING_COUNTS."user" = id
             LEFT JOIN USER_FOLLOWERS_COUNTS
                       ON USER_FOLLOWERS_COUNTS."user" = id;


CREATE OR REPLACE VIEW data.user_chats
AS
    WITH ordered_messages AS (
             SELECT id as message_id,
                    CASE
                        WHEN sent_by < sent_to
                            THEN sent_by
                        ELSE sent_to
                        END AS user_lower_id,
                    CASE
                        WHEN sent_by < sent_to
                            THEN sent_to
                        ELSE sent_by
                        END AS user_higher_id
             FROM data.messages
             ORDER BY created_at DESC,
                      id DESC
         ),
         last_messages_per_chat AS (
             SELECT DISTINCT ON (USER_LOWER_ID, USER_HIGHER_ID) *
             FROM ordered_messages
         ),
         chats_by_user AS (
             SELECT users.id as user_id,
                    CASE users.id
                        WHEN sent_by THEN sent_to
                        WHEN sent_to THEN sent_by
                        END as chat_user_id,
                    LAST_MESSAGES_PER_CHAT.MESSAGE_ID as last_message_id
             FROM data.users
                 JOIN LAST_MESSAGES_PER_CHAT
                      ON users.id in (USER_LOWER_ID, USER_HIGHER_ID)
                 JOIN data.messages
                      ON LAST_MESSAGES_PER_CHAT.MESSAGE_ID = messages.id
             ORDER BY users.id
         )
    SELECT CHATS.USER_ID,
           CHATS.CHAT_USER_ID,
           CHAT_USERS.visible_name as chat_visible_name,
           CHAT_USERS.username as chat_username,
           encode(CHAT_USERS.profile_picture, 'base64') as chat_profile_picture,
           messages.text as last_message_text,
           messages.created_at as last_message_created_at
    FROM CHATS_BY_USER chats
        JOIN data.users as chat_users
            ON CHAT_USER_ID = CHAT_USERS.id
        JOIN data.messages
            ON LAST_MESSAGE_ID = messages.id;


CREATE OR REPLACE VIEW data.user_posts
AS
    SELECT users.id as user_id,
           users.visible_name as user_visible_name,
           users.username as user_username,
           encode(users.profile_picture, 'base64') as user_profile_picture,
           posts.id as post_id,
           posts.text
    FROM data.posts
        JOIN data.users
            ON created_by = users.id
    ORDER BY posts.created_by,
             posts.created_at desc

-- END SCHEMA data ----------------------------------------------------------------------