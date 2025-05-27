# UltraGranit

**UltraGranit** — веб-застосунок для перегляду та адміністрування товарів з граніту.  
Система дозволяє користувачам переглядати товари, залишати відгуки, оформлювати замовлення тощо.  
Адміністраторам — керувати товарами, отримувати сповіщення в Telegram, бачити курси валют тощо.

---

## Технології

- **PHP 8+**
- **MySQL**
- **HTML/CSS/JavaScript (Vanilla)**
- **MVC-архітектура**
- **Telegram Bot API**
- **ПриватБанк API**
- та інші бібліотеки/інтеграції

---

## Запуск локально

1. Клонуй репозиторій:
   ```bash
   git clone https://github.com/OleksandrKoneschuk/UltraGranit.git
   ```

2. Скопіюй проєкт у директорію локального веб-сервера:
   - Для XAMPP: `htdocs/UltraGranit`
   - Для OpenServer: `domains/UltraGranit`

3. Імпортуй базу даних.

4. Налаштуй підключення до БД у файлі:
   ```
   MVC/config/db.php
   ```

5. Відкрий у браузері:
   ```
   http://localhost/UltraGranit/
   ```

---

## API (частково)

| Метод | Шлях                    | Опис                                     |
|-------|-------------------------|------------------------------------------|
| GET   | `/product/load-reviews` | Завантаження відгуків                    |
| POST  | `/product/add-review`   | Додавання нового відгуку                 |
| GET   | `/product/search-ajax`  | Пошук товарів та категорій               |
| GET   | `/product/load-more`    | Пагінація товарів                        |
| GET   | `/api/currency`         | Поточний курс USD з ПриватБанку         |
| POST  | `/order/create`         | Оформлення замовлення (та інше)         |

> Документація API — у [swagger.yaml](./swagger.yaml)
> (використовується Swagger UI для візуалізації)
> Для запуску Swagger UI, скопіюйте `swagger.yaml` у кореневу директорію проєкту та відкрийте у браузері:


---

## Документація

- Технічна документація: [DOCS.md](./DOCS.md)
- Конфіденційність: [PRIVACY_POLICY.md](./PRIVACY_POLICY.md)
- EULA: [EULA.md](./EULA.md) 
- Ліцензії: [license-report.md](./license-check-report.md)

---

## Конфіденційність та GDPR

- Проєкт враховує вимоги захисту персональних даних (GDPR).
- Cookie popup реалізований.
- Політика конфіденційності: [PRIVACY_POLICY.md](./PRIVACY_POLICY.md)

---

## Команди

| Команда                 | Опис                  |
|------------------------|------------------------|
| `php -S localhost:8000`| Локальний запуск PHP   |


---

## Автор

**Oleksandr Koneschuk**  
_Студент спеціальності "Інженерія програмного забезпечення"_  
GitHub: [@OleksandrKoneschuk](https://github.com/OleksandrKoneschuk)

---

## Ліцензія

Проєкт поширюється відповідно до [EULA (End User License Agreement)](./EULA.md)
