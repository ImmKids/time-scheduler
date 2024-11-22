
# **Database Schema**

## **Users Table**
Stores information about the users.

| Column    | Data Type        | Constraints                  | Description                   |
|-----------|------------------|------------------------------|-------------------------------|
| `id`      | `INT`            | Primary Key, Auto Increment  | Unique identifier for a user.|
| `name`    | `VARCHAR(255)`   | NOT NULL                    | Name of the user.            |
| `email`   | `VARCHAR(255)`   | UNIQUE, NOT NULL             | Email address of the user.   |

### **SQL for Users Table**
```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL
);
```

---

## **Weekly Availability Table**
Stores the weekly availability of users.

| Column         | Data Type      | Constraints                  | Description                                   |
|----------------|----------------|------------------------------|-----------------------------------------------|
| `id`           | `INT`          | Primary Key, Auto Increment  | Unique identifier for availability entry.     |
| `user_id`      | `INT`          | Foreign Key (users.id) NOT NULL | Links to the user in the `users` table.       |
| `day_of_week`  | `TINYINT`      | NOT NULL                    | Day of the week (0 = Sunday, 6 = Saturday).  |
| `start_time`   | `TIME`         | NOT NULL                    | Start time of availability (24-hour format). |
| `end_time`     | `TIME`         | NOT NULL                    | End time of availability (24-hour format).   |

### **SQL for Weekly Availability Table**
```sql
CREATE TABLE weekly_availability (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    day_of_week TINYINT NOT NULL, -- 0 for Sunday, 1 for Monday, ..., 6 for Saturday
    start_time TIME NOT NULL,     -- Start time of availability
    end_time TIME NOT NULL,       -- End time of availability
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

---

### **Relationships**
- The `weekly_availability.user_id` column references the `users.id` column, establishing a one-to-many relationship where one user can have multiple availability entries.
