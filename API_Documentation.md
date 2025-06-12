# JEMS Task Manager API Documentation

## Base URL
```
http://127.0.0.1:8000
```

## User Management Endpoints

### 1. Create User Account
**POST** `/api/users/register`

Creates a new user account in the system.

**Request Body:**
```json
{
    "email": "john.doe@example.com",
    "password": "SecurePassword123!",
    "firstName": "John",
    "lastName": "Doe",
    "username": "johndoe",
    "jobTitle": "Software Developer",
    "linkedinUrl": "https://linkedin.com/in/johndoe",
    "role": "user"
}
```

**Required Fields:**
- `email` (string): User's email address
- `password` (string): User's password
- `firstName` (string): User's first name
- `lastName` (string): User's last name

**Optional Fields:**
- `username` (string): User's username (auto-generated from email if not provided)
- `jobTitle` (string): User's job title
- `linkedinUrl` (string): User's LinkedIn profile URL
- `role` (string): "user" or "admin" (defaults to "user")

**Response (201 Created):**
```json
{
    "success": true,
    "message": "User created successfully",
    "data": {
        "id": 1,
        "email": "john.doe@example.com",
        "firstName": "John",
        "lastName": "Doe",
        "fullName": "John Doe",
        "jobTitle": "Software Developer",
        "linkedinUrl": "https://linkedin.com/in/johndoe",
        "roles": ["ROLE_USER"],
        "isAdmin": false,
        "profileCompletionPercentage": 50
    }
}
```

### 2. Get All Users
**GET** `/api/users/list`

Retrieves a list of all users in the system.

**Response (200 OK):**
```json
{
    "success": true,
    "message": "Users retrieved successfully",
    "data": [
        {
            "id": 1,
            "email": "john.doe@example.com",
            "firstName": "John",
            "lastName": "Doe",
            "fullName": "John Doe",
            "jobTitle": "Software Developer",
            "linkedinUrl": "https://linkedin.com/in/johndoe",
            "roles": ["ROLE_USER"],
            "isAdmin": false,
            "profileCompletionPercentage": 50
        }
    ],
    "count": 1
}
```

### 3. Get User by ID
**GET** `/api/users/{id}`

Retrieves a specific user by their ID.

**Response (200 OK):**
```json
{
    "success": true,
    "message": "User retrieved successfully",
    "data": {
        "id": 1,
        "email": "john.doe@example.com",
        "firstName": "John",
        "lastName": "Doe",
        "fullName": "John Doe",
        "jobTitle": "Software Developer",
        "linkedinUrl": "https://linkedin.com/in/johndoe",
        "roles": ["ROLE_USER"],
        "isAdmin": false,
        "profileCompletionPercentage": 50
    }
}
```

## Task Management Endpoints

### 1. Create Task
**POST** `/api/tasks/create`

Creates a new task in the system.

**Request Body:**
```json
{
    "title": "Complete API Documentation",
    "description": "Write comprehensive API documentation for the task management system including all endpoints and examples.",
    "createdByEmail": "admin@jems.com",
    "assigneeEmail": "john.doe@example.com",
    "dueDate": "2024-12-31",
    "finished": false
}
```

**Required Fields:**
- `title` (string): Task title (3-50 characters)
- `description` (string): Task description (10-300 characters)
- `createdByEmail` (string): Email of the user creating the task

**Optional Fields:**
- `assigneeEmail` (string): Email of the assigned user
- `assigneeName` (string): Name of external assignee (if not a system user)
- `dueDate` (string): Due date in YYYY-MM-DD format
- `finished` (boolean): Task completion status (defaults to false)

**Response (201 Created):**
```json
{
    "success": true,
    "message": "Task created successfully",
    "data": {
        "id": 1,
        "title": "Complete API Documentation",
        "description": "Write comprehensive API documentation...",
        "finished": false,
        "dueDate": "2024-12-31",
        "createdAt": "2024-12-20 10:30:00",
        "updatedAt": null,
        "assigneeName": null,
        "assigneeEmail": null,
        "assignedTo": {
            "id": 2,
            "email": "john.doe@example.com",
            "fullName": "John Doe"
        },
        "createdBy": {
            "id": 1,
            "email": "admin@jems.com",
            "fullName": "Admin User"
        }
    }
}
```

### 2. Get All Tasks
**GET** `/api/tasks`

Retrieves a list of all tasks in the system.

**Response (200 OK):**
```json
{
    "success": true,
    "message": "Tasks retrieved successfully",
    "data": [
        {
            "id": 1,
            "title": "Complete API Documentation",
            "description": "Write comprehensive API documentation...",
            "finished": false,
            "dueDate": "2024-12-31",
            "createdAt": "2024-12-20 10:30:00",
            "updatedAt": null,
            "assigneeName": null,
            "assigneeEmail": null,
            "assignedTo": {
                "id": 2,
                "email": "john.doe@example.com",
                "fullName": "John Doe"
            },
            "createdBy": {
                "id": 1,
                "email": "admin@jems.com",
                "fullName": "Admin User"
            }
        }
    ],
    "count": 1
}
```

### 3. Get Task by ID
**GET** `/api/tasks/{id}`

Retrieves a specific task by its ID.

**Response (200 OK):**
```json
{
    "success": true,
    "message": "Task retrieved successfully",
    "data": {
        "id": 1,
        "title": "Complete API Documentation",
        "description": "Write comprehensive API documentation...",
        "finished": false,
        "dueDate": "2024-12-31",
        "createdAt": "2024-12-20 10:30:00",
        "updatedAt": null,
        "assigneeName": null,
        "assigneeEmail": null,
        "assignedTo": {
            "id": 2,
            "email": "john.doe@example.com",
            "fullName": "John Doe"
        },
        "createdBy": {
            "id": 1,
            "email": "admin@jems.com",
            "fullName": "Admin User"
        }
    }
}
```

## Error Responses

### 400 Bad Request
```json
{
    "success": false,
    "message": "Field 'email' is required"
}
```

### 404 Not Found
```json
{
    "success": false,
    "message": "User not found"
}
```

### 409 Conflict
```json
{
    "success": false,
    "message": "User with this email already exists"
}
```

### 500 Internal Server Error
```json
{
    "success": false,
    "message": "An error occurred while creating the user",
    "error": "Detailed error message"
}
```

## How to Use with Postman

1. **Import Collection**: Import the `JEMS_Task_Manager_API.postman_collection.json` file into Postman
2. **Set Base URL**: The collection uses a variable `{{base_url}}` set to `http://127.0.0.1:8000`
3. **Create Users**: Start by creating user accounts using the "Create User Account" request
4. **Create Tasks**: Use the created user emails to create tasks
5. **Test Endpoints**: Use the various GET endpoints to retrieve data

## Example Workflow

1. Create an admin user
2. Create a regular user
3. Create tasks assigned to users
4. Retrieve all users and tasks to verify creation
