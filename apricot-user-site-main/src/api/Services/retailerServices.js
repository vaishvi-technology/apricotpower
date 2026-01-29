
import api from "../api";



export const addRetailer = async (data) => {
  try {
    const response = await api.post(`/retailer`,data);
    return response.data;
  } catch (error) {
    return error?.response.data;
  }
};
