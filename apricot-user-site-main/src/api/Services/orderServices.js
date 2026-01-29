
import api from "../api";

export const getOrderDetail = async (id) => {
  try {
    const response = await api.get(`/order/detail/${id}`);
    return response.data;
  } catch (error) {
    return error?.response.data;
  }
};

export const getCoinBaseUrl = async () => {
  try {
    const response = await api.post(`/coin-base`);
    return response.data;
  } catch (error) {
    return error?.response.data;
  }
};

