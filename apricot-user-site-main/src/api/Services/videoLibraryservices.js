/* eslint-disable no-useless-catch */
import api from "../api";

export const getVideo = async () => {
  try {
    const response = await api.get(`/slider/videos`);
    return response.data;
  } catch (error) {
    throw error;
  }
};

