<?php

// Підключення мови
require_once(__DIR__ . '/../../../config.php');

// Перевірка дозволу на виконання скрипту
defined('MOODLE_INTERNAL') || die();

/**
 * Встановлює плагін
 */
function xmldb_local_syllabus_report_install() {
    global $DB;

    // Запит на створення таблиці бази даних
    $sql = "
        CREATE TABLE {local_syllabus_report} (
            id SERIAL PRIMARY KEY,
            courseid INTEGER NOT NULL,
            content TEXT NOT NULL
        )
    ";

    // Виконання запиту
    $DB->execute($sql);

    // Повернення true, щоб показати, що встановлення успішно завершено
    return true;
}

// Виклик функції встановлення при завантаженні файлу
xmldb_local_syllabus_report_install();
