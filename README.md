# Social Network

A modern, responsive social network built with PHP, MySQL, and vanilla JavaScript.

## Features

- **User Authentication**: Sign up, login, and logout functionality
- **User Profiles**: Customizable profiles with profile pictures and bio
- **Posts**: Create, view, like, and comment on posts
- **Image Uploads**: Support for profile pictures and post images
- **Responsive Design**: Works on desktop and mobile devices
- **Real-time Interactions**: Like and comment without page refresh

## Installation

### 🐳 Docker Installation (Recommended)

**Prerequisites:**
- Docker
- Docker Compose

**Quick Start:**
```bash
# Clone the repository
git clone https://github.com/GARVIT-PALIWAL/Social_Network.git
cd Social_Network

# Run setup script
# On Windows:
docker-setup.bat

# On Linux/Mac:
chmod +x docker-setup.sh
./docker-setup.sh

# Start the application
docker-compose up -d
```

**Access the application:**
- **Social Network**: http://localhost:8080
- **phpMyAdmin**: http://localhost:8081

**Stop the application:**
```bash
docker-compose down
```

### 🖥️ Manual Installation

**Prerequisites:**
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web server (Apache/Nginx)

**Setup Instructions:**

1. **Clone/Download** the project to your web server directory

2. **Database Setup**:
   - Create a MySQL database
   - Import the `database.sql` file to create tables and sample data
   - Update database credentials in `classes/Database.php` if needed

3. **Configure Database**:
   Edit `classes/Database.php` and update these values:
   ```php
   private $host = 'localhost';
   private $db_name = 'social_network';
   private $username = 'your_username';
   private $password = 'your_password';
   ```

4. **Set Permissions**:
   Make sure the `uploads/` directory is writable:
   ```bash
   chmod 755 uploads/
   chmod 755 uploads/profiles/
   chmod 755 uploads/posts/
   ```

5. **Access the Application**:
   - Open your web browser
   - Navigate to your project directory
   - Start with `login.php` or `signup.php`

## File Structure

```
social_network/
├── classes/                 # PHP classes
│   ├── Database.php        # Database connection
│   ├── User.php           # User management
│   └── Post.php           # Post management
├── ajax/                   # AJAX handlers
│   ├── signup_process.php
│   ├── login_process.php
│   ├── logout_process.php
│   ├── create_post.php
│   ├── like_post.php
│   ├── add_comment.php
│   ├── get_posts.php
│   ├── get_comments.php
│   ├── update_profile.php
│   └── delete_post.php
├── uploads/                # File uploads
│   ├── profiles/          # Profile pictures
│   └── posts/             # Post images
├── css/
│   └── style.css          # Main stylesheet
├── js/
│   ├── auth.js            # Authentication scripts
│   ├── main.js            # Main functionality
│   └── profile.js         # Profile page scripts
├── index.php              # Home page (feed)
├── login.php              # Login page
├── signup.php             # Registration page
├── profile.php            # User profile page
├── database.sql           # Database schema
└── README.md              # This file
```

## Usage

### For Users

1. **Sign Up**: Create a new account with username, email, and password
2. **Login**: Access your account with email and password
3. **Create Posts**: Share text and images with the community
4. **Interact**: Like and comment on posts
5. **Manage Profile**: Update your profile information and picture

### For Developers

The application follows MVC-like architecture:

- **Models**: PHP classes in `classes/` directory
- **Views**: HTML pages with embedded PHP
- **Controllers**: AJAX handlers in `ajax/` directory

## Security Features

- Password hashing using PHP's `password_hash()`
- SQL injection prevention with prepared statements
- File upload validation
- Session management
- Input sanitization

## Customization

### Styling
- Edit `css/style.css` to modify the appearance
- The design is responsive and mobile-friendly

### Functionality
- Add new features by creating additional AJAX handlers
- Extend the database schema as needed
- Modify the PHP classes for additional functionality

## Troubleshooting

### Common Issues

1. **Database Connection Error**:
   - Check database credentials in `classes/Database.php`
   - Ensure MySQL server is running
   - Verify database exists and is accessible

2. **File Upload Issues**:
   - Check directory permissions for `uploads/` folder
   - Verify PHP upload settings in `php.ini`

3. **Session Issues**:
   - Ensure sessions are enabled in PHP
   - Check session storage permissions

## Sample Data

The database includes sample users and posts for testing:
- Username: `john_doe`, Email: `john@example.com`
- Username: `jane_smith`, Email: `jane@example.com`
- Password for both: `password` (hashed in database)

## License

This project is open source and available under the MIT License.

## Support

For issues or questions, please check the code comments or create an issue in the project repository.
