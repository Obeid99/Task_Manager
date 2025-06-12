# JEMS Task Manager

A comprehensive task management application built with Symfony 7, featuring user authentication, profile management, task CRUD operations, and REST API endpoints. This project demonstrates modern web development practices with a professional, industrial design and beautiful gradient UI.

![Login Screen](https://github.com/user-attachments/assets/login-screen.png)
*Beautiful gradient login interface with remember me functionality*

## 🚀 Features Overview

### 🔐 Authentication & Authorization System
- **Secure Login/Logout**: Beautiful gradient login form with remember me
- **Role-Based Access Control**: Admin vs Regular User permissions
- **Route Protection**: Automatic redirects for unauthorized access
- **Session Management**: Secure session handling with CSRF protection

![Task Management](https://github.com/user-attachments/assets/task-management.png)
*Clean task management interface with status tracking and assignment*

### 📋 Task Management System
- **Complete CRUD Operations**: Create, Read, Update, Delete tasks
- **Task Assignment**: Assign tasks to users by name and email
- **Status Tracking**: Mark tasks as completed or in progress
- **Due Date Management**: Set and track task deadlines
- **User-Based Filtering**: Users see only their tasks (except admins)
- **Admin Oversight**: Admins can view, edit, and delete all tasks

### 👥 User Management & Profiles
![User Management](https://github.com/user-attachments/assets/user-management.png)
*Advanced user management with search, role management, and profile completion tracking*

![User Profile](https://github.com/user-attachments/assets/user-profile.png)
*Comprehensive user profiles with education, work experience, and CV upload*

### 👤 Advanced User Profiles & Management
- **Multi-Section Profiles**: Personal info, work experience, education, skills
- **Profile Completion Tracking**: Visual progress indicators (75%, 50%, etc.)
- **Role Management**: User/Admin role assignment with visual badges
- **Search & Filter**: Advanced user search by name, email, username, job title
- **User Actions**: View profile, edit permissions, manage status

#### Profile Sections Include:
- **Personal Information**: Name, job title, LinkedIn profile, contact details
- **Education History**: Multiple university entries with degrees and specializations
- **Work Experience**: Professional background with companies and positions
- **Skills Management**: Technical, soft skills, languages, certifications with proficiency levels
- **CV/Resume Upload**: PDF file upload with drag-and-drop interface
- **Profile Completion**: Visual progress tracking for profile completeness

### 🔒 Authorization & Permission System
![Task Edit](https://github.com/user-attachments/assets/task-edit.png)
*Task editing interface with assignee management and validation*

#### Role-Based Permissions:
- **Regular Users**:
  - View and manage only their own tasks
  - Edit their own profile and upload CV
  - Cannot access admin functions or other users' data

- **Admin Users** (admin@jems.com):
  - Full access to all tasks (view, edit, delete)
  - User management capabilities
  - Can assign tasks to any user
  - Access to user search and management interface
  - Can view all user profiles and completion status

#### Security Features:
- **Route Protection**: `/task/*`, `/profile/*` require authentication
- **Admin Routes**: `/admin/*` require ROLE_ADMIN
- **CSRF Protection**: All forms protected against cross-site request forgery
- **Password Hashing**: Secure bcrypt password hashing
- **Session Security**: Secure session management with remember me option

### 🎨 UI/UX Features
- **Modern Gradient Design**: Beautiful orange-to-pink gradients throughout
- **Responsive Layout**: Mobile-first design with Bootstrap 5
- **Sidebar Navigation**: Collapsible sidebar with user info and role badges
- **Interactive Elements**: Hover effects, smooth transitions, modern buttons
- **Professional Color Scheme**: Industrial design with consistent branding
- **Accessibility**: Proper form labels, semantic HTML, keyboard navigation

### 🌐 REST API Endpoints
- **User Management API**: Create, list, and manage users
- **Task Management API**: Full CRUD operations via REST
- **JSON Responses**: Structured API responses with error handling
- **Postman Collection**: Ready-to-use API testing collection
- **API Test Interface**: Built-in HTML test page for quick API testing

## 🛠 Technology Stack

- **Backend**: Symfony 7 (PHP 8.2+)
- **Database**: MySQL 8.0 with Doctrine ORM
- **Frontend**: Twig templates with Bootstrap 5
- **Authentication**: Symfony Security Component with custom authenticator
- **File Handling**: Symfony File Upload Component with validation
- **API**: REST endpoints with JSON responses
- **Development**: Docker & Docker Compose
- **Database Management**: phpMyAdmin interface
- **UI Framework**: Bootstrap 5 with custom gradient styling
- **Icons**: Font Awesome for consistent iconography

## 📁 Project Structure

```
task_manager/
├── src/
│   ├── Controller/           # Application controllers
│   │   ├── Api/             # REST API controllers
│   │   ├── TaskController.php
│   │   ├── UserProfileController.php
│   │   ├── ProfileManagementController.php
│   │   └── RegistrationController.php
│   ├── Entity/              # Doctrine entities
│   │   ├── User.php         # User entity with relationships
│   │   ├── Task.php         # Task entity
│   │   ├── Education.php    # Education records
│   │   ├── WorkExperience.php
│   │   ├── Skill.php
│   │   └── CvUpload.php
│   ├── Form/                # Symfony forms
│   ├── Repository/          # Database repositories
│   └── Security/            # Authentication logic
├── templates/               # Twig templates
│   ├── task/               # Task management views
│   ├── user_profile/       # Profile display
│   ├── profile_management/ # Profile editing
│   ├── registration/       # User registration
│   └── security/           # Login/logout
├── config/                 # Symfony configuration
├── migrations/             # Database migrations
├── postman/               # API testing collection
├── public/uploads/        # File upload directory
└── docker-compose.yml     # Docker configuration
```

## 🔧 Installation & Setup

### Prerequisites
- Docker & Docker Compose
- PHP 8.2+ (for local development)
- Composer

### Quick Start

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd task_manager
   ```

2. **Start Docker services**
   ```bash
   docker-compose up -d
   ```
   This starts MySQL database and phpMyAdmin in the background.

3. **Install dependencies**
   ```bash
   composer install
   ```

4. **Configure environment**
   ```bash
   # Check your .env file contains:
   DATABASE_URL="mysql://symfony:secret@127.0.0.1:3306/symfony"
   ```

5. **Set up database**
   ```bash
   # Create database
   php bin/console doctrine:database:create

   # Run migrations to create tables
   php bin/console doctrine:migrations:migrate
   ```

6. **Create admin user**
   ```bash
   php bin/console app:create-admin
   ```
   Creates admin user: admin@jems.com / admin

7. **Start the development server**
   ```bash
   # With Symfony CLI (recommended)
   symfony server:start

   # OR without Symfony CLI
   php -S localhost:8000 -t public/
   ```

8. **Access the application**
   - Main app: http://localhost:8000
   - Login with: admin@jems.com / admin

### Database Configuration

The application uses MySQL with Docker. Default configuration:
- **Host**: localhost:3306
- **Database**: symfony
- **Username**: symfony
- **Password**: secret
- **phpMyAdmin**: http://localhost:8081 (symfony/secret)

## 🎯 Key Application Features Demonstrated

### 1. **Advanced User Profile System**
- **Multi-Section Profiles**: Personal, Education, Work Experience, Skills, CV Upload
- **Profile Completion Tracking**: Visual progress indicators showing completion percentage
- **Dynamic Forms**: Add multiple education entries, work experiences, and skills
- **File Upload**: Secure CV/Resume upload with file validation and storage
- **LinkedIn Integration**: Profile links and professional networking

### 2. **Sophisticated Authorization System**
- **Role-Based Access Control**: Clear distinction between User and Admin capabilities
- **Route-Level Security**: Different access levels for different application sections
- **Task Ownership**: Users can only manage their own tasks (except admins)
- **Admin Privileges**: Full system access including user management and all tasks
- **Visual Role Indicators**: Color-coded badges showing user roles and status

### 3. **Professional Task Management**
- **Assignment System**: Assign tasks by name and email with validation
- **Status Tracking**: Visual status indicators (In Progress, Completed)
- **Due Date Management**: Date picker with validation and tracking
- **Creator Attribution**: Track who created each task
- **Bulk Operations**: Admin can manage all tasks across the system

### 4. **Modern UI/UX Design**
- **Gradient Aesthetics**: Professional orange-to-pink gradient design
- **Responsive Design**: Works perfectly on desktop, tablet, and mobile
- **Interactive Elements**: Smooth hover effects and transitions
- **Consistent Branding**: JEMS branding throughout the application
- **Accessibility**: Proper form labels, semantic HTML, keyboard navigation

### Troubleshooting

**🚨 Common Issues & Solutions:**

1. **"Call to a member function isAdmin() on null"**
   ```bash
   # This means you're not logged in. Go to login page:
   http://localhost:8000/login
   ```

2. **Database connection errors**
   ```bash
   # Check if Docker is running
   docker-compose ps

   # Restart Docker services
   docker-compose down && docker-compose up -d

   # Wait 30 seconds for MySQL to start, then retry
   ```

3. **Permission errors**
   ```bash
   # Fix cache permissions
   php bin/console cache:clear
   chmod -R 777 var/
   ```

4. **Admin user doesn't exist**
   ```bash
   # Create or recreate admin user
   php bin/console app:create-admin
   ```

5. **Migrations fail**
   ```bash
   # Reset database (WARNING: deletes all data)
   php bin/console doctrine:database:drop --force
   php bin/console doctrine:database:create
   php bin/console doctrine:migrations:migrate
   php bin/console app:create-admin
   ```

6. **Port conflicts**
   ```bash
   # If port 3306 or 8081 are in use, edit docker-compose.yml:
   # Change "3306:3306" to "3307:3306"
   # Change "8081:80" to "8082:80"
   ```

## 🔐 Detailed Authentication & Authorization

### User Roles & Capabilities

#### 👤 Regular Users (ROLE_USER)
- **Task Management**: Create, edit, delete only their own tasks
- **Profile Management**: Complete access to their own profile sections
- **Education**: Add/edit multiple university entries with degrees
- **Work Experience**: Manage professional background and positions
- **Skills**: Add technical, soft skills, languages, certifications
- **CV Upload**: Upload and manage their resume/CV files
- **View Restrictions**: Cannot see other users' tasks or profiles

#### 👑 Admin Users (ROLE_ADMIN)
- **Full Task Access**: View, edit, delete ALL tasks in the system
- **User Management**: Access to user management interface with search
- **Task Assignment**: Can assign tasks to any user in the system
- **User Oversight**: View all user profiles and completion status
- **System Administration**: Full access to all application features
- **Default Admin**: admin@jems.com / admin

### Security Implementation
- **Password Hashing**: Secure bcrypt hashing with Symfony's security component
- **CSRF Protection**: All forms protected against cross-site request forgery
- **Remember Me**: Secure persistent login functionality
- **Session Management**: Secure session handling with proper timeout
- **Route Protection**: Automatic redirects for unauthorized access attempts
- **Input Validation**: Comprehensive form validation and sanitization

### Access Control Matrix
| Route Pattern | Required Role | Description |
|---------------|---------------|-------------|
| `/login`, `/register` | PUBLIC_ACCESS | Authentication pages |
| `/api/*` | PUBLIC_ACCESS | REST API endpoints |
| `/task/*` | ROLE_USER | Task management (own tasks only) |
| `/profile/*` | ROLE_USER | Profile management (own profile only) |
| `/admin/*` | ROLE_ADMIN | User management and system admin |

### Authorization Flow
1. **Unauthenticated Access**: Redirected to login page
2. **User Login**: Access to personal tasks and profile
3. **Admin Login**: Full system access including user management
4. **Route Protection**: Automatic enforcement based on user role
5. **Task Ownership**: Users can only access their own data (except admins)

## 📊 Database Schema & Entity Relationships

### Core Entities with Detailed Fields

#### 👤 User Entity
- **Authentication**: email, username, password (hashed), roles
- **Personal Info**: firstName, lastName, jobTitle, linkedinUrl
- **Relationships**: tasks, educations, workExperiences, skills, cvUpload
- **Methods**: isAdmin(), getFullName(), role management

#### 📋 Task Entity
- **Core Fields**: title, description, finished, dueDate
- **Assignment**: assigneeName, assigneeEmail, assignedTo (User)
- **Tracking**: createdBy (User), createdAt, updatedAt
- **Validation**: title (3-50 chars), description (10-300 chars)

#### 🎓 Education Entity
- **Institution**: institution, degree, fieldOfStudy
- **Timeline**: startYear, endYear, isCurrentlyStudying
- **Details**: description, gpa (0-4 scale)
- **Validation**: year range (1950-2030), GPA validation

#### 💼 WorkExperience Entity
- **Position**: company, position, location, employmentType
- **Timeline**: startDate, endDate, isCurrentPosition
- **Details**: description (up to 2000 chars)
- **Types**: Full-time, Part-time, Contract, Internship

#### 🛠 Skill Entity
- **Core**: name, category, level, yearsOfExperience
- **Categories**: Technical, Soft, Language, Certification
- **Levels**: Beginner, Intermediate, Advanced, Expert
- **Features**: isEndorsed, description

#### 📄 CvUpload Entity
- **File Info**: originalFileName, fileName, fileExtension, fileSize
- **Metadata**: description, uploadedAt
- **Validation**: PDF only, size limits, secure storage

### Entity Relationships
```
User (1) ←→ (Many) Task [createdBy, assignedTo]
User (1) ←→ (Many) Education
User (1) ←→ (Many) WorkExperience
User (1) ←→ (Many) Skill
User (1) ←→ (1) CvUpload
```

### Database Features
- **Migrations**: Versioned database schema changes
- **Constraints**: Foreign keys, unique constraints, validation
- **Indexing**: Optimized queries with proper indexing
- **Lifecycle Callbacks**: Automatic timestamp management
- **Eager Loading**: Optimized queries to prevent N+1 problems

## 🌐 API Endpoints

### User Management
- `POST /api/users/create` - Create new user account
- `GET /api/users` - List all users
- `GET /api/users/{id}` - Get user by ID

### Task Management
- `POST /api/tasks/create` - Create new task
- `GET /api/tasks` - List all tasks
- `GET /api/tasks/{id}` - Get task by ID

### API Testing
Use the included Postman collection: `postman/JEMS_Task_Manager_API.postman_collection.json`

## 🎨 User Interface

### Design Philosophy
- **Industrial & Professional**: Clean, business-focused design
- **Responsive**: Bootstrap 5 for mobile-friendly interface
- **User-Centric**: Intuitive navigation and clear information hierarchy
- **Accessibility**: Proper form labels and semantic HTML

### Key UI Features
- Sidebar navigation with collapsible sections
- Card-based layout for information display
- Interactive forms with validation feedback
- File upload with drag-and-drop interface
- Professional color scheme and typography

## 🧪 Testing

### Quick Test Workflow
1. **Start the application** (follow setup steps above)
2. **Access**: http://localhost:8000
3. **Login**: admin@jems.com / admin
4. **Create a task**: Click "New Task" button
5. **Test profile**: Go to "Profile" in navigation
6. **Test API**: Use Postman collection in `/postman/` folder

### API Testing
- **Postman Collection**: `postman/JEMS_Task_Manager_API.postman_collection.json`
- **Base URL**: http://127.0.0.1:8000
- **Test HTML Page**: http://localhost:8000/api-test.html
- Test all endpoints with sample data
- Validation and error handling verification

### Manual Testing Checklist
- ✅ User registration and authentication flows
- ✅ Task creation, editing, and deletion
- ✅ Profile management (education, work, skills)
- ✅ File upload functionality (CV upload)
- ✅ Admin vs regular user permissions
- ✅ Responsive design on mobile/tablet

## 📈 Development Workflow

This project follows Symfony best practices and demonstrates:
1. **MVC Architecture**: Clear separation of concerns
2. **Doctrine ORM**: Database abstraction and relationships
3. **Form Handling**: Symfony forms with validation
4. **Security**: Authentication and authorization
5. **API Development**: RESTful endpoints
6. **File Management**: Secure file upload handling
7. **Template Engine**: Twig for dynamic content

## 🚀 Deployment

The application is containerized and ready for deployment:
- Docker configuration included
- Environment-based configuration
- Production-ready security settings
- Database migration system

### Production Deployment Steps
1. Set `APP_ENV=prod` in environment
2. Configure production database URL
3. Run `composer install --no-dev --optimize-autoloader`
4. Run `php bin/console cache:clear --env=prod`
5. Set proper file permissions
6. Configure web server (Apache/Nginx)

## 🎯 Learning Objectives Achieved

This project demonstrates mastery of:
- ✅ **Symfony Framework**: Controllers, routing, services
- ✅ **Doctrine ORM**: Entities, repositories, migrations
- ✅ **Twig Templates**: Dynamic views, inheritance, forms
- ✅ **Security Component**: Authentication, authorization, CSRF
- ✅ **Form Handling**: Validation, file uploads, CRUD operations
- ✅ **API Development**: REST endpoints, JSON responses
- ✅ **Database Design**: Relationships, constraints, indexing
- ✅ **Modern PHP**: PHP 8.2+, attributes, type declarations

## 📝 Contributing

1. Follow Symfony coding standards
2. Write comprehensive tests
3. Update documentation for new features
4. Use meaningful commit messages

## 📄 License

This project is developed for educational purposes as part of a Symfony learning curriculum.

---

**Project Status**: ✅ Production Ready
**Last Updated**: December 2024
**Symfony Version**: 7.x
**PHP Version**: 8.2+
**Database**: MySQL 8.0
**Frontend**: Bootstrap 5 + Twig
