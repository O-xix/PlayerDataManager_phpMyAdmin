# Player Data Manager – Database Systems Project (CSS 475)

This repository contains the design and implementation of the **Player Data Manager**, a conceptual database application created in a 5-person team for **CSS 475 (Database Systems)** at the University of Washington Bothell.  
It is also currently deployed on the University's servers at https://students.washington.edu/snl9/index.php.

## 📖 Project Overview
The **Player Data Manager** is a relational database designed to track and analyze **character builds, items, and game statistics** across different games. While our sample dataset focused on **Destiny** and **Destiny 2**, the system was designed to be extensible to other games.  

The project highlights how a database system can serve multiple stakeholders:  
- **Players**: track and compare character builds  
- **Game Studios**: identify trends, usage rates, and underused items  
- **Producers/Executives**: analyze growth opportunities and market insights  

## ⚙️ Technologies Used
- **MySQL** – relational database management system  
- **PHP** – web interface for database interaction  
- **SQL** – CRUD operations, advanced queries, normalization  
- **Linux (Vergil server)** – deployment environment  

## 📝 Features
- **Relational Schema (3NF)** for accounts, games, builds, characters, stats, and items  
- **CRUD Operations**: insert, update, delete, and view records  
- **Analytical Queries**:
  - Player-focused (e.g., *What are the most popular builds?*)  
  - Studio-focused (e.g., *What items are least used in a game?*)  
  - Producer-focused (e.g., *What price tier has the most accounts?*)  
- **Security**: implemented prepared statements and parameterized queries to prevent SQL injection  

## 🔑 Key Skills Demonstrated
- Database design and **schema normalization (1NF → 3NF)**  
- Writing **complex SQL queries** for natural language use cases  
- Secure coding practices for **SQL injection prevention**  
- Web integration with PHP for a database-backed GUI  
- Collaborative teamwork and technical documentation  

## 📌 Note
This was a **class project** — the emphasis was on **database design, SQL, and secure application development**. The project demonstrates database programming concepts, not a production-ready system.  
