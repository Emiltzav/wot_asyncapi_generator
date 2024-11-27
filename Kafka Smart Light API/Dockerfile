# Use a base image
FROM python:3.9-slim

# Install netcat (nc)
RUN apt-get update && apt-get install -y netcat

# Set working directory
WORKDIR /app

# Copy the application files into the container
COPY . /app

# Copy the wait-for-kafka script
COPY wait-for-kafka.sh /app/wait-for-kafka.sh
RUN chmod +x /app/wait-for-kafka.sh

# Install necessary dependencies
RUN pip install --no-cache-dir -r requirements.txt

# Start the application after Kafka is ready
CMD /bin/bash -c "./wait-for-kafka.sh && python main.py"