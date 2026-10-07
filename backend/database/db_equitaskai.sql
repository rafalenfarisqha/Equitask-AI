-- =====================================================
-- DATABASE EQUITASK AI
-- PostgreSQL
-- =====================================================


-- =====================================================
-- 1. TABLE USERS
-- Menyimpan akun guru dan siswa
-- =====================================================

CREATE TABLE users (
    id BIGSERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,

    role VARCHAR(20) NOT NULL
        CHECK (role IN ('guru', 'siswa', 'admin')),

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


-- =====================================================
-- 2. TABLE STUDENT PROFILES
-- Menyimpan kebutuhan dan profil siswa
-- =====================================================

CREATE TABLE student_profiles (
    id BIGSERIAL PRIMARY KEY,

    user_id BIGINT NOT NULL UNIQUE,

    disability_type VARCHAR(100),
    support_needs TEXT,
    learning_notes TEXT,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_student_profile_user
        FOREIGN KEY (user_id)
        REFERENCES users(id)
        ON DELETE CASCADE
);


-- =====================================================
-- 3. TABLE CLASSES
-- Menyimpan kelas yang dibuat oleh guru
-- =====================================================

CREATE TABLE classes (
    id BIGSERIAL PRIMARY KEY,

    teacher_id BIGINT NOT NULL,

    name VARCHAR(100) NOT NULL,
    description TEXT,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_class_teacher
        FOREIGN KEY (teacher_id)
        REFERENCES users(id)
        ON DELETE CASCADE
);


-- =====================================================
-- 4. TABLE CLASS MEMBERS
-- Menghubungkan siswa dengan kelas
-- =====================================================

CREATE TABLE class_members (
    id BIGSERIAL PRIMARY KEY,

    class_id BIGINT NOT NULL,
    student_id BIGINT NOT NULL,

    joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_class_member_class
        FOREIGN KEY (class_id)
        REFERENCES classes(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_class_member_student
        FOREIGN KEY (student_id)
        REFERENCES users(id)
        ON DELETE CASCADE,

    CONSTRAINT unique_class_student
        UNIQUE (class_id, student_id)
);


-- =====================================================
-- 5. TABLE MATERIALS
-- Menyimpan materi yang diupload guru
-- =====================================================

CREATE TABLE materials (
    id BIGSERIAL PRIMARY KEY,

    teacher_id BIGINT NOT NULL,

    title VARCHAR(200) NOT NULL,
    description TEXT,

    file_name VARCHAR(255),
    file_path TEXT,
    file_type VARCHAR(50),

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_material_teacher
        FOREIGN KEY (teacher_id)
        REFERENCES users(id)
        ON DELETE CASCADE
);


-- =====================================================
-- 6. TABLE ASSESSMENTS
-- Menyimpan asesmen yang dibuat guru
-- =====================================================

CREATE TABLE assessments (
    id BIGSERIAL PRIMARY KEY,

    teacher_id BIGINT NOT NULL,
    material_id BIGINT,

    title VARCHAR(200) NOT NULL,
    description TEXT,

    target_bloom VARCHAR(10)
        CHECK (
            target_bloom IN
            ('C1', 'C2', 'C3', 'C4', 'C5', 'C6')
        ),

    assessment_type VARCHAR(20) NOT NULL
        CHECK (
            assessment_type IN
            ('reguler', 'adaptif')
        ),

    status VARCHAR(20) NOT NULL DEFAULT 'draft'
        CHECK (
            status IN
            ('draft', 'published', 'closed')
        ),

    duration_minutes INTEGER,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_assessment_teacher
        FOREIGN KEY (teacher_id)
        REFERENCES users(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_assessment_material
        FOREIGN KEY (material_id)
        REFERENCES materials(id)
        ON DELETE SET NULL
);


-- =====================================================
-- 7. TABLE QUESTIONS
-- Menyimpan bank soal
-- =====================================================

CREATE TABLE questions (
    id BIGSERIAL PRIMARY KEY,

    teacher_id BIGINT NOT NULL,
    material_id BIGINT,

    question_text TEXT NOT NULL,

    question_type VARCHAR(30) NOT NULL
        CHECK (
            question_type IN
            (
                'pilihan_ganda',
                'benar_salah',
                'isian',
                'essay'
            )
        ),

    options JSONB,

    correct_answer TEXT,

    bloom_level VARCHAR(10)
        CHECK (
            bloom_level IN
            ('C1', 'C2', 'C3', 'C4', 'C5', 'C6')
        ),

    version_type VARCHAR(20) NOT NULL
        CHECK (
            version_type IN
            ('reguler', 'adaptif')
        ),

    explanation TEXT,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_question_teacher
        FOREIGN KEY (teacher_id)
        REFERENCES users(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_question_material
        FOREIGN KEY (material_id)
        REFERENCES materials(id)
        ON DELETE SET NULL
);


-- =====================================================
-- 8. TABLE ASSESSMENT QUESTIONS
-- Menghubungkan soal dengan asesmen
-- =====================================================

CREATE TABLE assessment_questions (
    id BIGSERIAL PRIMARY KEY,

    assessment_id BIGINT NOT NULL,
    question_id BIGINT NOT NULL,

    question_order INTEGER NOT NULL,

    points NUMERIC(5,2) DEFAULT 1,

    CONSTRAINT fk_assessment_question_assessment
        FOREIGN KEY (assessment_id)
        REFERENCES assessments(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_assessment_question_question
        FOREIGN KEY (question_id)
        REFERENCES questions(id)
        ON DELETE CASCADE,

    CONSTRAINT unique_assessment_question
        UNIQUE (assessment_id, question_id)
);


-- =====================================================
-- 9. TABLE AI GENERATIONS
-- Mencatat proses generate asesmen menggunakan AI
-- =====================================================

CREATE TABLE ai_generations (
    id BIGSERIAL PRIMARY KEY,

    teacher_id BIGINT NOT NULL,
    material_id BIGINT,
    assessment_id BIGINT,

    target_bloom VARCHAR(10),

    status VARCHAR(20) NOT NULL DEFAULT 'processing'
        CHECK (
            status IN
            ('processing', 'success', 'failed')
        ),

    total_agents INTEGER DEFAULT 4,

    error_message TEXT,

    started_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    completed_at TIMESTAMP,

    CONSTRAINT fk_ai_generation_teacher
        FOREIGN KEY (teacher_id)
        REFERENCES users(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_ai_generation_material
        FOREIGN KEY (material_id)
        REFERENCES materials(id)
        ON DELETE SET NULL,

    CONSTRAINT fk_ai_generation_assessment
        FOREIGN KEY (assessment_id)
        REFERENCES assessments(id)
        ON DELETE SET NULL
);


-- =====================================================
-- 10. TABLE AI AGENT RESULTS
-- Menyimpan hasil dari masing-masing agent
-- =====================================================

CREATE TABLE ai_agent_results (
    id BIGSERIAL PRIMARY KEY,

    generation_id BIGINT NOT NULL,

    agent_name VARCHAR(100) NOT NULL,

    agent_order INTEGER NOT NULL,

    input_data JSONB,
    output_data JSONB,

    status VARCHAR(20) NOT NULL DEFAULT 'processing'
        CHECK (
            status IN
            ('processing', 'success', 'failed')
        ),

    error_message TEXT,

    started_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    completed_at TIMESTAMP,

    CONSTRAINT fk_agent_generation
        FOREIGN KEY (generation_id)
        REFERENCES ai_generations(id)
        ON DELETE CASCADE
);


-- =====================================================
-- 11. TABLE STUDENT ASSESSMENTS
-- Mencatat asesmen yang dikerjakan siswa
-- =====================================================

CREATE TABLE student_assessments (
    id BIGSERIAL PRIMARY KEY,

    assessment_id BIGINT NOT NULL,
    student_id BIGINT NOT NULL,

    status VARCHAR(20) NOT NULL DEFAULT 'not_started'
        CHECK (
            status IN
            ('not_started', 'in_progress', 'submitted')
        ),

    started_at TIMESTAMP,
    submitted_at TIMESTAMP,

    score NUMERIC(5,2),

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_student_assessment_assessment
        FOREIGN KEY (assessment_id)
        REFERENCES assessments(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_student_assessment_student
        FOREIGN KEY (student_id)
        REFERENCES users(id)
        ON DELETE CASCADE,

    CONSTRAINT unique_student_assessment
        UNIQUE (assessment_id, student_id)
);


-- =====================================================
-- 12. TABLE STUDENT ANSWERS
-- Menyimpan jawaban siswa
-- =====================================================

CREATE TABLE student_answers (
    id BIGSERIAL PRIMARY KEY,

    student_assessment_id BIGINT NOT NULL,
    question_id BIGINT NOT NULL,

    answer TEXT,

    is_correct BOOLEAN,

    points_obtained NUMERIC(5,2) DEFAULT 0,

    answered_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_student_answer_assessment
        FOREIGN KEY (student_assessment_id)
        REFERENCES student_assessments(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_student_answer_question
        FOREIGN KEY (question_id)
        REFERENCES questions(id)
        ON DELETE CASCADE,

    CONSTRAINT unique_student_question
        UNIQUE (student_assessment_id, question_id)
);


-- =====================================================
-- INDEX
-- Membantu pencarian data agar lebih efisien
-- =====================================================

CREATE INDEX idx_classes_teacher
    ON classes(teacher_id);

CREATE INDEX idx_class_members_class
    ON class_members(class_id);

CREATE INDEX idx_class_members_student
    ON class_members(student_id);

CREATE INDEX idx_materials_teacher
    ON materials(teacher_id);

CREATE INDEX idx_assessments_teacher
    ON assessments(teacher_id);

CREATE INDEX idx_assessments_material
    ON assessments(material_id);

CREATE INDEX idx_questions_material
    ON questions(material_id);

CREATE INDEX idx_assessment_questions_assessment
    ON assessment_questions(assessment_id);

CREATE INDEX idx_student_assessments_student
    ON student_assessments(student_id);

CREATE INDEX idx_student_answers_assessment
    ON student_answers(student_assessment_id);