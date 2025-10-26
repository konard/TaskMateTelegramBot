# Воркеры уведомлений TaskMate

## Обзор

Система воркеров обеспечивает постоянный мониторинг задач и своевременные уведомления пользователей и менеджеров.

## Запущенные воркеры

### 1. ✅ Отправка запланированных задач
- **Job**: `SendScheduledTasksJob`
- **Расписание**: каждые 5 минут
- **Функция**: Отправляет задачи пользователям когда наступает `appear_date`

### 2. ✅ Проверка просроченных задач
- **Job**: `CheckOverdueTasksJob`
- **Расписание**: каждые 10 минут
- **Функция**: Находит просроченные задачи и уведомляет менеджеров

### 3. ✅ Проверка приближающихся дедлайнов
- **Job**: `CheckUpcomingDeadlinesJob`
- **Расписание**: каждые 15 минут
- **Функция**: Напоминает о дедлайнах за 1, 2 и 4 часа

### 4. ✅ Проверка задач без ответов
- **Job**: `CheckUnrespondedTasksJob`
- **Расписание**: каждые 30 минут
- **Функция**: Напоминает о задачах без ответа через 2, 6 и 24 часа

### 5. ✅ Обработка повторяющихся задач
- **Job**: `ProcessRecurringTasksJob`
- **Расписание**: каждые 30 минут
- **Функция**: Создает новые экземпляры повторяющихся задач

### 6. ✅ Ежедневная сводка для менеджеров
- **Job**: `SendDailySummaryJob`
- **Расписание**: ежедневно в 20:00
- **Функция**: Отправляет сводку по задачам за день

### 7. ✅ Еженедельные отчеты
- **Job**: `SendWeeklyReportJob`
- **Расписание**: каждый понедельник в 09:00
- **Функция**: Отправляет еженедельные отчеты

### 8. ✅ Архивация старых задач
- **Job**: `ArchiveOldTasksJob`
- **Расписание**: ежедневно в 02:00
- **Функция**: Архивирует старые выполненные задачи

## Запуск воркеров

### Вариант 1: Supervisor (рекомендуется)

Создайте конфигурацию `/etc/supervisor/conf.d/taskmate-workers.conf`:

```ini
[program:taskmate-workers]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/your/project/artisan queue:work --queue=notifications --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/path/to/your/project/storage/logs/workers.log
stopwaitsecs=3600
```

Запуск:
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start taskmate-workers:*
```

### Вариант 2: Systemd

Создайте сервис `/etc/systemd/system/taskmate-workers.service`:

```ini
[Unit]
Description=TaskMate Queue Workers
After=network.target

[Service]
Type=simple
User=www-data
Group=www-data
Restart=always
ExecStart=/usr/bin/php /path/to/your/project/artisan queue:work --queue=notifications --sleep=3 --tries=3 --max-time=3600
WorkingDirectory=/path/to/your/project
StandardOutput=syslog
StandardError=syslog
SyslogIdentifier=taskmate-workers

[Install]
WantedBy=multi-user.target
```

Запуск:
```bash
sudo systemctl daemon-reload
sudo systemctl enable taskmate-workers
sudo systemctl start taskmate-workers
```

### Вариант 3: Запуск вручную (для разработки)

```bash
# Запуск воркеров
php artisan queue:work --queue=notifications --sleep=3 --tries=3

# Или в фоне
nohup php artisan queue:work --queue=notifications --sleep=3 --tries=3 > storage/logs/workers.log 2>&1 &
```

## Тестирование воркеров

### Тестирование отдельных воркеров:

```bash
# Тест всех воркеров
php artisan workers:test

# Тест проверки просроченных задач
php artisan workers:test overdue

# Тест приближающихся дедлайнов
php artisan workers:test upcoming

# Тест задач без ответов
php artisan workers:test unresponded

# Тест отправки запланированных задач
php artisan workers:test scheduled

# Тест повторяющихся задач
php artisan workers:test recurring
```

### Просмотр логов:

```bash
# Логи воркеров
tail -f storage/logs/laravel.log

# Поиск специфических записей
grep "CheckOverdueTasksJob" storage/logs/laravel.log
grep "CheckUpcomingDeadlinesJob" storage/logs/laravel.log
grep "CheckUnrespondedTasksJob" storage/logs/laravel.log
```

## Мониторинг

### Проверка статуса очереди:

```bash
# Количество задач в очереди
php artisan queue:monitor notifications

# Очистка зависших задач
php artisan queue:clear notifications

# Перезапуск неудачных задач
php artisan queue:retry all
```

### Supervisor команды:

```bash
# Статус воркеров
sudo supervisorctl status taskmate-workers:*

# Перезапуск воркеров
sudo supervisorctl restart taskmate-workers:*

# Просмотр логов
sudo supervisorctl tail taskmate-workers
```

## Частота работы

| Воркер | Частота | Назначение |
|--------|--------|------------|
| `SendScheduledTasksJob` | 5 минут | Мгновенная отправка задач |
| `CheckOverdueTasksJob` | 10 минут | Быстрое обнаружение просрочек |
| `CheckUpcomingDeadlinesJob` | 15 минут | Своевременные напоминания |
| `CheckUnrespondedTasksJob` | 30 минут | Напоминания о задачах без ответа |
| `ProcessRecurringTasksJob` | 30 минут | Обработка повторяющихся задач |
| `SendDailySummaryJob` | 20:00 ежедневно | Ежедневные отчеты |
| `SendWeeklyReportJob` | Пн 09:00 | Еженедельные отчеты |
| `ArchiveOldTasksJob` | 02:00 ежедневно | Архивация старых задач |

## Уведомления

### Типы уведомлений:

1. **Пользовательские уведомления**:
   - Новые задачи
   - Напоминания о дедлайнах (1ч, 2ч, 4ч до)
   - Напоминания о задачах без ответа (2ч, 6ч, 24ч)
   - Просроченные задачи (для менеджеров)

2. **Менеджерские уведомления**:
   - Просроченные задачи
   - Задачи без ответа (>6ч, >24ч)
   - Приближающиеся важные дедлайны (1ч, 2ч)
   - Ежедневные/еженедельные отчеты

### Форматы сообщений:

Все сообщения используют Markdown форматирование и включают:
- 📌 Заголовок задачи
- 📝 Описание (если есть)
- ⏰ Дедлайн (если есть)
- 👤 Исполнители (для менеджеров)
- 🎯 Интерактивные кнопки (OK, Выполнено, Перенести)

## Отладка

### Включение отладки:

```bash
# Включение детального логирования
php artisan config:clear
grep -i "workers\|jobs\|queue" storage/logs/laravel.log

# Тестирование воркеров с отладкой
php artisan workers:test upcoming --verbose
```

### Распространенные проблемы:

1. **Воркеры не запускаются**:
   - Проверьте права доступа к файлам
   - Убедитесь что queue driver настроен правильно
   - Проверьте наличие очереди `notifications`

2. **Уведомления не приходят**:
   - Проверьте `telegram_id` у пользователей
   - Убедитесь что бот имеет права отправки сообщений
   - Проверьте логи на наличие ошибок

3. **Воркеры зависают**:
   - Перезапустите воркеры: `php artisan queue:restart`
   - Очистите зависшие задачи: `php artisan queue:clear`