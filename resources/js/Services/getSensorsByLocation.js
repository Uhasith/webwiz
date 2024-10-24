import axios from 'axios';

const getSensorsByLocation = async (locationId) => {
  try {
    const response = await axios.get(`/public/sensors/location/${locationId}`);
    return response.data;
  } catch (error) {
    console.error('Error fetching sensors:', error);
    throw error; // Re-throw the error if you want to handle it later
  }
};

export default getSensorsByLocation;
