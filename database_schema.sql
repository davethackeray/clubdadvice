-- Dadvice Database Schema
CREATE DATABASE IF NOT EXISTS dadvice;
USE dadvice;

-- Main articles table
CREATE TABLE articles (
    id VARCHAR(255) PRIMARY KEY,
    title VARCHAR(500) NOT NULL,
    summary TEXT,
    full_content LONGTEXT,
    content_type ENUM('heartwarming-story', 'practical-tip', 'expert-technique', 'real-life-hack', 'research-insight', 'problem-solution', 'funny-moment', 'aha-moment') NOT NULL,
    urgency ENUM('timeless', 'trending', 'seasonal', 'urgent') DEFAULT 'timeless',
    engagement_score INT DEFAULT 0,
    practical_score INT DEFAULT 0,
    universal_appeal INT DEFAULT 0,
    quote_highlight TEXT,
    newsletter_priority INT DEFAULT 0,
    app_featured BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Age groups table
CREATE TABLE age_groups (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) UNIQUE NOT NULL,
    display_name VARCHAR(100) NOT NULL,
    sort_order INT DEFAULT 0
);

-- Categories table
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) UNIQUE NOT NULL,
    display_name VARCHAR(100) NOT NULL,
    description TEXT,
    sort_order INT DEFAULT 0
);

-- Tags table
CREATE TABLE tags (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) UNIQUE NOT NULL
);

-- Source information table
CREATE TABLE sources (
    id INT AUTO_INCREMENT PRIMARY KEY,
    article_id VARCHAR(255),
    podcast_title VARCHAR(500),
    episode_title VARCHAR(500),
    episode_url VARCHAR(1000),
    media_url VARCHAR(1000),
    timestamp VARCHAR(20),
    host_name VARCHAR(200),
    FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE
);

-- Junction tables for many-to-many relationships
CREATE TABLE article_age_groups (
    article_id VARCHAR(255),
    age_group_id INT,
    PRIMARY KEY (article_id, age_group_id),
    FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE,
    FOREIGN KEY (age_group_id) REFERENCES age_groups(id) ON DELETE CASCADE
);

CREATE TABLE article_categories (
    article_id VARCHAR(255),
    category_id INT,
    PRIMARY KEY (article_id, category_id),
    FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
);

CREATE TABLE article_tags (
    article_id VARCHAR(255),
    tag_id INT,
    PRIMARY KEY (article_id, tag_id),
    FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE,
    FOREIGN KEY (tag_id) REFERENCES tags(id) ON DELETE CASCADE
);

-- Actionable takeaways table
CREATE TABLE takeaways (
    id INT AUTO_INCREMENT PRIMARY KEY,
    article_id VARCHAR(255),
    takeaway TEXT NOT NULL,
    sort_order INT DEFAULT 0,
    FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE
);

-- Related topics table
CREATE TABLE related_topics (
    id INT AUTO_INCREMENT PRIMARY KEY,
    article_id VARCHAR(255),
    topic VARCHAR(200) NOT NULL,
    FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE
);

-- Insert default age groups
INSERT INTO age_groups (name, display_name, sort_order) VALUES
('newborn', 'Newborn (0-3 months)', 1),
('baby', 'Baby (3-12 months)', 2),
('toddler', 'Toddler (1-3 years)', 3),
('preschooler', 'Preschooler (3-5 years)', 4),
('school-age', 'School Age (5-11 years)', 5),
('tween', 'Tween (11-13 years)', 6),
('teenager', 'Teenager (13-18 years)', 7),
('young-adult', 'Young Adult (18+ years)', 8),
('all-ages', 'All Ages', 9);

-- Insert default categories
INSERT INTO categories (name, display_name, description, sort_order) VALUES
('sleep-solutions', 'Sleep Solutions', 'Tips and strategies for better sleep', 1),
('behaviour-management', 'Behaviour Management', 'Managing challenging behaviours', 2),
('emotional-intelligence', 'Emotional Intelligence', 'Building emotional skills', 3),
('communication-skills', 'Communication Skills', 'Improving parent-child communication', 4),
('educational-support', 'Educational Support', 'Supporting learning and development', 5),
('health-wellness', 'Health & Wellness', 'Physical and mental health advice', 6),
('family-dynamics', 'Family Dynamics', 'Building stronger family relationships', 7),
('screen-time-tech', 'Screen Time & Tech', 'Managing technology and screen time', 8),
('social-development', 'Social Development', 'Building social skills and friendships', 9),
('creativity-play', 'Creativity & Play', 'Encouraging creative expression', 10),
('nutrition-feeding', 'Nutrition & Feeding', 'Healthy eating and feeding advice', 11),
('safety-protection', 'Safety & Protection', 'Keeping children safe', 12),
('special-needs', 'Special Needs', 'Supporting children with special needs', 13),
('working-parent-tips', 'Working Parent Tips', 'Balancing work and parenting', 14),
('self-care-parent', 'Parent Self-Care', 'Taking care of yourself as a parent', 15),
('relationship-partnership', 'Relationship & Partnership', 'Maintaining relationships while parenting', 16);

-- Create indexes for better performance
CREATE INDEX idx_articles_content_type ON articles(content_type);
CREATE INDEX idx_articles_urgency ON articles(urgency);
CREATE INDEX idx_articles_created_at ON articles(created_at);
CREATE INDEX idx_articles_engagement_score ON articles(engagement_score);
CREATE INDEX idx_articles_newsletter_priority ON articles(newsletter_priority);