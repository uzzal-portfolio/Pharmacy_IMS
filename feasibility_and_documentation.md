# Pharmacy Inventory Management System: Feasibility Study & Documentation

## 1. Feasibility Study (for Bangladesh Market Standard)

### 1.1. Economical Feasibility

The development of a Pharmacy Inventory Management System for the Bangladesh market presents a strong economic feasibility. The current market often relies on manual or semi-automated processes, leading to inefficiencies, stockouts, expiry losses, and revenue leakage. A well-implemented system can significantly reduce operational costs by:

*   **Minimizing Stockouts and Overstocking:** Real-time inventory tracking prevents lost sales due to stockouts and reduces capital tied up in excess inventory.
*   **Reducing Expiry Losses:** Automated expiry tracking and alerts ensure timely sales or returns of near-expiry medicines, minimizing waste.
*   **Improving Procurement Efficiency:** Streamlined procurement processes reduce administrative overhead and enable better negotiation with suppliers.
*   **Enhancing Sales and Customer Management:** A POS system and customer database can improve sales speed, accuracy, and customer loyalty through purchase history analysis.
*   **Generating Accurate Reports:** Timely and accurate reports support better business decisions, leading to increased profitability.
*   **Compliance:** Adherence to GMP and DSCSA standards can prevent costly penalties and enhance reputation.

The initial investment in development, hardware (if needed), and training is expected to be offset by these operational savings and increased revenue within a reasonable timeframe, making it a financially viable project. The use of PHP and MySQL (XAMPP) keeps technology costs low, which is suitable for the Bangladesh market.

### 1.2. Technical Feasibility

The project is technically feasible given the chosen technology stack and common development practices in Bangladesh.

*   **PHP, MySQL, HTML, Bootstrap:** These are widely used and well-understood technologies. There is a large pool of developers proficient in these languages, and extensive documentation and community support are available.
*   **XAMPP Local Server:** This provides a complete local development environment, simplifying setup and deployment for local pharmacies or small chains.
*   **Web-based and Responsive Architecture:** Modern web development tools and frameworks (like Bootstrap) make it straightforward to build responsive interfaces that adapt to various devices (PC/Mobile), meeting the project's architectural requirements.
*   **Database Management:** MySQL is a robust and scalable relational database management system capable of handling the required data (users, medicines, sales, customers, audit logs) with full audit logging and batch tracking.
*   **Security:** Implementing industrial-level security features like role-based access control, encrypted transactions (though HTTPS would be needed for full encryption in production), and audit trails is technically achievable with PHP and MySQL best practices.
*   **PDF Generation:** Libraries for PHP exist to facilitate dynamic PDF report generation.
*   **Payment Gateway Integration:** Bangladesh-specific payment gateways (bKash, Nagad, SSL Commerz) typically provide APIs and SDKs for integration with web applications, making this technically feasible.

### 1.3. Operational Feasibility

The system is operationally feasible, meaning it can be successfully integrated into the daily operations of pharmacies in Bangladesh.

*   **User-Friendly Interface:** The web-based, responsive interface with Bootstrap will be intuitive for various user roles (admin, store clerk, online customer, report viewer), minimizing the learning curve.
*   **Streamlined Workflows:** The system is designed to automate and streamline core pharmacy operations, such as medicine receipt, sales, procurement requests, and reporting, leading to increased efficiency and reduced manual errors.
*   **Multi-role System:** Role-based access ensures that users only access functionalities relevant to their responsibilities, simplifying the user experience and enhancing security.
*   **Reporting Capabilities:** Dynamic PDF reports with custom date ranges will provide valuable insights for management, supporting better decision-making.
*   **Customer Management:** A centralized customer database with purchase history will enable personalized services and marketing efforts.
*   **Minimal Disruption:** The system can be implemented incrementally, allowing pharmacies to transition smoothly from existing processes. Training materials and support will be crucial for successful adoption.

## 2. Paragraphical Explanation of System Flow Chart Diagram

The Pharmacy Inventory Management System operates through a series of interconnected modules, starting with user authentication. Upon successful login, users are directed to a welcome page, where a dynamic navigation bar presents options based on their assigned role (Admin, Store Clerk, Online Customer, Report Viewer).

The **Inventory Management** module allows authorized users (Admin, Store Clerk) to either register new medicines into the system, providing details like name, code, expiry date, and location, or to add stock to existing medicines by searching for them and updating their quantities. The system maintains a comprehensive overview of all medicines, including their current stock levels.

The **Sales System** module enables authorized users (Admin, Store Clerk, Online Customer) to record sales. This involves searching for medicines, specifying quantities, and calculating the total price. The system automatically deducts sold items from the inventory. Optionally, sales can be linked to customer records.

The **Procurement** module allows authorized users (Admin, Store Clerk) to submit requests for new medicine purchases. These requests are tracked with a status (pending, approved, rejected), which can be updated by authorized personnel.

The **Reporting** module provides authorized users (Admin, Report Viewer) with various insights into the pharmacy's operations. Users can generate Stock Reports (current inventory), Expiry Reports (medicines expiring within a date range or already expired), Sales Reports (sales within a date range), and Procurement Request Reports (status of purchase requests within a date range), and Customer Reports (list of all customers).

The **User Management** module, exclusively for Admin users, facilitates the creation, editing, and deletion of user accounts, along with assigning roles to control access to different system functionalities.

Finally, the **Customer Database** module, accessible by Admin and Online Customers, allows for the management of customer information, including their contact details and a detailed purchase history.

Throughout the system, all significant actions are recorded in an **Audit Log** to maintain a comprehensive history of operations for security and compliance purposes. The system interacts with a **MySQL Database** to store and retrieve all inventory, sales, procurement, user, and customer data.

## 3. DFD (Data Flow Diagram) of this Project (Textual Description)

### Level 0 DFD: Pharmacy Inventory Management System

*   **External Entities:** User (Admin, Store Clerk, Online Customer, Report Viewer), Customer, Payment Gateway.
*   **Process:** Pharmacy Inventory Management System.
*   **Data Flows:**
    *   User -> Pharmacy Inventory Management System: Login Credentials, Medicine Details (New/Update), Stock Quantity (Add), Sale Details, Purchase Request, Report Criteria, User Details (Add/Update/Delete), Customer Details (Add/Update/Delete).
    *   Pharmacy Inventory Management System -> User: Authentication Status, Inventory Data, Sales Data, Procurement Request Status, Report Data, User List, Customer List, Purchase History.
    *   Pharmacy Inventory Management System -> Payment Gateway: Payment Request.
    *   Payment Gateway -> Pharmacy Inventory Management System: Payment Confirmation.

### Level 1 DFD: Pharmacy Inventory Management System

**Processes:**
1.  **User Authentication & Authorization:** Handles user login, session management, and role-based access control.
2.  **Inventory Management:** Manages medicine registration, stock entry, updates, and deletion.
3.  **Sales Processing:** Handles medicine search, recording sales, and updating inventory.
4.  **Procurement Management:** Manages submission and status updates of purchase requests.
5.  **Reporting & Analytics:** Generates various reports (stock, expiry, sales, procurement, customer).
6.  **User Administration:** Manages user accounts and roles.
7.  **Customer Relationship Management:** Manages customer details and purchase history.
8.  **Audit Logging:** Records all significant system actions.

**Data Stores:**
*   **DS1: Users:** Stores user credentials and roles.
*   **DS2: Medicines:** Stores medicine details (name, code, quantity, expiry, location).
*   **DS3: Sales:** Stores sales transaction details.
*   **DS4: Customers:** Stores customer information and purchase history.
*   **DS5: Procurement:** Stores purchase request details.
*   **DS6: Audit Log:** Stores system activity logs.

**Data Flows (Examples):**

*   **User Authentication & Authorization:**
    *   User -> 1: Login Credentials
    *   1 -> DS1: Validate Credentials
    *   DS1 -> 1: User Role, User ID
    *   1 -> User: Authentication Status, Welcome Page (with role-based navigation)

*   **Inventory Management:**
    *   User (Admin/Store Clerk) -> 2: New Medicine Details, Medicine ID, Quantity to Add, Updated Medicine Details, Medicine ID (for Delete)
    *   2 -> DS2: Add/Update/Delete Medicine
    *   DS2 -> 2: Medicine Data
    *   2 -> User (Admin/Store Clerk): Confirmation/Error, Inventory List

*   **Sales Processing:**
    *   User (Admin/Store Clerk/Online Customer) -> 3: Search Term, Medicine ID, Quantity, Total Price, Customer ID (Optional)
    *   3 -> DS2: Search Medicines, Deduct Stock
    *   DS2 -> 3: Medicine Details, Stock Availability
    *   3 -> DS4: Link Customer to Sale (if Customer ID provided)
    *   3 -> DS3: Record Sale
    *   3 -> User (Admin/Store Clerk/Online Customer): Sale Confirmation/Error, Search Results, Recent Sales

*   **Procurement Management:**
    *   User (Admin/Store Clerk) -> 4: Medicine Name, Quantity (Request), Request ID, Status (Update)
    *   4 -> DS5: Add/Update Procurement Request
    *   DS5 -> 4: Procurement Request Data
    *   4 -> User (Admin/Store Clerk): Confirmation/Error, Procurement Request List

*   **Reporting & Analytics:**
    *   User (Admin/Report Viewer) -> 5: Report Type, Date Range (Optional)
    *   5 -> DS2, DS3, DS4, DS5: Fetch Report Data
    *   5 -> User (Admin/Report Viewer): Generated Report (Table/PDF)

*   **User Administration:**
    *   User (Admin) -> 6: New User Details, User ID, Updated User Details, User ID (for Delete)
    *   6 -> DS1: Add/Update/Delete User
    *   DS1 -> 6: User Data
    *   6 -> User (Admin): Confirmation/Error, User List

*   **Customer Relationship Management:**
    *   User (Admin/Online Customer) -> 7: New Customer Details, Customer ID, Updated Customer Details, Customer ID (for Delete), Customer ID (for History)
    *   7 -> DS4: Add/Update/Delete Customer
    *   DS4 -> 7: Customer Data
    *   7 -> DS3: Fetch Purchase History
    *   DS3 -> 7: Purchase History Data
    *   7 -> User (Admin/Online Customer): Confirmation/Error, Customer List, Purchase History

*   **Audit Logging:**
    *   All Processes (1-7) -> 8: Action Details, User ID, Timestamp
    *   8 -> DS6: Record Audit Log

## 4. User Manual

### Pharmacy Inventory Management System User Manual

This manual provides instructions for using the Pharmacy Inventory Management System.

---

### 1. Getting Started

#### 1.1. Accessing the System

1.  Ensure your XAMPP Apache and MySQL servers are running.
2.  Open your web browser and navigate to: `http://localhost/pharmacy_management_1/public/login.php`

#### 1.2. Login

1.  On the login page, enter your **Username** and **Password**.
2.  Click the **"Login"** button.
3.  If you don't have an account, click **"Sign up now"** to register.

#### 1.3. Registration (Admin Only for Initial Setup)

1.  Navigate to `http://localhost/pharmacy_management_1/public/register.php` (or click "Sign up now" from the login page).
2.  Enter a **Username**, **Password**, and **Confirm Password**.
3.  Select your **Role** from the dropdown (e.g., `admin`, `store_clerk`, `online_customer`, `report_viewer`).
4.  Click **"Submit"**.

---

### 2. Navigation

After logging in, you will see a navigation bar at the top. The available links will depend on your assigned role:

*   **Home:** Returns to the welcome dashboard.
*   **Inventory:** (Admin, Store Clerk) Manage medicine registration and stock.
*   **Sales:** (Admin, Store Clerk, Online Customer) Record sales and view sales history.
*   **Procurement:** (Admin, Store Clerk) Manage purchase requests.
*   **Reports:** (Admin, Report Viewer) Generate various operational reports.
*   **User Management:** (Admin Only) Manage user accounts and roles.
*   **Customer Database:** (Admin, Online Customer) Manage customer information and view purchase history.
*   **Sign Out:** Logs you out of the system.

---

### 3. Modules

#### 3.1. Inventory Management (Admin, Store Clerk)

This module is divided into three sections:

##### 3.1.1. Register New Medicine

Use this section to add new medicine types to the system.

1.  Fill in the **Medicine Name**, **Medicine Code**, **Expiry Date**, and **Location Code**.
2.  Optionally, enter an **Initial Quantity** (defaults to 0 if left blank).
3.  Click **"Register Medicine"**.

##### 3.1.2. Medicine Entry (Add Stock)

Use this section to add stock to existing medicines.

1.  In the "Search Medicines" bar, enter a medicine **name** or **code** and click **"Search"**.
2.  From the search results, click the **"Add Stock"** button next to the desired medicine.
3.  A modal will appear. Enter the **Quantity to Add**.
4.  Click **"Add Stock"** in the modal.

##### 3.1.3. Current Inventory Overview

This table displays all medicines currently in the system.

*   **View:** Click the **"View"** button to see medicine details in a read-only modal.
*   **Edit:** Click the **"Edit"** button to open a modal where you can update the medicine's name, code, quantity, expiry date, and location code. Click **"Save changes"** to apply.
*   **Delete:** Click the **"Delete"** button to remove a medicine from the system. A confirmation prompt will appear.

#### 3.2. Sales System (Admin, Store Clerk, Online Customer)

##### 3.2.1. Search Medicines

1.  Enter a medicine **name** or **code** in the search bar and click **"Search"**.
2.  The results will show available medicines and their quantities.

##### 3.2.2. Record New Sale

1.  From the "Search Medicines" results, click **"Add to Sale"** next to the medicine you wish to sell. This will pre-fill the medicine details in the "Record New Sale" form.
2.  Enter the **Quantity** being sold.
3.  Enter the **Total Price** of the sale.
4.  Optionally, enter the **Customer ID** if the sale is linked to a registered customer.
5.  Click **"Record Sale"**.

##### 3.2.3. Recent Sales

This table displays a list of recently recorded sales.

#### 3.3. Procurement Management (Admin, Store Clerk)

##### 3.3.1. Request New Purchase

1.  Enter the **Medicine Name** and **Quantity** for the purchase request.
2.  Click **"Submit Request"**.

##### 3.3.2. All Procurement Requests

This table lists all submitted procurement requests.

*   **Update Status:** (Admin, Store Clerk) Use the dropdown in the "Actions" column to change the status of a request (Pending, Approved, Rejected).

#### 3.4. Reports (Admin, Report Viewer)

1.  Select a **Report Type** from the dropdown:
    *   **Stock Report:** Shows current inventory levels.
    *   **Expiry Report:** Shows medicines expiring within a date range or already expired.
    *   **Sales Report:** Shows sales data within a specified date range.
    *   **Procurement Report:** Shows procurement requests within a specified date range.
    *   **Customer Report:** Shows a list of all registered customers.
2.  For "Expiry", "Sales", and "Procurement" reports, select a **Start Date** and **End Date**.
3.  Click **"Generate Report"**.
4.  The report data will be displayed in a table.

#### 3.5. User Management (Admin Only)

##### 3.5.1. Add New User

1.  Enter a **Username**, **Password**, and **Confirm Password**.
2.  Select the user's **Role**.
3.  Click **"Add User"**.

##### 3.5.2. All Users

This table displays all registered users.

*   **Edit:** Click the **"Edit"** button to open a modal where you can update a user's username, role, or set a new password. Click **"Save changes"** to apply.
*   **Delete:** Click the **"Delete"** button to remove a user account. A confirmation prompt will appear.

#### 3.6. Customer Database (Admin, Online Customer)

##### 3.6.1. Add New Customer

1.  Enter the **Customer Name** (required).
2.  Optionally, enter their **Phone** number and **Email** address.
3.  Click **"Add Customer"**.

##### 3.6.2. All Customers

This table displays all registered customers.

*   **Edit:** Click the **"Edit"** button to open a modal where you can update a customer's name, phone, and email. Click **"Save changes"** to apply.
*   **Delete:** Click the **"Delete"** button to remove a customer record. A confirmation prompt will appear.
*   **History:** Click the **"History"** button to view a modal displaying the customer's past purchase history.

---

### 4. Troubleshooting

*   **"Database and tables created successfully!" but still errors:** Ensure your XAMPP MySQL server is running. If errors persist, contact support.
*   **"Fatal error: Uncaught mysqli_sql_exception: Table '...' doesn't exist":** This indicates a database issue. Re-run `http://localhost/pharmacy_management_1/database/setup.php` in your browser.
*   **Login issues:** Double-check your username and password. If you forgot your password, an admin can reset it via User Management.
*   **Access Denied:** Ensure your user role has the necessary permissions for the module you are trying to access.

---
