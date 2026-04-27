CREATE TABLE IF NOT EXISTS leads (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(50),
    message TEXT,
    source VARCHAR(100) DEFAULT 'website',
    status ENUM('new', 'contacted', 'qualified', 'closed', 'discarded') NOT NULL DEFAULT 'new',
    notes TEXT,
    ip_address VARCHAR(45),
    user_agent VARCHAR(500),
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_status (status),
    INDEX idx_created (created_at),
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
