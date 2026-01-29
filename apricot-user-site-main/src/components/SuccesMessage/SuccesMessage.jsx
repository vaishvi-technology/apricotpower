/* eslint-disable react/prop-types */
export default function SuccesMessage({ title, subTitle, setIsSuccess }) {
  return (
    <div className="success-message">
      <div className="icon">✔</div>

      <div className="message-content">
        <h4>{title}</h4>
        <p>{subTitle}</p>
      </div>

      <div className="cros-btn" onClick={() => setIsSuccess(false)}>
        ✖
      </div>
    </div>
  );
}
