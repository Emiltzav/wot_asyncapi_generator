from datetime import datetime, timezone
from typing import Optional
from pydantic import BaseModel, Field

class Message(BaseModel):
    smartLightID: int
    lumen: int
    sentAt: str = Field(default_factory=lambda: datetime.now(timezone.utc).isoformat())  # Default to current UTC time

class CommandOnMessage(BaseModel):
    smartLightID: int
    command: str = "ON"  # Default to "ON"
    sentAt: str = Field(default_factory=lambda: datetime.now(timezone.utc).isoformat())  # Default to current UTC time