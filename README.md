# DBMS_Final_112

# **THSR Train Search System**

> **NTUIM 112-1 Database Management Demo**
> 

## File Structure

- **`README`**: Provides setup instructions and information about the project.
- **`index.php`**: The home page of the THSR Train Search System that links to the user and admin interfaces.
- **`composer.json`**: Defines the project's PHP dependencies and other metadata.
- **`composer.lock`**: Lock file to record the exact versions of dependencies installed.
- **`eloquent.php`**: Sets up the Eloquent ORM configuration and initializes the database connection.
- **`admin_ORM.php`**: Administrator interface for the ORM-based search.
- **`admin.php`**: Administrator interface for the SQL-based search.
- **`user_ORM.php`**: User interface for the ORM-based search.
- **`user.php`**: User interface for the SQL-based search.
- **`style.css`**: Contains the CSS styles for the project's frontend.







## **Installation and Setup**

### **Step 1: System Requirements** 

- PHP 7.4+
- XAMPP
- Composer
- PostgreSQL

Please go through the following steps to set up your environment

### **Step 2: XAMPP Installation**

Download and install XAMPP from the [official website](https://www.apachefriends.org/index.html). If you do not plan to use MySQL, you may unselect it. 

### **Step 3: Composer Installation**

Install Composer from the [official website](https://getcomposer.org/download/).

### **Step 4: Project Deployment**

Unzip the provided project archive into the **`htdocs`** directory of XAMPP.

### **Step 5: Composer Dependencies**

Open your system console, navigate to the project folder, and execute **`composer install`** to install the required PHP packages, including Eloquent ORM. 

### **Step 6: Database Configuration** (skip this step if you have done so before)

Create a PostgreSQL database named **`NTUsed_1218`**. Import the provided **`.sql`** file to populate your database with the necessary tables and data.

### **Step 7: Eloquent Configuration**

Configure the database connection in **`eloquent.php`** with your PostgreSQL credentials. Put your password in **`db_password.txt`**. 

### **Step 8: PHP Connection Settings**

Modify the database connection settings in **`user.php`** and **`admin.php`** files to match your PostgreSQL credentials. Put your password in **`db_password.txt`**. 

### **Step 9: Installing PostgreSQL driver for PHP**

Go to your PHP directory (e.g., at **`C:\xampp\php`**) to edit php.ini using any plain text editor. Uncomment **`;extension=pdo_pgsql`** and **`;extension=pgsql'** by removing the semicolons. 









## **Running the Application**

### **Starting Apache**

After installation, start the Apache web server. To start Apache, go to the right directory (e.g., C:\xampp\apache\bin) to execute httpd.exe. If everything goes well, you may see the Apache homepage at **`http://localhost/`**.  

### **See the index page**

Access the system via **`http://localhost/your-project-folder/`** in your web browser.

### **User Interface**

Navigate to **`Register.php`** for the User Search interface.
Navigate to **`Login.php`**

### **Admin Interface**
Construct the following query in SQL：INSERT INTO admin (admin_id, member_id) VALUES ('最新編號', '自己的id')
You can login and navigate to 個人資料 in footer，then you can see the link of admin functions.


### **Database Performance (Optional)**

To improve performance, create indexes on the **`reserved_ticket`** and **`non_reserved_ticket`** tables for the **`trip_id`**, **`depart_station_id`**, **`arrive_station_id`**, and **`travel_date`** columns.

## **Styles and Aesthetics**

Update **`style.css`** to refine the aesthetics of the index menu and add the subtitle "NTUIM 112-1 Database Management" to the interface.
