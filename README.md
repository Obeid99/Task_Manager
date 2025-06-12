# 🎯 JEMS Task Manager

A modern, production-ready task management application built with **Symfony 7** and **Docker**. Features comprehensive user authentication, role-based authorization, task management, user profiles, and REST API endpoints. Perfect for learning modern web development practices with professional deployment capabilities.

## ✨ Key Features

### 🔐 **Authentication & Authorization**
- **Secure Login/Logout** with professional gradient UI
- **Role-Based Access Control** (Admin vs Regular Users)
- **Route Protection** with automatic redirects
- **Session Management** with CSRF protection and "Remember Me"

### 📋 **Smart Task Management**
- **Complete CRUD Operations** for tasks
- **Intelligent Assignment System** - Regular users create tasks for themselves, Admins can assign to anyone
- **Real-Time Status Updates** - No caching delays, immediate UI updates
- **Due Date Tracking** with visual indicators
- **Permission-Based Actions** - Edit/Delete only for creators and admins

### 👤 **Comprehensive User Profiles**
- **Multi-Section Profiles** - Personal info, education, work experience, skills
- **CV/Resume Upload** with secure file handling
- **Profile Completion Tracking** with visual progress indicators
- **LinkedIn Integration** for professional networking

### 👑 **Admin Panel**
- **User Management** with search and filtering
- **System-Wide Task Overview** - View and manage all tasks
- **Role Management** with visual badges
- **Profile Completion Monitoring** for all users

### 🌐 **REST API**
- **Complete API Endpoints** for tasks and users
- **JSON Responses** with proper error handling
- **Postman Collection** included for testing
- **API Documentation** with examples

### 🎨 **Modern UI/UX**
- **Professional Design** with industrial aesthetics
- **Top Navigation** with user profile and logout
- **Responsive Layout** - Works on all devices
- **Real-Time Updates** - No manual refresh needed
- **Consistent Branding** throughout the application

## 🚀 **Quick Deployment**

### **Option 1: One-Command Deploy**
```bash
# Windows
.\deploy.bat

# Linux/Mac
./deploy.sh
```

### **Option 2: Manual Docker**
```bash
cp .env.prod .env
docker-compose up --build -d
```

### **Access Points**
- **🌐 Web App**: http://localhost:8000
- **🗄️ Database**: localhost:3306
- **📊 phpMyAdmin**: http://localhost:8081

### **Default Login**
- **Email**: admin@jems.com
- **Password**: admin

## 🛠 **Technology Stack**

- **🐘 Backend**: Symfony 7 (PHP 8.2+)
- **🗄️ Database**: MySQL 8.0 with Doctrine ORM
- **🎨 Frontend**: Twig + Bootstrap 5 + Font Awesome
- **🔐 Security**: Symfony Security Component
- **📁 File Upload**: Secure CV/Resume handling
- **🌐 API**: RESTful endpoints with JSON
- **🐳 Deployment**: Docker + Docker Compose
- **📊 Admin**: phpMyAdmin for database management

## 🏗️ **Architecture Highlights**

### **Smart Authorization System**
- **Regular Users**: Create tasks for themselves only
- **Admin Users**: Full system access and task assignment
- **Real-Time Updates**: No caching delays on task operations
- **Secure Routes**: Role-based access control

### **Production-Ready Features**
- **Docker Containerization** for easy deployment
- **Environment Configuration** for dev/prod
- **Database Migrations** for schema management
- **File Upload Security** with validation
- **CSRF Protection** on all forms

## 📁 **Project Structure**

```
task_manager/
├── 🐳 Docker Files
│   ├── Dockerfile              # Production-ready container
│   ├── docker-compose.yml      # Multi-service orchestration
│   ├── deploy.bat              # Windows deployment
│   └── deploy.sh               # Linux/Mac deployment
├── 🎯 Application Core
│   ├── src/Controller/         # MVC Controllers
│   ├── src/Entity/            # Database entities
│   ├── src/Form/              # Symfony forms
│   ├── src/Repository/        # Data access layer
│   └── src/Security/          # Authentication
├── 🎨 Frontend
│   ├── templates/             # Twig templates
│   └── public/               # Static assets
├── 🗄️ Database
│   └── migrations/           # Schema versioning
└── 📚 Documentation
    ├── README.md             # This file
    ├── DEPLOYMENT.md         # Deployment guide
    └── postman/             # API testing
```

## ⚡ **Development Setup**

### **Prerequisites**
- Docker Desktop
- Git

### **Local Development**

1. **Clone & Deploy**
   ```bash
   git clone <repository-url>
   cd task_manager
   .\deploy.bat  # Windows
   # OR
   ./deploy.sh   # Linux/Mac
   ```

2. **Access Application**
   - **App**: http://localhost:8000
   - **Login**: admin@jems.com / admin
   - **Database**: http://localhost:8081 (symfony/secret)

### **Manual Setup (Alternative)**

```bash
# 1. Environment
cp .env.prod .env

# 2. Start services
docker-compose up -d

# 3. Wait for startup (30 seconds)
# 4. Access http://localhost:8000
```

### **Development Commands**

```bash
# View logs
docker-compose logs -f web

# Access container
docker-compose exec web bash

# Reset everything
docker-compose down -v
docker-compose up --build
```

## 🎯 **Key Features Demonstrated**

### **🔐 Authorization Logic**
```php
// Smart task creation based on user role
if (!$isAdmin) {
    // Regular users: tasks assigned to themselves
    $task->setAssignedTo($user);
} else {
    // Admins: can assign to anyone via form
    $this->handleAssigneeInfo($task, $entityManager);
}
```

### **⚡ Real-Time Updates**
- **No Caching Delays** - Task changes appear immediately
- **Permission-Based UI** - Buttons show/hide based on user role
- **Instant Status Updates** - Complete/Reopen without refresh

### **🎨 Professional UI**
- **Top Navigation** with user profile
- **Sidebar Navigation** with role-based menu items
- **Card-Based Layout** for clean information display
- **Responsive Design** for all screen sizes

### **🔒 Security Implementation**
- **Route Protection** - `/admin/*` requires ROLE_ADMIN
- **CSRF Protection** - All forms secured
- **File Upload Security** - PDF validation and secure storage
- **Password Hashing** - Bcrypt with Symfony Security

## 🚨 **Troubleshooting**

### **Common Issues**

**🔌 Port Conflicts**
```bash
# Edit docker-compose.yml if ports are in use:
# "3306:3306" → "3307:3306"
# "8081:80" → "8082:80"
```

**🗄️ Database Issues**
```bash
# Check services
docker-compose ps

# Restart everything
docker-compose down && docker-compose up -d
```

**🔑 Login Problems**
- Default admin: admin@jems.com / admin
- If admin doesn't exist, check container logs:
  ```bash
  docker-compose logs web
  ```

**🐳 Docker Issues**
```bash
# Reset everything
docker-compose down -v
docker-compose up --build
```

## 🔐 **Authorization System**

### **User Roles**

| Role | Task Management | User Access | Admin Panel |
|------|----------------|-------------|-------------|
| **👤 Regular User** | Own tasks only | Own profile | ❌ No access |
| **👑 Admin** | All tasks | All users | ✅ Full access |

### **Permission Matrix**

| Action | Regular User | Admin |
|--------|-------------|-------|
| Create Task | ✅ (assigned to self) | ✅ (can assign to anyone) |
| View Tasks | ✅ (own only) | ✅ (all tasks) |
| Edit Tasks | ✅ (own only) | ✅ (all tasks) |
| Delete Tasks | ✅ (own only) | ✅ (all tasks) |
| User Management | ❌ | ✅ |
| Profile Management | ✅ (own only) | ✅ (view all) |

### **Security Features**
- **🔒 Route Protection** - Role-based access control
- **🛡️ CSRF Protection** - All forms secured
- **🔑 Password Hashing** - Bcrypt encryption
- **📝 Input Validation** - Comprehensive form validation
- **🚪 Session Management** - Secure login/logout

## 📊 **Database Schema**

### **Core Entities**

```sql
User
├── Authentication (email, password, roles)
├── Profile (firstName, lastName, jobTitle)
├── Tasks (created & assigned)
├── Education records
├── Work experience
├── Skills
└── CV upload

Task
├── Content (title, description)
├── Status (finished, dueDate)
├── Assignment (assignedTo, assigneeName, assigneeEmail)
└── Tracking (createdBy, createdAt, updatedAt)
```

### **Key Relationships**
- **User → Tasks** (One-to-Many: created & assigned)
- **User → Profile Data** (One-to-Many: education, experience, skills)
- **User → CV** (One-to-One: file upload)

### **Database Features**
- **🔄 Migrations** - Versioned schema changes
- **🔗 Relationships** - Proper foreign keys
- **⚡ Optimized Queries** - Eager loading, no N+1 problems
- **📝 Validation** - Database-level constraints

## 🌐 **API Endpoints**

### **Available APIs**
```bash
# User Management
POST /api/users/create    # Create user
GET  /api/users          # List users
GET  /api/users/{id}     # Get user

# Task Management
POST /api/tasks/create   # Create task
GET  /api/tasks         # List tasks
GET  /api/tasks/{id}    # Get task
```

### **API Testing**
- **📁 Postman Collection**: `postman/JEMS_Task_Manager_API.postman_collection.json`
- **🌐 Base URL**: http://localhost:8000
- **📋 Test Page**: http://localhost:8000/api-test.html

## 🧪 **Testing**

### **Quick Test**
1. Deploy: `.\deploy.bat`
2. Access: http://localhost:8000
3. Login: admin@jems.com / admin
4. Create tasks, test profiles, try API

### **Test Scenarios**
- ✅ **Authentication** - Login/logout flows
- ✅ **Authorization** - Admin vs user permissions
- ✅ **Task Management** - CRUD operations
- ✅ **Profile System** - Multi-section profiles
- ✅ **File Upload** - CV upload functionality
- ✅ **API** - REST endpoints with Postman

## 🚀 **Production Deployment**

### **Railway Deployment**
1. **Push to GitHub**
   ```bash
   git add .
   git commit -m "Production ready"
   git push origin main
   ```

2. **Deploy on Railway**
   - Connect GitHub repository
   - Railway auto-detects Dockerfile
   - Set environment variables
   - Get live URL

### **Environment Variables**
```bash
APP_ENV=prod
APP_SECRET=your-secret-key
DATABASE_URL=mysql://user:pass@host:port/db
```

## 🎯 **Learning Achievements**

This project demonstrates:
- ✅ **Modern Symfony 7** with PHP 8.2+
- ✅ **Docker Containerization** for deployment
- ✅ **Role-Based Authorization** system
- ✅ **Real-Time UI Updates** without caching issues
- ✅ **Professional Design** with responsive layout
- ✅ **Production-Ready** deployment configuration
- ✅ **REST API** development
- ✅ **Database Design** with proper relationships

---

## 📋 **Project Info**

**🎯 Status**: ✅ Production Ready
**📅 Updated**: December 2024
**🐘 Framework**: Symfony 7.x
**🐳 Deployment**: Docker + Railway
**🎨 Frontend**: Bootstrap 5 + Twig
**🗄️ Database**: MySQL 8.0

**Perfect for portfolios and learning modern web development! 🚀**
