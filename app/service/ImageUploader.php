<?php

class ImageUploader
{
    private $uploadDir = __DIR__ . '/../../public/uploads';

    // Функция для загрузки изображения по URL
    public function downloadImage($url)
    {
        $imageName = basename($url); // Получаем имя файла изображения из URL

        // Санитизируем имя файла, заменяя недопустимые символы
        $imageName = $this->sanitizeFileName($imageName);

        // Получаем расширение файла
        $extension = pathinfo($imageName, PATHINFO_EXTENSION);

        // Получаем имя файла без расширения
        $filename = pathinfo($imageName, PATHINFO_FILENAME);

        $index = 0; // Индекс для создания уникального имени файла
        do {
            // Формируем новое имя файла с индексом
            $newImageName = $index > 0 ? $filename . "-" . $index . "." . $extension : $imageName;

            // Формируем полный путь к файлу
            $imagePath = $this->uploadDir . DIRECTORY_SEPARATOR . $newImageName;

            $index++;
        } while (file_exists($imagePath)); // Повторяем до тех пор, пока файл с таким именем существует

        // Создаем контекст потока с отключенной верификацией SSL
        $contextOptions = [
            "ssl" => [
                "verify_peer" => false,
                "verify_peer_name" => false,
            ],
        ];
        $context = stream_context_create($contextOptions);

        // Загружаем данные изображения по URL с использованием созданного контекста потока
        $imageData = file_get_contents($url, false, $context);
        if ($imageData === false) {
            // Если загрузка данных не удалась, возвращаем null
            return null;
        }

        $success = file_put_contents($imagePath, $imageData); // Сохраняем данные изображения в файл
        if ($success === false) {
            // Если сохранение данных в файл не удалось, возвращаем null
            return null;
        }

        // Если все прошло успешно, возвращаем путь к сохраненному файлу изображения
        return $imagePath;
    }

    public static function deleteImageByName($imageName)
    {
        $uploadDir = __DIR__ . '/../../public/uploads';
        $imagePath = $uploadDir . DIRECTORY_SEPARATOR . $imageName;

        if ($imageName && file_exists($imagePath)) {
            unlink($imagePath);
        }
    }

    private function sanitizeFileName($filename) {
        // Заменяем недопустимые символы на подчеркивание
        return preg_replace('/[^a-zA-Z0-9\-\._]/', '_', $filename);
    }

}
