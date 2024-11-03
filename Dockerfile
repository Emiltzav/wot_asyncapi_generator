FROM python:3-alpine

WORKDIR /usr/src/backend_thesis

COPY . .

RUN pip install Flask
RUN pip install -U flask-cors
RUN pip install mysql-connector-python
WORKDIR /usr/src/backend_thesis/API
EXPOSE 5000
CMD python -m flask --app v1 run --debug --host 0.0.0.0


