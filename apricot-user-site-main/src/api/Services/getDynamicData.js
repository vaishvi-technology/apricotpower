/* eslint-disable no-useless-catch */
import api from "../api";

export const getCmcPages = async (name) => {
  try {
    const response = await api.get(`/content/${name}`);
    return response.data;
  } catch (error) {
    throw error;
  }
};
export const getTag = async (id) => {
  try {
    const response = await api.get(`/pages/tags/${id}`);
    return response.data;
  } catch (error) {
    throw error;
  }
};
