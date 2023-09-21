<?php require '../app/views/header.php'; ?>

<link rel="stylesheet" href="/NewsWebSitePhp/public/css/viewLog.css">

<div class="tab">
    <?php foreach ($logFiles as $index => $logFile): ?>
        <!-- Создание кнопок для каждого файла лога -->
        <button class="tablinks" onclick="openLog(event, 'log<?= $index ?>')"><?= $logFile['name'] ?></button>
    <?php endforeach; ?>
</div>

<?php foreach ($logFiles as $index => $logFile): ?>
    <div id="log<?= $index ?>" class="tabcontent">
        <!-- Отображение содержимого файла лога с подсветкой разных уровней логирования -->
        <pre><?php
            $logContentLines = explode("\n", $logFile['content']);
            foreach ($logContentLines as $line) {
                $level = 'unknown';
                // Использование регулярного выражения для поиска уровня логирования в строке
                if (preg_match('/\>(\s)*([A-Z]+)(\s)*\>/', $line, $matches)) {
                    $level = strtolower($matches[2]);
                }
                // Отображение строки лога с применением CSS класса, соответствующего уровню логирования
                echo "<div class='log-level-{$level}'>{$line}</div>";
            }
            ?></pre>
    </div>
<?php endforeach; ?>

<script>
    function openLog(evt, logName) {
        var i, tabcontent, tablinks;
        tabcontent = document.getElementsByClassName("tabcontent");
        for (i = 0; i < tabcontent.length; i++) {
            // Скрываем все элементы с классом "tabcontent"
            tabcontent[i].style.display = "none";
        }
        tablinks = document.getElementsByClassName("tablinks");
        for (i = 0; i < tablinks.length; i++) {
            // Удаляем класс "active" у всех кнопок
            tablinks[i].className = tablinks[i].className.replace(" active", "");
        }
        // Отображаем текущий элемент и добавляем класс "active" к текущей кнопке
        document.getElementById(logName).style.display = "block";
        evt.currentTarget.className += " active";
    }

    // Активируем первую кнопку при загрузке страницы
    document.getElementsByClassName("tablinks")[0].click();
</script>

<?php require '../app/views/footer.php'; ?>
