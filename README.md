# Mizan (Justice Law Office)

Mizan is a comprehensive digital platform for managing legal services and connecting lawyers with clients. The platform provides a modern and seamless experience for case management, direct communication, and requesting various legal services.

## Key Features

- **For Lawyers:**
  - Legal services management (Create, Edit, and Publish).
  - Client request management.
  - Comprehensive dashboard for tracking ongoing cases.
  - Real-time chat system with clients.
- **For Clients:**
  - Browse the legal services marketplace and choose the right lawyer.
  - Submit custom service requests.
  - Real-time tracking of case and request status.
  - Direct chat with lawyers to discuss details.

## Technology Stack

- **Backend:** PHP 8.2
- **Database:** MySQL 8.0
- **Frontend:** Tailwind CSS + Iconify
- **Environment:** Docker & Docker Compose

## How to Run

The platform is fully equipped to run using Docker. Follow these steps to get started:

1. Ensure **Docker** and **Docker Compose** are installed on your system.
2. Run the following command in the project directory:
   ```bash
   docker-compose up -d --build
   ```
3. Wait for the containers to become ready.
4. Open your browser and navigate to:
   - Application: `http://localhost:8080`
   - Database Management (phpMyAdmin): `http://localhost:8081`

## Test Login Credentials

The common password for all accounts is: `password`

### Lawyer Accounts
- **Ahmed Mahmoud El-Shafei:** `ahmed.lawyer@mizan.com`
- **Sara Hassan El-Amery:** `sara.lawyer@mizan.com`
- **Mohammed Ali Zein:** `mohammed.lawyer@mizan.com`

### Client Accounts
- **Khaled Saeed Al-Mansouri:** `khaled.client@example.com`
- **Mariam Youssef El-Qadi:** `mariam.client@example.com`

## File Structure

- `actions`: Contains data processing and operations files.
- `assets`: Images, CSS, and JS files.
- `includes`: Shared files such as header, footer, and configuration.
- `pages`: Application pages categorized by user roles.
- `init-db`: Database creation and initialization files.

---
Developed by: **Ahmed Mohamed Mahmoud Abdelrahman**
© 2026 Justice Law Office
