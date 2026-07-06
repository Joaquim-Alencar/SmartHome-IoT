# SmartHome-IoT
An IoT-based Smart Home system developed using Arduino, Raspberry Pi, and a web dashboard. The project monitors environmental conditions, automates home devices, and allows remote control through a web interface and Telegram.

Features
🌡️ Temperature monitoring
💧 Humidity monitoring
🔥 Fire detection with alarm activation
📷 Motion detection using an infrared sensor and automatic image capture
🤖 Telegram bot notifications and remote commands
❄️ Automatic air conditioning control
🌬️ Automatic ventilation control
🚪 RFID door access system
📊 Real-time dashboard for monitoring and control

Hardware
Arduino
Raspberry Pi
DHT11 Temperature & Humidity Sensor
Flame Sensor
PIR Motion Sensor
RFID Reader (MFRC522)
Servo Motor (Door)
Buzzer (Alarm)
LEDs (Air Conditioner, Fan and Alarm indicators)
Camera Module

# The projects is separated in 3 different directories:

## **ProjetoTI_Python**
This folder contains the Raspberry Pi software responsible for controlling several hardware components and coordinating advanced system features. It manages the flame sensor, RFID reader, and the traffic light module used to represent the air conditioner, house ventilation, and alarm status.

The Raspberry Pi is also responsible for capturing images with the camera whenever motion is detected by the PIR sensor. In addition, it communicates with the web API to exchange sensor data, actuator states, and control commands, ensuring synchronization between the physical smart home, the dashboard, and the automation system.

## **ProjetoIT_Arduino**
This folder contains the Arduino firmware responsible for controlling the smart home's hardware components. It manages the servo motor (door mechanism), temperature and humidity sensor, PIR motion sensor, and buzzer alarm.

The Arduino continuously reads data from the sensors, controls the actuators according to commands received from the system, and sends the current status of sensors and actuators to the website through HTTP POST requests to the API. This enables real-time monitoring and synchronization between the physical smart home and the web dashboard.

## **ProjetoTI_WebSite**

This folder contains the source code for the Smart Home web application, including the dashboard, API, authentication pages, and supporting resources.

**Folder Structure**

### _API/_

Contains the backend API responsible for communication between the web application and the hardware devices.

* *api.php** – Handles HTTP GET and POST requests exchanged between the Arduino, Raspberry Pi, and the website.
* *upload.php** – Processes and stores images captured by the Raspberry Pi camera.
* *files/** – Stores the sensor readings and actuator states received from the Arduino and Raspberry Pi.

### _cooldowns/_

Contains the files used to manage the cooldown periods for Telegram bot notifications, preventing duplicate or excessive alert messages.

### _images/_

Stores the images used throughout the project, including interface assets and uploaded photos.

### _dashboard.php_

Implements the main dashboard, providing real-time monitoring of sensors and actuators, as well as the current status of the smart home.

### _enviar_dados.php_

Provides the manual control interface, allowing users to remotely control the air conditioner, ventilation system, and door.

### _index.php_

Implements the login page used to authenticate users before accessing the dashboard.

### _styles/_

Contains the CSS files responsible for the appearance and layout of the web application.

