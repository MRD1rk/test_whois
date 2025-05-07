# WHOIS Lookup Service

Невеликий REST-API та веб-інтерфейс для отримання WHOIS-інформації про домен.
Розгортається за допомогою Docker Compose (PHP-FPM + Nginx) та побудований на Laravel.

---

## 📂 Структура проекту

```
.
├── docker
│   ├── php
│   │   └── Dockerfile               # PHP-FPM образ з розширеннями й Composer
│   └── nginx
│       ├── Dockerfile               # Nginx на Alpine
│       └── default.conf             # Конфіг для Laravel (root → public)
├── config
│   └── whois.php                    # Ассоціативний масив TLD → WHOIS-сервер
├── docker-compose.yml               # Опис сервісів php та nginx
├── routes
│   ├── api.php                      # POST /api/whois
│   └── web.php                      # GET / → форма введення домену
├── app
│   ├── Http
│   │   ├── Controllers
│   │   │   └── WhoIsController.php  # Логіка lookup через fsockopen()
│   │   └── Requests
│   │       └── LookupWhoIsRequest.php  # Правила валідації domain
│   └── …                            # Стандартний каркас Laravel
├── resources
│   └── views
│       └── whois
│           └── form.blade.php       # HTML + JS форма + кнопка Download JSON
└── README.md                        # Опис проекту
```

---

## ⚙️ Налаштування середовища

1. **Клонуємо репозиторій** та переходимо в папку проекту:

   ```bash
   git clone git@github.com:USER/REPO.git
   cd REPO
   ```
2. **Запускаємо Docker-стек**:

   ```bash
   docker-compose up -d --build
   ```
3. Веб-інтерфейс доступний за адресою:

   ```
   http://localhost:8085
   ```

---

## 🚀 Як користуватися

### Веб-інтерфейс

1. Введіть домен у полі (наприклад, `example.com`).
2. Натисніть **Перевірити**.
3. Отримаєте “сиру” WHOIS-відповідь у блоці `<pre>`.
4. Натисніть кнопку **Download JSON** для збереження повного JSON-об’єкта.

### REST-API

* **Маршрут**: `POST /api/whois`
* **Тіло запиту** (JSON):

  ```json
  { "domain": "example.com" }
  ```
* **Успішна відповідь** (HTTP 200):

  ```json
  {
    "domain": "example.com",
    "server": "whois.verisign-grs.com",
    "whois": "…WHOIS-текст…"
  }
  ```
* **Помилки**:

  * **422 Validation Error** (неправильний формат domain)
  * **400 Bad Request** (TLD не підтримується)
  * **503 Service Unavailable** (не вдалося з’єднатися)

---

## 🔍 Реалізація

1. **Docker + Nginx**

   * PHP-FPM образ із базовими розширеннями (GD, PDO, mbstring, opcache, Xdebug).
   * Nginx на Alpine, який проксить запити `.php` на `php:9000` і віддає статику.

2. **Laravel**

   * **Контролер `WhoIsController`**:

     * `form()` → повертає view з формою.
     * `lookup()` → обробляє запит, валідує через `LookupWhoIsRequest`, визначає WHOIS-сервер за конфігом, відкриває сокет до порту 43, читає відповідь і віддає JSON.
   * **FormRequest `LookupWhoIsRequest`**:

     * Правило: `required|string|regex:/^[a-zA-Z0-9\.-]+\.[a-zA-Z]{2,}$/`.
     * При помилці валідації повертає JSON 422.
   * **Конфіг `config/whois.php`**:

     * Ассоціативний масив усіх TLD із відповідними WHOIS-серверами (з переліку Nir Sofer).
   * **Blade-шаблон**:

     * Форма для введення домену, JS-функція `fetch` → `/api/whois`.
     * Генерація `Blob`–файла та кнопка **Download JSON**.

3. **Обробка особливих випадків**

   * Автоматичний вибір серверів для SLD (наприклад `com.ua`, `org.ua` тощо) за двома елементами домену.
   * Виключаємо префікс `=` тільки для `ru`; для `ua` та інших надсилаємо простий `domain\r\n`.

---

## 🔧 Додаткові команди

* **Перезапуск Nginx** (щоб перечитати конфіг):

  ```bash
  docker-compose restart nginx
  ```

  або

  ```bash
  docker-compose exec nginx nginx -s reload
  ```
* **Створення контролера під своїм UID/GID**:

  ```bash
  docker-compose exec --user $(id -u):$(id -g) php php artisan make:controller WhoIsController
  ```

---

## 🤔 Далі

* Кешувати конфіг `whois.php` для підвищення швидкодії.
* Додати обробку DNS-cache та rate limiting.
* Розширити валідацію (IDN-доменів, нові TLD).
* Окремий PHPUnit-тест для контролера та FormRequest.

---

> **Автор:** команда розробки
> **Дата:** травень 2025
> **Ліцензія:** MIT

