This Web of Things (WoT) toolset and system architecture implementation was developed — and is actively being extended — as part of the PhD research of Aimilios Tzavaras and the postgraduate thesis of Manousos Boliotis, under the supervision of Professor Euripides Petrakis and Lab Instructor Chrysi Tsinaraki, at the Intelligent Systems Laboratory of the Technical University of Crete.

# wot_asyncapi_generator
A service in Python Flask for the generation of WoT AsyncAPI service descriptions for specific IoT devices (e.g. sensors, actuators) that work asynchronously (i.e. not synchronously, not REST-based).

# Instructions
Without Docker:  
    - Enable Vertual Env:  
        - `API\v1\.venv\Scripts\activate` 

    - Run flask application:  
        - `cd \API`  
        - `python -m flask --app v1 run --debug`  

With Docker:  
    - Build & run from Dockerfile in the project root directory (Docker should be already running in your machine):  
        - `docker build -t flask_app .`  
        - `docker run -p 5000:5000 flask_app`

# Notes for Web Server (PHP) Service
You have to enter the PHP container (app) docker container (e.g. using Docker Desktop) and run `composer install` in the terminal (after navigating in the /docker folder!!) in order to install the dependencies and generate vendor/autoload.php inside the /docker folder.
