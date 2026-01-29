/* eslint-disable no-useless-catch */

import api from "../api";

export async function generateAuthToken() {
  try {
    const response = await api.get(`/genrate/token`);
    return response.data;
  } catch (error) {
    throw error;
  }
}


export async function generateTempToken(userId, userEmail) {
  const secret = "ec9f7364e1c6a05cde2cd0017d6350a7";

  const date = new Date().toISOString().split(".")[0] + "Z";

  const encoder = new TextEncoder();
  const raw = userId + date + userEmail + secret;
  const data = encoder.encode(raw);

  const hashBuffer = await crypto.subtle.digest("SHA-1", data); 
  const hashArray = Array.from(new Uint8Array(hashBuffer));
  const token = hashArray.map(b => b.toString(16).padStart(2, "0")).join("");

  return { token, date };
}