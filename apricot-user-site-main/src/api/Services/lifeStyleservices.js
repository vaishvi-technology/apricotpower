/* eslint-disable no-useless-catch */
import api from "../api";

export const getLifeStyle = async () => {
  try {
    const response = await api.get(`/gallery`);
    return response.data;
  } catch (error) {
    throw error;
  }
};

