SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;

CREATE DATABASE IF NOT EXISTS singup_task CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE singup_task;

-- تعيين ترميز الاتصال لضمان دعم اللغة العربية بشكل كامل
SET character_set_client = utf8mb4;
SET character_set_results = utf8mb4;
SET character_set_connection = utf8mb4;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('client', 'lawyer') NOT NULL,
    remember_token VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS cases (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_id INT NOT NULL,
    lawyer_id INT DEFAULT NULL,
    title VARCHAR(255) NOT NULL,
    client_name VARCHAR(255) NOT NULL,
    lawyer_name VARCHAR(255) DEFAULT NULL,
    case_type VARCHAR(100) DEFAULT NULL,
    status ENUM('active', 'pending', 'completed') DEFAULT 'pending',
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (client_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (lawyer_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS appointments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_id INT NOT NULL,
    lawyer_id INT NOT NULL,
    client_name VARCHAR(255) NOT NULL,
    lawyer_name VARCHAR(255) DEFAULT NULL,
    title VARCHAR(255) NOT NULL,
    appointment_date DATE NOT NULL,
    appointment_time TIME DEFAULT NULL,
    status ENUM('pending', 'confirmed', 'completed', 'cancelled') DEFAULT 'pending',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (client_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (lawyer_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    lawyer_id INT NOT NULL,
    lawyer_name VARCHAR(255) NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    status ENUM('published', 'draft') DEFAULT 'published',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (lawyer_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS service_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_id INT NOT NULL,
    lawyer_id INT DEFAULT NULL,
    service_id INT NOT NULL,
    service_name VARCHAR(255) NOT NULL,
    client_name VARCHAR(255) NOT NULL,
    lawyer_name VARCHAR(255) DEFAULT NULL,
    description TEXT,
    status ENUM('pending', 'accepted', 'rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (client_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (lawyer_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT NOT NULL,
    receiver_id INT NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (receiver_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- بيانات تجريبية واقعية للمنصة
INSERT INTO users (name, email, password, role) VALUES
('أحمد محمود الشافعي', 'ahmed.lawyer@mizan.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'lawyer'),
('سارة حسن العامري', 'sara.lawyer@mizan.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'lawyer'),
('محمد علي زين', 'mohammed.lawyer@mizan.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'lawyer'),
('ليلى إبراهيم فوزي', 'laila.lawyer@mizan.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'lawyer'),
('خالد سعيد المنصوري', 'khaled.client@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'client'),
('مريم يوسف القاضي', 'mariam.client@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'client'),
('عمر عبد العزيز', 'omar.client@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'client');

INSERT INTO services (lawyer_id, lawyer_name, name, description, price) VALUES
(1, 'أحمد محمود الشافعي', 'استشارة قانونية جنائية', 'جلسة استشارية متخصصة في القضايا الجنائية تشمل دراسة الأوراق وتقديم الرأي القانوني المبدئي.', 750.00),
(1, 'أحمد محمود الشافعي', 'تمثيل أمام محكمة الجنايات', 'تمثيل قانوني كامل في القضايا الجنائية أمام محاكم الجنايات بمختلف درجاتها، بما في ذلك إعداد المذكرات.', 5000.00),
(2, 'سارة حسن العامري', 'صياغة ومراجعة العقود', 'خدمة احترافية لصياغة العقود المدنية والتجارية ومراجعتها لضمان حقوق كافة الأطراف وتقليل المخاطر.', 1200.00),
(2, 'سارة حسن العامري', 'قضايا الأحوال الشخصية', 'متابعة وترافع في قضايا الأحوال الشخصية (الأسرة، الميراث، الحضانة) باحترافية عالية وسرية تامة.', 2500.00),
(3, 'محمد علي زين', 'تأسيس الشركات التجارية', 'تقديم الاستشارات اللازمة والقيام بكافة الإجراءات القانونية لتأسيس الشركات بمختلف أنواعها وتسجيلها.', 3500.00),
(3, 'محمد علي زين', 'التحكيم التجاري الدولي', 'تمثيل الشركات في جلسات التحكيم التجاري وتسوية النزاعات خارج القضاء بطرق ودية وقانونية.', 6000.00),
(4, 'ليلى إبراهيم فوزي', 'استشارات الملكية الفكرية', 'حماية العلامات التجارية وبراءات الاختراع وحقوق التأليف والنشر وتقديم الدعم القانوني اللازم.', 2000.00);

INSERT INTO cases (client_id, lawyer_id, title, client_name, lawyer_name, case_type, status, description) VALUES
(5, 1, 'قضية نزاع عقاري - التجمع الخامس', 'خالد سعيد المنصوري', 'أحمد محمود الشافعي', 'مدني', 'active', 'نزاع حول ملكية قطعة أرض في منطقة التجمع الخامس ومطالبة بتثبيت الملكية بناءً على عقود البيع.'),
(6, 2, 'مراجعة عقد توريد دولي', 'مريم يوسف القاضي', 'سارة حسن العامري', 'تجاري', 'pending', 'طلب مراجعة قانونية لعقد توريد بضائع من الخارج لضمان التوافق مع القوانين المحلية والاتفاقيات الدولية.'),
(7, 3, 'نزاع على علامة تجارية', 'عمر عبد العزيز', 'محمد علي زين', 'تجاري', 'active', 'قضية تعدي على علامة تجارية مسجلة ومطالبة بوقف الاستخدام غير القانوني والتعويض عن الأضرار.');

INSERT INTO messages (sender_id, receiver_id, content) VALUES
(5, 1, 'السلام عليكم يا أستاذ أحمد، هل تم تقديم المذكرة الأخيرة للمحكمة في قضية الأرض؟'),
(1, 5, 'وعليكم السلام يا خالد، نعم تم تقديمها اليوم وبانتظار تحديد موعد الجلسة القادمة للاطلاع.'),
(6, 2, 'أهلاً أستاذة سارة، قمت برفع مسودة العقد على المنصة، يرجى الاطلاع عليها لإبداء الملاحظات.'),
(2, 6, 'أهلاً مريم، سأقوم بمراجعتها غداً صباحاً وأوافيك بالتقرير القانوني المفصل.'),
(7, 3, 'أستاذ محمد، ما هي الأوراق المطلوبة لرفع قضية العلامة التجارية؟');