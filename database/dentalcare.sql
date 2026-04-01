-- ============================================================
--  DentalCare - Complete Database
--  File: database/dentalcare.sql
--  Import this file in phpMyAdmin
-- ============================================================

CREATE DATABASE IF NOT EXISTS dentalcare
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE dentalcare;

-- ============================================================
-- 1. USERS (system access: admin, dentist, receptionist)
-- ============================================================
CREATE TABLE users (
  id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name        VARCHAR(100)  NOT NULL, 
  email       VARCHAR(150)  NOT NULL UNIQUE,
  password    VARCHAR(255)  NOT NULL,
  role        ENUM('admin','dentist','receptionist') NOT NULL DEFAULT 'receptionist',
  avatar      VARCHAR(255)  NULL,
  is_active   TINYINT(1)    NOT NULL DEFAULT 1,
  created_at  TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at  TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================================
-- 2. DENTISTS (linked to a user account)
-- ============================================================
CREATE TABLE dentists (
  id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id       INT UNSIGNED NOT NULL,
  license       VARCHAR(50)  NOT NULL,
  specialty     VARCHAR(100) NOT NULL,
  phone         VARCHAR(20)  NULL,
  work_days     VARCHAR(50)  NOT NULL DEFAULT 'Mon,Tue,Wed,Thu,Fri',
  start_time    TIME         NOT NULL DEFAULT '08:00:00',
  end_time      TIME         NOT NULL DEFAULT '18:00:00',
  color         VARCHAR(7)   NOT NULL DEFAULT '#1D9E75',
  is_active     TINYINT(1)   NOT NULL DEFAULT 1,
  created_at    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_dentist_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- 3. INSURANCE (health insurance plans)
-- ============================================================
CREATE TABLE insurance (
  id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name        VARCHAR(100) NOT NULL,
  coverage    DECIMAL(5,2) NOT NULL DEFAULT 0.00 COMMENT 'coverage % (0-100)',
  is_active   TINYINT(1)   NOT NULL DEFAULT 1,
  created_at  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================================
-- 4. PATIENTS
-- ============================================================
CREATE TABLE patients (
  id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  first_name    VARCHAR(80)  NOT NULL,
  last_name     VARCHAR(80)  NOT NULL,
  email         VARCHAR(150) NULL,
  phone         VARCHAR(20)  NOT NULL,
  birth_date    DATE         NOT NULL,
  gender        ENUM('male','female','other') NOT NULL DEFAULT 'other',
  id_number     VARCHAR(20)  NULL COMMENT 'DNI / passport',
  address       VARCHAR(255) NULL,
  insurance_id  INT UNSIGNED NULL,
  notes         TEXT         NULL,
  is_active     TINYINT(1)   NOT NULL DEFAULT 1,
  created_at    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_patient_insurance FOREIGN KEY (insurance_id) REFERENCES insurance(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ============================================================
-- 5. PROCEDURES (catalog of dental treatments)
-- ============================================================
CREATE TABLE procedures (
  id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name        VARCHAR(150) NOT NULL,
  description TEXT         NULL,
  duration    INT          NOT NULL DEFAULT 60 COMMENT 'minutes',
  price       DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  category    VARCHAR(80)  NOT NULL DEFAULT 'General',
  color       VARCHAR(7)   NOT NULL DEFAULT '#378ADD',
  is_active   TINYINT(1)   NOT NULL DEFAULT 1,
  created_at  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================================
-- 6. APPOINTMENTS
-- ============================================================
CREATE TABLE appointments (
  id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  patient_id    INT UNSIGNED NOT NULL,
  dentist_id    INT UNSIGNED NOT NULL,
  procedure_id  INT UNSIGNED NOT NULL,
  date          DATE         NOT NULL,
  start_time    TIME         NOT NULL,
  end_time      TIME         NOT NULL,
  status        ENUM('scheduled','confirmed','completed','cancelled','no_show')
                NOT NULL DEFAULT 'scheduled',
  notes         TEXT         NULL,
  created_by    INT UNSIGNED NOT NULL COMMENT 'user who booked it',
  created_at    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_appt_patient   FOREIGN KEY (patient_id)   REFERENCES patients(id)   ON DELETE CASCADE,
  CONSTRAINT fk_appt_dentist   FOREIGN KEY (dentist_id)   REFERENCES dentists(id)   ON DELETE CASCADE,
  CONSTRAINT fk_appt_procedure FOREIGN KEY (procedure_id) REFERENCES procedures(id) ON DELETE CASCADE,
  CONSTRAINT fk_appt_user      FOREIGN KEY (created_by)   REFERENCES users(id)      ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- 7. ANAMNESIS (medical history per patient)
-- ============================================================
CREATE TABLE anamnesis (
  id                INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  patient_id        INT UNSIGNED NOT NULL,
  chief_complaint   TEXT         NOT NULL,
  medical_history   TEXT         NULL,
  current_meds      TEXT         NULL,
  smoker            TINYINT(1)   NOT NULL DEFAULT 0,
  pregnant          TINYINT(1)   NOT NULL DEFAULT 0,
  blood_type        VARCHAR(5)   NULL,
  notes             TEXT         NULL,
  created_by        INT UNSIGNED NOT NULL,
  created_at        TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at        TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_anamnesis_patient FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE,
  CONSTRAINT fk_anamnesis_user    FOREIGN KEY (created_by) REFERENCES users(id)    ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- 8. ALLERGIES (linked to an anamnesis)
-- ============================================================
CREATE TABLE allergies (
  id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  anamnesis_id  INT UNSIGNED NOT NULL,
  name          VARCHAR(100) NOT NULL,
  severity      ENUM('mild','moderate','severe') NOT NULL DEFAULT 'mild',
  created_at    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_allergy_anamnesis FOREIGN KEY (anamnesis_id) REFERENCES anamnesis(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- 9. MEDICAL CONDITIONS (linked to an anamnesis)
-- ============================================================
CREATE TABLE medical_conditions (
  id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  anamnesis_id  INT UNSIGNED NOT NULL,
  name          VARCHAR(100) NOT NULL,
  created_at    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_condition_anamnesis FOREIGN KEY (anamnesis_id) REFERENCES anamnesis(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- 10. CLINICAL RECORDS (treatment record per appointment)
-- ============================================================
CREATE TABLE clinical_records (
  id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  appointment_id  INT UNSIGNED NOT NULL,
  patient_id      INT UNSIGNED NOT NULL,
  dentist_id      INT UNSIGNED NOT NULL,
  procedure_id    INT UNSIGNED NOT NULL,
  teeth           VARCHAR(100) NULL COMMENT 'e.g. 16,17,18',
  description     TEXT         NOT NULL,
  observations    TEXT         NULL,
  duration        INT          NULL COMMENT 'actual minutes spent',
  created_at      TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at      TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_record_appt      FOREIGN KEY (appointment_id) REFERENCES appointments(id)  ON DELETE CASCADE,
  CONSTRAINT fk_record_patient   FOREIGN KEY (patient_id)     REFERENCES patients(id)      ON DELETE CASCADE,
  CONSTRAINT fk_record_dentist   FOREIGN KEY (dentist_id)     REFERENCES dentists(id)      ON DELETE CASCADE,
  CONSTRAINT fk_record_procedure FOREIGN KEY (procedure_id)   REFERENCES procedures(id)    ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- 11. TRANSACTIONS (payments)
-- ============================================================
CREATE TABLE transactions (
  id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  appointment_id  INT UNSIGNED NOT NULL,
  patient_id      INT UNSIGNED NOT NULL,
  amount          DECIMAL(10,2) NOT NULL,
  payment_method  ENUM('cash','credit_card','debit_card','transfer','insurance','other')
                  NOT NULL DEFAULT 'cash',
  status          ENUM('pending','paid','refunded') NOT NULL DEFAULT 'pending',
  paid_at         DATETIME     NULL,
  notes           TEXT         NULL,
  created_by      INT UNSIGNED NOT NULL,
  created_at      TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at      TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_trans_appt    FOREIGN KEY (appointment_id) REFERENCES appointments(id) ON DELETE CASCADE,
  CONSTRAINT fk_trans_patient FOREIGN KEY (patient_id)     REFERENCES patients(id)     ON DELETE CASCADE,
  CONSTRAINT fk_trans_user    FOREIGN KEY (created_by)     REFERENCES users(id)        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- 12. SETTINGS (clinic configuration)
-- ============================================================
CREATE TABLE settings (
  id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  key_name    VARCHAR(80)  NOT NULL UNIQUE,
  value       TEXT         NULL,
  updated_at  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================================
-- INDEXES (performance)
-- ============================================================
CREATE INDEX idx_appointments_date       ON appointments(date);
CREATE INDEX idx_appointments_dentist    ON appointments(dentist_id);
CREATE INDEX idx_appointments_patient    ON appointments(patient_id);
CREATE INDEX idx_appointments_status     ON appointments(status);
CREATE INDEX idx_patients_name           ON patients(last_name, first_name);
CREATE INDEX idx_transactions_date       ON transactions(created_at);
CREATE INDEX idx_transactions_status     ON transactions(status);
CREATE INDEX idx_clinical_records_patient ON clinical_records(patient_id);

-- ============================================================
-- SAMPLE DATA
-- ============================================================

-- Users (passwords are hashed: Admin1234!)
INSERT INTO users (name, email, password, role) VALUES
('Admin System',   'admin@dentalcare.com',    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
('Dr. John Smith', 'john@dentalcare.com',     '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'dentist'),
('Dr. Mary Jones', 'mary@dentalcare.com',     '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'dentist'),
('Dr. Carlos Ruiz','carlos@dentalcare.com',   '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'dentist'),
('Sara Reception', 'sara@dentalcare.com',     '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'receptionist');

-- Dentists
INSERT INTO dentists (user_id, license, specialty, phone, color) VALUES
(2, 'CRO-001234', 'Endodontics',    '555-1001', '#1D9E75'),
(3, 'CRO-005678', 'Orthodontics',   '555-1002', '#378ADD'),
(4, 'CRO-009012', 'Implantology',   '555-1003', '#D85A30');

-- Insurance plans
INSERT INTO insurance (name, coverage) VALUES
('No Insurance',   0.00),
('Rimac Dental',  70.00),
('Pacifico Plus',  60.00),
('Mapfre Dental',  50.00),
('La Positiva',    55.00);

-- Patients
INSERT INTO patients (first_name, last_name, email, phone, birth_date, gender, id_number, address, insurance_id) VALUES
('Ana',     'Paula',   'ana@email.com',     '555-4321', '1985-03-14', 'female', '12345678', 'Av. Flores 123, Lima', 2),
('Roberto', 'Mendes',  'roberto@email.com', '555-3210', '1978-07-21', 'male',   '23456789', 'Av. Paulista 456, Lima', 1),
('Juliana', 'Ferreira','juliana@email.com', '555-2109', '1992-11-07', 'female', '34567890', 'Calle Lima 789, Lima', 3),
('Pedro',   'Almeida', 'pedro@email.com',   '555-1098', '1988-05-29', 'male',   '45678901', 'Jr. Cusco 321, Lima', 1),
('Lucia',   'Torres',  'lucia@email.com',   '555-9876', '1995-08-15', 'female', '56789012', 'Av. Brasil 654, Lima', 4);

-- Procedures catalog
INSERT INTO procedures (name, description, duration, price, category, color) VALUES
('Cleaning & Prophylaxis', 'Complete dental cleaning and fluoride application', 60,  80.00, 'Prevention',    '#1D9E75'),
('Root Canal Treatment',   'Endodontic treatment single canal',                 90,  350.00,'Endodontics',   '#D85A30'),
('Dental Restoration',     'Composite resin restoration',                       45,  120.00,'Restorative',   '#378ADD'),
('Orthodontic Maintenance','Monthly orthodontic adjustment',                    30,  80.00, 'Orthodontics',  '#7F77DD'),
('Implant Consultation',   'Initial implant evaluation and X-ray',              60,  150.00,'Implantology',  '#EF9F27'),
('Tooth Extraction',       'Simple extraction under local anesthesia',          30,  100.00,'Surgery',       '#E24B4A'),
('Teeth Whitening',        'In-office whitening session',                       90,  200.00,'Aesthetics',    '#FAC775'),
('Dental X-Ray',           'Periapical or panoramic X-ray',                    15,  40.00, 'Diagnostics',   '#888780');

-- Appointments
INSERT INTO appointments (patient_id, dentist_id, procedure_id, date, start_time, end_time, status, created_by) VALUES
(1, 1, 2, CURDATE(), '09:00:00', '10:30:00', 'confirmed',  1),
(2, 1, 1, CURDATE(), '10:30:00', '11:30:00', 'confirmed',  1),
(3, 2, 4, CURDATE(), '09:00:00', '09:30:00', 'confirmed',  1),
(4, 3, 5, CURDATE(), '14:00:00', '15:00:00', 'scheduled',  1),
(1, 1, 2, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '09:00:00', '10:30:00', 'scheduled', 1),
(5, 2, 3, DATE_ADD(CURDATE(), INTERVAL 2 DAY), '11:00:00', '11:45:00', 'scheduled', 1);

-- Anamnesis
INSERT INTO anamnesis (patient_id, chief_complaint, medical_history, current_meds, blood_type, created_by) VALUES
(1, 'Pain in upper right tooth', 'Hypertension since 2018', 'Losartan 50mg daily', 'O+', 1),
(2, 'Routine cleaning visit',    'No relevant history',     'None',                 'A+', 1),
(3, 'Orthodontic follow-up',     'No relevant history',     'None',                 'B+', 1),
(4, 'Implant consultation',      'Type 2 Diabetes',         'Metformin 500mg',      'AB+',1);

-- Allergies
INSERT INTO allergies (anamnesis_id, name, severity) VALUES
(2, 'Penicillin',  'severe'),
(1, 'Ibuprofen',   'mild'),
(4, 'Latex',       'moderate');

-- Medical conditions
INSERT INTO medical_conditions (anamnesis_id, name) VALUES
(1, 'Hypertension'),
(4, 'Type 2 Diabetes');

-- Clinical records
INSERT INTO clinical_records (appointment_id, patient_id, dentist_id, procedure_id, teeth, description, duration) VALUES
(1, 1, 1, 2, '16', 'First root canal session. Coronal opening and chemo-mechanical preparation.', 90),
(2, 2, 1, 1, NULL, 'Complete cleaning and fluoride application. Good oral hygiene.', 45),
(3, 3, 2, 4, NULL, 'Monthly orthodontic adjustment. Lower arch wire changed.', 30);

-- Transactions
INSERT INTO transactions (appointment_id, patient_id, amount, payment_method, status, paid_at, created_by) VALUES
(1, 1, 350.00, 'credit_card', 'paid', NOW(), 1),
(2, 2, 80.00,  'cash',        'paid', NOW(), 1),
(3, 3, 80.00,  'insurance',   'paid', NOW(), 1);

-- Settings
INSERT INTO settings (key_name, value) VALUES
('clinic_name',     'DentalCare'),
('clinic_address',  'Av. Javier Prado 1234, San Isidro, Lima'),
('clinic_phone',    '(01) 234-5678'),
('clinic_email',    'contact@dentalcare.com'),
('clinic_tax_id',   '20123456789'),
('work_hours_week', '08:00-18:00'),
('work_hours_sat',  '08:00-13:00'),
('currency',        'S/'),
('timezone',        'America/Lima'),
('appointment_interval', '30');