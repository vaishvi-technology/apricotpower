
import api from "../api";


export const getBlogsBySlug = async (slug) => {
  try {
    const response = await api.get(`/blog/${slug}`);
    return response.data;
  } catch (error) {
    return error?.response.data;
  }
};

