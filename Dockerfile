FROM islamicnetwork/php:8.2-cli

COPY . /qutuz/

RUN cd /qutuz/ && composer install --no-dev

ENTRYPOINT ["php", "/qutuz/qutuz", "dns:ensure"]
