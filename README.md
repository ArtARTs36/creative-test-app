### Installation

----

- Установка PHP зависимостей: `composer install`
- Обновление схемы БД: `php bin/console orm:schema-tool:update --force`
- Импорт данных: `php bin/console fetch:trailers`

### Run Via Docker

----

`sh docker-project-install.sh`

---

### О проведенной работе

----

По сути я получил уже жирный скелетон, в котором 60% от задания реализовано. Делал мелкие правки.
Со слимом не работал ни разу, напоминает смесь Java Servlet + Symfony.

Сделал модель User и собственно авторизацию. Адекватных пакетов для авторизации не находилось, написал свой маленький драйвер через сессию.

Сделал модель Like для возможности поставить/убрать лайк на трейлер.

---

### Страницы

---
Приложение имеет следующие страницы:

/ - Главная <br/>
/movies/{id} - Отдельная страница трейлера <br/>
/signup - Регистрация пользователя <br/>
/signin - Авторизация пользователя <br/>

---

### FetchDataCommand

Увидел что xml-элемент, в котором постер - заблочен. Вытащил путь до картинки регуляркой.

Наверняка, есть удобные xml-парсеры, которые еще бы разбирали encoded-элементы, но не хотелось засорять приложение кучей зависимостей.

По пункту "предложите другую схему получения rss": да вполне себе обычный file_get_contents() справится с задачей получения rss.

---

### Валидация сущностей

В Symfony для орм-ок есть хорошая аннотация Constraint. Вполне можно было подключить их в этот проект. Помимо набора имеющихся constraint'ов, можно расширить своими классами-constraint'ами.

---
