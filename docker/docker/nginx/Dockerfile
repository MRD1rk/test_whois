# Легкий образ Nginx на Alpine
FROM nginx:stable-alpine

# Видаляємо стандартну конфіг
RUN rm /etc/nginx/conf.d/default.conf

# Копіюємо наш конфіг для сайту
COPY conf/conf.d/default.conf /etc/nginx/conf.d/

# Папка, де змонтується ваш код
WORKDIR /var/www

# Логи в стандартні місця всередині контейнера
# (їх ми вже пробросили на хост через volumes)

