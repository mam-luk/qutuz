[![](https://img.shields.io/github/license/mam-luk/qutuz.svg)](https://github.com/mam-luk/qutuz/blob/master/LICENSE)

<p align="center"><img src=".mamluk/logo-horizontal.svg" alt="Kipchak by Mamluk" title="Kipchak by Mamluk - an API Toolkit" width="377"/>
</p>

# Qutuz by Mamluk

### Zilore Sunsetting the DNS Service

_**This utility was originally designed to work with Zilore DNS, who are unsetting their DNS service on July 14, 2024. Whilst there is a plan for us to support other GeoDNS providers with the same declarative YAML, there is currently no due date for this. Please keep this in mind when using this utility.**_

### A Declarative DNS Management Utility for Zilore GeoDNS

This is a command line interface for the <a href="https://zilore.com/" target="_blank">Zilore DNS</a> service. 
It allows you to declaratively define your GeoDNS records in a YAML file and then 
apply them to your Zilore account.
It ensures that the state of the domain, records, geodns records and the failover records
matches what is in the YAML file, and it removes what is not in the YAML file from Zilore.

### A little detour in history - who was Sultan Qutuz?

Sultan Saif al-Din Qutuz (https://en.wikipedia.org/wiki/Qutuz) was the the third Sultan of the Mamluk Sultanate of Cairo. It was during his reign that the historic battle of Ayn Jalut (https://en.wikipedia.org/wiki/Battle_of_Ain_Jalut) took place outside Jerusalem in 1260 which stopped the movement of the Mongols westward. Sultan Qutuz ruled from 1259 to 1260.

## Installation
Using this requires php 8.2 (a Docker version is coming soon which will not require PHP).

Simply clone this git repo. Then run:

```
composer install
```

## Usage

To use this utility you need PHP 8.2 installed and you must:

1. Get an API key from your Zilore account.
2. Create a YAML file that describes your domain and records and place it in the ```domains``` folder.

Then:

### With Docker

The docker container is available at ```ghcr.io/mam-luk/qutuz```.

Run:

```
docker run -v $(pwd)/domains:/qutuz/domains ghcr.io/mam-luk/qutuz <API_KEY> qutuz/domains/<filename>.yml
```

### Without Docker

Make sure you have PHP 8.2 installed and run:
```
./qutuz dns:ensure <API_KEY> domains/<filename>.yaml
```

Then sit back and watch the terminal. It will tell you what it is doing.

## License

MIT. See [LICENSE](LICENSE) for more details.

### Credits

* symfony/console
* symfony/yaml
* This utility has been built for Mamluk (https://mamluk.net), 7x (https://7x.ax) and Islamic Network (https://islamic.network)



