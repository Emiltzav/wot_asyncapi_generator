-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Εξυπηρετητής: db
-- Χρόνος δημιουργίας: 03 Νοε 2024 στις 17:08:21
-- Έκδοση διακομιστή: 8.3.0
-- Έκδοση PHP: 8.2.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Βάση δεδομένων: `web_of_things`
--

-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `thing_description`
--

CREATE TABLE `thing_description` (
  `id` int NOT NULL,
  `device_name` varchar(60) NOT NULL,
  `category` varchar(30) DEFAULT NULL,
  `specification_type` varchar(10) NOT NULL,
  `td` json NOT NULL,
  `date_inserted` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Άδειασμα δεδομένων του πίνακα `thing_description`
--

INSERT INTO `thing_description` (`id`, `device_name`, `category`, `specification_type`, `td`, `date_inserted`) VALUES
(1, 'dht22-sensor-mqtt-protocol', 'temperature sensors', 'asyncapi', '{\"info\": {\"title\": \"DHT22 Temperature and Humidity Sensor MQTT API\", \"version\": \"1.0.0\", \"description\": \"The DHT22 Sensor API allows you to remotely receive temeprature and humidity measurements of the sensor.\\n### Check out its awesome features:\\n* Receive real-time temperature and humidity measurements 📈\"}, \"servers\": {\"mqttBroker\": {\"host\": \"localhost:1883\", \"protocol\": \"mqtt\", \"security\": [{\"$ref\": \"#/components/securitySchemes/basicAuth\"}], \"description\": \"The MQTT broker to which measurement data are published and consumed.\"}}, \"asyncapi\": \"3.0.0\", \"channels\": {\"humidityMeasured\": {\"address\": \"esp32/dht/humidity\", \"messages\": {\"humidityMeasurement\": {\"$ref\": \"#/components/messages/humidityMeasurement\"}}, \"parameters\": {\"sensorId\": {\"$ref\": \"#/components/parameters/sensorId\"}}, \"description\": \"The topic on which measured humid values may be produced and consumed.\"}, \"temperatureMeasured\": {\"address\": \"esp32/dht/temperature\", \"messages\": {\"temperatureMeasurement\": {\"$ref\": \"#/components/messages/temperatureMeasurement\"}}, \"parameters\": {\"sensorId\": {\"$ref\": \"#/components/parameters/sensorId\"}}, \"description\": \"The topic on which measured temperature values may be produced and consumed.\"}}, \"components\": {\"schemas\": {\"sentAt\": {\"type\": \"string\", \"format\": \"date-time\", \"description\": \"Date and time when the message was sent.\"}, \"humidityMeasuredPayload\": {\"type\": \"object\", \"properties\": {\"h\": {\"type\": \"number\", \"minimum\": 0, \"description\": \"The humidity percentage value.\"}, \"sentAt\": {\"$ref\": \"#/components/schemas/sentAt\"}}}, \"temperatureMeasuredPayload\": {\"type\": \"object\", \"properties\": {\"temp\": {\"type\": \"number\", \"minimum\": 0, \"description\": \"The temperature value.\"}, \"sentAt\": {\"$ref\": \"#/components/schemas/sentAt\"}}}}, \"messages\": {\"humidityMeasurement\": {\"name\": \"humidityMeasurement\", \"title\": \"Humidity measured\", \"payload\": {\"$ref\": \"#/components/schemas/humidityMeasuredPayload\"}, \"summary\": \"Inform about environmental humidity conditions of a room.\", \"contentType\": \"application/json\"}, \"temperatureMeasurement\": {\"name\": \"temperatureMeasurement\", \"title\": \"Temperature measured\", \"payload\": {\"$ref\": \"#/components/schemas/temperatureMeasuredPayload\"}, \"summary\": \"Inform about environmental temperature conditions of a room.\", \"contentType\": \"application/json\"}}, \"parameters\": {\"sensorId\": {\"description\": \"The ID of the sensor.\"}}, \"securitySchemes\": {\"basicAuth\": {\"type\": \"userPassword\", \"description\": \"Basic authentication with username and password.\"}}}, \"operations\": {\"receiveHumidityMeasurement\": {\"tags\": [{\"name\": \"Properties\", \"description\": \"An operation for retrieving measurements of a Thing property\"}], \"action\": \"receive\", \"channel\": {\"$ref\": \"#/channels/humidityMeasured\"}, \"summary\": \"Inform about environmental humidity conditions of a particular room.\", \"messages\": [{\"$ref\": \"#/channels/humidityMeasured/messages/humidityMeasurement\"}]}, \"receiveTemperatureMeasurement\": {\"tags\": [{\"name\": \"Properties\", \"description\": \"An operation for retrieving measurements of a Thing property\"}], \"action\": \"receive\", \"channel\": {\"$ref\": \"#/channels/temperatureMeasured\"}, \"summary\": \"Inform about environmental temperature conditions of a particular room.\", \"messages\": [{\"$ref\": \"#/channels/temperatureMeasured/messages/temperatureMeasurement\"}]}}, \"defaultContentType\": \"application/json\"}', '2024-10-12 18:26:16'),
(2, 'streetlights-kafka-protocol', 'streetlight actuators', 'asyncapi', '{\"info\": {\"title\": \"Streetlights Kafka API\", \"license\": {\"url\": \"https://www.apache.org/licenses/LICENSE-2.0\", \"name\": \"Apache 2.0\"}, \"version\": \"1.0.0\", \"description\": \"The Smartylighting Streetlights API allows you to remotely manage the city\\nlights.\\n### Check out its awesome features:\\n\\n* Turn a specific streetlight on/off 🌃  \\n* Dim a specific streetlight 😎\\n* Receive real-time information about environmental lighting conditions 📈\"}, \"servers\": {\"mtls-connections\": {\"host\": \"test.mykafkacluster.org:28092\", \"tags\": [{\"name\": \"env:test-mtls\", \"description\": \"This environment is meant for running internal tests through mtls\"}, {\"name\": \"kind:remote\", \"description\": \"This server is a remote server. Not exposed by the application\"}, {\"name\": \"visibility:private\", \"description\": \"This resource is private and only available to certain users\"}], \"protocol\": \"kafka-secure\", \"security\": [{\"$ref\": \"#/components/securitySchemes/certs\"}], \"description\": \"Test broker secured with X509\"}, \"scram-connections\": {\"host\": \"test.mykafkacluster.org:18092\", \"tags\": [{\"name\": \"env:test-scram\", \"description\": \"This environment is meant for running internal tests through scramSha256\"}, {\"name\": \"kind:remote\", \"description\": \"This server is a remote server. Not exposed by the application\"}, {\"name\": \"visibility:private\", \"description\": \"This resource is private and only available to certain users\"}], \"protocol\": \"kafka-secure\", \"security\": [{\"$ref\": \"#/components/securitySchemes/saslScram\"}], \"description\": \"Test broker secured with scramSha256\"}}, \"asyncapi\": \"3.0.0\", \"channels\": {\"lightsDim\": {\"address\": \"smartylighting.streetlights.1.0.action.{streetlightId}.dim\", \"messages\": {\"dimLight\": {\"$ref\": \"#/components/messages/dimLight\"}}, \"parameters\": {\"streetlightId\": {\"$ref\": \"#/components/parameters/streetlightId\"}}}, \"lightTurnOn\": {\"address\": \"smartylighting.streetlights.1.0.action.{streetlightId}.turn.on\", \"messages\": {\"turnOn\": {\"$ref\": \"#/components/messages/turnOnOff\"}}, \"parameters\": {\"streetlightId\": {\"$ref\": \"#/components/parameters/streetlightId\"}}}, \"lightTurnOff\": {\"address\": \"smartylighting.streetlights.1.0.action.{streetlightId}.turn.off\", \"messages\": {\"turnOff\": {\"$ref\": \"#/components/messages/turnOnOff\"}}, \"parameters\": {\"streetlightId\": {\"$ref\": \"#/components/parameters/streetlightId\"}}}, \"lightingMeasured\": {\"address\": \"smartylighting.streetlights.1.0.event.{streetlightId}.lighting.measured\", \"messages\": {\"lightMeasured\": {\"$ref\": \"#/components/messages/lightMeasured\"}}, \"parameters\": {\"streetlightId\": {\"$ref\": \"#/components/parameters/streetlightId\"}}, \"description\": \"The topic on which measured values may be produced and consumed.\"}}, \"components\": {\"schemas\": {\"sentAt\": {\"type\": \"string\", \"format\": \"date-time\", \"description\": \"Date and time when the message was sent.\"}, \"dimLightPayload\": {\"type\": \"object\", \"properties\": {\"sentAt\": {\"$ref\": \"#/components/schemas/sentAt\"}, \"percentage\": {\"type\": \"integer\", \"maximum\": 100, \"minimum\": 0, \"description\": \"Percentage to which the light should be dimmed to.\"}}}, \"turnOnOffPayload\": {\"type\": \"object\", \"properties\": {\"sentAt\": {\"$ref\": \"#/components/schemas/sentAt\"}, \"command\": {\"enum\": [\"on\", \"off\"], \"type\": \"string\", \"description\": \"Whether to turn on or off the light.\"}}}, \"lightMeasuredPayload\": {\"type\": \"object\", \"properties\": {\"lumens\": {\"type\": \"integer\", \"minimum\": 0, \"description\": \"Light intensity measured in lumens.\"}, \"sentAt\": {\"$ref\": \"#/components/schemas/sentAt\"}}}}, \"messages\": {\"dimLight\": {\"name\": \"dimLight\", \"title\": \"Dim light\", \"traits\": [{\"$ref\": \"#/components/messageTraits/commonHeaders\"}], \"payload\": {\"$ref\": \"#/components/schemas/dimLightPayload\"}, \"summary\": \"Command a particular streetlight to dim the lights.\"}, \"turnOnOff\": {\"name\": \"turnOnOff\", \"title\": \"Turn on/off\", \"traits\": [{\"$ref\": \"#/components/messageTraits/commonHeaders\"}], \"payload\": {\"$ref\": \"#/components/schemas/turnOnOffPayload\"}, \"summary\": \"Command a particular streetlight to turn the lights on or off.\"}, \"lightMeasured\": {\"name\": \"lightMeasured\", \"title\": \"Light measured\", \"traits\": [{\"$ref\": \"#/components/messageTraits/commonHeaders\"}], \"payload\": {\"$ref\": \"#/components/schemas/lightMeasuredPayload\"}, \"summary\": \"Inform about environmental lighting conditions of a particular streetlight.\", \"contentType\": \"application/json\"}}, \"parameters\": {\"streetlightId\": {\"description\": \"The ID of the streetlight.\"}}, \"messageTraits\": {\"commonHeaders\": {\"headers\": {\"type\": \"object\", \"properties\": {\"my-app-header\": {\"type\": \"integer\", \"maximum\": 100, \"minimum\": 0}}}}}, \"operationTraits\": {\"kafka\": {\"bindings\": {\"kafka\": {\"clientId\": {\"enum\": [\"my-app-id\"], \"type\": \"string\"}}}}}, \"securitySchemes\": {\"certs\": {\"type\": \"X509\", \"description\": \"Download the certificate files from service provider\"}, \"saslScram\": {\"type\": \"scramSha256\", \"description\": \"Provide your username and password for SASL/SCRAM authentication\"}}}, \"operations\": {\"turnOn\": {\"action\": \"send\", \"traits\": [{\"$ref\": \"#/components/operationTraits/kafka\"}], \"channel\": {\"$ref\": \"#/channels/lightTurnOn\"}, \"messages\": [{\"$ref\": \"#/channels/lightTurnOn/messages/turnOn\"}]}, \"turnOff\": {\"action\": \"send\", \"traits\": [{\"$ref\": \"#/components/operationTraits/kafka\"}], \"channel\": {\"$ref\": \"#/channels/lightTurnOff\"}, \"messages\": [{\"$ref\": \"#/channels/lightTurnOff/messages/turnOff\"}]}, \"dimLight\": {\"action\": \"send\", \"traits\": [{\"$ref\": \"#/components/operationTraits/kafka\"}], \"channel\": {\"$ref\": \"#/channels/lightsDim\"}, \"messages\": [{\"$ref\": \"#/channels/lightsDim/messages/dimLight\"}]}, \"receiveLightMeasurement\": {\"action\": \"receive\", \"traits\": [{\"$ref\": \"#/components/operationTraits/kafka\"}], \"channel\": {\"$ref\": \"#/channels/lightingMeasured\"}, \"summary\": \"Inform about environmental lighting conditions of a particular streetlight.\", \"messages\": [{\"$ref\": \"#/channels/lightingMeasured/messages/lightMeasured\"}]}}, \"defaultContentType\": \"application/json\"}', '2024-10-31 18:54:28');

--
-- Ευρετήρια για άχρηστους πίνακες
--

--
-- Ευρετήρια για πίνακα `thing_description`
--
ALTER TABLE `thing_description`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT για άχρηστους πίνακες
--

--
-- AUTO_INCREMENT για πίνακα `thing_description`
--
ALTER TABLE `thing_description`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
