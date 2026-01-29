import { useEffect } from "react";

export default function LoyaltyLionWidget() {
  useEffect(() => {
    if (window.loyaltylion && window.loyaltylion.ui) {
      window.loyaltylion.ui.refresh();
    }
  }, []);

  return (
    <>
      

      <div data-lion-account></div>
    </>
  );
}
