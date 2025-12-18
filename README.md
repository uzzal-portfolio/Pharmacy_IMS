# Pharmacy Inventory Management System (IMS)

A comprehensive, open-source web application designed to streamline pharmacy operations, including inventory management, point of sale (POS), reporting, and procurement.

![Pharmacy IMS](src/img/Masterlogo.png)

## 🚀 Key Features

### 1. **Point of Sale (POS)**
-   **Efficient Checkout**: Fast product search, now including **Expiry Protection** to prevent selling expired items.
-   **Smart Customer Search**: Instantly find customers by Name or Phone (partial match supported).
-   **Dynamic Customer Creation**: Create new customers directly from the POS interface if they don't exist.
-   **Cart Management**: Add multiple items, adjust quantities.
-   **Discounts**: Apply percentage-based discounts to sales.
-   **Payment Methods**: Support for Cash, Card, and other payment types.
-   **Receipts**: Professional receipt generation.

### 2. **Inventory Management**
-   **Smart Management**: Categorize medicines with new **Medicine Groups**.
-   **Duplicate Prevention**: System actively prevents adding medicines with duplicate Codes.
-   **Advanced Search**: Autocomplete search for Medicine Name, Code, and Group.
-   **CSV Upload**: Bulk import support including Medicine Groups.
-   **Real-time Tracking**: Monitor stock levels, expiry dates, and pricing.

### 3. **Reporting & Analytics**
-   **Procurement Reports**: **[NEW]** Generate reports for medicine requests and their status.
-   **Expiry Reports**: Track expiring stock effectively.
-   **Sales Reports**: Detailed financial logs with transaction IDs and discounts.
-   **Stock Reports**: Quick overview of current inventory.
-   Powered by **FPDF** for reliable PDF generation.

### 4. **Procurement**
-   **Smart Requisition**: Search medicines by Name or Code to auto-fill requests, streamlining the restocking process.
-   **Request Tracking**: Manage procurement status (Pending, Approved, Rejected).

### 5. **Customer Management**
-   **Flexible Data**: Names are mandatory, but Phone and Email are now optional.
-   **Validation**: Phone numbers are strictly validated (digits & + only, max 15 chars).
-   **Seamless Integration**: Fully integrated with POS for quick access.

### 6. **Role-Based Access Control (RBAC)**
Secure login system with distinct user roles:
-   **Admin**: Full access to all modules (Inventory, Sales, Reports, Procurement, User Mgmt).
-   **Store Clerk**: Access to Inventory, Sales, and Procurement.
-   **Report Viewer**: Access to Sales History and Reports only.

### 7. **Additional Modules**
-   **User Management**: Add, edit, and delete system users and manage roles.
-   **Audit Log**: Track user actions for security and accountability.

## 🛠 Tech Stack
-   **Frontend**: HTML5, CSS3, Bootstrap 4, JavaScript (jQuery & jQuery UI)
-   **Backend**: PHP (PDO for Database Interaction)
-   **Database**: MySQL
-   **PDF Engine**: FPDF
-   **Server**: Apache (via XAMPP/WAMP)

## 📥 Installation

1.  **Prerequisites**:
    -   Install [XAMPP](https://www.apachefriends.org/) or any PHP/MySQL environment.

2.  **Clone the Repository**:
    ```bash
    git clone https://github.com/yourusername/pharmacy-ims.git
    cd pharmacy-ims
    ```
    Place the folder in your `htdocs` directory (e.g., `C:\xampp\htdocs\Pharmacy_IMS`).

3.  **Database Setup**:
    -   Start **Apache** and **MySQL** in XAMPP.
    -   Navigate to the database setup script in your browser:
        ```
        http://localhost/Pharmacy_IMS/database/setup_full.php
        ```
    -   This script will automatically:
        -   Create the `pharmacy_management` database.
        -   Create all necessary tables (`users`, `medicines`, `sales`, `procurement`, `customers`, etc.).
        -   Create a default **Admin** user.

4.  **Login**:
    -   **URL**: `http://localhost/Pharmacy_IMS/`
    -   **Default Admin Credentials**:
        -   Username: `admin`
        -   Password: `admin123`

## 👥 Contributors
-   **Uzzal Chandra Boisssha** - Team Lead
-   **Md. Mosaddek Al Hameem** - Lead Developer
-   **Afsana Mimi** - Designer

## 📜 License
This project is open-source software licensed under the **GNU General Public License v3.0**.
*"Free to run, free to study, free to change, and free to share."*
