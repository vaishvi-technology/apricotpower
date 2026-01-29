import React from "react";

const EmailPrefHead = (props) => {
  return (
      <div className={`email-preferences-head email-preferences-head-${props.variant}`}>
        <h5 className="email-preferences-title">
          {props.text}
        </h5>
      </div>
  );
};

export default EmailPrefHead;
