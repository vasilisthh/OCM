# User Management Application

A full-stack web application for managing users with a Laravel backend API and a Vue.js frontend.

## Features

- User management (Create, Read, Update, Delete)
- Search functionality
- Data fetching from external API
- Responsive UI with modern components
- Form validation
- Notifications and confirmations
- Pagination

## Prerequisites

- PHP >= 8.0
- Composer
- Node.js >= 14.0
- NPM or Yarn
- MySQL or SQLite
- Git

## Application Structure

The application is divided into two main parts:

- **Backend**: Laravel-based REST API
- **Frontend**: Vue.js single-page application

## Setup Instructions

### Backend Setup

1. **Navigate to the backend directory**:
   ```
   cd backend
   ```

2. **Install PHP dependencies**:
   ```
   composer install
   ```

3. **Set up the environment file**:
   ```
   cp .env.example .env
   ```

4. **Generate application key**:
   ```
   php artisan key:generate
   ```

5. **Configure the database**:
   
   Edit the `.env` file and set your database credentials:
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=ocm
   DB_USERNAME=root
   DB_PASSWORD=0000
   ```

   Alternatively, for SQLite:
   ```
   DB_CONNECTION=sqlite
   # Comment out or remove the other DB_* settings
   ```
   
   For SQLite, create an empty database file:
   ```
   touch database/database.sqlite
   ```

6. **Run migrations**:
   ```
   php artisan migrate
   ```

7. **Start the Laravel development server**:
   ```
   php artisan serve
   ```
   
   The backend API will be available at http://localhost:8000/api

### Frontend Setup

1. **Navigate to the frontend directory**:
   ```
   cd frontend
   ```

2. **Install NPM dependencies**:
   ```
   npm install
   ```

3. **Set up environment variables**:
   
   Create a `.env` file in the frontend directory:
   ```
   VITE_API_URL=http://localhost:8000/api
   ```

4. **Start the development server**:
   ```
   npm run dev
   ```
   
   The frontend application will be available at http://localhost:5173

## Using the Application

1. After starting both the backend and frontend servers, open your browser and navigate to http://localhost:5173
2. You can:
   - View the list of users
   - Search for users by name, username, or email
   - Add new users using the "Add User" button
   - Edit existing users by clicking the "Edit" button
   - Delete users with the "Delete" button (confirmation required)
   - Fetch data from an external API with the "Fetch From External API" button

## Troubleshooting

- **CORS Issues**: If you experience CORS issues, make sure your backend is properly configured to allow requests from the frontend origin.
- **Database Connection**: Ensure your database server is running and credentials are correct.
- **API Connection**: Check that the backend URL in the frontend environment file is correct.

## Additional Notes

- The backend API follows RESTful conventions
- The frontend is built with Vue.js 3 and TypeScript
- The application uses custom-built UI components for modals, notifications, and confirmations 