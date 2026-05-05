# 💼 Shaghalny – Student Job Marketplace Platform

A full-stack web-based job marketplace designed to connect students with short-term and part-time job opportunities.  
The platform allows users to post and apply for jobs, communicate, interact through a community feed, and manage their profiles, while administrators oversee system operations.

---

## 📷 Demo
<img width="1864" height="776" alt="Screenshot 2026-05-05 034245" src="https://github.com/user-attachments/assets/cddf3207-8104-4c60-9af3-ce202c1bd0c4" />
<img width="1880" height="724" alt="Screenshot 2026-05-05 034506" src="https://github.com/user-attachments/assets/61585a2e-8b89-43e1-a251-f00b3cfff721" />
<img width="1874" height="761" alt="Screenshot 2026-05-05 034535" src="https://github.com/user-attachments/assets/9fb0a018-f8bb-4614-8e9c-a15dff6ff087" />
<img width="1886" height="775" alt="Screenshot 2026-05-05 034626" src="https://github.com/user-attachments/assets/1afeebe3-1d28-4f8c-b8bf-599022d9d077" />
<img width="1890" height="790" alt="Screenshot 2026-05-05 034214" src="https://github.com/user-attachments/assets/70f47c27-d3b2-4272-a814-00ffbfd4bc03" />




▶️ Watch project demo:  
[Google Drive Video Link](https://drive.google.com/file/d/15WGMX0N6l2tZ86XG2gI6wNGbWozGOz-g/view?usp=sharing)



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
