import json

from app.core.gateways.kafka import Kafka
from app.core.models.message import Message, CommandOnMessage
from app.dependencies.kafka import get_kafka_instance

from fastapi import APIRouter, Depends

router = APIRouter()

# Existing RESTful API POST endpoint for sending light measurement messages
@router.post("")
async def send(data: Message, server: Kafka = Depends(get_kafka_instance)):
    """
    Sends a (simulated) lighting measurement for the smartlight device via Kafka.
    """
    try:
        topic_name = server._topic
        await server.aioproducer.send_and_wait(topic_name, json.dumps(data.dict()).encode("ascii"))
    except Exception as e:
        await server.aioproducer.stop()
        raise e
    return 'Message sent successfully'

# New POST endpoint for sending "turn ON" command
@router.post("/turn-on")
async def send_on_command(data: CommandOnMessage, server: Kafka = Depends(get_kafka_instance)):
    """
    Sends an "turn ON" command to a smartlight device via Kafka.
    """
    try:
        topic_name = "smartlight.turnon"
        print(f"Sending 'turn ON' command to Kafka topic '{topic_name}': {data.dict()}")
        await server.aioproducer.send_and_wait(topic_name, json.dumps(data.dict()).encode("ascii"))
    except Exception as e:
        await server.aioproducer.stop()
        raise e
    return {"message": "Turn ON command sent successfully"}