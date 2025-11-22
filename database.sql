-- Job Posting Website Database Schema
-- Database: jobsite_db

CREATE DATABASE IF NOT EXISTS jobsite_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE jobsite_db;

-- Jobs Table
CREATE TABLE IF NOT EXISTS jobs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    company VARCHAR(255) NOT NULL,
    category VARCHAR(100) NOT NULL,
    location VARCHAR(100) NOT NULL,
    job_type VARCHAR(50) DEFAULT 'Full-time',
    salary VARCHAR(100),
    short_description TEXT NOT NULL,
    full_description TEXT NOT NULL,
    requirements TEXT,
    contact_email VARCHAR(255),
    contact_phone VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    status ENUM('active', 'inactive') DEFAULT 'active',
    INDEX idx_category (category),
    INDEX idx_location (location),
    INDEX idx_created (created_at),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Newsletter Subscribers Table
CREATE TABLE IF NOT EXISTS newsletter_subscribers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    subscribed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('active', 'unsubscribed') DEFAULT 'active',
    INDEX idx_email (email),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert Sample Jobs
INSERT INTO jobs (title, company, category, location, job_type, salary, short_description, full_description, requirements, contact_email, contact_phone) VALUES
('Senior PHP Developer', 'Tech Solutions Pvt Ltd', 'IT & Software', 'Karachi - Clifton', 'Full-time', 'Rs. 80,000 - 120,000', 'Looking for experienced PHP developer with 3+ years experience.', 'We are seeking a talented Senior PHP Developer to join our growing team. You will be responsible for developing and maintaining web applications using PHP, MySQL, and modern frameworks.', 'PHP 7+, MySQL, Laravel/CodeIgniter, JavaScript, Git, 3+ years experience', 'careers@techsolutions.com', '0300-1234567'),
('Marketing Manager', 'ABC Corporation', 'Marketing & Sales', 'Karachi - Gulshan', 'Full-time', 'Rs. 60,000 - 90,000', 'Experienced marketing professional needed for leading retail company.', 'Join our dynamic marketing team as a Marketing Manager. You will develop marketing strategies, manage campaigns, and lead a team of marketing professionals.', 'MBA Marketing, 5+ years experience, Digital Marketing expertise, Team management skills', 'hr@abccorp.com', '0321-9876543'),
('Graphic Designer', 'Creative Studio', 'Design & Creative', 'Karachi - DHA', 'Part-time', 'Rs. 30,000 - 50,000', 'Creative graphic designer for branding and digital media projects.', 'We are looking for a creative Graphic Designer to work on various branding, print, and digital media projects. Must be proficient in Adobe Creative Suite.', 'Adobe Photoshop, Illustrator, InDesign, Portfolio required, 2+ years experience', 'jobs@creativestudio.pk', '0333-5554444'),
('Accountant', 'Finance Group Ltd', 'Accounting & Finance', 'Karachi - Saddar', 'Full-time', 'Rs. 40,000 - 60,000', 'Qualified accountant for financial reporting and taxation.', 'We need a qualified Accountant to handle financial reporting, taxation, and bookkeeping. ACCA/CA-Inter preferred.', 'ACCA/CA-Inter, Tally/QuickBooks, MS Excel, 3+ years experience', 'accounts@financegroup.pk', '0300-7778888'),
('Customer Service Representative', 'Call Center Solutions', 'Customer Service', 'Karachi - North Nazimabad', 'Full-time', 'Rs. 25,000 - 35,000', 'Join our customer service team for international clients.', 'We are hiring Customer Service Representatives for our international call center. Night shifts available with transportation.', 'Excellent English communication, Computer skills, Shift flexibility', 'recruitment@callcentersol.com', '0322-4445566');
