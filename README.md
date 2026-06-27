# Pizza Delivery Website

A full-stack pizza ordering and delivery web application with separate Admin and Customer panels, built using PHP, MySQL, and Bootstrap.

## Features

- User registration, login, and session-based authentication
- Browse menu with live search to quickly find items
- Dynamic shopping cart with real-time updates and order summary
- Checkout with COD (Cash on Delivery) and UPI payment options
- Admin panel for menu management, image upload, and order tracking
- Role-based access — separate views and permissions for Admin and Customer
- Responsive UI built with Bootstrap for a seamless experience across devices
- Optimized MySQL queries (including INNER JOIN-based filtering) for accurate, fast order history retrieval

## Tech Stack

- **Backend:** PHP
- **Database:** MySQL
- **Frontend:** HTML, CSS, Bootstrap, JavaScript
- **Environment:** XAMPP (Apache + MySQL)

## How It Works

1. Customers register/login and browse the pizza menu with live search.
2. Items are added to a dynamic cart that updates in real time.
3. At checkout, customers choose COD or UPI payment.
4. Orders are saved to the database and linked to payment status.
5. The Admin panel displays only successfully paid orders in order history, using an INNER JOIN between the orders and payments tables.
6. Admins can manage the menu, upload item images, and track all incoming orders.

## Setup & Installation

1. Install [XAMPP](https://www.apachefriends.org/) and start the Apache and MySQL modules.
2. Clone the repository into your XAMPP `htdocs` folder:
   ```bash
   cd C:\xampp\htdocs
   git clone https://github.com/Shrinidhi-111/Pizza-Delivery-Website.git
   ```
3. Create a MySQL database (e.g. `pizzahub`) using phpMyAdmin and import the provided SQL file.
4. Update the database connection details in the project's config file to match your local MySQL setup.
5. Open your browser and go to:
   ```
   http://localhost/Pizza-Delivery-Website/
   ```

## Project Structure

```
Pizza-Delivery-Website/
├── admin/             # Admin panel pages
├── user/          # Customer-facing pages
├── config/          # Shared PHP includes (DB connection)
├── assets/            # CSS, JS, images
├── database.sql       # Database schema
└── index.php      #Main entry file
```

## Known Limitations

- Payment integration is simulated (COD/UPI selection only, no real payment gateway).
- Built for local development with XAMPP; not yet configured for production deployment.
