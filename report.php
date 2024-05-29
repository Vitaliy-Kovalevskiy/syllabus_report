<?php
require_once('../../config.php'); // Підключення до файлу конфігурації Moodle

// Отримання всіх курсів
$courses = $DB->get_records('course');

// Отримання всіх категорій курсів
$categories = $DB->get_records_menu('course_categories', null, '', 'id, name');

// Заголовок сторінки
$PAGE->set_pagelayout('standard');
$PAGE->set_title('Силабуси');
$PAGE->set_heading('KSU-Online');

// Виведення заголовка сторінки
echo $OUTPUT->header();

// Виведення заголовка таблиці та поля введення для пошуку
echo "<div class='container'>";
echo "<h2>Перелік силабусів</h2>";

// Додавання поля введення для пошуку
echo "<div class='search-container'>";
echo "<input type='text' id='searchInput' onkeyup='searchTable()' placeholder='Пошук за назвою курсу'>";
echo "</div>";

echo "<table class='table'>";
echo "<thead><tr><th>№</th><th>ID курсу</th><th>Повне ім'я курсу</th><th>Категорія</th><th>Посилання</th></tr></thead>";
echo "<tbody>";

// Цикл по всім курсам
$rowNum = 1;
foreach ($courses as $course) {
    // Отримання ресурсів курсу
    $resources = $DB->get_records('resource', array('course' => $course->id));

    // Перевірка наявності слова "Силабус" у назві кожного ресурсу
    $contains_syllabus = false;

    foreach ($resources as $resource) {
        if (stripos($resource->name, 'Силабус') !== false) {
            $contains_syllabus = true;
            // Виведення результатів у табличному форматі
            echo "<tr>";
            // Порядковий номер рядка
            echo "<td>{$rowNum}</td>";
            // ID курсу
            echo "<td>{$course->id}</td>";
            // Повне ім'я курсу
            echo "<td>{$course->fullname}</td>";
            // Категорія
            $category_name = isset($categories[$course->category]) ? $categories[$course->category] : 'Не визначено';
            echo "<td>{$category_name}</td>";
            // Посилання на курс
            $course_url = new moodle_url('/course/view.php', array('id' => $course->id));
            echo "<td>" . html_writer::link($course_url, 'Посилання') . "</td>";
            echo "</tr>";
            $rowNum++;
            break; // Виходимо з циклу, якщо слово знайдено в одному ресурсі курсу
        }
    }

    // Якщо не знайдено жодного силабусу на курсі, пропускаємо його
    if (!$contains_syllabus) {
        // Пропустити цикл, не додавати цей курс до списку
        continue;
    }
}

// Завершення таблиці
echo "</tbody></table>";

// Кнопки пагінації з CSS-стилями
echo "<div class='pagination' style='display: flex; justify-content: center; margin-top: 20px;'>";
echo "<button onclick='prevPage()'>&lt;&lt;</button>"; // Кнопка "<<" для попередньої сторінки
echo "<button onclick='nextPage()'>&gt;&gt;</button>"; // Кнопка ">>" для наступної сторінки
echo "</div>";

echo "</div>";

// Виведення підвалу сторінки
echo $OUTPUT->footer();
?>

<!-- JavaScript для пагінації та пошуку -->
<script>
    var currentPage = 1;
    var rowsPerPage = 10; // Кількість рядків
    var tableRows = document.querySelectorAll(".table tbody tr");

    function showRows() {
        var startIndex = (currentPage - 1) * rowsPerPage;
        var endIndex = startIndex + rowsPerPage;
        for (var i = 0; i < tableRows.length; i++) {
            if (i >= startIndex && i < endIndex) {
                tableRows[i].style.display = "table-row";
            } else {
                tableRows[i].style.display = "none";
            }
        }
    }

    function prevPage() {
        if (currentPage > 1) {
            currentPage--;
            showRows();
        }
    }

    function nextPage() {
        if (currentPage < Math.ceil(tableRows.length / rowsPerPage)) {
            currentPage++;
            showRows();
        }
    }

    function searchTable() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("searchInput");
        filter = input.value.toUpperCase();
        table = document.querySelector(".table");
        tr = table.getElementsByTagName("tr");

        // Показувати стандартну кількість рядків при порожньому полі пошуку
        if (filter === "") {
            showRows();
            return;
        }

        for (i = 0; i < tr.length; i++) {
            // Пошук за назвою курсу та категорією
            tdName = tr[i].getElementsByTagName("td")[2]; // Індекс колонки з ім'ям курсу
            tdCategory = tr[i].getElementsByTagName("td")[3]; // Індекс колонки з категорією
            if (tdName || tdCategory) {
                txtValueName = tdName.textContent || tdName.innerText;
                txtValueCategory = tdCategory.textContent || tdCategory.innerText;
                if (txtValueName.toUpperCase().indexOf(filter) > -1 || txtValueCategory.toUpperCase().indexOf(filter) > -1) {
                    tr[i].style.display = "";
                } else {
                    tr[i].style.display = "none";
                }
            }
        }
    }

    showRows(); // Відображення першої сторінки після завантаження сторінки
</script>
