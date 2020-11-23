function getDataEnvironment() {
  fetch("assets/environment.json").then(response => {
    response.json().then(data => {
      //console.log(data);
      updateHtmlEnvironment(data[data.length - 1]);
    })
  }).catch(err => {
    console.log(err);
  })
}

function updateHtmlEnvironment(data) {
  let datetime = new Date(data.datetime);
  let temperature = data.temperature;
  let gas = data.gas;
  let humidity = data.humidity;
  let pressure = data.pressure;

  //update HTML
  document.getElementById("envTime").innerHTML = datetime.toLocaleString("fr-FR",
    {year: "numeric", month: "long", day: "2-digit", hour: "2-digit",
      minute: "2-digit", second: "2-digit", timeZoneName: "short"});
  document.getElementById("temperature").innerHTML = temperature + " °C";
  document.getElementById("temperatureLabel").innerHTML = "Fahrenheit : " + cToF(temperature) + " °F";
  document.getElementById("humidity").innerHTML = humidity + " %";
  document.getElementById("humidityLabel").innerHTML = "RH";
  document.getElementById("pressure").innerHTML = pressure;
  document.getElementById("pressureLabel").innerHTML = "hPa";
  if (gas == null) {
    document.getElementById("gas").innerHTML = "Gaz : Veuillez patienter la prochaine prise de mesure, le capteur doit être chaud...";
    document.getElementById("gas").style.color = "#d32f2f";
  } else {
    document.getElementById("gas").innerHTML = "Gaz : " + gas + " Ohms";
    document.getElementById("gas").style.color = "#676767";
  }

  //set colors
  let colorsTemperature = getColorTemperature(temperature);
  document.getElementById("containerTemperature").style.background = colorsTemperature.bg;
  document.getElementById("containerTemperature").style.color = colorsTemperature.text;
  document.getElementById("containerHumidity").style.background = "linear-gradient(to right bottom, #bdbdbd, #aaabb1, #949ba6, #7b8b99, #607d8b)";
  document.getElementById("containerHumidity").style.color = "white";
  document.getElementById("containerPressure").style.background = "linear-gradient(to right bottom, #bdbdbd, #aaabb1, #949ba6, #7b8b99, #607d8b)";
  document.getElementById("containerPressure").style.color = "white";

  function cToF(celsius) {
    const cTemp = celsius;
    const cToFahr = cTemp * 9 / 5 + 32;
    return +(Math.round(cToFahr + "e+2")  + "e-2");
    //const message = `${cTemp}\xB0C is ${cToFahr} \xB0F.`;
    //console.log(message);
  }
}

function getColorTemperature(value) {
  let bgImageColor;
  switch (true) {
    case (value >= -10 && value < 0):
      bgImageColor = "linear-gradient(to right bottom, #3b82f6, #3875f4, #3c67f1, #4457ec, #4f46e5)";
      textColor = "white";
      break;
    case (value >= 0 && value < 15):
      bgImageColor = "linear-gradient(to right bottom, #bae6fd, #9dd7fd, #83c7fd, #6eb7fc, #60a5fa)";
      textColor = "black";
      break;
    case (value >= 15 && value < 20):
      bgImageColor = "linear-gradient(to right bottom, #4ade80, #00c9a1, #00b1ae, #3d97a6, #607d8b)";
      textColor = "white";
      break;
    case (value >= 20 && value < 25):
      bgImageColor = "linear-gradient(to right bottom, #a3e635, #4ae278, #00d7ab, #00c8cc, #06b6d4)";
      textColor = "black";
      break;
    case (value >= 25 && value < 30):
      bgImageColor = "linear-gradient(to right bottom, #fef08a, #fce872, #fbdf59, #fad63d, #facc15)";
      textColor = "black";
      break;
    case (value >= 30 && value < 35):
      bgImageColor = "linear-gradient(to right bottom, #fde047, #ffc630, #ffac1d, #fd9014, #f97316)";
      textColor = "white";
      break;
    case (value >= 35):
      bgImageColor = "linear-gradient(to right bottom, #fb923c, #ec7831, #dc5d28, #cb4021, #b91c1c)";
      textColor = "white";
      break;
    default:
      bgImageColor = "linear-gradient(to right bottom, #4b5563, #47434e, #3d3338, #2d2525, #1c1917)";
      textColor = "white";
  }
  return {bg: bgImageColor, text: textColor};
}
