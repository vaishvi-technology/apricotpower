
import api from "../api";

export const getStates = async (id) => {
  try {
    const response = await api.get(`/state/${id}`);
    return response.data;
  } catch (error) {
    return error?.response.data;
  }
};
export const getCountry = async () => {
  try {
    const response = await api.get(`/countries`);
    return response.data;
  } catch (error) {
    return error?.response.data;
  }
};
export const getShipping = async () => {
  try {
    const response = await api.get(`/user-shipping-address`);
    return response.data;
  } catch (error) {
    return error?.response.data;
  }
};
export const updateShippingAddress = async (data) => {
  try {
    const response = await api.post(`/update/shipping-address`,data);
    return response.data;
  } catch (error) {
    return error?.response.data;
  }
};
