export function weatherIcons(status) {
    if (status) {
        if (status === "sunny") {
            return "/images/sunny.png";
        } else if (status === "rainy") {
            return "/images/rainy.png";
        } else if (status === "cloudy") {
            return "/images/cloudy.png";
        } else if (status === "partly cloudy") {
            return "/images/partly_cloudy.png";
        }
        return "";
    }

}
