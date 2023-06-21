# Zilore DNS CLI
This is a command line interface for the <a href="https://zilore.com/?r=455e9e0de5cd86a9c371000077f6bb9f" target="_blank">Zilore DNS</a> service. 
It allows you to declaratively define your DNS records in a YAML file and then 
apply them to your Zilore account.
It ensures that the state of the domain, records, geo dns records and the failover records
matches what is in that YAML file and it removes what is not in the YAML file from Zilore.

## Installation
Using this requires php 8.2 (a Docker version is coming soon which will not require PHP).

Simply clone this git repo. Then run:

```
composer install
```

## Usage
To use this utility you need to :

  1. Get an API key from your Zilore account.
  2. Create a YAML file that describes your domain and records and place it in the ```domains``` folder.

Then run:
```
./zilore dns:ensure <API_KEY> domains/<filename>.yaml
```

Then sit back and watch the terminal. It will tell you what it is doing.

A sample example.com file is already available in the domains folder.

## License
MIT. See [LICENSE](LICENSE) for more details.


