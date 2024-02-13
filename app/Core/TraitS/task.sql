CREATE TABLE
    users (
        id INT AUTO_INCREMENT,
        username VARCHAR(50) NOT NULL,
        email VARCHAR(155) NOT NULL,
        password VARCHAR(255) NOT NULL,
        role ENUM ('admin', 'programmer', 'tester') NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    );

CREATE TABLE
    tasks (
        id INT AUTO_INCREMENT,
        name VARCHAR(100) NOT NULL,
        description TEXT,
        startDate DATE,
        endDate DATE,
        status ENUM ('todo', 'doing', 'done') NOT NULL,
        userId INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        FOREIGN KEY (userId) REFERENCES users (id)
    );

CREATE TABLE
    user_tasks (
        userId INT,
        taskId INT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (userId) REFERENCES users (id),
        FOREIGN KEY (taskId) REFERENCES tasks (id)
    );

CREATE TABLE
    reports (
        id INT AUTO_INCREMENT,
        taskId INT,
        userId INT,
        name VARCHAR(255) NOT NULL,
        description TEXT (255) NOT NULL,
        filename VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        FOREIGN KEY (taskId) REFERENCES tasks (id)
    );

CREATE TABLE
    tokens (
        id INT AUTO_INCREMENT,
        userId INT,
        token VARCHAR(255) NOT NULL,
        expiry DATETIME NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        FOREIGN KEY (userId) REFERENCES users (id)
    );