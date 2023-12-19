# DBMS_Final_112

# **NTUsed Book transaction platform System**

> **NTUIM 112-1 Database Management**
> 

## File Structure

- **`README`**: Provides setup instructions and information about the project.
- **`register.php`**: The register page of the NTUsed System to regist your member account.
- **`composer.json`**: Defines the project's PHP dependencies and other metadata.
- **`composer.lock`**: Lock file to record the exact versions of dependencies installed.
- **`eloquent.php`**: Sets up the Eloquent ORM configuration and initializes the database connection.
- **`add_to_cart.php`**: Function to use to add the project to shopping_cart.
- **`admin_blocklistinfo.php`**: Record the list of user that are blocked.
- **`admin_info.php`**: Personal page that record the information and can lead to admin function page.
- **`admin_memberinfo.php`**: record all the infomation of the user that admin can see it.
- **`admin_projectinfo.php`**: record all the infomation of the project that admin can see it.
- **`admin_removedlistinfo.php`**: record the project which are reported.
- **`admin_reportaccountinfo.php`**: record the account and reason that of member which are reported.
- **`admin_reportprojectinfo.php`**: record the project and reason of which are reported.
- **`admin_transactioninfo.php`**: record the transaction infomation.
- **`db_password.txt`**: password
- **`footer.php`**: Footer of main page that links to other pages.
- **`forget_password.php`**: forget password page
- **`send_success.php`**: forget password and send the email to you.
- **`login.php`**: login page
- **`main_page.php`**: main page to present project list
- **`member_info.php`**: about your personal info if you are not admin
- **`admin_info.php`**: about your personal info if you are admin
- **`merchandise.php`**: detail of one project info
- **`self_market.php`**: all the projects which you are selling, sold, and in transaction progress.
- **`other_market.php`**: all the projects which other people are selling.
- **`remove_from_cart.php`**: remove the project in your shopping_cart function.
- **`reporting_account.php`**: report other seller.
- **`reporting_project.php`**: report other project.
- **`shopping_cart.php`**: record the project you added in your shopping cart
- **`transaction_record.php`**: record your personal transaction_record
- **`update_project_status.php`**: update the project status in admin function of **`admin_projectinfo.php`**
- **`update_status.php`**: update the member status in admin function of **`admin_memberinfo.php`**

- 
- **`style.css`**: Contains the CSS styles for the project's frontend.
- **`admin_info.css`**: Contains the CSS styles for the **admin_info.php** frontend.
- **`footer.css`**: Contains the CSS styles for the **footer.php** frontend in main page.
- **`login.css`**: Contains the CSS styles for the **login.php** frontend in main page.
- **`member_info.css`**: Contains the CSS styles for the **member_info.php** frontend in main page.
- **`admin_info.css`**: Contains the CSS styles for the **admin_info.php** frontend in main page.
- **`merchandise.css`**: Contains the CSS styles for the **merchandise.php** frontend in main page.
- **`register.css`**: Contains the CSS styles for the **register.php** frontend in main page.
- **`shopping_cart.css`**: Contains the CSS styles for the **shopping_cart.php** frontend in main page.
- **`transaction_record.css`**: Contains the CSS styles for the **transaction_record.php** frontend in main page.





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
After you sign in, you can use the **`footer.php`** to navigate to main page, shopping cart page, transaction_record page, and personal info page.
You can click project link to merchandise page, and get to other's market by clicking their name.

### **Admin Interface**
Construct the following query in SQL：**INSERT INTO admin (admin_id, member_id) VALUES ('最新編號', '自己的id')**
You can login and navigate to 個人資料 in  **`footer.php`**，then you can see the link of admin functions.


### **Database Performance (Optional)**

To improve performance, create indexes on the 


