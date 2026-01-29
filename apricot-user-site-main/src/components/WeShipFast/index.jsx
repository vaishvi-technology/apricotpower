/* eslint-disable react/prop-types */
import React from 'react'

const WeShipFast = ({data}) => {
  return (
    <section className="we-ship-fast-sec">
        <div className="container">
            <div className="row">
                <div className="col-md-12">
                    <div className="we-ship-fast-content">
                        <div
                      dangerouslySetInnerHTML={{ __html: data?.description }}
                    />
                        {/* <h2>We Ship Fast | Same Day Shipping¹ | Located in the US</h2>
                        <h3>100% Money Back Guaranteed²</h3>
                        <h4>Contact us:</h4>
                        <div className="phone-email">
                            <p><span>Phone:</span> <a href="tel:866-468-7487">866-468-7487</a></p>
                            <p><span>Email:</span> <a href="mailto:CustomerService@ApricotPower.com">CustomerService@ApricotPower.com</a></p>
                        </div>
                        <div className="swe-ship-fast-moreDetail">
                            <p>¹ Shipping cutoff 2PM CST. Open Monday - Friday.</p>
                            <p>² Please see return policy for further details.</p> */}
                        {/* </div> */}
                    </div>
                </div>
            </div>
        </div>
      </section>
  )
}

export default WeShipFast
