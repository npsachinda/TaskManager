# Task Manager Application

A modern task management application built with Laravel, React, and Inertia.js.

## Tech Stack

- **Backend:** Laravel 12
- **Frontend:** React 18
- **Server-Side Rendering:** Inertia.js
- **Build Tool:** Vite
- **Styling:** Tailwind CSS
- **Database:** MySQL
- **Authentication:** Laravel Breeze

## Features

- User Authentication
- Task Management
- List Organization
- User Assignment
- Task Filtering & Search
- Responsive Design
- Real-time Updates

## Prerequisites

- PHP 8.2 or higher
- Node.js 16.0 or higher
- Composer
- MySQL
- Git

## Installation

1. **Clone the repository**
```bash
git clone <repository-url>
cd task-manager
```

2. **Install PHP dependencies**
```bash
composer install
```

3. **Install Node.js dependencies**
```bash
npm install
```

4. **Environment Setup**
```bash
cp .env.example .env
php artisan key:generate
```

5. **Configure your database in .env file**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=task_manager
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

6. **Run database migrations and seeders**
```bash
php artisan migrate
php artisan db:seed
```

## Development

1. **Start the Laravel development server**
```bash
php artisan serve
```

2. **Start the Vite development server**
```bash
npm run dev
```

## Building for Production

1. **Compile assets**
```bash
npm run build
```

2. **Optimize Laravel**
```bash
php artisan optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Project Structure

```
task-manager/
├── app/
│   ├── Http/Controllers/    # Controllers
│   ├── Models/             # Eloquent Models
│   ├── Repositories/       # Repository Pattern Implementation
│   └── Services/          # Business Logic Services
├── resources/
│   ├── js/                # React Components
│   │   ├── Components/    # Reusable Components
│   │   ├── Layouts/      # Page Layouts
│   │   └── Pages/        # Page Components
│   └── css/              # Stylesheets
└── routes/               # Application Routes
```

## Architecture

- **Repository Pattern**: Data access layer abstraction
- **Service Layer**: Business logic implementation
- **Inertia.js**: Server-side rendering with React
- **Component-Based**: Modular React components
- **Responsive Design**: Mobile-first approach with Tailwind CSS

## Available Scripts

- `npm run dev`: Start Vite development server
- `npm run build`: Build production assets
- `php artisan serve`: Start Laravel development server
- `php artisan test`: Run PHPUnit tests
- `php artisan db:seed`: Seed the database with sample data

## Testing

```bash
# Run PHP tests
php artisan test

# Run JavaScript tests
npm run test
```

## Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Acknowledgments

- [Laravel](https://laravel.com)
- [React](https://reactjs.org)
- [Inertia.js](https://inertiajs.com)
- [Tailwind CSS](https://tailwindcss.com)
- [Vite](https://vitejs.dev) 