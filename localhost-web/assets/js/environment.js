function getDataEnvironment() {
  fetch("assets/environment.json").then(response => {
    response.json().then(data => {
      //console.log(data);
      updateHtmlEnvironment(data[data.length-1]);
    })
  }).catch(err => {
    console.log(err);
  })
}

function updateHtmlEnvironment(data) {
  let datetime = data.datetime;
  let temperature = data.temperature;
  let gas = data.gas;
  let humidity = data.humidity;
  let pressure = data.pressure;

  //update HTML
  document.getElementById("envTime").innerHTML = datetime;
  document.getElementById("temperature").innerHTML = temperature + " °C";
  document.getElementById("temperatureLabel").innerHTML = "en Fahrenheit : " + cToF(temperature) + " °F";
  document.getElementById("humidity").innerHTML = humidity + " %";
  document.getElementById("pressure").innerHTML = pressure;
  document.getElementById("pressureLabel").innerHTML = "hPa";
  if(gas == null) {
    document.getElementById("gas").innerHTML = "Gaz : Veuillez patienter la prochaine prise de mesure, le capteur doit être chaud...";
    document.getElementById("gas").style.color = "#d32f2f";
  }
  else {
    document.getElementById("gas").innerHTML = "Gaz : " + gas + " Ohms";
    document.getElementById("gas").style.color = "#676767";
  }

  //set colors
  colorsTemperature = getColorTemperature(temperature);
  document.getElementById("containerTemperature").style.background = colorsTemperature.bg;
  document.getElementById("containerTemperature").style.color = colorsTemperature.text;
  document.getElementById("containerHumidity").style.background = "#607d8b";
  document.getElementById("containerHumidity").style.color = "white";
  document.getElementById("containerPressure").style.background = "#607d8b";
  document.getElementById("containerPressure").style.color = "white";

  function cToF(celsius)
  {
    const cTemp = celsius;
    const cToFahr = cTemp * 9 / 5 + 32;
    return cToFahr;
    //const message = `${cTemp}\xB0C is ${cToFahr} \xB0F.`;
    //console.log(message);
  }
}

function getColorTemperature(value) {
  switch (true) {
    case (value >= -10 && value < 0):
      color = "blue";
      break;
    case (value >= 0 && value < 15):
      color = "lightblue";
      break;
    case (value >= 15 && value < 20):
      color = "green";
      break;
    case (value >= 20 && value < 25):
      color = "lime";
      break;
    case (value >= 25 && value < 30):
      color = "yellow";
      break;
    case (value >= 30 && value < 35):
      color = "orange";
      break;
    case (value >= 35):
      color = "red";
      break;
    default:
      color = "black";
  }
  return {bg: color, text: (value < 0 || color === "green" || value >= 35) ? "white" : "black"};
}
