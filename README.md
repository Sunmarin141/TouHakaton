# AI Translate Platform
Multimedia Transcription & Translation (PHP + Docker + OpenAI)

Платформа для распознавания аудио/видео, обработки текстов, перевода на несколько языков и генерации кратких заголовков.  
Полностью работает внутри Docker, поддерживает авторизацию и историю обработанных файлов.

## Возможности

### Распознавание аудио (MP3)
- Whisper-1
- Возврат текста + перевод + заголовок

### Распознавание видео (MP4/WebM)
- ffmpeg извлекает аудио
- Автоматическая конвертация

### Обработка TXT
- Прямое чтение
- Перевод и заголовок

### Перевод на 3 языка
- Русский
- Казахский
- Английский

### Генерация заголовков
- Модель gpt-4o-mini
- Короткое название, max 2 слова

## Стек
- PHP 8 + PDO
- Docker + Docker Compose
- MySQL 8
- OpenAI API
- FFmpeg

## Структура проекта
main/
  api/
  controller/
  css/
  js/
  model/
  uploads/
  view/
  Dockerfile
  docker-compose.yml

## Установка

```
docker-compose up --build -d
```

## API

### POST /api/uploadFile.php
Загружает файл и возвращает JSON с данными.

## Авторизация
Реализована на PHP сессиях.

