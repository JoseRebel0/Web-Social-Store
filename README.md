#📌 Social Store - Management System

#🌍 About the Project

This PHP-based management system allows administrators, users, and clients to efficiently handle various operations, such as managing content, users, and service requests.

#🚀 Features

🔑 Administrators

🛠 Manage users and clients (add, edit, delete)

📰 Handle articles and content

📋 Manage requests and vouchers

📡 Monitor system status

👤 Users

📝 Create and edit profile

📌 Request services or articles

🔍 View system information

🏥 Clients

🎯 Access specific services

🏷 Update personal information (if allowed)

🛠 How Each Function Works

🔍 check_status.php

Checks if a user is logged in and retrieves their status from the database.

Redirects users with restricted access.

🛠 config.php

Defines database connection settings and initializes a connection.

💳 creditos.php

Ensures the user is logged in before allowing access to credit-related features.

❌ delete-*.php (e.g., delete-user.php, delete-article.php)

Deletes records from respective database tables.

Shows alerts confirming success or failure.

⚠️ Possible Issues & Improvements

📱 Responsiveness: The site may have display issues on mobile devices.

🔒 Security: Some critical pages may need additional protection.

⚡ Optimization: Page loading speed can be improved.

🏗 How to Run the Project

Install a local server (XAMPP, WAMP, etc.).

Place the files in the htdocs folder or equivalent.

Configure the database in config.php.

Access via a web browser.

This document can be updated as new features are added! 🚀

