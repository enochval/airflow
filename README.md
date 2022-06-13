## Installation Guide

### Prerequisite

- Install Docker and Docker Compose on the host machine.

### Installation

- Clone the repo ``git clone https://github.com/enochval/airflow.git``


- Run ``cd airflow``


- Run ``cp .env.example .env``


- Run ``composer install``


- Run ``docker-compose up -d``


- Go to ``http://127.0.0.1:8000`` - App
  

- Go to ``http://127.0.0.1:8000/horizon`` - Queue Service
  

- Go to ``http://127.0.0.1:8000/telescope`` - Telescope Service


- Go to ``http://127.0.0.1:8000/log-viewer`` - Logs
  

- Go to ``http://127.0.0.1:8100`` - Mailhog - for tracking emails


## Testing

- Run ``docker-compose exec app sh``


- Run ``php artisan test``
