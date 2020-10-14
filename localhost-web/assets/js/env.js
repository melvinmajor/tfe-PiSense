function getData() {
  fetch("assets/env.json").then(response => {
    response.json().then(data => {
      //console.log(data);
      updateHtml(data[data.length-1]);
    })
  }).catch(err => {
    console.log(err);
  })
}

function updateHtml(data) {
  let datetime = data.datetime;
  let temperature = data.temperature;
  let gas = data.gas;
  let humidity = data.humidity;
  let pressure = data.pressure;

  //update HTML
  document.getElementById("envTime").innerHTML = datetime;
  document.getElementById("temperature").innerHTML = temperature + " °C";
  document.getElementById("humidity").innerHTML = humidity + " %";
  document.getElementById("pressure").innerHTML = pressure + " hPa";

  //set colors
  colorsTemperature = getColor(temperature);
  document.getElementById("containerTemperature").style.background = colorsTemperature.bg;
  document.getElementById("containerTemperature").style.color = colorsTemperature.text;
}

function getColor(value) {
  switch (true) {
    case (value >= 50 && value < 100):
      color = "yellow";
      break;
    case (value >= 100 && value < 150):
      color = "orange";
      break;
    case (value >= 150 && value < 200):
      color = "red";
      break;
    default:
      color = "Lime";
  }
  return {bg: color, text: (value > 200) ? "white" : "black"};
}
