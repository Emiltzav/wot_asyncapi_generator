import paho.mqtt.client as mqtt
import time

# HiveMQ server details
BROKER = "hivemq"
PORT = 1883
TOPIC = "test/topic"

def main():
    # Initialize MQTT client
    client = mqtt.Client()
    client.connect(BROKER, PORT, 60)

    # Publish messages
    for i in range(30):
        message = f"Measurement {i+1}"
        client.publish(TOPIC, message)
        print(f"Published: {message}")
        time.sleep(1)

    # Disconnect
    client.disconnect()
    print("Publisher disconnected.")

if __name__ == "__main__":
    main()