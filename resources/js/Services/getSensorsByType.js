import axios from 'axios';

const fetchSensorsByType = async (typeId) => {
  try {
    const response = await axios.get(`/sensors/${typeId}`);
    return response.data;
  } catch (error) {
    console.error('Error fetching sensors:', error);
    throw error; // Re-throw the error if you want to handle it later
  }
};

export default { fetchSensorsByType };
