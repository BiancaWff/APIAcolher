#!/bin/bash

 echo "Iniciando instalação..."
 composer install
 cp .env.example .env

 read -p "Altere os valores do banco de dados do arquivo .env e tecle <enter>"

 php artisan key:generate
 php artisan migrate:fresh --seed
 npm install --save-dev vite
 npm update
 npm run dev &
 php artisan serve