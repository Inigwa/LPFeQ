# LPFeQ

## Описание
Веб-приложение для занесения данных о ЛесоПожарныхФормированиях и технике

## Требования
PHP 8.1+  
Сomposer

### Установка
1. Склонируйте репозиторий: git clone 
2. Запуск сервера базы данных PostgresSQL (в текстовом документы scripttt SQL скрипты позволяющие создать базу данных или в database.sql есть готовая база даннх)
3. Запуск сервера Апач
4. Перенесите все файлы относящиеся к дериктории html в директорию var/www/html
  
# Использование 
1. В браузере пути localhost будет открыто веб приложение
2. Команда для запуска тестов vendor/bin/phpunit tests/

# Структура 
├── composer.json
├── composer.lock
├── database.sql
├── html
│   ├── dbconnection.php
│   ├── EquipmentReference.html
│   ├── EqulpmentLfpData.php
│   ├── index.html
│   ├── LfpReference.html
│   ├── LpfEquipmentData.php
│   ├── Manager.html
│   ├── ManagerRequests.php
│   └── Operator.html
├── tests
│   ├── DatabaseConnectionTest.php
│   ├── EquipmentDataTest.php
│   ├── EquipmentLfpDataTest.php
│   ├── LinkEquipmentTest.php
│   ├── LpfDataJsonTest.php
│   ├── LpfEquipmentDataTest.php
│   └── OperatorRequestsTest.php
├── vendor
│   ├── autoload.php
│   ├── bin
│   ├── composer
│   ├── myclabs
│   ├── nikic
│   ├── phar-io
│   ├── phpunit
│   ├── sebastian
│   └── theseer
└── скриптСкл
    └── scripttt


