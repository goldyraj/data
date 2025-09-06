<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Warther Demo</title>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
  <h2>Weather Info</h2>
  <input type="text" id="city" placeholder="Enter city name">
  <button id="getWeather">Get Weather</button>

  <div id="result"></div>

  <script>
    $(document).ready(function(){
      $("#getWeather").click(function(){
        var city = $("#city").val();
        var apiKey = "YOUR_API_KEY"; // Replace with your OpenWeather API key
        var url = "https://api.openweathermap.org/data/2.5/weather?q=" + city  + "&units=metric";

        $.ajax({
          url: url,
          type: "GET",
          dataType: "json",
          success: function(data) {
            var output = `
              <h3>Weather in ${data.name}, ${data.sys.country}</h3>
              <p><b>Temperature:</b> ${data.main.temp} °C</p>
              <p><b>Feels like:</b> ${data.main.feels_like} °C</p>
              <p><b>Condition:</b> ${data.weather[0].description}</p>
              <p><b>Humidity:</b> ${data.main.humidity}%</p>
              <p><b>Wind Speed:</b> ${data.wind.speed} m/s</p>
            `;
            $("#result").html(output);
          },
          error: function() {
            $("#result").html("<p style='color:red;'>City not found. Please try again.</p>");
          }
        });
      });
    });
  </script>
</body>
</html>
