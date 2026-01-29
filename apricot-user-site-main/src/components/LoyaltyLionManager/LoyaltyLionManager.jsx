/* eslint-disable react/prop-types */
import { useEffect, useRef } from "react";
import {  generateTempToken } from "../../api/Services/generateAuthToken";

export default function LoyaltyLionManager({ user }) {
  const isInitialized = useRef(false);

  useEffect(() => {
    const initLoyaltyLion = async () => {
      if (!window.loyaltylion) return;

      if (!isInitialized.current) {
        window.loyaltylion.init({
          token: "187b89fd99358b2deef2337972f7f790", 
        });
        isInitialized.current = true;
      }

      window.loyaltylion.on("ready", async () => {


        if (!user) {
          window.loyaltylion.ui?.refresh({});
          return;
        }

        const authData = await generateTempToken(user?.id,user?.email);

        const payload = {
          customer: { id: user.id, email: user.email },
          auth: authData,
        };

          window.loyaltylion.authenticateCustomer(payload);
      });
    };

    initLoyaltyLion();
  }, [user]);

  return null;
}