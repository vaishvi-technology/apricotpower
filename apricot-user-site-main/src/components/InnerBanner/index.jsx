/* eslint-disable react/prop-types */

const InnerBanner = (props) => {
  return (
    <section className={`inner-banner ${props.className} `}>
      <div className="container-fluid">
        <div className="row">
          <div className="col-md-12 p-0">
            <div className="inner-banner-head">
              {props?.small ? (
                <h3
                  style={{
                    color: "white",
                  }}
                >
                  {props.boldText1 && (
                    <span className="bold-font">{props.boldText1}</span>
                  )}{" "}
                  {props.lightText && (
                    <span className="normal-font">{props.lightText}</span>
                  )}{" "}
                  {props.boldText && (
                    <span className="bold-font">{props.boldText}</span>
                  )}
                </h3>
              ) : (
                <h1>
                  {props.boldText1 && (
                    <span className="bold-font">{props.boldText1}</span>
                  )}{" "}
                  {props.lightText && (
                    <span className="normal-font">{props.lightText}</span>
                  )}{" "}
                  {props.boldText && (
                    <span className="bold-font">{props.boldText}</span>
                  )}
                </h1>
              )}
            </div>
          </div>
        </div>
      </div>
    </section>
  );
};

export default InnerBanner;
