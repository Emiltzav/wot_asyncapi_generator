#!/bin/bash

# Wait for Kafka to be available
echo "Waiting for Kafka to start..."
while ! nc -z kafka 9092; do
  sleep 1
done
echo "Kafka is up and running!"