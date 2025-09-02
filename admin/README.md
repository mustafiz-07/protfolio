# Admin Panel

This is the admin panel for the portfolio website. It provides a secure interface for managing projects and education records.

## Features

- **Secure Login System**: Username/password authentication with session management
- **Remember Me**: Optional cookie-based auto-login for 30 days
- **Session Management**: Automatic logout after 30 minutes of inactivity
- **Projects Management**: Add, edit, delete, and view projects
- **Education Management**: Manage education records and academic achievements
- **Responsive Design**: Works on desktop and mobile devices
- **Security**: Session-based authentication with proper logout

## Default Credentials

- **Username**: admin
- **Password**: admin123

## File Structure

```
admin/
├── login.php              # Login page with authentication
├── dashboard.php          # Main dashboard with navigation
├── logout.php             # Logout handler
├── admin-style.css        # Styles for admin panel
├── admin-script.js        # JavaScript functionality
└── sections/
    ├── dashboard.php      # Dashboard overview
    ├── projects.php       # Projects management
    └── education.php      # Education management
```

## Security Features

1. **Session Management**: PHP sessions for user authentication
2. **Auto-logout**: Sessions expire after 30 minutes of inactivity
3. **Remember Me**: Secure cookie-based authentication (optional)
4. **CSRF Protection**: Forms use POST method with proper validation
5. **Input Sanitization**: All user inputs are sanitized using htmlspecialchars()

## Usage

1. Navigate to `/admin/login.php`
2. Login with the default credentials
3. Use the sidebar to navigate between sections:
   - **Dashboard**: Overview and quick actions
   - **Projects**: Manage portfolio projects
   - **Education**: Manage education records

## Database Integration

Currently uses mock data. To integrate with a database:

1. Create MySQL tables for projects and education
2. Replace mock data arrays with database queries
3. Add proper CRUD operations in each section file
4. Implement user management with hashed passwords

## Customization

- Modify the CSS variables in `admin-style.css` to change colors
- Add new sections by creating files in the `sections/` directory
- Extend functionality by modifying the JavaScript in `admin-script.js`
