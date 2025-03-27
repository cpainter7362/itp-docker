CREATE TABLE IF NOT EXISTS todos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name TEXT
);

INSERT INTO todos (name) VALUES 
    ('To-do item 1'),
    ('To-do item 2'),
    ('To-do item 3');