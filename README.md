# プロジェクトについて

---

## アプリケーション名

### coachtechフリマ

## 概要

### アイテムの出品と購入をするためのフリマアプリを開発

## 主な機能

- 会員登録 / ログイン機能（Laravel Fortify）
- 商品一覧表示
- 商品検索（部分一致）
- 商品詳細表示
- 商品出品機能
- 商品購入機能（Stripe Checkout）
- いいね機能（トグル）
- コメント機能
- マイページ（購入一覧 / 出品一覧）
- プロフィール編集（画像アップロード対応）

# 環境構築

---

## Dockerビルド

### ①git clone git@github.com:wakame251/free-market.git

### ②cd free-market

### ③docker-compose up -d --build

## Laravel環境構築

### ①docker-compose exec php bash

### ②composer install

### ③cp .env.example .env

_.envのDB設定を以下に変更_

DB_HOST=mysql

DB_DATABASE=laravel_db

DB_USERNAME=laravel_user

DB_PASSWORD=laravel_pass

### ④ php artisan storage:link

### ⑤ php artisan migrate

### ⑥ php artisan db:seed

### ⑦ php artisan test

## 開発環境

### ・商品一覧画面（トップページ）：http://localhost/

### ・会員登録画面：http://localhost/register

### ・ログイン画面:http://localhost/login

### ・phpMyAdmin：http://localhost:8080/

# テストに使用したアカウント

---

## 一般ユーザー①

### ユーザー名：一般ユーザー①

### メールアドレス：test@userone.com

### パスワード：userone0000

## 一般ユーザー②

### ユーザー名：一般ユーザー②

### メールアドレス：test@usertwo.com

### パスワード：usertwo0000

# 使用技術（実行環境）

---

## ・PHP Version 8.1.34

## ・Laravel Framework 8.83.8

## ・mysql Ver 8.0.26

## ・nginx/1.21.1

## ・mailhog

# ER図

---

<img width="1600" height="801" alt="free-market drawio" src="https://github.com/user-attachments/assets/82890d8a-1a0b-4bed-a66e-04e0d77556f0" />
