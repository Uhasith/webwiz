import axios from 'axios';
import {modifyDate} from "@/Utils/LastUpdate.js";

const fetchData = async (typeId, sensorId, ranking) => {
    try {
        const response = await axios.get('/public/sensors-data/recent', {
            params: {
                'sensorIds[]': sensorId,  // pass sensorId or 'all'
                'equipmentIds[]': typeId,  // pass typeId or 'all'
                'ranking': ranking  // pass ranking or 'all'
            }
        });
        return response.data;
    } catch (error) {
        console.log("Error:", error);
        throw error;
    }
}
const fetch2hHData = async (sensor_location_id) => {
    try {
        const response = await axios.get('/public/sensors-data/24h-recent', {
            params: {
                'sensor_location_id': sensor_location_id
            }
        });
        return response.data;
    } catch (error) {
        console.log("Error:", error);
        throw error;
    }
}
const fetchSensorDataHistory = async (locationId, sensorId, startDate, endDate,today = null) => {
    try {
        const response = await axios.get('/public/sensor-data/history', {
            params: {
                'locationId': locationId,
                'equipmentId': sensorId,
                'startTime': startDate,
                'endTime': endDate,
                'today': today,
            }
        });
        return response.data;
    } catch (error) {
        console.log("Error:", error);
        throw error;
    }
}
export default {fetchData, fetch2hHData,fetchSensorDataHistory};
