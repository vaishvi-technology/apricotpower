/* eslint-disable react/prop-types */

import "./style.css";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";

const CustomButton = (props) => {
  return (
    <>
      <button
        type={props?.type}
        style={props.style}
        disabled={props?.disabled}
        className={`customButton ${props?.variant} ${props?.className}`}
        onClick={props?.onClick}
      >
        {props?.text} <FontAwesomeIcon icon={props.icon} style={{fontSize:'20px',}} className="px-2" />
      </button>
    </>
  );
};
export default CustomButton;
