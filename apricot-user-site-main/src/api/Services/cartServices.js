
import api from "../api";


export const getCart = async () => {
  try {
    const response = await api.get(`/cart`);
    return response.data;
  } catch (error) {
    return error?.response.data;
  }
};
export const addCart = async (data) => {
  try {
    const response = await api.post(`/cart/add`,data);
    return response.data;
  } catch (error) {
    return error?.response.data;
  }
};

