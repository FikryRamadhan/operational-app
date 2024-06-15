# Operational-App
Project ini saya buat dengan menggunakan bahasa php dan framework Laravel.project ini dibuat ketika saya pkl di PT Adiva Sumber Solusi.kegunaan dari project ini adalah untuk mempermudah karyawan staff untuk mengelola keuangan dan juga stock yang terdapat di gudang perusahaan.

## Run Locally

Clone the project

```bash
  git clone https://github.com/FikryRamadhan/operational-app.git
```

Go to the project directory

```bash
  cd  operational-app
```

Copy file .env.example

```bash
  cp .env.example .env
```

Install dependencies

```bash
  composer install
```

Create key

```bash
  php artisan key:generate
```

Runing migration

```bash
  php artisan migrate
```

Start the server

```bash
  php artisan serve
