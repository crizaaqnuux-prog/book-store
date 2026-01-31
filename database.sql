-- Create Database
CREATE DATABASE IF NOT EXISTS bookstore;
USE bookstore;

-- Users Table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    role ENUM('admin', 'user') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Books Table
CREATE TABLE IF NOT EXISTS books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    author VARCHAR(100) NOT NULL,
    category VARCHAR(50) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    stock INT NOT NULL,
    description TEXT,
    image_url VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Orders Table
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    total_amount DECIMAL(10, 2) NOT NULL,
    status ENUM('pending', 'completed', 'cancelled') DEFAULT 'pending',
    delivery_status ENUM('Ordered', 'Shipped', 'Delivered') DEFAULT 'Ordered',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Order Items Table
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    book_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE
);

-- Payments Table
CREATE TABLE IF NOT EXISTS payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    payment_method VARCHAR(50) NOT NULL,
    payment_number VARCHAR(20),
    transaction_id VARCHAR(100) UNIQUE,
    status VARCHAR(50) DEFAULT 'pending',
    amount DECIMAL(10, 2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
);

-- Insert Admin User (password: admin123)
-- Using password_hash('admin123', PASSWORD_DEFAULT)
INSERT INTO users (username, password, email, role) 
VALUES ('admin', '$2y$10$89W1fvxKovv8L2fT5X/K/uK3V.rW/X4fH.v6K3l5L/F.U.1y.V5i.', 'admin@bookstore.com', 'admin');

-- Insert Sample Books
INSERT INTO books (title, author, category, price, stock, description, image_url) VALUES
('The Great Gatsby', 'F. Scott Fitzgerald', 'Fiction', 15.99, 10, 'A classic novel of the Jazz Age.', 'https://images.unsplash.com/photo-1543004218-28302061404c?auto=format&fit=crop&q=80&w=400'),
('1984', 'George Orwell', 'Dystopian', 12.50, 20, 'A chilling look at a totalitarian future.', 'https://images.unsplash.com/photo-1541963463532-d68292c34b19?auto=format&fit=crop&q=80&w=400'),
('To Kill a Mockingbird', 'Harper Lee', 'Fiction', 10.99, 15, 'A story of race and justice in the American South.', 'https://images.unsplash.com/photo-1544947950-fa07a98d237f?auto=format&fit=crop&q=80&w=400'),
('The Hobbit', 'J.R.R. Tolkien', 'Fantasy', 14.99, 5, 'The prefix to The Lord of the Rings.', 'https://images.unsplash.com/photo-1621351123081-49fa9b433433?auto=format&fit=crop&q=80&w=400'),
('Atomic Habits', 'James Clear', 'Self-Help', 22.00, 30, 'An easy and proven way to build good habits and break bad ones.', 'https://images.unsplash.com/photo-1589829085413-56de8ae18c73?auto=format&fit=crop&q=80&w=400'),
('Project Hail Mary', 'Andy Weir', 'Sci-Fi', 18.50, 12, 'A lone astronaut must save the earth from disaster.', 'https://images.unsplash.com/photo-1614544048536-0d28caf77f41?auto=format&fit=crop&q=80&w=400'),
('The Psychology of Money', 'Morgan Housel', 'Finance', 16.95, 25, 'Doing well with money isn’t necessarily about what you know.', 'https://images.unsplash.com/photo-1554224155-8d04cb21cd6c?auto=format&fit=crop&q=80&w=400'),
('Dune', 'Frank Herbert', 'Sci-Fi', 25.00, 8, 'Set on the desert planet Arrakis, Dune is the story of the boy Paul Atreides.', 'https://images.unsplash.com/photo-1543004218-28302061404c?auto=format&fit=crop&q=80&w=400'),
-- IT Books
('Modern Java in Action', 'Raoul-Gabriel Urma', 'IT', 45.00, 15, 'Manning Publications book about Java 8, 9, 10, and 11.', 'https://images.unsplash.com/photo-1517694712202-14dd9538aa97?auto=format&fit=crop&q=80&w=400'),
('PHP and MySQL for Dynamic Web Sites', 'Larry Ullman', 'IT', 39.99, 20, 'Learn how to build dynamic websites using PHP and MySQL.', 'https://images.unsplash.com/photo-1599507591144-c6a147881766?auto=format&fit=crop&q=80&w=400'),
('Computer Networking: A Top-Down Approach', 'James Kurose', 'IT', 55.00, 10, 'A balanced approach to networking fundamentals and practices.', 'https://images.unsplash.com/photo-1544197150-b99a580bb7a8?auto=format&fit=crop&q=80&w=400'),
('Clean Code', 'Robert C. Martin', 'IT', 42.50, 25, 'A handbook of agile software craftsmanship.', 'https://images.unsplash.com/photo-1515879218367-8466d910aaa4?auto=format&fit=crop&q=80&w=400');
