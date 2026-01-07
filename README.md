# Job Recruitment Management System – Candidate Portal & Admin Dashboard
## Overview
A full-stack PHP web application designed to streamline the recruitment process. This project features a custom-built job board and candidate management system seamlessly integrated into the Joomla CMS using an Iframe wrapper architecture.

## Tech Stack 
Frontend: HTML5, CSS3 (Joomla Cassiopeia Template), JavaScript.  
Backend: PHP 8.2 (Object-Oriented via PDO).  
Database: MySQL / MariaDB (managed via XAMPP/phpMyAdmin).  
Tools: PHPMailer for SMTP services, .env for secure credential management.  

## How it works
The Job Recruitment System operates by integrating a custom PHP backend into a Joomla CMS framework using an Iframe wrapper to maintain a consistent user experience.   
The workflow begins with a secure authentication process where user credentials entered in login_page.php are validated against a MySQL database.   
Once logged in, candidates access a dynamic dashboard that retrieves active listings from the jobs table, while the apply.php script manages the submission of personal data and file uploads.  
The system ensures data integrity by recording applications in the job_applications table before utilizing PHPMailer and SMTP to dispatch automated confirmation emails and PDF summaries to the applicants.  

**Data Flow:** 
<img width="665" height="390" alt="{6B196DF5-AF93-48FB-AE95-20D8551A09F9}" src="https://github.com/user-attachments/assets/e1898f2e-e5df-43d7-ab6a-e3e8700f813c" />

## Key Features
-> Joomla Integration: Hosted within a Joomla environment to leverage CMS navigation and template consistency.  

-> Candidate Dashboard: A secure area for applicants to view active job openings fetched dynamically from a MySQL database.  

-> Automated Applications: Support for multi-part forms, including file uploads for signatures and photographs.  

-> Email Confirmations: Integrated with PHPMailer and SMTP to provide instant PDF application summaries to candidates upon submission.  

-> Admin Control: A dedicated administrative interface for posting new job openings and managing applicant data.  

## Performance 
<img width="1211" height="597" alt="{4D002BEA-EE38-4EB7-8D5F-62F76AC2DD97}" src="https://github.com/user-attachments/assets/090abc00-d716-4ec4-9723-91ec75bdfdc1" />  

~ Initial Document Load: 549ms for the main job portal entry point.  
~ Total Page Load Time: 939ms (Total time until all resources are fully rendered).  
~ DOM Content Loaded: 710ms, providing a fast "Time to Interactive" for users.  
~ Resource Efficiency: Uses browser caching (Status 304) for static assets like icons to reduce server load.  

## Installation (Local)
--> Clone the repository into your XAMPP htdocs folder.  

--> Import the job_portal.sql file into phpMyAdmin.  

--> Configure your database credentials and SMTP settings in the .env file.  

--> Access the portal via http://localhost/Your_Project_Folder.  

## Experience 
I chose to use fundamental technologies for this recruitment portal during my project tenure at CSIR to ensure a deep understanding of every system component. To maintain full control over the data flow, I avoided heavy frameworks; instead, I utilized PHP’s standard library and PDO for database operations, and PHPMailer as the primary external library for SMTP integration. By prioritizing a "clean-code" approach with standard PHP and MySQL, I created a lightweight, scalable architecture that can be easily expanded or migrated in the future.Although the portal successfully achieves the goal of managing job listings and candidate applications within the Joomla environment, there are several enhancements I plan to implement:  
Dynamic Configuration: Currently, environment variables are managed through a .env file. I would like to build a dedicated Admin Settings GUI that allows users to update SMTP and Database credentials directly from the browser without touching the code.  
Algorithmic Filtering: I aim to experiment with different candidate ranking algorithms.   
Moving from a simple chronological list to a relevancy-based "Match Score" (comparing candidate skills to job descriptions) would be an ideal next step.  
Enhanced Error Handing: While the system is stable, adding more robust validation for user-inputted URLs and file formats would prevent edge-case crashes and improve the overall security posture.  
Below is a summary of the pros and cons of my current Job Application Portal    

## Pros
High Performance: Minimal overhead due to standard library usage (939ms load time).  
Seamless Integration: Operates perfectly within a Joomla Iframe wrapper.  
Data Integrity: Reliable SQL transactions ensure no application data is lost.  

## Cons 
Manual Setup: Requires manual configuration of SMTP keys in the backend.  
Basic UI: The frontend relies on standard CSS rather than complex reactive frameworks.  
Local Constraints: Currently optimized for XAMPP rather than cloud-native scaling.
