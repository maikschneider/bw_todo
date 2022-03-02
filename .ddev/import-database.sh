#!/bin/bash

DB_LOADED=$(echo "select * from pages" | vendor/bin/typo3cms database:import)

if test -z "$DB_LOADED"
then
  echo 'loading db'
  gzip -dc /var/www/html/.ddev/db_minimal.sql.gz | mysql db
fi
