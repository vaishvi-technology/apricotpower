import { createSlice } from "@reduxjs/toolkit";
import { base_url } from "../../api";

const initialState = {
  data: null,
};

export const GetProfileData = () => async (dispatch) => {
  const ip = JSON.parse(localStorage.getItem("storefeuinverau_country_code"));

  try {
    const queryParams = new URLSearchParams();
    if (ip.ip) queryParams.append("ip", ip.ip);
    const token = localStorage.getItem("login");
    if (token) {
      const response = await fetch(base_url + `/auth/user?${queryParams}`, {
        method: "GET",
        headers: {
          "Content-Type": "application/json",
          Authorization: `Bearer ${token}`,
        },
      });
      if (!response.ok) {
        throw new Error("Failed to fetch profile data");
      }

      const result = await response.json();
      // dispatch(SaveProfileData(result));
    }
  } catch (error) {
    console.error("Error fetching profile data:", error);
    throw error;
  }
};

export const UserSlice = createSlice({
  name: "user",
  initialState,
  reducers: {
    SaveProfileData: (state, action) => {
      state.data = action.payload;
    },
    removeProfileData: (state) => {
      state.data = null;
    },
  },
});

export const { SaveProfileData, removeProfileData } = UserSlice.actions;

export default UserSlice.reducer;
