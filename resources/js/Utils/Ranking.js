export function ranking(markerAqi, defaultAqi) {
    if (markerAqi) {
        markerAqi = markerAqi?.SL;
        if (markerAqi === "Good") {
            return "good";
        } else if (markerAqi === "Moderate") {
            return "moderate";
        } else if (markerAqi === "Bad" || markerAqi === "Poor") {
            return "poor";
        } else if (markerAqi === "Unhealthy" || markerAqi === "severe") {
            return "severe";
        }
        return "hazardous";
    }
    defaultAqi = defaultAqi?.SL;
    if (defaultAqi === "Good") {
        return "good";
    } else if (defaultAqi === "Moderate") {
        return "moderate";
    } else if (defaultAqi === "Bad" || defaultAqi === "Poor") {
        return "poor";
    } else if (defaultAqi === "Unhealthy" || defaultAqi === "severe") {
        return "severe";
    }
    return "hazardous";
}
export function markerIcons(markerAqi) {
    if (markerAqi) {
        markerAqi = markerAqi?.aqi?.SL;
        console.log(markerAqi)
        if (markerAqi === "Good") {
            return "/images/markers/greenmarker.svg";
        } else if (markerAqi === "Moderate") {
            return "/images/markers/moderatemarker.png";
        } else if (markerAqi === "Bad" || markerAqi === "Poor") {
            return "/images/markers/poormarker.png";
        } else if (markerAqi === "Unhealthy") {
            return "/images/markers/unhealthymarker.png";
        } else if (markerAqi === "severe") {
            return "/images/markers/severemarker.png";
        }
        return "/images/markers/hazardousmarker.png";
    }
}
export function faces(markerAqi, defaultAqi) {
    if (markerAqi) {
        markerAqi = markerAqi?.SL;
        if (markerAqi === "Good") {
            return "/images/greenface.png";
        } else if (markerAqi === "Moderate") {
            return "/images/moderate.png";
        } else if (markerAqi === "Bad" || markerAqi === "Poor") {
            return "/images/poor.png";
        } else if (markerAqi === "Unhealthy") {
            return "/images/unhealthy.png";
        }else if (markerAqi === "severe") {
            return "/images/severe.png";
        }
        return "/images/hazardous.png";
    }
    defaultAqi = defaultAqi?.SL;
    if(defaultAqi){
        if (defaultAqi === "Good") {
            return "/images/greenface.png";
        } else if (defaultAqi === "Moderate") {
            return "/images/moderate.png";
        } else if (defaultAqi === "Bad" || defaultAqi === "Poor") {
            return "/images/poor.png";
        } else if (defaultAqi === "Unhealthy") {
            return "/images/unhealthy.png";
        } else if (defaultAqi === "severe") {
            return "/images/severe.png";
        }
        return "/images/hazardous.png";
    }
    return "";
}
export function colours(markerAqi, opacity) {
    if (markerAqi) {
        if (markerAqi === "Good") {
            return opacity? "rgba(25,175,84,0.1)": "rgba(25,175,84,1)";
        } else if (markerAqi === "Moderate") {
            return opacity? "rgba(207,234,43,0.1)":"rgba(207,234,43,1)";
        } else if (markerAqi === "Bad" || markerAqi === "Poor") {
            return opacity? "rgba(238,181,48,0.1)":"rgba(238,181,48,1)";
        } else if (markerAqi === "Unhealthy") {
            return opacity? "rgba(253,37,37,0.1)":"rgba(253,37,37,1)";
        }else if (markerAqi === "severe") {
            return opacity? "rgba(158,55,242,0.1)":"rgba(158,55,242,1)";
        }
        return opacity? "rgba(177,0,7,0.1)":"rgba(177,0,7,1)";
    }
}
