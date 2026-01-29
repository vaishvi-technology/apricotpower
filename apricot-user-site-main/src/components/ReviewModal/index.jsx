/* eslint-disable react/prop-types */
import { useEffect } from "react";
import CustomModal from "../CustomModal";

export default function ReviewModal({ show, setshow }) {
  useEffect(() => {
    const script = document.createElement("script");
    script.src = "https://api.feefo.com/api/javascript/apricot-power";
    script.async = true;
    document.body.appendChild(script);

    return () => {
      document.body.removeChild(script);
    };
  }, [show]);
  return (
    <CustomModal show={show} size="lg" close={() => setshow(false)}>
      <div
        id="feefo-service-review-widgetId"
        className="feefo-review-widget-service"
      ></div>
    </CustomModal>
  );
}
