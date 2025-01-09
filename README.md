# Note App

A modern note-taking application that enables users to create, edit, update, and delete notes. Additionally, users can securely share notes with other registered users using their email addresses, with customizable access permissions.

---

## Features

### User Management
- **User Registration**: Create an account to use the application.
- **Profile Picture (Optional)**: Upload a profile picture during registration or update it later.

### Note Management
- **Create Notes**: Add new notes with a user-friendly editor.
- **Edit Notes**: Modify existing notes anytime.
- **Delete Notes**: Remove notes you no longer need.
- **Update Notes**: Keep your notes up to date with changes.

### Note Sharing
- **Share Notes**: Share notes with specific registered users using their email addresses.
- **Access Permissions**:
  - **Read-Only**: Allow the recipient to view the note but not make changes.
  - **Read and Write**: Grant the recipient permission to both view and edit the note.

---

## Getting Started

### Prerequisites
- A server environment with PHP installed.
- MySQL database setup.
- A web browser to access the application.

### Installation
1. Clone the repository:
   ```bash
   git clone https://github.com/the-bsx/NoteMe.git
   ```
2. Navigate to the project directory:
   ```bash
   cd note-app
   ```
3. Import the database:
   - Open your MySQL client.
   - Import the provided SQL file:
     ```sql
     NoteMe/sql/notes.sql;
     ```
4. Configure the database connection:
   - Update the `config.php` file with your database credentials.

### Running the Application
1. Start your local server (e.g., using XAMPP, WAMP, or a similar tool).
2. Place the project folder in your server’s root directory (e.g., `htdocs` for XAMPP).
3. Access the application in your browser:
   ```
   http://localhost/note-app
   ```

---

## Usage

### Register an Account
1. Sign up with your email and a password.
2. (Optional) Upload a profile picture.

### Create and Manage Notes
- After logging in, use the dashboard to create, edit, update, or delete notes.

### Share Notes
1. Select the note you want to share.
2. Enter the email address of the registered user you want to share the note with.
3. Set the access permissions (Read-Only or Read and Write).
4. Share the note securely.

---

## Technologies Used
- **Frontend**: HTML, CSS
- **Backend**: PHP
- **Database**: MySQL
- **Authentication**: Custom session-based authentication
- **File Uploads**: PHP file handling (for profile picture upload)

---

## Contributing
Contributions are welcome! Please follow the steps below:
1. Fork the repository.
2. Create a new branch:
   ```bash
   git checkout -b feature-name
   ```
3. Commit your changes:
   ```bash
   git commit -m "Add new feature"
   ```
4. Push to your branch:
   ```bash
   git push origin feature-name
   ```
5. Create a pull request.

---

## License
This project is licensed under the MIT License. See the LICENSE file for details.

---

## Contact
For any questions or suggestions, feel free to contact us at **www.github.com/the-bsx**.

