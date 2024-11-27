import json
import schedule
import time
from datetime import datetime
from kafka import KafkaProducer, KafkaConsumer
from threading import Thread
import logging
import signal
import sys

# Initialize logging
logging.basicConfig(level=logging.INFO, format="%(asctime)s [%(levelname)s] %(message)s")

TOPIC_LIGHTING_MEASURED = "smartylighting.streetlights.lighting.measured"
TOPIC_LIGHT_COMMAND = "smartylighting.streetlights.light.commands"

# Kafka Producer
producer = KafkaProducer(
    bootstrap_servers=['kafka:9092'],
    value_serializer=lambda v: json.dumps(v).encode('utf-8')
)

# Kafka Consumer
consumer = KafkaConsumer(
    TOPIC_LIGHT_COMMAND,
    bootstrap_servers=['kafka:9092'],
    value_deserializer=lambda v: json.loads(v.decode('utf-8')),
    group_id="streetlight-group"  # Add consumer group for scalability
)

# Function to send light measurements
def produce_light_measurement(streetlight_id=1, lumens=50):
    if not (0 <= lumens <= 1000):
        logging.warning(f"Invalid lumens value: {lumens}. Must be between 0 and 1000.")
        return

    message = {
        "streetlightId": streetlight_id,
        "lumens": lumens,
        "sentAt": time.strftime('%Y-%m-%dT%H:%M:%SZ')
    }
    producer.send(TOPIC_LIGHTING_MEASURED, value=message)
    producer.flush()  # Ensure the message is immediately sent
    logging.info(f"Produced: {message}")

# Schedule producer
schedule.every(5).seconds.do(produce_light_measurement)

# Function to listen for light commands
def consume_light_commands():
    logging.info(f"Listening for messages on {TOPIC_LIGHT_COMMAND}...")
    try:
        for message in consumer:
            logging.info(f"Consumed: {message.value}")
            handle_command(message.value)
    except Exception as e:
        logging.error(f"Error in consumer loop: {e}", exc_info=True)

# Handle light commands
def handle_command(command):
    streetlight_id = command.get("streetlightId")
    action = command.get("action")
    logging.info(f"Streetlight {streetlight_id} received command: {action}")
    if action == "on":
        logging.info("Turning ON the light.")
    elif action == "off":
        logging.info("Turning OFF the light.")
    elif action == "dim":
        dim_level = command.get("dimLevel")
        logging.info(f"Dimming the light to {dim_level}%.")

# Graceful shutdown
def shutdown(signal, frame):
    logging.info("Shutting down...")
    producer.close()
    consumer.close()
    sys.exit(0)

signal.signal(signal.SIGINT, shutdown)
signal.signal(signal.SIGTERM, shutdown)

# Main loop
if __name__ == "__main__":
    # Run the consumer in a separate thread
    consumer_thread = Thread(target=consume_light_commands, daemon=True)
    consumer_thread.start()

    # Run the scheduled producer
    while True:
        schedule.run_pending()
        time.sleep(1)