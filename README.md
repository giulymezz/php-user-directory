# PHP User Directory

## Documentation

This project is a simple PHP web application running on an Apache server inside a Docker container.
It loads users from a data source, allows you to filter, sort and view the results in a table or thumbnail view. It includes a basic MVC structure.

---

### 📂 Project Structure

```text
/
├── assets/
│   ├── css/
|   |   └── style.css
│   └── js/
|       └── validate.js
├── data/
│   ├── images/
│   ├── cache/         <-- Generated thumbnails (ignored in Git)
│   └── data.txt
├── views/
│   ├── form.php
│   ├── layout.php
│   ├── table.php
│   └── thumb.php
├── ImageHelper.php
├── UserService.php
├── UserController.php
├── index.php
├── Dockerfile
├── .dockerignore
├── .gitignore
└── README.md
```

---

### 🧪 Features

✔ User filters

- Filter by active/inactive status
- Filter by date range (```from``` / ```to```)
- Filter by name or surname
- Choose between table or thumbnail view

✔ Date validation (client + server side)

The application now performs robust date validation both on the client side, the form from being sent if the dates are invalid, and on the server side, ensuring data integrity and preventing malformed or manipulated requests.
Accepts formats:

- ```d/m/Y```
- ```d/m/Y H:i```
- ```d/m/Y H:i:s```

✔ Thumbnail generation

Images are automatically resized once and cached in ```data/cache/```.

---

### 📂 File Responsibilities

#### UserController.php

- Reads request parameters
- Applies filters (active, dates, name, surname)
- Sorts results
- Passes data to a view
- Displays “no results” when needed

#### ImageHelper.php

- Provides:
  - generateThumbnail()
  - getCacheDir()

#### table.php / thumb.php

- Displays results in different formats
- Loads the cached thumbnails

#### layout.php

- Common HTML structure (title, CSS, JS, content wrapper)

#### form.php

- The main user filter form
- JS validation for date inputs

---

### 🚀 Running the Project with Docker

1. Build the Docker image

   ```bash
   docker build -t phpuserdirectory .
   ```

2. Run the container

   ```bash
   docker run -d -p 8080:80 phpuserdirectory
   ```

The app will now be available at: 👉 <http://localhost:8080>

---

### 🐳 Dockerfile Overview

This project uses ```php:8.2-apache``` and installs GD for image thumbnail generation.

Key features:

- Installs only the required packages (```--no-install-recommends```)
- Cleans APT cache to keep the image small
- Enables Apache ```mod_rewrite```
- Copies project files into the container
- Sets correct file and directory permissions

---

### 📦 .dockerignore and .gitignore

The project includes a ```.dockerignore``` to speed up Docker builds.
This prevents unnecessary files from being sent to the Docker build context.

The project also includes a ```.gitignore``` to ensure that generated or local files do not enter the repository.

---

### 🔧 Build & Run Commands Summary

| Action               | Command                                              |
| :---------------------- | :--------------------------------------------------- |
| Build Docker image      | ```docker build -t phpuserdirectory .```             |
| Run container           | ```docker run -d -p 8080:80 phpuserdirectory```      |
| Stop container          | ```docker ps``` → get ID → ```docker stop <id>```    |
| View logs               | ```docker logs <id>```                               |

---

### 📌 Useful Docker Commands

Stop all running containers

```bash
docker ps -q | xargs docker stop
```

Remove unused containers and images

```bash
docker system prune
```

Rebuild the image from scratch

```bash
docker build --no-cache -t phpuserdirectory .
```

---

### 📝 Known Limitations

- The project is intentionally minimal (no routing system, no framework, no database).
- Image thumbnails assume JPEG-only input.
- Filtering is performed in PHP on an in-memory dataset.

---

### 🔧 How to modify / extend functionality

Add new filters

→ Edit methods inside UserController.php under the ```filterBy...``` section.

Change design

→ Update the CSS file located in ```assets/css/```.

Modify the global layout

→ Edit ```views/layout.php```.

---

### ⚠️ Important Notes

The folder ```data/cache/``` contains auto-generated thumbnails and must not be tracked by Git.

---

### 📜 License

This project is for educational purposes and can be freely modified.