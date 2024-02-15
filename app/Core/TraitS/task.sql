-- **users**
CREATE TABLE users (
    id INT AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL,
    email VARCHAR(155) NOT NULL DEFAULT 'your_email@domain.com',
    password VARCHAR(255) NOT NULL,
    role ENUM ('admin', 'programmer', 'tester') NOT NULL DEFAULT 'programmer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
);

ALTER TABLE users
    PARTITION BY RANGE (id) (
        PARTITION p0 VALUES LESS THAN (10000),
        PARTITION p1 VALUES LESS THAN (20000),
        PARTITION p2 VALUES LESS THAN (30000),
        PARTITION p3 VALUES LESS THAN (40000),
        PARTITION p4 VALUES LESS THAN (50000),
        PARTITION p5 VALUES LESS THAN (60000)
    );

CREATE INDEX idx_username_users ON users (username);
CREATE INDEX idx_email_users ON users (email);
CREATE INDEX idx_role_users ON users (role);



-- **tasks**
CREATE TABLE tasks (
    id INT AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    startDate DATE,
    endDate DATE,
    status ENUM ('todo', 'doing', 'done') NOT NULL DEFAULT "todo",
    userId INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
);

ALTER TABLE tasks
    PARTITION BY RANGE (id) (
        PARTITION p0 VALUES LESS THAN (10000),
        PARTITION p1 VALUES LESS THAN (20000),
        PARTITION p2 VALUES LESS THAN (30000),
        PARTITION p3 VALUES LESS THAN (40000),
        PARTITION p4 VALUES LESS THAN (50000),
        PARTITION p5 VALUES LESS THAN (60000)
    );

CREATE INDEX idx_userid_tasks ON tasks (userId);
CREATE INDEX idx_startdate_tasks ON tasks (startDate);
CREATE INDEX idx_enddate_tasks ON tasks (endDate);

-- **user_tasks**
CREATE TABLE user_tasks (
    userId INT,
    taskId INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE INDEX idx_userid_user_tasks ON user_tasks (userId);
CREATE INDEX idx_taskid_user_tasks ON user_tasks (taskId);

-- **reports**
CREATE TABLE reports (
    id INT AUTO_INCREMENT,
    taskId INT,
    userId INT,
    name VARCHAR(255) NOT NULL,
    description TEXT (255) NOT NULL,
    filename VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
);

CREATE INDEX idx_taskid_reports ON reports (taskId);
CREATE INDEX idx_userid_reports ON reports (userId);

-- **tokens**
CREATE TABLE tokens (
    id INT AUTO_INCREMENT,
    userId INT,
    token VARCHAR(255) NOT NULL,
    expiry DATETIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
);

ALTER TABLE tokens
    PARTITION BY RANGE (id) (
        PARTITION p0 VALUES LESS THAN (10000),
        PARTITION p1 VALUES LESS THAN (20000),
        PARTITION p2 VALUES LESS THAN (30000),
        PARTITION p3 VALUES LESS THAN (40000),
        PARTITION p4 VALUES LESS THAN (50000),
        PARTITION p5 VALUES LESS THAN (60000)
    );


-- **users**
INSERT INTO users (username, email, password, role)
SELECT
    CONCAT('user', LPAD(FLOOR(RAND() * 9999), 4, '0')),
    CONCAT('email', LPAD(FLOOR(RAND() * 9999), 4, '0'), '@domain.com'),
    MD5(RAND()),
    ELT(FLOOR(1 + RAND() * 3), 'admin', 'programmer', 'tester')
FROM
 (SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9 UNION SELECT 10) AS dummy;

-- **tasks**
-- **tasks**
INSERT INTO tasks (name, description, startDate, endDate, status, userId)
SELECT
    CONCAT('task', LPAD(FLOOR(RAND() * 9999), 4, '0')),
    CONCAT('This is a description for task', LPAD(FLOOR(RAND() * 9999), 4, '0')),
    DATE_ADD(CURDATE(), INTERVAL -FLOOR(RAND() * 10) DAY),
    DATE_ADD(CURDATE(), INTERVAL FLOOR(RAND() * 10) DAY),
    ELT(FLOOR(1 + RAND() * 3), 'todo', 'doing', 'done'),
    FLOOR(1 + RAND() * (SELECT MAX(id) FROM users))
FROM
    (SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9 UNION SELECT 10) AS dummy;

-- **user_tasks**
INSERT INTO user_tasks (userId, taskId)
SELECT
    FLOOR(1 + RAND() * (SELECT MAX(id) FROM users)),
    FLOOR(1 + RAND() * (SELECT MAX(id) FROM tasks))
FROM
(SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9 UNION SELECT 10) AS dummy;


-- **reports**
INSERT INTO reports (taskId, userId, name, description, filename)
SELECT
    FLOOR(1 + RAND() * (SELECT MAX(id) FROM tasks)),
    FLOOR(1 + RAND() * (SELECT MAX(id) FROM users)),
    CONCAT('report', LPAD(FLOOR(RAND() * 9999), 4, '0')),
    CONCAT('This is a report description for report', LPAD(FLOOR(RAND() * 9999), 4, '0')),
    CONCAT('report', LPAD(FLOOR(RAND() * 9999), 4, '0'), '.pdf')
FROM
(SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9 UNION SELECT 10) AS dummy;



