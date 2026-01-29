/* eslint-disable react/prop-types */
import { Modal } from "react-bootstrap";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import {
  faTimes,
  faQuestionCircle,
  faCheckCircle,
} from "@fortawesome/free-solid-svg-icons";

import CustomButton from "../CustomButton";



import "./style.css";

const CustomModal = (props) => {
  return (
    <>
      <Modal
        show={props?.show}
        centered
        onHide={props?.close}
        size={props.size || "md"}
      >
        <button className={`closeButton cros-inner`} onClick={props?.close}>
          <FontAwesomeIcon icon={faTimes} />
        </button>
        <Modal.Body className={props.children ? "" : "text-center"}>
          {props?.children ? (
            ""
          ) : // You can add alternative content here, like an image or text
          props?.success ? (
            <FontAwesomeIcon icon={faCheckCircle} className="checkMark" />
          ) : (
            <FontAwesomeIcon icon={faQuestionCircle} className="questionMark" />
          )}

          <div className="modalContent">
            {props?.heading ? (
              <h2 className="modalHeading lemonMilk-med ">{props?.heading}</h2>
            ) : (
              <></>
            )}

            {props?.children ? (
              <>
                {/* <form onSubmit={props?.handleSubmit} className='formDataStyle'> */}
                {props?.children}
                {/* </form> */}
              </>
            ) : props?.success ? (
              <CustomButton
                onClick={props?.close}
                variant="primaryButton"
              
                text={props?.btnTxt ? props?.btnTxt : "Ok"}
              />
            ) : (
              <>
                <CustomButton
                  onClick={props?.action}
                  variant="primaryButton"
                  text="Yes"
                  className="me-2"
                />
                <CustomButton
                  onClick={props?.close}
                  variant="secondaryButton"
                  text="No"
                />
              </>
            )}
          </div>
        </Modal.Body>
      </Modal>
    </>
  );
};

export default CustomModal;
