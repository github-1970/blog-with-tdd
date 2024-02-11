**Simple Laravel Blog Project for Testing Practice**

This project is a basic blogging platform designed for practicing unit testing, utilizing the Laravel framework. The README provides insights into the project's purpose, test results, and setup instructions.

**Test Results:**

The following tests have been executed:

- **Database Connection Test:** Passed
  - Connection is established successfully.

- **HTTP Authentication Controller Test:** Passed
  - Register functionality.
  - Validation for registration.
  - Login functionality.
  - Validation for login.

- **Post Comment Controller Test:** Passed
  - Post comment creation.
  - Post comment update.
  - Restricted user access to update comments.
  - Post comment deletion.
  - Restricted user access to delete comments.

- **Post Controller Test:** Passed
  - Listing posts.
  - Displaying a single post.
  - Creating a new post.
  - Updating a post.
  - Restricted user access to update posts.
  - Deleting a post.
  - Restricted user access to delete posts.

- **Login Alert Mail Test:** Passed
  - Mailable content test.

- **Send Email Test:** Passed
  - Email sending test on login.

- **Login Alert Notification Test:** Passed
  - Notification sending test on login.

- **Category Model Test:** Passed
  - Category selection.
  - Category insertion.
  - Category update.
  - Category deletion.
  - Category restoration.
  - Category-post relationship.
  - Category hierarchy.

- **Comment Model Test:** Passed
  - Comment selection.
  - Comment insertion.
  - Comment update.
  - Comment deletion.
  - Comment restoration.
  - Comment-user relationship.
  - Comment-post relationship.

- **Post Model Test:** Passed
  - Post selection.
  - Post insertion.
  - Post update.
  - Post deletion.
  - Post restoration.
  - Post-user relationship.
  - Post-category relationship.
  - Post-comment relationship.
  - Post-tag relationship.

- **Tag Model Test:** Passed
  - Tag selection.
  - Tag insertion.
  - Tag update.
  - Tag deletion.
  - Tag restoration.
  - Tag-post relationship.

- **User Model Test:** Passed
  - User selection.
  - User insertion.
  - User update.
  - User deletion.
  - Admin user identification.
  - User-post relationship.
  - User-comment relationship.
  - User access to comments via posts.

- **Home View Test:** Passed
  - Accessing home view.
  - Rendering admin panel link for admin users.

- **Single Post View Test:** Passed
  - Accessing single post view.
  - Rendering comment form for logged-in users.

**Summary:**
- Total Tests: 71
- Passed: 71
- Time: 44.93s

**Setup Instructions:**

1. **Clone the Repository:**
   ```
   git clone https://github.com/github-1970/blog-with-tdd
   ```

2. **Install Dependencies:**
   ```
   composer install
   npm install
   ```

3. **Database Setup:**
   - Create a new database for the project.
   - Copy the `.env.example` file to `.env.testing` and configure the database connection settings accordingly.
   ```
   cp .env.example .env.testing
   ```

4. **Generate Application Key:**
   ```
   php artisan key:generate
   ```

5. **Run Migrations and Seeders:**
   ```
   php artisan migrate --env=testing
   ```

6. **Run Tests:**
   ```
   php artisan test --testsuite=Feature --stop-on-failure
   ```

By following these instructions, you can set up the project locally, run tests to ensure its functionality, and start exploring and developing further based on your testing practice needs.
