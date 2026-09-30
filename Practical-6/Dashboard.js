let studentId = sessionStorage.getItem("studentId");

document.getElementById("studentId").innerText = studentId;
document.getElementById("enrollmentNumber").innerText = studentId;




let locationName = "Changa";



let locationUrl =
    "https://geocoding-api.open-meteo.com/v1/search" +
    "?name=" + encodeURIComponent(locationName) +
    "&count=1" +
    "&language=en" +
    "&format=json";


fetch(locationUrl)
    .then(function(response) {

        if (!response.ok) {
            throw new Error("Location could not be found");
        }

        return response.json();

    })
    .then(function(locationData) {

        if (!locationData.results || locationData.results.length === 0) {
            throw new Error("Location not found");
        }

        let location = locationData.results[0];

        let latitude = location.latitude;
        let longitude = location.longitude;

        let cityName = location.name;
        let countryName = location.country;


      

        let weatherUrl =
            "https://api.open-meteo.com/v1/forecast" +
            "?latitude=" + latitude +
            "&longitude=" + longitude +
            "&current=temperature_2m,relative_humidity_2m,weather_code" +
            "&temperature_unit=celsius";


        return fetch(weatherUrl)
            .then(function(response) {

                if (!response.ok) {
                    throw new Error("Weather data could not be loaded");
                }

                return response.json();

            })
            .then(function(weatherData) {

                let temperature =
                    weatherData.current.temperature_2m;

                let humidity =
                    weatherData.current.relative_humidity_2m;

                let weatherCode =
                    weatherData.current.weather_code;


                document.getElementById("location").innerText =
                    cityName + ", " + countryName;

                document.getElementById("temperature").innerText =
                    temperature + " °C";

                document.getElementById("humidity").innerText =
                    humidity + "%";

                document.getElementById("condition").innerText =
                    getWeatherCondition(weatherCode);

            });

    })
    .catch(function(error) {

        console.log(error);

        document.getElementById("location").innerText =
            "Location not found";

        document.getElementById("temperature").innerText =
            "Unable to load";

        document.getElementById("humidity").innerText =
            "Unable to load";

        document.getElementById("condition").innerText =
            "Unable to load";

    });



function getWeatherCondition(code) {

    if (code === 0) {
        return "Clear Sky";
    }

    if (code === 1 || code === 2 || code === 3) {
        return "Partly Cloudy";
    }

    if (code === 45 || code === 48) {
        return "Fog";
    }

    if (code === 51 || code === 53 || code === 55) {
        return "Drizzle";
    }

    if (code === 61 || code === 63 || code === 65) {
        return "Rain";
    }

    if (code === 71 || code === 73 || code === 75) {
        return "Snow";
    }

    if (code === 80 || code === 81 || code === 82) {
        return "Rain Showers";
    }

    if (code === 95) {
        return "Thunderstorm";
    }

    if (code === 96 || code === 99) {
        return "Thunderstorm with Hail";
    }

    return "Unknown";
}