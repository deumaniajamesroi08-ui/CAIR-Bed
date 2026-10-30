const bpmDisplay = document.getElementById("bpm-display");
const irDisplay = document.getElementById("ir-display");
const statusDisplay = document.getElementById("status-display");
const liveCountDisplay = document.getElementById("live-count-display"); 


const brokerIP = CONFIG.broker_IP; 
const brokerPort = CONFIG.broker_Port; 


const clientID = "CareBed-Dashboard-" + Math.floor(Math.random() * 10000);
const client = new Paho.MQTT.Client(brokerIP, brokerPort, clientID);


client.onConnectionLost = function(responseObject) {
    if (responseObject.errorCode !== 0) {
        console.error("MQTT Connection Lost: " + responseObject.errorMessage);
        statusDisplay.innerText = "Dashboard Disconnected from Server";
        statusDisplay.style.color = "red";
    }
};


client.onMessageArrived = function(message) {
    
    const data = JSON.parse(message.payloadString);
    
    
    irDisplay.innerText = data.ir;

    
    if (data.bpm > 0) {
        bpmDisplay.innerText = data.bpm;
    }

    
    if (data.ir < 50000) {
        statusDisplay.innerText = "No finger detected";
        statusDisplay.className = "status-text no-finger";
        liveCountDisplay.style.display = "none"; 
    } else {
        if (data.isCounting === true) {
            statusDisplay.innerText = "Measuring... Hold Still (15s)";
            statusDisplay.className = "status-text measuring";
            liveCountDisplay.innerText = "Live Beats Detected: " + data.beatCount;
            liveCountDisplay.style.display = "block"; 
        } else {
            statusDisplay.innerText = "Measurement Complete!";
            statusDisplay.className = "status-text ready";
            liveCountDisplay.style.display = "none"; 
        }
    }
};


const options = {
    timeout: 3,
    onSuccess: function() {
        console.log("Dashboard Connected to MQTT Broker successfully!");
        
        client.subscribe("carebed/vitals");
    },
    onFailure: function(message) {
        console.error("Dashboard Failed to connect: " + message.errorMessage);
    }
};

console.log("Attempting to connect to Mosquitto...");
client.connect(options);