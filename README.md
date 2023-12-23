# Clean your Lando

## What is Lando ?

Lando is a Docker based developement environement.
Read more at [Lando.dev](https://lando.dev/)

## What it does

This script is cleaning your Lando installation from old unused images.
It is also perform lando destroy in all folder older than XX Days

## How to Set Up ?

1. Rename `conf-sample.php` to `conf.php`
2. DIR constant is the full path to your lando folder
3. KEEP_ALIVE is an array of folder you do not want to destroy

## How to use ?

`php clean-lando.php` command will launch it
Adding this line in your `crontab -e` (Linux) will run it at each reboot
`@reboot /usr/bin/php /path/to/clean-lando.php `
