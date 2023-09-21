<?php
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;
class LoggerService
{
    public static function getLogger($logFileName = 'parsers.log'): Logger
    {
        // Создаем новый форматтер с настраиваемым форматом даты и времени
        $dateFormat = "Y-m-d H:i:s";
        $output = "%datetime% > %level_name% > %message% %context% %extra%\n";
        $formatter = new LineFormatter($output, $dateFormat);

        // Создаем и настраиваем экземпляр логгера
        $logger = new Logger('BaseParser');
        $stream = new StreamHandler($logFileName, Logger::INFO);
        $stream->setFormatter($formatter);  // Применяем форматтер к обработчику
        $logger->pushHandler($stream);

        return $logger;
    }
}
