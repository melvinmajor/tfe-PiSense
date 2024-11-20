/*********
  Title: Raspberry Pi Pico WH with Pimoroni BME680 sensor (from Bosch)
  Author: Melvin Campos Casares
  Project details: https://github.com/melvinmajor/tfe-PiSense
  -----
  Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files.
  The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
*********/

#include <Wire.h>
#include <Adafruit_Sensor.h>
#include "Adafruit_BME680.h"
#include <WiFi.h>
#include <PubSubClient.h>
#include <WiFiClientSecure.h>

#define SEALEVELPRESSURE_HPA (1013.25)

// Replace with your WiFi credentials
const char* ssid = "YourSSID";
const char* password = "YourPassword";

// MQTT Broker
const char* mqtt_devicename = "PicoBME680"; // Name of the device in MQTT
const char* mqtt_server = "192.168.xxx.xxx"; // IP of the MQTT broker (Home Assistant)
const int mqtt_port = 1883; // Default port of Mosquitto MQTT is 1883. The secure one with SSL/TLS is 8883.
const char* mqtt_user = "mqttUserName";
const char* mqtt_password = "mqttPassword";


unsigned long lastMeasurementTime = 0;
const unsigned long measurementInterval = 600000; // 10 minutes
unsigned long startAttemptTime = 0; // for the reconnection chrono
const unsigned long connectionTimeout = 60000; // timeout for the reconnection tentative
unsigned long previousBlinkTime = 0; // Store time of last blink
int blinkCount = 0;  // Count the blink
int blinkState = LOW;  // Actual state of the LED (on or off)
unsigned long previousWiFiAttemptTime = 0;
bool wifiLedState = LOW;
unsigned long previousReconnectTime = 0;
unsigned long previousBlinkTimeReconnect = 0;
unsigned long previousBlinkTimeMeasurement = 0;
bool blinkMeasurementState = LOW;

Adafruit_BME680 bme; // I2C (default pins for Raspberry Pi Pico: GPIO 4 (SDA), GPIO 5(SCL)
WiFiClient espClient;
PubSubClient client(espClient);
WiFiClientSecure secureClient; // Secured client for SSL/TLS


void blinkError(int times) {
  unsigned long currentMillis = millis();
  
  if (blinkCount < times) {
    if (currentMillis - previousBlinkTime >= 300) {  // If 300ms passed since last blink
      previousBlinkTime = currentMillis;
      blinkState = !blinkState;  // Change state of LED (on/off)
      digitalWrite(LED_BUILTIN, blinkState);
      
      if (blinkState == LOW) {
        blinkCount++;  // Increment count once LED is off
      }
    }
  }
}

void setup_wifi() {
  Serial.println();
  Serial.print("Connecting to ");
  Serial.println(ssid);

  WiFi.begin(ssid, password);

  while (WiFi.status() != WL_CONNECTED) {
    unsigned long currentMillis = millis();

    if (currentMillis - previousWiFiAttemptTime >= 100) {
      previousWiFiAttemptTime = currentMillis;
      digitalWrite(LED_BUILTIN, wifiLedState);
      wifiLedState = !wifiLedState;  // Change state of the LED every 100ms
    }
    Serial.print(".");
  }

  Serial.println("");
  Serial.println("WiFi connected!");
  Serial.print("IP address: ");
  Serial.println(WiFi.localIP());
}

void checkWiFi() {
  if (WiFi.status() != WL_CONNECTED) {
    Serial.println("WiFi disconnected, reconnecting...");
    WiFi.begin(ssid, password);
    while (WiFi.status() != WL_CONNECTED) {
      delay(1000);
      Serial.print(".");
    }
    Serial.println("WiFi reconnected!");
  }
}

void reconnect() {
  startAttemptTime = millis(); // init the chrono
  Serial.println("Starting MQTT reconnection process...");

  // Loop until we're reconnected or timeout reached
  while (!client.connected()) {
    Serial.print("Attempting MQTT connection...");

    unsigned long currentMillis = millis();
    if (currentMillis - previousBlinkTimeReconnect >= 100) {
      previousBlinkTimeReconnect = currentMillis;
      digitalWrite(LED_BUILTIN, !digitalRead(LED_BUILTIN));  // Change state of LED
    }
    
    // Attempt to connect
    if (client.connect(mqtt_devicename, mqtt_user, mqtt_password)) {
      Serial.println("Connected!");
      client.subscribe("home/pico/command"); // Resubscribe to topic
    } else {
      Serial.print("Failed to connect, state ");
      Serial.println(client.state());
      blinkError(3); // Blink 3 times to show there is an error
      delay(10000); // 10 seconds wait before retrying

      // Timeout after 60 seconds
      if (millis() - startAttemptTime > connectionTimeout) {
        Serial.println("Timeout reached, skipping reconnection for now...");
        return;
      }
    }
  }
}

void callback(char* topic, byte* payload, unsigned int length) {
  Serial.print("Message from topic: ");
  Serial.println(topic);
  
  // Convert payload to varchar
  char message[length + 1];  // Add 1 for the end chararcter '\0'
  for (unsigned int i = 0; i < length; i++) {
    message[i] = (char)payload[i];
  }
  message[length] = '\0';  // End chain
  
  // Check message and execute related action
  if (strcmp(message, "ON") == 0) {
    digitalWrite(LED_BUILTIN, HIGH);  // Turn on LED
    Serial.println("LED ON");
  } else if (strcmp(message, "OFF") == 0) {
    digitalWrite(LED_BUILTIN, LOW);  // Turn off LED
    Serial.println("LED OFF");
  } else {
    Serial.println("Unrecognized command from broker...");
  }
}


void setup() {
  // initialize digital pin LED_BUILTIN as an output.
  pinMode(LED_BUILTIN, OUTPUT);
  digitalWrite(LED_BUILTIN, HIGH);

  setup_wifi();
  client.setServer(mqtt_server, 1883);
  client.setCallback(callback);

  Serial.begin(115200);
  while (!Serial);
  Serial.println(F("BME680 async test"));

  if (!bme.begin(0x76)) { // If 0x77 does not work, try 0x76
    Serial.println(F("Could not find a valid BME680 sensor, check wiring!"));
    blinkError(3); // Blink 3 times to show there is an error
    while (1);
  }

  Serial.println("BME680 detected!");

  // Oversampling and filter initialization
  bme.setTemperatureOversampling(BME680_OS_8X);
  bme.setHumidityOversampling(BME680_OS_2X);
  bme.setPressureOversampling(BME680_OS_4X);
  bme.setIIRFilterSize(BME680_FILTER_SIZE_3);
  bme.setGasHeater(320, 150); // 320*C for 150 ms
}

void loop() {
  checkWiFi();
  if (!client.connected()) {
    reconnect();
  }

  // Regularly call to maintain MQTT connection
  client.loop();

  // Chrono to perform new measure
  unsigned long currentTime = millis();
  if (currentTime - lastMeasurementTime >= measurementInterval) {
    lastMeasurementTime = currentTime; // Reinit the chrono

    digitalWrite(LED_BUILTIN, HIGH);

    // Begin BME680 measurement
    unsigned long endTime = bme.beginReading();
    if (endTime == 0) {
      Serial.println(F("Failed to begin reading..."));
      blinkError(3); // Blink 3 times to show there is an error
      return;
    }

    // Blink during process
    unsigned long startTime = millis();
    while (millis() < endTime) {
      if (millis() - previousBlinkTimeMeasurement >= 100) {
        previousBlinkTimeMeasurement = millis();
        blinkMeasurementState = !blinkMeasurementState;
        digitalWrite(LED_BUILTIN, blinkMeasurementState);
      }
    }

    // Obtain measurement results from BME680
    // Note that this operation isn't instantaneous even if milli() >= endTime due to I2C/SPI latency.
    if (!bme.endReading()) {
      Serial.println(F("Failed to perform the reading..."));
      blinkError(3); // Blink 3 times to show there is an error
      return;
    }

    // Indicate success with a quick LED blink
    digitalWrite(LED_BUILTIN, HIGH);
    delay(100);
    digitalWrite(LED_BUILTIN, LOW);

    // Push sensor data to MQTT
    String payload = "{";
    payload += "\"temperature\":";
    payload += bme.temperature;
    payload += ",\"humidity\":";
    payload += bme.humidity;
    payload += ",\"pressure\":";
    payload += bme.pressure / 100.0;
    payload += ",\"gas\":";
    payload += bme.gas_resistance / 1000.0;
    payload += "}";

    client.publish("home/pico/bme680", payload.c_str());
    Serial.println("Published data to MQTT: ");
    Serial.println(payload);

    // Print data via serial port just in case
    Serial.print(F("Temp = "));
    Serial.print(bme.temperature);
    Serial.print(F(" *C - "));

    Serial.print(F("Humidity = "));
    Serial.print(bme.humidity);
    Serial.print(F(" % - "));

    Serial.print(F("Pressure = "));
    Serial.print(bme.pressure / 100.0);
    Serial.print(F(" hPa - "));

    Serial.print(F("Gas = "));
    Serial.print(bme.gas_resistance / 1000.0);
    Serial.println(F(" KOhms"));

    Serial.print(F("Approx. Altitude = "));
    Serial.print(bme.readAltitude(SEALEVELPRESSURE_HPA));
    Serial.println(F(" m"));

    Serial.println();
    digitalWrite(LED_BUILTIN, LOW); // turn the LED off by making the voltage LOW
  }
}
