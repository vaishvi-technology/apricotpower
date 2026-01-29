/* eslint-disable react-refresh/only-export-components */
/* eslint-disable react/prop-types */
// CartContext.js
import { createContext, useContext, useState } from "react";
import { base_url } from "../api";
import { IPInfoContext } from "ip-info-react";

export const CartContext = createContext();

export const CartProvider = ({ children }) => {
  const userInfo = useContext(IPInfoContext);
  const [cartItems, setCartItems] = useState([]);
  const waitForIP = async () => {
    while (!userInfo.ip) {
      await new Promise((resolve) => setTimeout(resolve, 100));
    }
  };

  const fetchCount = async () => {
    try {
      await waitForIP();

      const token = localStorage.getItem("login");

      const queryParams = new URLSearchParams();

      if (!token && userInfo?.ip) {
        queryParams.append("ip", userInfo.ip);
      }

      const response = await fetch(
        `${base_url}/auth/user?${queryParams.toString()}`,
        {
          method: "GET",
          headers: {
            Accept: "application/json",
            ...(token && { Authorization: `Bearer ${token}` }),
            "Content-Type": "application/json",
          },
        }
      );

      if (response.status === 400) {
        console.log("Received 400 Bad Request. Rewards not updated.");
        return;
      }

      const data = await response.json();
      setCartItems(data || []);
    } catch (error) {
      console.log(error);
    }
  };

  return (
    <CartContext.Provider value={{ cartItems, fetchCount }}>
      {children}
    </CartContext.Provider>
  );
};
