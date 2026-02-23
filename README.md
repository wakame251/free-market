# 環境構築

---
## 1. アプリケーション名

### coachtechフリマ

## 2. Dockerビルド

### ①git clone git@github.com:wakame251/free-market.git

### ②cd free-market

### ③docker-compose up -d --build

## 3. Laravel環境構築

### ①docker-compose exec php bash

### ②composer install

### ③cp .env.example .env

_.envファイルは、11行目以降のDBの部分を以下の内容に変更_

DB_HOST=127.0.0.1   →   DB_HOST=mysql

DB_DATABASE=laravel   →   DB_DATABASE=laravel_db

DB_USERNAME=root   →   DB_USERNAME=laravel_user

DB_PASSWORD=   →   DB_PASSWORD=laravel_pass

### ④php artisan key:generate

### ⑤php artisan migrate

### ⑥php artisan db:seed

## 4. 開発環境

### ・商品一覧画面：http://localhost/

### ・会員登録画面：http://localhost/register

### ・phpMyAdmin：http://localhost:8080/

## 5. 使用技術（実行環境）

### ・PHP Version 8.1.34

### ・Laravel Framework 8.83.8

### ・mysql Ver 8.0.26

### ・nginx/1.21.1

## 6. ER図
<img width="1370" height="831" alt="free-market drawio" src="https://github.com/user-attachments/assets/10251a76-4597-49c3-961a-59bc6de49879" />

