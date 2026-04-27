CREATE TABLE IF NOT EXISTS lead_notes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    lead_id INT NOT NULL,
    user_id INT,
    body TEXT NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_lead (lead_id, created_at),
    CONSTRAINT fk_lead_notes_lead FOREIGN KEY (lead_id) REFERENCES leads(id) ON DELETE CASCADE,
    CONSTRAINT fk_lead_notes_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
