
import axios from "axios";
import api from "../api";


export const getTestimonial = async () => {
  try {
    const response = await api.get(`/testimonials/list/`);
    return response.data;
  } catch (error) {
    return error?.response.data;
  }
};
export const getImageUrl = async (data) => {
  try {
    const response = await axios.post(`https://staging.apricotpower.com/apricot-app/upload.php`,data);
    return response.data;
  } catch (error) {
    return error?.response.data;
  }
};
export const addTestimonial = async (data,id) => {
  try {
    const response = await api.post(`/testimonials/add?order_id=${id}`,data);
    return response.data;
  } catch (error) {
    return error?.response.data;
  }
};

