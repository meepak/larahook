# LaraHook

LaraHook is a self-hosted webhook.site alternative, designed to provide essential API delivery testing features with a focus on security and simplicity.

## Features

- Basic webhook.site alternative for testing API deliveries.
- Secure user authentication with Google 2FA.
- Each user gets a unique static link with private data access.
- Designed for self-hosting, with an option to restrict users to a specific email domain.
- No extra dependencies beyond the Laravel framework.

## Installation

### Prerequisites

Ensure you have the following installed:

- PHP 8.2+
- Laravel (latest stable version)
- Composer
- MySQL or PostgreSQL (for database storage)

### Setup Instructions

1. Clone the repository:
   ```sh
   git clone https://github.com/meepak/larahook.git
   cd larahook
   ```
2. Install dependencies:
    ```sh
    composer install
    ```
3. Configure environment:
    ```sh
    cp .env.example .env
    ```
    - Update .env with database and authentication configurations.
4. Generate application key:
    ```sh
    php artisan key:generate
    ```
5. Run migrations:
    ```sh
    php artisan migrate
    ```
6. Start the Laravel server:
    ```sh
    php artisan serve
    ```

### Authentication
    - LaraHook uses Google 2FA for user authentication.  The application can be configured to restrict user registration to specific email domains.

### Usage
    - Register/Sign in
    - Copy your unique static webhook URL.
    - Send API requests to your webhook URL.
    - View incoming requests and responses in real time.

### Deployment
LaraHook is designed for self-hosting. It can be deployed using:
    - Laravel Sail (for Docker-based environments)
    - Apache/Nginx with PHP-FPM
    - Laravel Vapor (for AWS-based serverless deployment)

### License
    - Opensource, do whatever you like

### Contributing
    - Pull requests are welcome. 

### Contact
    - For questions or support, open an issue on the repository