/* eslint-disable react/prop-types */
// import { useState } from 'react'
// import "./style.css"

// import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
// import { faEyeSlash, faEye } from '@fortawesome/free-solid-svg-icons'

// const CustomInput = (props) => {

//   const [typePass, setTypePass] = useState(true)

//   const togglePassType = () => {
//     setTypePass(!typePass)
//   }

//   return (
//     <>
//       <div className="inputWrapper">
//         {props?.label && <label htmlFor={props?.id} className={props?.labelClass}>{props?.label} {props.icon}<span>{props?.required ? '*' : ''}</span></label>}
//         {props?.type === 'password'
//           ?
//           <div className="passwordWrapper">
//             <input type={typePass ? 'password' : 'text'}  placeholder={props?.placeholder} required={props?.required} id={props?.id} name={props?.name} className={`${props?.inputClass} passInput`} onChange={props?.onChange} value={props.value} />
//             <button type='button' className='eyeButton' onClick={togglePassType}><FontAwesomeIcon icon={typePass ? faEyeSlash : faEye} /></button>
//           </div>
//           :
//           <input type={props?.type}
//            placeholder={props?.placeholder}
//            required={props?.required}
//            disabled={props.disabled}
//           id={props?.id}
//           name={props?.name}
//           className={props?.inputClass}
//           onChange={props?.onChange}
//           onFocus={props?.onFocus}
//           onBlur={props?.onBlur}
//           accept={props.accept}
//           onKeyDown={props?.onKeyDown}
//           readOnly={props.readonly}
//           value={props.value} />
//         }
//       </div>
//     </>
//   )
// }
// export default CustomInput;

import { useState } from "react";
import InputMask from "react-input-mask";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import { faEyeSlash, faEye } from "@fortawesome/free-solid-svg-icons";
import "./style.css";

const CustomInput = (props) => {
  const [typePass, setTypePass] = useState(true);
  const togglePassType = () => setTypePass(!typePass);
  const renderInput = () => {
    const inputProps = {
      placeholder: props?.placeholder,
      required: props?.required,
      disabled: props?.disabled,
      id: props?.id,
      name: props?.name,
      className: props?.inputClass,
      onChange: props?.onChange,
      onFocus: props?.onFocus,
      onBlur: props?.onBlur,
      accept: props?.accept,
      onKeyDown: props?.onKeyDown,
      readOnly: props?.readonly,
      value: props?.value,
      type: props?.type,
    };

    if (props?.mask) {
      return <InputMask mask={props.mask} {...inputProps} />;
    }

    return <input {...inputProps} />;
  };

  return (
    <div className="inputWrapper">
      {props?.label && (
        <label htmlFor={props?.id} className={props?.labelClass}>
          {props?.label} {props.icon}
          <span>{props?.required ? "*" : ""}</span>
        </label>
      )}

      {props?.type === "password" ? (
        <div className="passwordWrapper">
          <input
            type={typePass ? "password" : "text"}
            placeholder={props?.placeholder}
            required={props?.required}
            id={props?.id}
            name={props?.name}
            className={`${props?.inputClass} passInput`}
            onChange={props?.onChange}
            value={props.value}
          />
          <button type="button" className="eyeButton" onClick={togglePassType}>
            <FontAwesomeIcon icon={typePass ? faEyeSlash : faEye} />
          </button>
        </div>
      ) : (
        renderInput()
      )}
    </div>
  );
};

export default CustomInput;
