
import axios from 'axios';
import qs from 'qs';


export const baseUrl = 'https://staging.apricotpower.com:5013';


const api = axios.create({
    baseURL: baseUrl,
    headers: {
        'Content-Type': 'application/json',
    },
});

api.interceptors.request.use(
    (config) => {
        const token = localStorage.getItem('login');
        if (token) {
            config.headers.Authorization = `Bearer ${token}`;
        }
        if (config.data instanceof FormData) {
            config.headers['Content-Type'] = 'multipart/form-data';
        } else if (typeof config.data === 'object' && !(config.data instanceof Array)) {
            config.headers['Content-Type'] = 'application/json';
        } else if (config.headers['Content-Type'] === 'application/x-www-form-urlencoded') {
            config.data = qs.stringify(config.data);
        }

        return config;
    },
    (error) => Promise.reject(error)
);

api.interceptors.response.use(
    (response) => response,
    (error) => {
        if (error.response) {
            switch (error.response.status) {
                case 401:
                    console.error('Unauthorized access - possibly invalid token');
                    break;
                case 403:
                    console.error('Access forbidden');
                    break;
                case 404:
                    console.error('Resource not found');
                    break;
                case 500:
                    console.error('Internal server error');
                    break;
                default:
                    console.error('An error occurred');
            }
        }
        return Promise.reject(error);
    }
);

export default api;
