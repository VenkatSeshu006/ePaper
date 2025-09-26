# ePaper - Digital Newspaper Management System

A comprehensive web-based digital newspaper (ePaper) management system built with PHP and MySQL. This system enables publishing, viewing, and managing digital newspaper editions with advanced features like image clipping, area mapping, analytics, and social sharing.

## 🌟 Features

### 📰 Core Functionality
- **Digital Edition Management**: Create, update, and organize newspaper editions by categories
- **Multi-Image Support**: Upload and manage multiple pages per edition with ordering
- **Category Management**: Organize editions into customizable categories
- **Responsive Design**: Mobile-friendly interface for all devices

### 🎨 Advanced Features
- **Image Area Mapping**: Interactive hotspot creation on newspaper images
- **Image Clipping Tool**: Crop and save specific sections from newspaper pages
- **Logo Integration**: Add branding logos to clipped images
- **Social Sharing**: Share clips on Facebook, Twitter, WhatsApp, LinkedIn, Telegram
- **Print & Download**: Print or download clipped content
- **Date Picker Navigation**: Navigate between editions by date

### 📊 Analytics & Monitoring
- **Comprehensive Analytics**: Track page views, unique visitors, and user engagement
- **Daily Statistics**: Aggregate daily analytics data for reporting
- **Real-time Monitoring**: Track user activity and popular content
- **Performance Metrics**: Monitor edition performance and category engagement

### ⚙️ Administrative Features
- **Admin Dashboard**: Complete backend management interface
- **Storage Management**: Monitor and manage file storage usage (5GB default limit)
- **User Management**: Handle user accounts and permissions
- **Settings Configuration**: Customize site appearance and behavior
- **Color Schema**: Dynamic color theme management
- **Menu Management**: Create and manage site navigation

## 🚀 Technology Stack

- **Backend**: PHP 7.4+
- **Database**: MySQL 8.0+ / MariaDB 10.4+
- **Frontend**: HTML5, CSS3, JavaScript (ES6+)
- **CSS Framework**: Bootstrap 5.3.3
- **Icons**: Font Awesome 6.0
- **Image Processing**: PHP GD Library
- **Additional Libraries**:
  - Cropper.js for image cropping
  - jQuery UI for date picker
  - AdminLTE for admin interface

## 📋 System Requirements

- **Web Server**: Apache 2.4+ or Nginx 1.18+
- **PHP**: 7.4 or higher
- **Database**: MySQL 8.0+ or MariaDB 10.4+
- **PHP Extensions**:
  - PDO MySQL
  - GD Library
  - JSON
  - Session
  - File Upload

## 🔧 Installation

### 1. Clone/Download the Project
```bash
git clone <repository-url>
# or download and extract ZIP file
```

### 2. Set Up Web Server
Place the project in your web server's document root:
- **XAMPP**: `C:\xampp\htdocs\ePaper`
- **WAMP**: `C:\wamp64\www\ePaper`
- **Linux**: `/var/www/html/ePaper`

### 3. Database Setup
1. Create a MySQL database named `saas`
2. Import the database schema:
```sql
mysql -u username -p saas < saas_backup_20250925_141800.sql
```

### 4. Configuration
Edit `config.php` with your database credentials:
```php
<?php
$host = 'localhost';       // Database host
$db = 'saas';             // Database name
$user = 'your_username';   // Database username
$pass = 'your_password';   // Database password
define('BASE_URL', 'http://your-domain.com/');
```

### 5. Directory Permissions
Ensure write permissions for upload directories:
```bash
chmod 755 uploads/
chmod 755 uploads/categories/
chmod 755 uploads/editions/
chmod 755 uploads/clips/
chmod 755 uploads/settings/
chmod 755 uploads/areamaps/
```

### 6. Access the Application
- **Public Site**: `http://localhost/ePaper/public/`
- **Admin Panel**: `http://localhost/ePaper/admin/`

## 📁 Project Structure

```
ePaper/
├── config.php                 # Database configuration
├── index.php                  # Root redirect to public
├── info.php                   # PHP info (development only)
├── saas_backup.sql           # Database backup/schema
│
├── admin/                     # Administrative interface
│   ├── index.php            # Admin dashboard
│   ├── logout.php           # Logout functionality
│   ├── assets/              # Admin CSS/JS assets
│   ├── controllers/         # Backend controllers
│   │   ├── AnalyticsController.php
│   │   ├── CategoryController.php
│   │   ├── ClipController.php
│   │   ├── EditionController.php
│   │   ├── PageController.php
│   │   └── HomepageSettingsController.php
│   ├── includes/            # Admin templates
│   └── views/               # Admin pages
│
├── public/                    # Public-facing interface
│   ├── index.php            # Homepage (redirects based on settings)
│   ├── categories.php       # Category listing page
│   ├── edition.php          # Edition viewer with clipping tools
│   ├── clips.php            # Individual clip viewer
│   ├── save_clip.php        # Clip processing endpoint
│   ├── load_public_areas.php # Area mapping loader
│   ├── assets/              # Public CSS/JS assets
│   └── includes/            # Public templates
│
└── uploads/                   # File storage
    ├── categories/          # Category images
    ├── editions/            # Edition page images
    ├── clips/               # User-generated clips
    ├── areamaps/           # Area mapping coordinates
    └── settings/           # System assets (logos, etc.)
```

## 🗄️ Database Schema

### Core Tables
- **`editions`**: Newspaper edition metadata
- **`edition_images`**: Individual page images for editions
- **`categories`**: Edition categories/sections
- **`clipped_images`**: User-generated image clips

### Feature Tables
- **`area_mappings`**: Interactive hotspot definitions
- **`analytics`**: Raw analytics events
- **`analytics_daily`**: Aggregated daily statistics
- **`settings`**: System configuration
- **`color_schema`**: Theme customization
- **`users`**: User account management

### Supporting Tables
- **`pages`**: Static page content
- **`menus`** & **`menu_items`**: Navigation structure

## 💡 Usage Guide

### For Administrators

#### 1. Adding Categories
1. Access Admin Panel → Categories
2. Click "Add Category"
3. Provide name, alias, description, and upload category image
4. Save changes

#### 2. Creating Editions
1. Go to Admin Panel → Editions
2. Click "Add Edition"
3. Fill in title, select category, set edition date
4. Upload newspaper page images
5. Arrange page order as needed
6. Publish edition

#### 3. Managing Clips
1. Navigate to Admin Panel → User Clips
2. View all user-generated clips
3. Delete individual or bulk clips
4. Set automatic cleanup rules

#### 4. Analytics Dashboard
1. Access Admin Panel → Analytics
2. View traffic statistics and trends
3. Monitor popular editions and categories
4. Export reports for analysis

### For End Users

#### 1. Browsing Editions
1. Visit the public site
2. Browse categories or use date picker
3. Click on edition to view full pages
4. Navigate between pages using controls

#### 2. Creating Clips
1. Open any edition page
2. Use the clipping tool to select area
3. Adjust crop boundaries as needed
4. Add logo if desired (configured by admin)
5. Save clip and get shareable URL

#### 3. Sharing Content
1. Create or view existing clip
2. Use social sharing buttons
3. Copy direct link for sharing
4. Print or download for offline use

## 🎨 Customization

### Theme Customization
Access Admin Panel → Settings → Color Schema to modify:
- Primary and secondary colors
- Header and footer styling  
- Text and link colors
- Button appearances

### Homepage Configuration
Configure homepage display in Admin Panel → Settings:
- Show all categories (default)
- Redirect to specific edition
- Custom landing page content

### Logo and Branding
Upload custom logos in Admin Panel → Settings:
- Site header logo
- Clip watermark logo
- Favicon and mobile icons

## 🔧 API Endpoints

### Public Endpoints
- `GET /public/edition.php?id={id}` - View edition
- `GET /public/clips.php?id={id}` - View clip
- `POST /public/save_clip.php` - Create new clip
- `GET /public/load_public_areas.php` - Load area mappings

### Analytics Tracking
- Automatic page view tracking
- Edition engagement metrics
- User session monitoring
- Click-through rate analysis

## 🚦 Performance Optimization

### Storage Management
- 5GB default storage limit (configurable)
- Automatic cleanup for old clips
- Image compression for web delivery
- CDN integration ready

### Caching Strategy
- Static asset caching with version control
- Database query optimization
- Gzip compression for text content
- Browser caching headers

## 🔒 Security Features

- SQL injection prevention with PDO prepared statements
- XSS protection through input sanitization
- CSRF protection for admin forms
- File upload validation and type checking
- Session management and timeout handling

## 🐛 Troubleshooting

### Common Issues

#### Database Connection Errors
- Verify credentials in `config.php`
- Check MySQL service status
- Ensure database exists and is accessible

#### File Upload Issues
- Check directory permissions (755)
- Verify PHP upload limits in `php.ini`
- Ensure sufficient disk space

#### Image Processing Problems
- Confirm GD library is installed
- Check memory limits for large images
- Verify supported image formats

### Log Files
- PHP error logs: Check server error logs
- Application logs: Custom logging in controllers
- Analytics data: Raw events in `analytics` table

## 🤝 Contributing

1. Fork the repository
2. Create feature branch (`git checkout -b feature/new-feature`)
3. Commit changes (`git commit -am 'Add new feature'`)
4. Push to branch (`git push origin feature/new-feature`)
5. Create Pull Request

### Development Guidelines
- Follow PSR-4 autoloading standards
- Use prepared statements for all database queries
- Implement proper error handling and logging
- Add comments for complex business logic
- Test across different browsers and devices

## 📄 License

This project is licensed under the MIT License - see the LICENSE file for details.

## 📞 Support

For technical support or questions:
- Create an issue in the repository
- Check documentation and troubleshooting guide
- Contact the development team

## 🚀 Roadmap

### Planned Features
- [ ] REST API for mobile app integration
- [ ] Multi-language support (i18n)
- [ ] Advanced user roles and permissions
- [ ] Email newsletter integration
- [ ] Advanced analytics and reporting
- [ ] Content scheduling and automation
- [ ] SEO optimization tools
- [ ] Progressive Web App (PWA) support

### Performance Improvements
- [ ] Redis caching implementation
- [ ] Image optimization pipeline
- [ ] Database indexing optimization
- [ ] CDN integration
- [ ] Load balancing support

---

**Version**: 1.0.0  
**Last Updated**: September 2025  
**Developed by**: [Your Organization Name]