function getDataAqi() {
  fetch("assets/aqi.json").then(response => {
    response.json().then(data => {
      //console.log(data);
      updateHtmlAqi(data[data.length - 1]);
    })
  }).catch(err => {
    console.log(err);
  })
}

function updateHtmlAqi(data) {
  let datetime = new Date(data.datetime);
  let aqiPm25 = calcAQIpm25(data.PM2);
  let aqiPm10 = calcAQIpm10(data.PM10);

  //update HTML
  document.getElementById("aqiTime").innerHTML = datetime.toLocaleString("fr-FR",
    {year: "numeric", month: "long", day: "2-digit", hour: "2-digit",
      minute: "2-digit", second: "2-digit", timeZoneName: "short"});
  document.getElementById("aqiPm25").innerHTML = aqiPm25;
  document.getElementById("aqiPm10").innerHTML = aqiPm10;
  document.getElementById("pm25").innerHTML = "(PM2.5: " + data.PM2 + " µg/m³)";
  document.getElementById("pm10").innerHTML = "(PM10: " + data.PM10 + " µg/m³)";

  //set info section
  let aqiInfo =getAqiInfo(aqiPm25, aqiPm10);

  //set colors
  let colorsPm25 = getColorAqi(aqiPm25);
  let colorsPm10 = getColorAqi(aqiPm10);
  document.getElementById("containerPm25").style.background = colorsPm25.bg;
  document.getElementById("containerPm25").style.color = colorsPm25.text
  document.getElementById("containerPm10").style.background = colorsPm10.bg;
  document.getElementById("containerPm10").style.color = colorsPm10.text;
  document.getElementById("aqiInfo").innerHTML = aqiInfo.text;
  document.getElementById("aqiInfo").style.color = aqiInfo.color;
}

function getColorAqi(aqi) {
  let bgImageColor, textColor;

  switch (true) {
    case (aqi >= 50 && aqi < 100):
      bgImageColor = "linear-gradient(to right bottom, #fef08a, #fce872, #fbdf59, #fad63d, #facc15)";
      textColor = "black";
      break;
    case (aqi >= 100 && aqi < 150):
      bgImageColor = "linear-gradient(to right bottom, #fde047, #ffc630, #ffac1d, #fd9014, #f97316)";
      textColor = "white";
      break;
    case (aqi >= 150 && aqi < 200):
      bgImageColor = "linear-gradient(to right bottom, #fb923c, #ec7831, #dc5d28, #cb4021, #b91c1c)";
      textColor = "white";
      break;
    case (aqi >= 200 && aqi < 300):
      bgImageColor = "linear-gradient(to right bottom, #c084fc, #b06ef1, #a057e5, #8f3fda, #7e22ce)";
      textColor = "white";
      break;
    case (aqi >= 300):
      bgImageColor = "linear-gradient(to right bottom, #a16207, #98560b, #8e4b0d, #83400e, #78350f)";
      textColor = "white";
      break;
    default:
      bgImageColor = "linear-gradient(to right bottom, #a3e635, #4ae278, #00d7ab, #00c8cc, #06b6d4)";
      textColor = "black";
  }
  return {bg: bgImageColor, text: textColor};
}

function getAqiInfo(aqiPm25, aqiPm10) {
  let description, textColor;

  switch (true) {
    case ((aqiPm25 >= 300) || (aqiPm10 >= 300)):
      description = "<strong>Condition d'urgence !</strong>" +
        "<br>Exposition à des niveaux extrêmement élevés de pollution particulaire !";
      textColor = "#78350f";
      break;
    case ((aqiPm25 >= 200 && aqiPm25 < 300) || (aqiPm10 >= 200 && aqiPm10 < 300)):
      description = "<strong>Alerte !</strong> Effets graves sur votre santé !";
      textColor = "#7e22ce";
      break;
    case ((aqiPm25 >= 150 && aqiPm25 < 200) || (aqiPm10 >= 150 && aqiPm10 < 200)):
      description = "<strong>Effets néfastes sur la santé !</strong>" +
        "<br>Attention plus particulièrement aux personnes du groupe sensible pouvant ressentir des effets plus graves."
      textColor = "#b91c1c";
      break;
    case ((aqiPm25 >= 100 && aqiPm25 < 150) || (aqiPm10 >= 100 && aqiPm10 < 150)):
      description = "Les groupes sensibles courent un plus grand risque d'exposition à l'ozone." +
        "<br>Ils sont plus à risque en raison de la présence de particules dans l'air.";
      textColor = "#fd9014";
      break;
    case ((aqiPm25 >= 50 && aqiPm25 < 100) || (aqiPm10 >= 50 && aqiPm10 < 100)):
      description = "<strong>Pensez à ouvrir la fenêtre...</strong>";
      textColor = "#fd9014";
      break;
    default:
      description = "D'après les mesures de l'indice de qualité de l'air, il n'y a rien à signaler.";
      textColor = "inherit";

  }
  return {text: description, color: textColor};
}

function calcAQIpm25(pm25) {
  let pm1 = 0;
  let pm2 = 12;
  let pm3 = 35.4;
  let pm4 = 55.4;
  let pm5 = 150.4;
  let pm6 = 250.4;
  let pm7 = 350.4;
  let pm8 = 500.4;

  let aqi1 = 0;
  let aqi2 = 50;
  let aqi3 = 100;
  let aqi4 = 150;
  let aqi5 = 200;
  let aqi6 = 300;
  let aqi7 = 400;
  let aqi8 = 500;

  let aqipm25 = 0;

  if (pm25 >= pm1 && pm25 <= pm2) {
    aqipm25 = ((aqi2 - aqi1) / (pm2 - pm1)) * (pm25 - pm1) + aqi1;
  } else if (pm25 >= pm2 && pm25 <= pm3) {
    aqipm25 = ((aqi3 - aqi2) / (pm3 - pm2)) * (pm25 - pm2) + aqi2;
  } else if (pm25 >= pm3 && pm25 <= pm4) {
    aqipm25 = ((aqi4 - aqi3) / (pm4 - pm3)) * (pm25 - pm3) + aqi3;
  } else if (pm25 >= pm4 && pm25 <= pm5) {
    aqipm25 = ((aqi5 - aqi4) / (pm5 - pm4)) * (pm25 - pm4) + aqi4;
  } else if (pm25 >= pm5 && pm25 <= pm6) {
    aqipm25 = ((aqi6 - aqi5) / (pm6 - pm5)) * (pm25 - pm5) + aqi5;
  } else if (pm25 >= pm6 && pm25 <= pm7) {
    aqipm25 = ((aqi7 - aqi6) / (pm7 - pm6)) * (pm25 - pm6) + aqi6;
  } else if (pm25 >= pm7 && pm25 <= pm8) {
    aqipm25 = ((aqi8 - aqi7) / (pm8 - pm7)) * (pm25 - pm7) + aqi7;
  }
  return aqipm25.toFixed(0);
}

function calcAQIpm10(pm10) {
  let pm1 = 0;
  let pm2 = 54;
  let pm3 = 154;
  let pm4 = 254;
  let pm5 = 354;
  let pm6 = 424;
  let pm7 = 504;
  let pm8 = 604;

  let aqi1 = 0;
  let aqi2 = 50;
  let aqi3 = 100;
  let aqi4 = 150;
  let aqi5 = 200;
  let aqi6 = 300;
  let aqi7 = 400;
  let aqi8 = 500;

  let aqipm10 = 0;

  if (pm10 >= pm1 && pm10 <= pm2) {
    aqipm10 = ((aqi2 - aqi1) / (pm2 - pm1)) * (pm10 - pm1) + aqi1;
  } else if (pm10 >= pm2 && pm10 <= pm3) {
    aqipm10 = ((aqi3 - aqi2) / (pm3 - pm2)) * (pm10 - pm2) + aqi2;
  } else if (pm10 >= pm3 && pm10 <= pm4) {
    aqipm10 = ((aqi4 - aqi3) / (pm4 - pm3)) * (pm10 - pm3) + aqi3;
  } else if (pm10 >= pm4 && pm10 <= pm5) {
    aqipm10 = ((aqi5 - aqi4) / (pm5 - pm4)) * (pm10 - pm4) + aqi4;
  } else if (pm10 >= pm5 && pm10 <= pm6) {
    aqipm10 = ((aqi6 - aqi5) / (pm6 - pm5)) * (pm10 - pm5) + aqi5;
  } else if (pm10 >= pm6 && pm10 <= pm7) {
    aqipm10 = ((aqi7 - aqi6) / (pm7 - pm6)) * (pm10 - pm6) + aqi6;
  } else if (pm10 >= pm7 && pm10 <= pm8) {
    aqipm10 = ((aqi8 - aqi7) / (pm8 - pm7)) * (pm10 - pm7) + aqi7;
  }
  return aqipm10.toFixed(0);
}
