# 💼 Shaghalny – Student Job Marketplace Platform

A full-stack web-based job marketplace designed to connect students with short-term and part-time job opportunities.  
The platform allows users to post and apply for jobs, communicate, interact through a community feed, and manage their profiles, while administrators oversee system operations.

---

## 📷 Demo

▶️ Watch project demo:  
[Google Drive Video Link](YOUR_LINK_HERE)

---

## 🚀 Features

### 💼 Job Management & Lifecycle System

The platform implements a complete job lifecycle system that manages how jobs move through different states:

- **Job Posting**: Users can create jobs with full details (title, description, payment, location, duration)
- **Available Jobs**: Jobs are visible to all users and open for applications

- **Application System**:
  - Users can apply for jobs
  - Job posters can **accept or reject applicants**
  - Once a candidate is accepted, the job status changes from *Available* → *Pending*

- **Active Job State**:
  - A job becomes **Pending (assigned)** once a worker is selected
  - The job remains locked until completion time

- **Completion Flow**:
  - After the job duration ends, it transitions to **Closed**
  - Payment is processed to the worker
  - Both parties can rate each other

---

### 📊 Personal Job History System
Each user has a full activity dashboard:

- **Posted Jobs** → jobs created by the user
- **Applied Jobs** → jobs the user applied for
- **Working Jobs** → currently accepted / pending jobs
- **Completed Jobs** → finished and rated jobs

Each entry includes:
- Full job details
- Current status (Available / Pending / Closed)
- Application or acceptance history
- Ratings after completion  

---

### 🧑‍💼 Admin Features
- Manage user accounts (view, edit, suspend, delete)  
- Moderate job listings and community content  
- Monitor reports and support requests  
- Control platform features and permissions  
- Generate system activity insights  

---

### 🗺️ System Features
- Interactive job discovery using map integration (Leaflet API)  
- MVC architecture for clean system design  
- REST-like backend structure  
- Secure data handling and validation  
- MySQL database integration  

---

## ⚙️ Tech Stack

- Frontend: HTML, CSS, JavaScript  
- Backend: PHP / MVC structure (based on SRS architecture)  
- Database: MySQL  
- APIs: Leaflet Maps API  
- Architecture: MVC + Repository Pattern + Singleton Pattern  

---

## 🧠 System Design

This project follows a structured software engineering design:

- MVC Architecture for separation of concerns  
- Repository Pattern for database abstraction  
- Singleton Pattern for database connection management  
- Server-side validation for data integrity  
- Modular design for scalability  

---

## 🗺️ Key Modules

- User Account Management  
- Job Management System  
- Messaging System  
- Community Interaction System  
- Support & Reporting System  
- Admin Dashboard  

---

## 🎯 Purpose

This system was designed as a student-oriented job marketplace to:
- Enable flexible job opportunities for students  
- Improve communication between users  
- Provide a structured job application system  
- Maintain platform safety through admin moderation  

---

## 📌 Notes

- Built as a Software Engineering academic project  
- Includes full system design documentation (SDD)  
- Implements real-world software architecture principles 
