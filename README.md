# Pharmacy Inventory Management System: Final Documentation

## ABSTRACT

This document outlines the design and analysis of a comprehensive Pharmacy Inventory Management System. The system aims to streamline pharmacy operations by providing robust functionalities for inventory control, sales processing, procurement management, reporting, user administration, and customer relationship management. Developed using PHP, MySQL, HTML, and Bootstrap, the web-based application is designed to be responsive and secure, adhering to industry standards for data integrity and user access control. This documentation covers the system's requirements, architectural design, implementation details, and testing strategies, providing a complete overview from conception to deployment.

## INTRODUCTION

The modern pharmacy environment demands efficient and accurate management of a vast array of medicines and related products. Manual or outdated inventory systems often lead to significant challenges such as stockouts, expired goods, inefficient procurement, and difficulties in tracking sales and customer data. This project addresses these issues by proposing and developing a web-based Pharmacy Inventory Management System. The system is designed to automate critical processes, enhance data accuracy, improve operational efficiency, and provide valuable insights through comprehensive reporting, ultimately contributing to better patient care and business profitability.

## PROJECT AIMS AND OBJECTIVES

**Project Aim:** To create a comprehensive, secure, and user-friendly Pharmacy Inventory Management System that automates core pharmacy operations, improves efficiency, and ensures compliance with industry standards.

**Objectives:**

*   To develop a web-based, responsive application using PHP, MySQL, HTML, and Bootstrap.
*   To implement robust inventory management features including medicine receipt, storage with physical location codes, and expiry tracking.
*   To build a versatile sales system encompassing both manual POS functionality with search capabilities and a foundation for an online store with Bangladesh payment gateway integration.
*   To establish an efficient procurement module for managing store requests and future purchases.
*   To create a dynamic reporting system capable of generating PDF reports for stock, expiry, sales, and procurement with customizable date filters.
*   To design and implement a multi-role user management system (admin, store clerk, online customer, report viewer).
*   To develop a customer database for storing phone/email and tracking purchase history.
*   To ensure industrial-level security, including audit trails and role-based access control.
*   To adhere to GMP and DSCSA compliance standards in system design and functionality.

## BACKGROUND OF PROJECT

The pharmaceutical sector in Bangladesh is rapidly growing, yet many pharmacies, especially smaller to medium-sized ones, still rely on traditional, often manual, methods for inventory and sales management. This leads to several critical problems:

*   **Inefficient Stock Management:** Difficulty in tracking exact stock levels, leading to frequent stockouts of essential medicines or overstocking of less popular ones, resulting in financial losses.
*   **Expiry Management Challenges:** Manual tracking of expiry dates is prone to errors, leading to the sale of expired medicines or significant wastage of near-expiry products.
*   **Slow Sales Process:** Manual POS systems can be slow, leading to longer customer waiting times and reduced customer satisfaction.
*   **Lack of Data Insights:** Absence of automated reporting makes it difficult for pharmacy owners to analyze sales trends, procurement needs, and overall business performance.
*   **Security and Compliance Risks:** Manual systems lack robust security features and audit trails, making it challenging to comply with regulatory standards like GMP and DSCSA, and increasing the risk of fraud or errors.
*   **Poor Customer Relationship Management:** Without a centralized customer database, personalized services and targeted marketing are difficult to implement.

This project aims to address these challenges by providing a modern, efficient, and secure digital solution tailored to the needs of the Bangladesh pharmacy market.

## OPERATION ENVIRONMENT

### Hardware Requirements

*   **Server:** A standard computer capable of running XAMPP (Windows, Linux, macOS compatible). Minimum 4GB RAM, Dual-core processor, 100GB HDD recommended.
*   **Client:** Any device with a modern web browser (PC, Laptop, Tablet, Smartphone).

### Software Requirements

*   **Operating System:** Windows (e.g., Windows 10/11), Linux, or macOS.
*   **Web Server Environment:** XAMPP (Apache, MySQL, PHP).
    *   **Apache:** Web server.
    *   **MySQL:** Database server.
    *   **PHP:** Server-side scripting language (PHP 7.4 or higher recommended).
*   **Web Browser:** Google Chrome, Mozilla Firefox, Microsoft Edge, Safari (latest versions).
*   **Development Tools:** Text editor/IDE (e.g., VS Code).

## SYSTEM ANALYSIS

### Functional Requirements

1.  **User Management:**
    *   The system shall allow users to register with a username, password, and assigned role.
    *   The system shall support multiple user roles: Admin, Store Clerk, Online Customer, Report Viewer.
    *   The system shall allow administrators to add, view, edit, and delete user accounts.
    *   The system shall enforce role-based access control for all modules.
2.  **Inventory Management:**
    *   The system shall allow registration of new medicines with details: name, code, initial quantity, expiry date, and physical location code.
    *   The system shall allow searching for existing medicines by name or code.
    *   The system shall enable adding stock (quantity) to existing medicines.
    *   The system shall allow viewing, editing, and deleting medicine records.
    *   The system shall track medicine quantities and expiry dates.
3.  **Sales System:**
    *   The system shall allow store clerks and admins to record manual sales.
    *   The system shall allow searching for medicines by name or code during sales.
    *   The system shall automatically deduct sold quantities from inventory.
    *   The system shall allow linking sales to customer records.
    *   The system shall display a history of all sales transactions.
4.  **Procurement:**
    *   The system shall allow store clerks and admins to submit requests for medicine purchases (medicine name, quantity).
    *   The system shall track the status of procurement requests (pending, approved, rejected).
    *   The system shall allow authorized users to update the status of procurement requests.
5.  **Reporting:**
    *   The system shall generate a Stock Report (current inventory levels).
    *   The system shall generate an Expiry Report (medicines expiring within a date range or already expired).
    *   The system shall generate a Sales Report (sales data within a specified date range).
    *   The system shall generate a Procurement Request Report (status of requests within a date range).
    *   The system shall generate a Customer Report (list of all registered customers).
    *   Reports shall be viewable in a tabular format and eventually exportable (e.g., PDF).
6.  **Customer Database:**
    *   The system shall allow adding, viewing, editing, and deleting customer records (name, phone, email).
    *   The system shall display the purchase history for individual customers.
7.  **Audit Trails:**
    *   The system shall log all significant user actions (e.g., login, medicine addition/update/deletion, sale recording, request status change).

### Non-Functional Requirements

1.  **Security:**
    *   The system shall implement role-based access control.
    *   User passwords shall be securely hashed.
    *   Transactions involving sensitive data shall be protected (e.g., through HTTPS in a production environment).
    *   Comprehensive audit trails shall be maintained.
2.  **Performance:**
    *   The system shall provide quick response times for common operations (e.g., searching medicines, loading reports).
    *   Database queries shall be optimized for efficiency.
3.  **Usability:**
    *   The user interface shall be intuitive and easy to navigate.
    *   The system shall be web-based and responsive, adapting to various screen sizes (PC, mobile).
    *   Clear error messages and feedback shall be provided to users.
4.  **Reliability:**
    *   The system shall ensure data integrity through proper database design and transaction management (e.g., for sales).
    *   Robust error handling mechanisms shall be in place.
5.  **Maintainability:**
    *   The codebase shall be modular, well-structured, and follow coding standards.
    *   Documentation shall be clear and up-to-date.
6.  **Compliance:**
    *   The system shall be designed with consideration for GMP (Good Manufacturing Practices) and DSCSA (Drug Supply Chain Security Act) standards, particularly in inventory tracking and audit logging.

## SOFTWARE REQUIREMENT SPECIFICATION (SRS)

*(This section formalizes the System Analysis into a detailed specification. Much of the content from the Functional and Non-Functional Requirements above would be expanded here with specific details, use cases, and acceptance criteria. For brevity, we refer to the expanded details from the System Analysis section.)*

### 1. Introduction

1.1. Purpose
1.2. Scope
1.3. Definitions, Acronyms, and Abbreviations
1.4. References
1.5. Overview

### 2. Overall Description

2.1. Product Perspective
2.2. Product Functions
2.3. User Characteristics
2.4. Constraints (e.g., XAMPP environment, PHP/MySQL)
2.5. Assumptions and Dependencies

### 3. Specific Requirements

3.1. External Interface Requirements

3.1.1. User Interfaces (Web-based, Bootstrap)
3.1.2. Hardware Interfaces (Standard PC/Mobile)
3.1.3. Software Interfaces (MySQL, PHP functions)
3.1.4. Communications Interfaces (HTTP/HTTPS)

3.2. Functional Requirements (Detailed as per System Analysis - Functional Requirements)

3.2.1. User Management (FR1)
3.2.2. Inventory Management (FR2)
3.2.3. Sales System (FR3)
3.2.4. Procurement (FR4)
3.2.5. Reporting (FR5)
3.2.6. Customer Database (FR6)
3.2.7. Audit Trails (FR7)

3.3. Non-Functional Requirements (Detailed as per System Analysis - Non-Functional Requirements)

3.3.1. Performance (NFR1)
3.3.2. Security (NFR2)
3.3.3. Usability (NFR3)
3.3.4. Reliability (NFR4)
3.3.5. Maintainability (NFR5)
3.3.6. Compliance (NFR6)

## EXISTING VS PROPOSED

**Existing System (Manual/Semi-Automated):**

*   **Inventory:** Often tracked using physical ledgers, spreadsheets, or basic software. Prone to human error, difficult to get real-time stock, high risk of expiry losses, inefficient stocktaking.
*   **Sales:** Manual entry, slow transaction processing, difficulty in tracking customer purchases, no automated sales analytics.
*   **Procurement:** Ad-hoc ordering, lack of systematic request tracking, potential for duplicate orders or missed opportunities for bulk discounts.
*   **Reporting:** Time-consuming manual compilation of data, often outdated, limited insights for decision-making.
*   **Customer Management:** Fragmented customer data, no centralized purchase history, limited personalized service.
*   **Security/Compliance:** Low security, difficult to maintain audit trails, challenges in meeting regulatory requirements.

**Proposed System (Pharmacy Inventory Management System):**

*   **Inventory:** Real-time digital tracking, automated expiry alerts, efficient stock entry and management, reduced losses from expired or overstocked items.
*   **Sales:** Fast and accurate POS, automatic inventory deduction, easy customer linking, instant sales reports and analytics.
*   **Procurement:** Centralized request system, clear status tracking, improved planning for purchases, better supplier negotiations.
*   **Reporting:** Dynamic, on-demand reports (Stock, Expiry, Sales, Procurement, Customer) with date filters, supporting strategic decision-making.
*   **Customer Management:** Centralized customer database, detailed purchase history, enabling personalized marketing and improved customer loyalty.
*   **Security/Compliance:** Role-based access, password hashing, audit trails for all actions, designed with GMP/DSCSA compliance in mind.

## SOFTWARE TOOL USED

*   **Server-Side Scripting:** PHP (version 7.4+)
*   **Database Management System:** MySQL
*   **Web Server:** Apache (via XAMPP)
*   **Frontend Development:**
    *   HTML5
    *   CSS3
    *   JavaScript (with jQuery library)
    *   Bootstrap 4 (for responsive design and UI components)
*   **Local Development Environment:** XAMPP
*   **Code Editor:** Visual Studio Code (or similar IDE)

## FEASIBILITY STUDY

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

## METHODOLOGY ADOPTED

The project adopted an **Iterative and Incremental Development Methodology**. This approach allowed for continuous feedback and adaptation throughout the development lifecycle. Key characteristics included:

*   **Phased Development:** The system was broken down into core modules (e.g., User Management, Inventory, Sales, Procurement, Reporting, Customer Database), with each module developed and integrated in iterations.
*   **Early Feedback:** After each significant module or feature was implemented, it was presented for review and testing, allowing for early identification and resolution of issues.
*   **Flexibility:** The iterative nature allowed for adjustments to requirements and design based on new insights or changing market needs.
*   **Risk Management:** Risks were addressed incrementally, as each iteration provided a working version of the system, reducing overall project risk.

This methodology was chosen to ensure that the system evolved in response to practical needs and to deliver a functional product efficiently.

## SYSTEM DESIGN

### High-Level Architecture

The Pharmacy Inventory Management System follows a **Client-Server, 3-Tier Architecture**:

1.  **Presentation Tier (Client-Side):**
    *   Consists of HTML, CSS (Bootstrap), and JavaScript (jQuery).
    *   Provides the user interface accessible via web browsers on various devices.
    *   Handles user input and displays information.

2.  **Application Tier (Server-Side Logic):**
    *   Implemented using PHP.
    *   Contains the business logic, processes user requests, interacts with the database, and generates dynamic web content.
    *   Includes classes for Inventory, Sales, Procurement, Report, User, and Customer management.

3.  **Data Tier (Database):**
    *   Managed by MySQL.
    *   Stores all application data (users, medicines, sales, customers, procurement requests, audit logs).
    *   Ensures data integrity, consistency, and provides efficient data retrieval.

### Module Design

Each core module (Inventory, Sales, Procurement, Reporting, User Management, Customer Database) is designed with a clear separation of concerns, encapsulating its specific business logic within dedicated PHP classes (`src/`). These classes interact with the database through `mysqli` prepared statements to ensure security and efficiency. The public-facing pages (`public/`) handle user interaction, form processing, and display of data, utilizing Bootstrap for a consistent and responsive UI.

## TABLE DESIGN

*(Refer to `database/database.sql` for the complete schema. Below is a summary of key tables and their primary purpose.)*

1.  **users**
    *   **Purpose:** Stores user authentication and authorization information.
    *   **Key Columns:** `id` (PK), `username` (UNIQUE), `password` (hashed), `role` (ENUM: admin, store_clerk, online_customer, report_viewer).

2.  **medicines**
    *   **Purpose:** Stores details of all medicines in the inventory.
    *   **Key Columns:** `id` (PK), `name`, `code` (UNIQUE), `quantity`, `expiry_date`, `location_code`.

3.  **customers**
    *   **Purpose:** Stores customer contact information and acts as a reference for sales history.
    *   **Key Columns:** `id` (PK), `name`, `phone`, `email`.

4.  **sales**
    *   **Purpose:** Records individual sales transactions.
    *   **Key Columns:** `id` (PK), `medicine_id` (FK to medicines), `customer_id` (FK to customers, nullable), `quantity`, `total_price`, `sale_date`.

5.  **procurement**
    *   **Purpose:** Stores details of medicine purchase requests.
    *   **Key Columns:** `id` (PK), `medicine_name`, `quantity`, `status` (ENUM: pending, approved, rejected), `request_date`.

6.  **audit_log**
    *   **Purpose:** Records significant actions performed within the system for security and compliance.
    *   **Key Columns:** `id` (PK), `user_id` (FK to users), `action`, `log_date`.

## DATA FLOW DIAGRAM’S

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

## SYSTEM DEVELOPMENT

The system development followed an iterative approach, building out core functionalities module by module. Each module involved:

1.  **Database Schema Definition:** Defining or refining necessary tables and relationships in `database.sql`.
2.  **Backend Logic Implementation:** Creating PHP classes in the `src/` directory to encapsulate business logic and database interactions for the module (e.g., `Inventory.php`, `Sales.php`).
3.  **Frontend User Interface Development:** Designing and implementing responsive HTML pages in `public/` using Bootstrap for forms, tables, and navigation.
4.  **Integration:** Connecting frontend forms to backend logic and ensuring data persistence.
5.  **Testing:** Initial manual testing of functionalities, including data input, retrieval, updates, and deletions.
6.  **Refinement:** Addressing bugs, improving usability, and enhancing features based on testing and requirements.

Key development milestones included:

*   Initial project setup and database creation.
*   User authentication and role-based access control.
*   Core Inventory Management (registration, stock entry, overview).
*   Sales System (recording sales, medicine search).
*   Procurement Management (requesting purchases, status updates).
*   Reporting Module (stock, expiry, sales, procurement, customer reports).
*   User Management (CRUD for users).
*   Customer Database (CRUD for customers, purchase history).

## MODULE DESCRIPTION

1.  **User Management:** Allows administrators to create, view, edit, and delete user accounts, assigning specific roles (Admin, Store Clerk, Online Customer, Report Viewer) to control system access.
2.  **Inventory Management:** Facilitates the registration of new medicines with essential details and enables efficient stock entry for existing medicines. It provides a comprehensive overview of all inventory items, including their quantities, expiry dates, and physical locations, with options to view, edit, and delete records.
3.  **Sales System:** Provides a manual Point-of-Sale (POS) interface where authorized users can search for medicines, record sales transactions, and automatically update inventory levels. It also allows linking sales to customer records and viewing a history of all sales.
4.  **Procurement:** Enables authorized personnel to submit and track requests for new medicine purchases. Requests move through statuses (pending, approved, rejected), ensuring a structured procurement process.
5.  **Reporting:** Offers dynamic generation of various reports crucial for business analysis, including Stock Reports (current inventory), Expiry Reports (medicines near or past expiry), Sales Reports (transaction summaries), Procurement Request Reports (status of purchase requests), and Customer Reports (customer details). Reports can be filtered by date ranges.
6.  **Customer Database:** Manages a centralized repository of customer information, including names, contact details, and a detailed history of their purchases, supporting customer relationship management efforts.

## SCREEN SHOTS

*(Note: As an AI, I cannot generate actual screenshots. In a real project documentation, this section would include visual representations of key system interfaces, such as:)*

*   Login Page
*   Welcome Dashboard (with role-based navigation)
*   Inventory Management - Register New Medicine Form
*   Inventory Management - Medicine Entry (Add Stock) Search & Modal
*   Inventory Management - Current Inventory Overview Table
*   Sales System - Search Medicines & Record Sale Form
*   Sales System - Recent Sales Table
*   Procurement Management - Request New Purchase Form
*   Procurement Management - All Procurement Requests Table
*   Reports Page - Stock Report Display
*   Reports Page - Sales Report with Date Filters
*   User Management - Add New User Form
*   User Management - All Users Table & Edit User Modal
*   Customer Database - Add New Customer Form
*   Customer Database - All Customers Table & Purchase History Modal

## SYSTEM TESTING

System testing was conducted throughout the development lifecycle using an iterative approach, focusing on ensuring that all functional and non-functional requirements were met. The testing phases included:

1.  **Unit Testing:** Focused on individual components and methods.
2.  **Integration Testing:** Verified interactions between different modules.
3.  **User Acceptance Testing (UAT):** (Simulated) Ensured the system met user needs and business requirements.

## UNIT TESTING

Unit testing involved verifying the correctness of individual PHP classes and their methods in isolation. For example:

*   **Inventory Class:**
    *   `addMedicine()`: Tested with valid and invalid inputs, ensuring correct insertion and handling of duplicate codes.
    *   `getAllMedicines()`: Verified retrieval of all records.
    *   `getMedicineById()`: Tested with existing and non-existing IDs.
    *   `updateMedicine()`: Verified updates to various fields, including quantity.
    *   `deleteMedicine()`: Tested successful deletion and handling of non-existent records.
*   **Sales Class:**
    *   `recordSale()`: Tested with sufficient and insufficient stock, ensuring transaction integrity (deduction and sale record).
    *   `getAllSales()`: Verified correct retrieval and joins.
*   **User Class:**
    *   `addUser()`: Tested with valid inputs, password hashing, and duplicate usernames.
    *   `updateUser()`: Verified updates to username, role, and optional password changes.

Each method was tested to ensure it performed its intended function correctly and handled edge cases or erroneous inputs gracefully.

## INTEGRATION TESTING

Integration testing focused on verifying the interactions and data flow between different modules of the system. Key integration test scenarios included:

*   **Sales and Inventory:**
    *   Recording a sale for a medicine and verifying that its quantity is correctly deducted in the inventory.
    *   Attempting to record a sale with insufficient stock and verifying the appropriate error message and no inventory change.
*   **User Management and Module Access:**
    *   Logging in with different roles (Admin, Store Clerk, Report Viewer, Online Customer) and verifying that the navigation bar and module access are correctly restricted/granted based on the role.
*   **Procurement and Inventory (Future Integration):**
    *   (Currently, procurement requests do not directly update inventory. Future integration would involve testing the flow from an approved procurement request to an increase in inventory stock.)
*   **Sales and Customer Database:**
    *   Recording a sale linked to an existing customer and verifying that the sale appears in the customer's purchase history.
*   **Reporting and All Modules:**
    *   Generating various reports (Stock, Sales, Procurement, Customer) and verifying that the data accurately reflects the current state of the respective modules.

These tests ensured that the different components of the system worked together seamlessly as a cohesive unit.

## CONCLUSION & FUTURE SCOPE

**Conclusion:**

The Pharmacy Inventory Management System successfully addresses the core requirements outlined in the project plan. It provides a functional, web-based solution for managing inventory, sales, procurement, users, and customers, with robust reporting capabilities. The system is built on a solid foundation of PHP and MySQL, utilizing Bootstrap for a responsive and user-friendly interface. The iterative development approach allowed for continuous refinement and ensured that key functionalities were implemented effectively, laying the groundwork for a comprehensive pharmacy management solution.

**Future Scope:**

To further enhance the system and meet the evolving needs of the Bangladesh market, the following features are recommended for future development:

1.  **Online Store Integration:** Develop a full-fledged online storefront for customers to browse medicines and place orders, integrated with the existing inventory and sales modules.
2.  **Bangladesh Payment Gateway Integration:** Implement direct integration with popular local payment gateways (bKash, Nagad, SSL Commerz) for online sales.
3.  **Advanced Reporting & Analytics:** Introduce more sophisticated reporting features, including graphical representations (charts, graphs), predictive analytics for demand forecasting, and customizable report generation (e.g., PDF export).
4.  **Supplier Management:** A dedicated module to manage supplier information, purchase orders, and payment tracking.
5.  **Barcode Scanning:** Integrate barcode scanning functionality for faster and more accurate medicine entry and sales processing.
6.  **Mobile Application:** Develop native or hybrid mobile applications for store clerks and online customers to enhance accessibility and user experience.
7.  **Notifications and Alerts:** Implement a system for automated notifications for low stock, expiring medicines, and new procurement requests.
8.  **Multi-Branch Support:** Extend the system to support multiple pharmacy branches with centralized inventory and sales management.
9.  **Enhanced Security:** Implement two-factor authentication, more granular permission controls, and regular security audits.

## REFERENCES

*(Refer to `standard_books.txt` for a list of standard books and resources that informed the development of this project.)*

*   **Project Plan & Progress:** `C:\xampp\htdocs\pharmacy_management_1\project_plan_&_progress.md`
*   **Feasibility Study & Documentation:** `C:\xampp\htdocs\pharmacy_management_1\feasibility_and_documentation.md`
*   **Standard Books:** `C:\xampp\htdocs\pharmacy_management_1\standard_books.txt`
*   **Bootstrap Documentation:** `https://getbootstrap.com/docs/4.5/`
*   **PHP Manual:** `https://www.php.net/manual/en/`
*   **MySQL Documentation:** `https://dev.mysql.com/doc/`
*   **OWASP Top 10:** `https://owasp.org/www-project-top-ten/`
