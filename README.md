# Inventory
This project is a web-based Inventory Management System designed to manage items, SKUs, suppliers, stock, and related data efficiently. It focuses on accurate product variant handling (color, size, SKU), data validation, and Excel-based import/export workflows.

🔧 Core Features

User Authentication

Secure login system

Role-based access control (admin / system user)

Dashboard

Overview of inventory status

Quick access to key modules

Product Management

Create, update, and delete products

Support for multiple variants per product (Color, Size, Admin No, SKU)

Prevents duplicate variants during update

SKU generation logic handled in controller level

SKU Management

Each SKU is linked to a valid Item_Code

SQL Server MERGE logic used for insert/update

Foreign key integrity enforced between Item and SKU tables

Supplier Management

Manage supplier master data

Link suppliers to inventory items

Inventory & Stock Control

Stock quantity validation (integer-based)

Byte-length validation for text fields (supports Unicode)

Prevents invalid or oversized data before saving to database

Excel Import / Export

Import items and SKUs from Excel files

Row-level validation before database insert

Handles duplicate detection and foreign key checks

Export SKUs based on item code search (even when filtered by name)

Search, Filter & Pagination

Client-side search with comma-separated keywords

Pagination with proper scope separation

Search state preserved during export actions

Activity Log & Reports

Tracks system user actions

Generates inventory-related reports

🛠 Tech Stack

Backend: Laravel (PHP)

Database: SQL Server

Frontend: HTML, CSS, JavaScript

Excel Handling: SheetJS (XLSX)

Validation: Custom JavaScript + Server-side validation

Architecture: Controller → Stored Procedure → Database

🎯 Project Goals

Maintain data integrity between Item, SKU, and Inventory tables

Avoid duplicate data during updates and imports

Support real-world inventory workflows with variants

Provide a stable base for future expansion (reports, audit, automation)
