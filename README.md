## База данных

### Данные для подключения

| Параметр     | Значение                |
| :----------- | :---------------------- |
| **Host**     | `zephyr.proxy.rlwy.net` |
| **Port**     | `57097`                 |
| **Database** | `railway`               |
| **User**     | `readonly_user`         |
| **Password** | `123`                   |

### Примеры подключения

**CLI:**

```bash
mysql -h zephyr.proxy.rlwy.net -u readonly_user -p --port 57097 --protocol=TCP railway
```

**URL:**

```
mysql://readonly_user:123@zephyr.proxy.rlwy.net:57097/railway
```

### Структура таблиц

- `sales` — Продажи
- `orders` — Заказы
- `stocs` — Склады
- `incomes` — Доходы

---

## Описание классов

- `App\Api` — Пространство имен для классов-клиентов, отвечающих за получение данных из API.
- `App\Importers` — Классы, отвечающие за сохранение данных в базу.
- `App\Console\Commands\ImportCommand` — Консольная команда, являющаяся входной точкой для запуска процесса.

---

## Настройки подключения API

Параметры для подключения задаются через .env

```bash
API_KEY=akalsjlkjf
API_HOST=127.0.0.1
```

---

## Пример использования

```bash
php artisan import {entity} {--startPage=1}
```

**Доступные сущности для импорта:**

```bash
# Импорт продаж
php artisan import sales

# Импорт заказов
php artisan import orders

# Импорт остатков на складах
php artisan import stocs

# Импорт доходов
php artisan import incomes
```

### Контакты

Telegram: https://t.me/ilya14747466
