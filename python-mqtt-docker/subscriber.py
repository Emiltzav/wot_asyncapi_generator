import paho.mqtt.client as mqtt

# HiveMQ server details
BROKER = "hivemq"
PORT = 1883
TOPIC = "test/topic"

def on_connect(client, userdata, flags, rc):
    print("Subscriber connected.")
    client.subscribe(TOPIC)

def on_message(client, userdata, msg):
    print(f"Received message: {msg.payload.decode()} on topic: {msg.topic}")

def main():
    # Initialize MQTT client
    client = mqtt.Client()
    client.on_connect = on_connect
    client.on_message = on_message

    client.connect(BROKER, PORT, 60)
    client.loop_forever()

if __name__ == "__main__":
    main()