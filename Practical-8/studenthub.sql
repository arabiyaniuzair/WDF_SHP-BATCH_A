
CREATE DATABASE IF NOT EXISTS studenthub;
USE studenthub;

CREATE TABLE students (
    student_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    mobile VARCHAR(15) NOT NULL,
    password VARCHAR(255) NOT NULL,
    course VARCHAR(50) NOT NULL,
    year INT NOT NULL,
    gender VARCHAR(20) NOT NULL
);

CREATE TABLE events (
    event_id INT AUTO_INCREMENT PRIMARY KEY,
    event_name VARCHAR(100) NOT NULL,
    event_date DATE NOT NULL,
    venue VARCHAR(150) NOT NULL
);

CREATE TABLE registrations (
    registration_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    event_id INT NOT NULL,
    registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(student_id),
    FOREIGN KEY (event_id) REFERENCES events(event_id)
);

INSERT INTO events (event_name, event_date, venue) VALUES
('Tech Fest 2026', '2026-08-25', 'CHARUSAT Auditorium'),
('Sports Week', '2026-09-05', 'Sports Ground'),
('Coding Competition', '2026-09-10', 'IT Department Lab'),
('Cultural Night', '2026-09-18', 'Open Air Theatre'),
('Placement Drive', '2026-09-25', 'Placement Cell');
