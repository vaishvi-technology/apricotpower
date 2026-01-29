/* eslint-disable react/prop-types */
import { useState } from "react";
import "./style.css";

const CustomTextArea = (props) => {
  return (
    <div className="inputWrapper">
      {props?.label && (
        <label htmlFor={props?.id} className={props?.labelClass}>
          {props?.label} {props.icon}
          <span>{props?.required ? "*" : ""}</span>
        </label>
      )}

      <textarea
        id={props?.id}
        name={props?.name}
        placeholder={props?.placeholder}
        required={props?.required}
        disabled={props?.disabled}
        className={props?.inputClass}
        onChange={props?.onChange}
        onFocus={props?.onFocus}
        onBlur={props?.onBlur}
        onKeyDown={props?.onKeyDown}
        readOnly={props?.readonly}
        rows={props?.rows || 4}
        value={props?.value}
      ></textarea>
    </div>
  );
};

export default CustomTextArea;
