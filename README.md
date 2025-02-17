# User-CDA
# **Symfony User Management System**

A user management system built with Symfony, featuring authentication via a web interface (Twig) and an API (JWT for admins). The interface is styled with **Bootstrap**.

---

## **Features**

- **Authentication**:
  - Web login for all users
  - API login secured with JWT (admins only)
- **CRUD Operations**:
  - Create, read, update, and delete users
- **Security**:
  - Role-based access: `ROLE_USER` and `ROLE_ADMIN`
  - Restricted access to authenticated users
- **Modern UI**:
  - Responsive interface styled with **Bootstrap**

---

## **Getting Started**

### **Prerequisites**

- PHP 8.1 or higher
- Composer
- Node.js and Yarn (for assets)
- A database compatible with Doctrine (MySQL)
- OpenSSL (for JWT keys)

### **Installation**

1. **Clone the repository**
2. **Install PHP dependencies** composer install
3. **Install Node.js depencecies** yarn install
4. **Configure the database** Update .env
5. **Create the database and run migrations**
6. **Compile assets** run : yarn encore dev
7. **Generate JWT keys**
     - mkdir -p config/jwt
     - openssl genrsa -out config/jwt/private.pem -aes256 4096
     - openssl rsa -pubout -in config/jwt/private.pem -out config/jwt/public.pem
   
8.**Update the .env file with the JWT configuration**

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-rbsA2VBKQ1hA4LPCXxUgVrG8FRJsZZbJbG1zP9pv9S8aE3aRoq2ykh6dP4MquU6g" crossorigin="anonymous"></script>


pour scanner:

sonar-scanner.bat -D"sonar.projectKey=User-CDA" -D"sonar.sources=." -D"sonar.host.url=http://localhost:9000" -D"sonar.token=sqp_b460d7c9841a66a84a6f4f1231960fd989883125"
