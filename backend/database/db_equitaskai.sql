-- =========================================================
-- QUERY INSERT DATA DUMMY PRESISI UNTUK 16 TABEL db_equitaskai
-- =========================================================

-- 1. Tabel users
INSERT INTO public.users (id, name, email, nisn, role, password, first_name, last_name, phone, institution, avatar, created_at, updated_at) VALUES
(1, 'Budi Santoso', 'guru@equitaskai.com', NULL, 'teacher', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Budi', 'Santoso', '081234567890', 'SMA Negeri 1', 'avatar1.png', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
(2, 'Siti Rahma', 'siswa@equitaskai.com', '1234567890', 'student', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Siti', 'Rahma', '089876543210', 'SMA Negeri 1', 'avatar2.png', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);

-- 2. Tabel student_profiles
INSERT INTO public.student_profiles (id, user_id, disability_type, support_needs, learning_notes, created_at, updated_at) VALUES
(1, 2, 'Low Vision', 'Teks berukuran besar dan kontras tinggi', 'Siswa lebih mudah memahami materi visual', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);

-- 3. Tabel classes
INSERT INTO public.classes (id, teacher_id, name, description, created_at, updated_at) VALUES
(1, 1, 'Matematika Kelas X', 'Kelas Matematika Dasar Semester Ganjil', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);

-- 4. Tabel class_members
INSERT INTO public.class_members (id, class_id, student_id, joined_at) VALUES
(1, 1, 2, CURRENT_TIMESTAMP);

-- 5. Tabel materials
INSERT INTO public.materials (id, teacher_id, title, description, file_name, file_path, file_type, created_at, updated_at) VALUES
(1, 1, 'Modul Aljabar Dasar', 'Materi dasar aljabar', 'aljabar.pdf', '/storage/materials/aljabar.pdf', 'pdf', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);

-- 6. Tabel questions
INSERT INTO public.questions (id, teacher_id, material_id, question_text, question_type, options, correct_answer, bloom_level, version_type, explanation, created_at, updated_at) VALUES
(1, 1, 1, 'Berapakah hasil dari 2x + 4 = 10?', 'pilihan_ganda', '{"A": "2", "B": "3", "C": "4", "D": "5"}'::jsonb, 'B', 'C2', 'reguler', '2x = 6 maka x = 3', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);

-- 7. Tabel assessments
INSERT INTO public.assessments (id, user_id, nama, mapel, status, visibility, icon, color, created_at, updated_at) VALUES
(1, 1, 'Kuis Aljabar 1', 'Matematika', 'Published', 'public', 'eco', '#2563EB', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);

-- 8. Tabel assessment_questions
INSERT INTO public.assessment_questions (id, assessment_id, question_id, question_order, points) VALUES
(1, 1, 1, 1, 10.00);

-- 9. Tabel ai_generations
INSERT INTO public.ai_generations (id, teacher_id, material_id, assessment_id, target_bloom, status, total_agents, error_message, started_at, completed_at) VALUES
(1, 1, 1, 1, 'C3', 'success', 4, NULL, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);

-- 10. Tabel ai_agent_results
INSERT INTO public.ai_agent_results (id, generation_id, agent_name, agent_order, input_data, output_data, status, error_message, started_at, completed_at) VALUES
(1, 1, 'Bloom Analyzer Agent', 1, '{"input": "aljabar"}'::jsonb, '{"output": "C3 taxonomy validated"}'::jsonb, 'success', NULL, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);

-- 11. Tabel student_assessments
INSERT INTO public.student_assessments (id, assessment_id, student_id, status, started_at, submitted_at, score, created_at, updated_at) VALUES
(1, 1, 2, 'submitted', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 100.00, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);

-- 12. Tabel student_answers
INSERT INTO public.student_answers (id, student_assessment_id, question_id, answer, is_correct, points_obtained, answered_at) VALUES
(1, 1, 1, 'B', true, 10.00, CURRENT_TIMESTAMP);

-- 13. Tabel notifications
INSERT INTO public.notifications (id, user_id, title, message, type, is_read, created_at, updated_at) VALUES
(1, 2, 'Kuis Baru Ditambahkan', 'Guru menambahkan Kuis Aljabar 1', 'assessment', false, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);

-- 14. Tabel cache
INSERT INTO public.cache (key, value, expiration) VALUES
('laravel_cache_key', 'sample_value', 1800000000);

-- 15. Tabel cache_locks
INSERT INTO public.cache_locks (key, owner, expiration) VALUES
('laravel_lock_key', 'system', 1800000000);

-- 16. Tabel migrations
INSERT INTO public.migrations (id, migration, batch) VALUES
(1, '2024_01_01_000000_create_users_table', 1);

-- =========================================================
-- SYNCHRONIZE SEQUENCES (DARI FILE BACKUP ORIGINAL)
-- =========================================================
SELECT setval('public.users_id_seq', 2, true);
SELECT setval('public.classes_id_seq', 5, true);
SELECT setval('public.class_members_id_seq', 1, true);
SELECT setval('public.migrations_id_seq', 11, true);
SELECT setval('public.notifications_id_seq', 26, true);
SELECT setval('public.ai_agent_results_id_seq', 1, true);
SELECT setval('public.ai_generations_id_seq', 1, true);
SELECT setval('public.assessment_questions_id_seq', 1, true);
SELECT setval('public.assessments_id_seq', 1, true);
SELECT setval('public.materials_id_seq', 1, true);
SELECT setval('public.questions_id_seq', 1, true);
SELECT setval('public.student_answers_id_seq', 1, true);
SELECT setval('public.student_assessments_id_seq', 1, true);
SELECT setval('public.student_profiles_id_seq', 1, true);