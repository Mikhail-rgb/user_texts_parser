# Развертка

```
docker compose up -d
```

# Вызов скриптов

- Для того, чтобы для каждого пользователя посчитать среднее 
количество строк в его текстовых файлах, нужно выполнить команду:

```
docker exec -it php-app php main.php semicolon countAverageLineCount
```

- Для того, чтобы заменить формат даты в файлах, нужно выполнить команду:

```
docker exec -it php-app php main.php semicolon replaceDates
```