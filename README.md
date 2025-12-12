# Pharmacy Inventory Management System (IMS)

A comprehensive, open-source web application designed to streamline pharmacy operations, including inventory management, point of sale (POS), reporting, and procurement.

![Pharmacy IMS](src/img/Masterlogo.png)

## 🚀 Key Features

### 1. **Point of Sale (POS)**
-   **Efficient Checkout**: Fast product search with autocomplete.
-   **Cart Management**: Add multiple items, adjust quantities.
-   **Discounts**: Apply percentage-based discounts to sales.
-   **Payment Methods**: Support for Cash, Card, and other payment types.
-   **Receipts**: Auto-generate and print professional receipts.

### 2. **Inventory Management**
-   **Real-time Tracking**: Monitor stock levels, expiry dates, and pricing.
-   **CSV Upload**: Bulk import medicine catalogs via CSV.
-   **Low Stock Alerts**: Visual indicators for items needing replenishment.
-   **Search**: Advanced search by name or code.

### 3. **Reporting & Analytics**
-   **Stock Reports**: PDF generation for current inventory status.
-   **Sales Reports**: Detailed sales history having transaction IDs, discounts, and final prices.
-   **Expiry Reports**: Track expiring medicines to reduce waste.
-   Powered by **FPDF** for reliable PDF generation.

### 4. **Role-Based Access Control (RBAC)**
Secure login system with distinct user roles:
-   **Admin**: Full access to all modules (Inventory, Sales, Reports, Procurement, User Mgmt).
-   **Store Clerk**: Access to Inventory, Sales, and Procurement.
-   **Report Viewer**: Access to Sales History and Reports only.

### 5. **Additional Modules**
-   **Procurement**: Manage purchase requests for out-of-stock items.
-   **User Management**: Add, edit, and delete system users and manage roles.
-   **Customer Database**: Maintain customer records.

## 🛠 Tech Stack
-   **Frontend**: HTML5, CSS3, Bootstrap 4, JavaScript (jQuery)
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
        -   Create all necessary tables (`users`, `medicines`, `sales`, etc.).
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
