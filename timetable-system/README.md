# Schedule Time Table Management System

A comprehensive web-based timetable management system built with PHP, MySQL, HTML, CSS, and JavaScript. This system provides efficient scheduling capabilities with conflict detection, role-based access control, and multiple calendar views.

## Features

### Core Features
- **User Management**: Role-based authentication (Admin, Teacher, Student)
- **Timetable Creation**: Create, read, update, delete timetable events
- **Conflict Detection**: Real-time conflict checking for rooms, teachers, and classes
- **Calendar Views**: Daily, weekly, and monthly calendar views
- **Export Options**: PDF and CSV export functionality
- **Responsive Design**: Mobile-optimized interface using Bootstrap 5

### Advanced Features
- **Automated Scheduling**: Smart time slot recommendations
- **Recurring Events**: Support for daily, weekly, and monthly recurring schedules
- **Audit Trail**: Complete logging of all system activities
- **Admin Dashboard**: Comprehensive system overview and user management
- **Security**: CSRF protection, SQL injection prevention, secure session management

## Technology Stack

- **Backend**: PHP 8.0+
- **Database**: MySQL 8.0+
- **Frontend**: HTML5, CSS3, JavaScript, Bootstrap 5
- **Architecture**: MVC (Model-View-Controller) pattern
- **Security**: Password hashing, prepared statements, input validation

## Installation

### Prerequisites
- PHP 8.0 or higher
- MySQL 8.0 or higher
- Web server (Apache/Nginx)
- Composer (optional, for dependencies)

### Setup Instructions

1. **Clone or Download** the project files to your web server directory.

2. **Database Setup**:
   ```sql
   -- Import the database schema
   mysql -u root -p < database_schema.sql
   ```

3. **Configuration**:
   - Update `config/database.php` with your database credentials
   - Modify `config/constants.php` if needed for your environment
   - Set proper file permissions (755 for directories, 644 for files)

4. **Web Server Configuration**:
   - Ensure your web server can execute PHP files
   - Set the document root to the project directory
   - Enable URL rewriting if using Apache (optional)

5. **Access the System**:
   - Navigate to `http://your-domain/` in your browser
   - Use the demo credentials to login:
     - **Admin**: admin / password
     - **Teacher**: teacher1 / password
     - **Student**: student1 / password

## Project Structure

```
project-root/
├── config/
│   ├── database.php          # Database configuration
│   └── constants.php         # Application constants
├── models/
│   ├── User.php              # User model
│   └── TimetableEvent.php    # Timetable event model
├── assets/
│   ├── css/
│   │   └── dashboard.css    # Custom styles
│   ├── js/                  # JavaScript files
│   └── images/              # Image assets
├── includes/
│   └── functions.php        # Utility functions
├── admin/
│   ├── dashboard.php         # Admin dashboard page
│   └── users.php             # User management page
├── index.php               # Home page
├── login.php               # Login page
├── admin/dashboard.php           # Main dashboard
├── calendar.php            # Calendar views
├── export.php              # Export functionality
├── profile.php             # User profile page
└── database_schema.sql     # Database schema
```

## User Roles and Permissions

### Administrator
- Full system access
- User management (create, edit, delete users)
- System overview and statistics
- Conflict resolution
- Bulk operations

### Teacher
- Create and manage their own schedules
- View student schedules for their classes
- Export their timetables
- Access to calendar views

### Student
- View-only access to personal schedules
- Calendar views
- Export personal timetables

## Key Features Explained

### Conflict Detection
The system automatically detects conflicts when:
- A room is double-booked
- A teacher is scheduled for multiple events simultaneously
- A class has overlapping events

### Calendar Views
- **Daily View**: Detailed view of a single day's events
- **Weekly View**: Week overview with time slots
- **Monthly View**: Traditional calendar grid view

### Export Functionality
- **CSV Export**: For data analysis and external processing
- **PDF Export**: Print-friendly formatted reports

### Security Features
- Password hashing using PHP's `password_hash()`
- CSRF token validation
- SQL injection prevention with prepared statements
- XSS protection with input sanitization
- Secure session management

## Database Schema

The system uses a normalized database design with the following main tables:
- `users`: User accounts and authentication
- `timetable_events`: Main scheduling data
- `subjects`: Subject/course information
- `rooms`: Room and location data
- `classes`: Class/group information
- `time_slots`: Configurable time periods
- `time_conflicts`: Conflict tracking
- `audit_log`: Activity logging

## Customization

### Adding New Event Types
1. Update the `event_type` enum in the database schema
2. Add the new type to `config/constants.php`
3. Update the UI forms and displays in `timetable.php`

### Modifying Time Slots
Time slots are configurable through the `time_slots` table. You can add, modify, or remove time slots as needed via a database tool like phpMyAdmin.

### Styling
The system uses Bootstrap 5 with custom CSS in `assets/css/dashboard.css`. You can customize the appearance by modifying this file.

## Security Considerations

- Change default passwords immediately
- Use HTTPS in production
- Regularly update dependencies
- Monitor audit logs
- Implement proper backup procedures
- Use strong database passwords

## Performance Optimization

- Database indexes are included for optimal query performance.
- Pagination is implemented for large datasets.
- Efficient algorithms for conflict detection.
- Optimized CSS and JavaScript loading.

## Troubleshooting

### Common Issues

1. **Database Connection Error**:
   - Check database credentials in `config/database.php`
   - Ensure your MySQL service is running.
   - Verify the `timetable_management` database exists and was imported correctly.

2. **Permission Denied**:
   - Check file permissions (e.g., 755 for directories, 644 for files).
   - Ensure your web server has read access to the project files.

3. **Session Issues**:
   - Check your PHP session configuration.
   - Ensure the session save path is writable by the web server.

### Debug Mode
Enable debug mode by setting `ini_set('display_errors', 1);` in `config/constants.php` for development.

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly
5. Submit a pull request

## License

This project is open source and available under the MIT License.

## Version History

- **v1.0.0**: Initial release with core functionality
  - User management system
  - Timetable CRUD operations
  - Conflict detection
  - Calendar views
  - Export functionality
  - Admin dashboard


Designed for educational institutions and organizations that need efficient timetable management.
