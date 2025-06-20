# BitKode Marketing Management Platform

This repository contains a small PHP application used to manage marketing clients and their campaigns. It relies on a MySQL database and uses the pages inside the `public/` directory as the web root.

The production version of this application is published at <https://brand.bitkode.com>.

## Features

- **Authentication** – Users log in via `login.php` and can remain logged in with a "remember me" cookie. CSRF protection is handled by `config/csrf.php`.
- **User permissions** – Administrators manage roles and permissions from `users.php` and `config.php`.
- **Client management** – Add, edit and search clients (`clients.php` and `client_form.php`). Each client can have links and uploaded files.
- **Campaigns** – Create and track campaigns for each client using `client_campaigns.php` and `client_campaign_form.php`.
- **File uploads** – Marketing assets are stored under `public/uploads/clients/<id>` and managed in `client_upload.php`.
- **Dashboard** – `welcome.php` displays recent activity along with totals for clients and uploaded files.

## Setup

1. Ensure PHP and MySQL are available on your system.
2. Update the credentials in `config/db.php` with your database information.
3. From the repository root, start a development server:

   ```bash
   php -S localhost:8000 -t public
   ```

4. Visit `http://localhost:8000/login.php` to sign in.

The application uses prepared statements and includes basic CSRF token generation for improved security.

