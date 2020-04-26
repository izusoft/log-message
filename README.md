# Laravel Logger

Класс для сохранения логов в произвольный файл. Умеет логировать строку, массив, объект
## Установка
в `composer.json` файл добавить код
```$xslt
"repositories": [
    {
        "type": "vcs",
        "url": "git@gitlab.com:cut_code/laravel-logger.git"
    }
],
"require": {
    "cut_code/laravel-logger": "^1.0"
},
```

##### Example
```$xslt
\LogMessage::debug($file, $message, $content);

$file - название файла
$message - сообщение: string|array|object|null
$content - доп сообщение: string|array|object|null
```