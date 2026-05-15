# Planco Cloud (Backend) 
<img width="200" height="200" alt="image" src="https://github.com/user-attachments/assets/20afdb34-3846-465b-899d-d5a00fcc7e1d" />

| Metadata | Information |
| --- | --- |
| **Organization** | Planco |
| **Project Status** | 🔴 Alpha |
| **Primary Owner** | @TobiasClausen |
| **Primary Tech Stack** | Python, Postgres |
| **CI/CD Status** | NOT SETUP |

---

## Purpose

This repository contains the main backend service for the Planco platform. It provides core 
API endpoints, user management, authentication, database operations, and machine learning 
capabilities. This backend serves as the central hub for all Planco applications and 
handles business logic, data persistence, and intelligent features.

## Description

Planco Cloud Backend is a Python-based REST API built with modern web frameworks and 
PostgreSQL database. It manages user accounts, authentication, authorization, and 
provides machine learning functionalities for predictive analytics and intelligent features. 
The backend is designed to be scalable, maintainable, and secure, serving multiple client 
applications (web, mobile, etc.).

### Key Components

* **User Management:** Registration, authentication, profiles, roles, and permissions
* **Authentication & Authorization:** Token-based auth, session management, RBAC
* **Database:** PostgreSQL with ORM layer for data persistence
* **Machine Learning:** ML models for predictions, analytics, and intelligent features
* **API Layer:** RESTful endpoints for client applications
* **Background Jobs:** Async task processing and scheduled jobs

## Prerequisites

Before you begin, ensure you have the following installed:

* Python 3.9+ 
* PostgreSQL 12+
* pip (Python package manager)
* virtualenv or conda
* Git
* Access to Planco's internal infrastructure

## Installation & Setup

Step-by-step instructions to get the development environment running.

```bash
# Clone the repository
git clone git@github.com:PlancoCH/planco-cloud.git

# Navigate to project directory
cd planco-cloud

# Create virtual environment
python -m venv venv

# Activate virtual environment
# On Windows:
venv\Scripts\activate
# On macOS/Linux:
source venv/bin/activate

# Install dependencies
pip install -r requirements.txt

# Set up environment variables
cp .env.example .env

# Initialize database
python manage.py db upgrade

# Load initial data (optional)
python manage.py seed

```

## Configuration

Create a `.env` file in the project root with the following variables:

```
DATABASE_URL=postgresql://user:password@localhost:5432/planco
SECRET_KEY=your-secret-key-here
FLASK_ENV=development
DEBUG=True
ML_MODEL_PATH=./models
```

## Usage

Examples of how to run the project locally or interact with the API.

```bash
# Start development server
python -m flask run

# Or with Flask CLI
flask run

# Access API at http://localhost:5000

# Run background workers
celery -A app.celery worker

# Run scheduled tasks
celery -A app.celery beat

```

## Testing

How to run the automated tests for this system.

```bash
# Testing is not setup yet

```

## Support & Contact

If you have questions regarding this repository or encounter issues, please reach out to the maintainers:

* **Primary Contact:** [Tobias Clausen] (@TobiasClausen)
* **Discord Channel:** `#planco`

---

## License & Ownership

**Copyright © 2026 Planco. All rights reserved.**

This software and all associated files are the exclusive property of **Planco**. Unauthorized copying, distribution, or modification of this file, via any medium, is strictly prohibited. This code is proprietary and confidential. Any use of this material without express written permission from Planco is prohibited.
