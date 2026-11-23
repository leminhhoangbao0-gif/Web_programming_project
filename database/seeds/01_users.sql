-- Default password for both accounts: Atlas@2026
INSERT INTO users (role, full_name, email, phone, password_hash, status) VALUES
('admin', 'Atlas Administrator', 'admin@atlas.test', '0901234567', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'active'),
('customer', 'Cao Thanh Dat', 'dat@atlas.test', '0337548798', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'active');

INSERT INTO addresses (user_id, recipient_name, phone, address_line, ward, district, city, is_default) VALUES
(2, 'Le Minh Hoang Bao', '0336356754', 'Bảo tự nhập đi t ko bt', 'Phuong Hiep Binh', 'Thu Duc', 'Ho Chi Minh City', 1);
