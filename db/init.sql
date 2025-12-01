-- Create users table
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create comments table for XSS Stored lab
CREATE TABLE IF NOT EXISTS comments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL,
    email VARCHAR(100),
    content LONGTEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert default users
INSERT INTO users (username, password, email) VALUES 
('admin', '0192023a7bbd73250516f069df18b500', 'admin@dvwa.local'),
('user', '24c9e15e52afc47c225b757e7bee1f9d', 'user@dvwa.local');

-- Note: Passwords are MD5 hashes:
-- admin123 = 0192023a7bbd73250516f069df18b500
-- user123 = 24c9e15e52afc47c225b757e7bee1f9d

-- Insert sample comments for SQLi lab
INSERT INTO comments (username, email, content) VALUES
('admin', 'admin@dvwa.local', 'This is a test comment'),
('user', 'user@dvwa.local', 'Another comment for testing'),
('test_user', 'test@dvwa.local', 'Sample data in the database');
