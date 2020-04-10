# blog

TODO
==============
- [ ] Древовидые категории
- [X] Админка (react admin)
- [X] Сртировка постов
- [X] Поиск
- [X] Автарка пользователю
- [X] Thumb изрбражений
- [X] Social oauth2
- [ ] uml auth


# Installation

```bash
$ git clone https://github.com/noi95/blog.git
```
```bash
$ cd blog/
$ composer install
```
```bash
$ yarn install && yarn encore production
```
```bash
# Change DATABASE_URL
```
```bash
$ php bin/console doctrine:database:create
$ php bin/console doctrine:migrations:migrate
$ php bin/console doctrine:fixtures:load
```

# Usage
```bash
$ symfony serve
```
    Admin account: admin@blog.com:admin
