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
    Build and run from dockerfile
