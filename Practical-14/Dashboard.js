
fetch("https://geocoding-api.open-meteo.com/v1/search?name=Changa&count=1&language=en&format=json")
    .then(function(response) {
        if (!response.ok) {
            throw new Error("Location request failed");
        }
        return response.json();
    })
    .then(function(data) {
        if (!data.results || data.results.length === 0) {
            throw new Error("Location not found");
        }

        let place = data.results[0];

        document.getElementById("location").innerText =
            place.name + ", " + (place.country || "");

        let url = "https://api.open-meteo.com/v1/forecast?latitude=" +
            place.latitude + "&longitude=" + place.longitude +
            "&current=temperature_2m,relative_humidity_2m,weather_code&temperature_unit=celsius";

        return fetch(url);
    })
    .then(function(response) {
        if (!response.ok) {
            throw new Error("Weather request failed");
        }
        return response.json();
    })
    .then(function(data) {
        document.getElementById("temperature").innerText =
            data.current.temperature_2m + " °C";

        document.getElementById("humidity").innerText =
            data.current.relative_humidity_2m + "%";

        document.getElementById("condition").innerText =
            getWeatherCondition(data.current.weather_code);
    })
    .catch(function(error) {
        console.error(error);
        document.getElementById("location").innerText = "Unavailable";
        document.getElementById("temperature").innerText = "Unavailable";
        document.getElementById("humidity").innerText = "Unavailable";
        document.getElementById("condition").innerText = "Unavailable";
    });

function getWeatherCondition(code) {
    if (code === 0) return "Clear Sky";
    if ([1, 2, 3].includes(code)) return "Partly Cloudy";
    if ([45, 48].includes(code)) return "Fog";
    if ([51, 53, 55, 56, 57].includes(code)) return "Drizzle";
    if ([61, 63, 65, 66, 67].includes(code)) return "Rain";
    if ([71, 73, 75, 77].includes(code)) return "Snow";
    if ([80, 81, 82].includes(code)) return "Rain Showers";
    if ([85, 86].includes(code)) return "Snow Showers";
    if ([95, 96, 99].includes(code)) return "Thunderstorm";
    return "Unknown";
}