-- 1. Modify class_id in ebooks to allow NULL
ALTER TABLE ebooks MODIFY COLUMN class_id INT(11) NULL DEFAULT NULL;

-- 2. Add mapel_id, ekstra_id, and custom_category to ebooks table
ALTER TABLE ebooks ADD COLUMN mapel_id INT(11) NULL DEFAULT NULL AFTER class_id;
ALTER TABLE ebooks ADD COLUMN ekstra_id INT(11) NULL DEFAULT NULL AFTER mapel_id;
ALTER TABLE ebooks ADD COLUMN custom_category VARCHAR(100) NULL DEFAULT NULL AFTER ekstra_id;

-- 3. Create ebook_reading_history table
CREATE TABLE IF NOT EXISTS ebook_reading_history (
    id_history INT(11) AUTO_INCREMENT PRIMARY KEY,
    user_id INT(11) NOT NULL,
    ebook_id INT(11) NOT NULL,
    last_page INT(11) DEFAULT 1,
    total_pages INT(11) DEFAULT 1,
    last_read_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY user_ebook (user_id, ebook_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
