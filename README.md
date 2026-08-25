# 家計簿 (Kakeibo)

個人用の家計簿Webアプリケーションです。収入・支出をカテゴリ別に記録し、月ごとの収支を集計・確認できます。
学習・ポートフォリオ目的で、Laravel 12 + Docker環境を用いてゼロから設計・実装しました。

## 主な機能

- **収支記録**：日付・金額・種別（収入/支出）・カテゴリ・メモを登録し、一覧から編集・削除ができる
- **カテゴリ管理**：収入用・支出用のカテゴリを追加・編集・削除できる（例：食費、交通費、給与など）
- **一覧・絞り込み**：収支履歴を新しい日付順に一覧表示し、日付・カテゴリ・種別で絞り込みができる
- **月別集計**：選択した月の収入合計・支出合計・収支（差引）を表示し、月を切り替えて過去の集計も確認できる
- **認証機能**：Laravel Breezeによるログイン・ユーザー登録
- **データ分離**：Eloquentのグローバルスコープにより、他ユーザーのデータは一切参照・操作できない

## 技術構成

| カテゴリ | 使用技術 |
|---|---|
| バックエンド | PHP 8.2 / Laravel 12 |
| データベース | MySQL 8.0 |
| フロントエンド | Blade / Tailwind CSS / Alpine.js |
| 認証 | Laravel Breeze |
| 開発環境 | Docker / Docker Compose |
| コード整形 | Laravel Pint |
| テスト | PHPUnit |

## データベース構成

| テーブル | 役割 |
|---|---|
| `users` | ログインするユーザー |
| `categories` | ユーザーごとの収支カテゴリ（収入用/支出用を`type`で区別） |
| `transactions` | 収支記録（日付・金額・種別・カテゴリ・メモ） |

**リレーション**：`User` 1 - N `Category` 1 - N `Transaction`（`Transaction`は`User`にも直接紐づく）

## セットアップ手順（Docker）

### 前提

- Docker Desktop がインストール済みであること

### 手順

```bash
# 1. リポジトリをクローン
git clone https://github.com/asuka0120/kakeibo.git
cd kakeibo

# 2. 環境変数ファイルを作成
cp .env.example .env

# 3. コンテナを起動
docker compose up -d

# 4. 依存パッケージをインストール
docker compose exec app composer install
docker compose exec app npm install

# 5. アプリケーションキーを生成
docker compose exec app php artisan key:generate

# 6. マイグレーションを実行（テーブル作成）
docker compose exec app php artisan migrate

# 7. フロントエンド資材をビルド
docker compose exec app npm run build
```

起動後、以下のURLにアクセスできます。

- アプリ本体: http://localhost:8000
- phpMyAdmin（DB確認用）: http://localhost:8080

## テストの実行方法

```bash
# PHPUnitによる自動テスト
docker compose exec app php artisan test

# Laravel Pintによるコードスタイルチェック（差分確認のみ）
docker compose exec app ./vendor/bin/pint --test

# Laravel Pintによるコードスタイル自動整形
docker compose exec app ./vendor/bin/pint
```

## スクリーンショット

<!-- TODO: スクリーンショットを追加する -->
<!-- 例）ダッシュボード（月別集計） -->
<!-- 例）収支記録一覧 -->
<!-- 例）カテゴリ管理画面 -->

## ディレクトリ構成（主要部分のみ）

```
app/
  Http/
    Controllers/   … CategoryController, TransactionController, DashboardController など
    Requests/      … 入力バリデーション（Store/Update系 FormRequest）
  Models/          … Category, Transaction, User
  Policies/        … カテゴリ・収支記録の操作権限（自分のデータのみ許可）
  Listeners/       … ユーザー登録時の初期カテゴリ自動作成
database/
  migrations/      … categories, transactions テーブル定義
resources/views/
  categories/      … カテゴリ管理画面
  transactions/    … 収支記録画面
  dashboard.blade.php … 月別集計画面
docker/            … PHP/nginx/MySQLのDocker設定
```

## ライセンス

本プロジェクトは学習・ポートフォリオ目的で作成したものです。商用利用は想定していません。
