
import api from "../api";


export const getProduct = async () => {
  try {
    const response = await api.get(`/products`);
    return response.data;
  } catch (error) {
    return error?.response.data;
  }
};

export const upSellProduct = async () => {
  try {
    const response = await api.get(`/products?filter=upsell`);
    return response.data;
  } catch (error) {
    return error?.response.data;
  }
};

