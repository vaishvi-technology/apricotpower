/* eslint-disable react/prop-types */
import React from "react";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import { faTrashAlt } from "@fortawesome/free-solid-svg-icons";
import "./style.css";

export const CustomSelectAutoShip = (props) => {
  const handleClick = (e) => {
    // Optional: handle remove if needed
  };

  const handleChange = (e) => {
    const [freqType, freq] = e.target.value.split("|");
    if (props.setValue) {
      props.setValue({ freqType, freq });
    }
    if (props.onChange) {
      props.onChange(e);
    }
  };

  return (
    <div className="inputWrapper">
      <div className="inputIcon">
        <FontAwesomeIcon icon={props?.iconShow} />
      </div>
      {props?.label && (
        <label htmlFor={props?.id} className={`mainLabel ${props?.labelClass}`}>
          {props?.label}
          {props?.required ? "*" : ""}
        </label>
      )}
      <div className="fieldData">
        <select
          className={props?.selectClass}
          name={props?.name}
          onChange={handleChange}
          value={`${props.value?.freqType || ""}|${props.value?.freq || ""}`}
        >
          {Array.isArray(props.option) &&
            props.option.map((item, index) => (
              <option value={`${item.freqType}|${item.freq}`} key={index}>
                {item.name}
              </option>
            ))}
        </select>
        {props?.buttonAction && (
          <button type="button" onClick={handleClick}>
            <FontAwesomeIcon
              icon={faTrashAlt}
              className="removeField"
            ></FontAwesomeIcon>
          </button>
        )}
      </div>
    </div>
  );
};
