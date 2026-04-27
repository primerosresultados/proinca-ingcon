INSERT INTO settings (setting_key, setting_value) VALUES
    ('notification_email', ''),
    ('notification_from', ''),
    ('autoreply_enabled', '0'),
    ('autoreply_subject', 'Recibimos tu mensaje'),
    ('autoreply_body', 'Hola {{name}},\n\nGracias por contactarnos. Te responderemos a la brevedad.'),
    ('ga_id', ''),
    ('pixel_id', ''),
    ('timezone', 'America/Argentina/Buenos_Aires')
ON DUPLICATE KEY UPDATE setting_key = setting_key;
