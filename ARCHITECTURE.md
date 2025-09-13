# VanillaCRM Architecture Documentation

## System Architecture Overview

```mermaid
graph TB
    subgraph "Frontend Layer"
        UI[Web Interface - Bootstrap 5]
        API_Client[JavaScript API Client]
    end

    subgraph "Application Layer"
        subgraph "Controllers"
            WC[Web Controllers]
            AC[API Controllers]
        end

        subgraph "Services"
            ERS[ExpenseRequestService]
            RS[Report Services]
            CS[CashierReportService]
            PS[PermissionService]
        end

        subgraph "Models"
            User[User Model]
            Company[Company Model]
            Department[Department Model]
            Post[Post Model]
            Permission[Permission Model]
            Report[Report Model]
            Field[Field Model]
        end

        subgraph "Authentication"
            Sanctum[Laravel Sanctum]
            Middleware[Auth Middleware]
        end
    end

    subgraph "External Services"
        VanillaFlow[VanillaFlow API]
        DB[(MariaDB Database)]
        Cache[Redis Cache]
        Storage[File Storage]
    end

    UI --> WC
    API_Client --> AC
    WC --> Services
    AC --> Services
    Services --> Models
    Models --> DB
    Middleware --> Auth
    ERS --> VanillaFlow
    Services --> Cache
    Services --> Storage
```

## Database Schema Architecture

```mermaid
erDiagram
    User {
        int id PK
        string name
        string full_name
        string email
        string password
        string phone
        string telegram_id
        string remember_token
        string role
        string status
        int com_id FK
        int dep_id FK
        int post_id FK
        timestamp created_at
        timestamp updated_at
    }

    Company {
        int id PK
        string name
        timestamp created_at
        timestamp updated_at
    }

    Department {
        int id PK
        string name
        int com_id FK
        timestamp created_at
        timestamp updated_at
    }

    Post {
        int id PK
        string name
        int com_id FK
        int dep_id FK
        text permissions
        timestamp created_at
        timestamp updated_at
    }

    Permission {
        int id PK
        string name
        string description
        timestamp created_at
        timestamp updated_at
    }

    Field {
        int id PK
        string name
        string type
        int user_id FK
        text value
        timestamp created_at
        timestamp updated_at
    }

    Report {
        int id PK
        string type
        string name
        json data
        int user_id FK
        int com_id FK
        timestamp created_at
        timestamp updated_at
    }

    User ||--o{ Company : "belongs_to"
    User ||--o{ Department : "belongs_to"
    User ||--o{ Post : "belongs_to"
    User ||--o{ Field : "has_many"
    User ||--o{ Report : "has_many"
    Company ||--o{ Department : "has_many"
    Company ||--o{ Post : "has_many"
    Department ||--o{ Post : "has_many"
    Post ||--o{ User : "has_many"
```

## Authentication Flow Architecture

```mermaid
sequenceDiagram
    participant User
    participant Frontend
    participant Middleware
    participant Controller
    participant Sanctum
    participant Database

    User->>Frontend: Login Request
    Frontend->>Middleware: AdminMiddleware/UserMiddleware
    Middleware->>Database: Check User Status/Role
    Database-->>Middleware: User Data
    Middleware-->>Frontend: Authentication Result
    Frontend->>Controller: Proceed if Authenticated

    Note over Controller: API Authentication Flow
    Controller->>Sanctum: Validate Token
    Sanctum->>Database: Token Verification
    Database-->>Sanctum: Token Valid
    Sanctum-->>Controller: User Identity
    Controller->>Database: Load User Data
    Database-->>Controller: User with Permissions
```

## API Architecture

```mermaid
graph LR
    subgraph "Web Routes"
        A[Home /]
        B[Theme /theme]
        C[Auth /login /logout]
        D[Admin /admin/*]
        E[User /user/*]
        F[Moderator /moderator/*]
    end

    subgraph "API Routes"
        G[POST /api/session/login]
        H[POST /api/session/logout]
        I[GET /api/up]
        J[GET /api/v1/user]
        K[POST /api/v1/user]
    end

    subgraph "Route Protection"
        L[AdminMiddleware]
        M[UserMiddleware]
        N[EnsureTokenIsFromAdmin]
        O[Rate Limiting]
    end

    D --> L
    E --> M
    F --> M
    J --> N
    K --> N
    A --> O
    B --> O
    G --> O
    H --> O
    I --> O
```

## Business Domain Architecture

```mermaid
graph TD
    subgraph "User Management Domain"
        A[User Registration]
        B[User Authentication]
        C[User Profile Management]
        D[Role Assignment]
        E[Permission Management]
    end

    subgraph "Organizational Structure Domain"
        F[Company Management]
        G[Department Management]
        H[Position Management]
        I[Hierarchical Organization]
    end

    subgraph "Financial Domain"
        J[Expense Tracking]
        K[Report Generation]
        L[Cashier Reports]
        M[XLSX Export]
    end

    subgraph "Integration Domain"
        N[VanillaFlow API]
        O[Data Synchronization]
        P[External Services]
    end

    A --> B
    B --> D
    D --> E
    E --> C
    F --> G
    G --> H
    H --> I
    I --> A
    J --> K
    K --> L
    L --> M
    J --> N
    N --> O
    O --> P
```

## External Integration Architecture

```mermaid
sequenceDiagram
    participant CRM as VanillaCRM
    participant Service as ExpenseRequestService
    participant API as VanillaFlow API
    participant DB as Database

    CRM->>Service: Get Expense Requests
    Service->>API: GET /expense-requests
    API-->>Service: Expense Data
    Service->>DB: Cache Data

    CRM->>Service: Export Expense Data
    Service->>API: GET /expense-requests/export
    API-->>Service: CSV/Excel Data
    Service->>CRM: Processed Export

    Note over Service: Token Management
    Service->>API: Authentication Token
    API-->>Service: Session Validation
```

## Service Layer Architecture

```mermaid
graph TB
    subgraph "Service Layer"
        subgraph "Core Services"
            ERS[ExpenseRequestService]
            RS[ReportService]
            CRS[CashierReportService]
            PS[PermissionService]
        end

        subgraph "Utility Services"
            XLSX[XlsxService]
            Mail[MailService]
            Cache[CacheService]
        end
    end

    subgraph "Data Access Layer"
        Models[Eloquent Models]
        Repositories[Custom Repositories]
        API_Clients[External API Clients]
    end

    subgraph "External Services"
        VanillaFlow[VanillaFlow API]
        Storage[File Storage]
        Cache_Store[Redis]
    end

    ERS --> Models
    ERS --> API_Clients
    RS --> Models
    RS --> XLSX
    CRS --> Models
    CRS --> XLSX
    PS --> Models
    XLSX --> Storage
    Mail --> External
    Cache --> Cache_Store
    API_Clients --> VanillaFlow
```

## Deployment Architecture

```mermaid
graph TB
    subgraph "Production Environment"
        subgraph "Application Server"
            Nginx[Nginx - Port 8002]
            PHP[PHP-FPM]
            App[Laravel Application]
        end

        subgraph "Database Server"
            MariaDB[MariaDB - Port 3306]
            phpMyAdmin[phpMyAdmin - Port 8080]
        end

        subgraph "Cache Server"
            Redis[Redis Cache]
        end

        subgraph "External Services"
            VanillaFlow[VanillaFlow API]
            Mail[Mail Service]
        end
    end

    Nginx --> PHP
    PHP --> App
    App --> MariaDB
    App --> Redis
    App --> VanillaFlow
    App --> Mail
    MariaDB --> phpMyAdmin
```

## Data Flow Architecture

```mermaid
flowchart TD
    subgraph "Input Layer"
        A[Web Form]
        B[API Request]
        C[File Upload]
    end

    subgraph "Processing Layer"
        D[Validation]
        E[Business Logic]
        F[Authorization]
        G[Data Transformation]
    end

    subgraph "Storage Layer"
        H[Database]
        I[File Storage]
        J[Cache]
    end

    subgraph "Output Layer"
        K[Web Response]
        L[API Response]
        M[File Download]
        N[Email Notification]
    end

    A --> D
    B --> D
    C --> D
    D --> E
    E --> F
    F --> G
    G --> H
    G --> I
    G --> J
    H --> K
    H --> L
    I --> M
    J --> K
    J --> L
    E --> N
```

## Security Architecture

```mermaid
graph TD
    subgraph "Authentication"
        A[Laravel Sanctum]
        B[Session Management]
        C[Token Validation]
        D[Role Verification]
    end

    subgraph "Authorization"
        E[AdminMiddleware]
        F[UserMiddleware]
        G[Permission Check]
        H[Route Protection]
    end

    subgraph "Data Protection"
        I[Input Validation]
        J[SQL Injection Protection]
        K[XSS Protection]
        L[CSRF Protection]
    end

    subgraph "Network Security"
        M[Rate Limiting]
        N[HTTPS/SSL]
        O[Firewall Rules]
        P[Secure Headers]
    end

    A --> C
    C --> D
    D --> E
    E --> F
    F --> G
    G --> H
    I --> J
    J --> K
    K --> L
    M --> N
    N --> O
    O --> P
```