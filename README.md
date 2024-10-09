# wot_asyncapi_generator
A service in Python Flask for the generation of WoT AsyncAPI service descriptions for specific IoT devices (e.g. sensors, actuators) that work asynchronously (i.e. not synchronously, not REST-based).

# Instructions
Without Docker:
    Enable Vertual Env:
        API\v1\.venv\Scripts\activate

    Run flask application:
        cd \API
        python -m flask --app v1 run --debug

With Docker:
    Build and run from Dockerfile (Docker should be already running in your machine):
        Go to project root directory
        docker build -t flask_app .
        docker run -p 5000:5000 flask_app