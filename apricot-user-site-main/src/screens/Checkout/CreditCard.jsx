/* eslint-disable react/prop-types */
import { useState } from "react";
import { Col, Row } from "react-bootstrap";
import visa from "../../assets/images/visa.png";
import mastercard from "../../assets/images/mastercard.png";
import discover from "../../assets/images/discover.png";
import amex from "../../assets/images/amex.png";
import unionpay from "../../assets/images/unionpay.png";
import jcb from "../../assets/images/jcb.png";
import dinersclub from "../../assets/images/dinersclub.png";
import maestro from "../../assets/images/maestro.png";
import visaElectron from "../../assets/images/visaElectron.png";
import ruPay from "../../assets/images/ruPay.png";
import solo from "../../assets/images/solo.png";
import elo from "../../assets/images/elo.png";
import { detectCardType } from "./data";

export default function CreditCard({ setFormData }) {
  const [expiryDate, setExpiryDate] = useState("");
  const [cardNumber, setCardNumber] = useState("");
  const [cardType, setCardType] = useState({
    name: "Unknown",
    maxLength: 16,
    formattedName: "Unknown Card",
  });

  const cardImages = {
    Visa: visa,
    Mastercard: mastercard,
    Discover: discover,
    Amex: amex,
    Unionpay: unionpay,
    Jcb: jcb,
    DinersClub: dinersclub,
    Maestro: maestro,
    VisaElectron: visaElectron,
    RuPay: ruPay,
    Solo: solo,
    Elo: elo,
  };

  const handleCardNumberChange = (e) => {
    let value = e.target.value;
    value = value.replace(/\D/g, "");
    const type = detectCardType(value);
    setCardType(type);
    let maxLength = 16;
    if (type === "Amex") {
      maxLength = 15;
    }
    if (value.length > maxLength) {
      value = value.slice(0, maxLength);
    }
    if (type === "Amex") {
      value = value.replace(/(\d{4})(?=\d)/g, "$1 ");
    } else {
      value = value.replace(/(\d{4})(?=\d)/g, "$1 ");
    }

    setCardNumber(value);
    setFormData((prev) => ({
      ...prev,
      card_number: value,
    }));
  };
  const handleExpiryChange = (e) => {
    let value = e.target.value;
    value = value.replace(/[^\d]/g, "");
    if (value.length > 2) {
      value = value.slice(0, 2) + "/" + value.slice(2, 4);
    }
    if (value.length > 5) {
      value = value.slice(0, 5);
    }
    setExpiryDate(value);
    setFormData((prev) => ({
      ...prev,
      expiry_date: value,
    }));
  };
  return (
    <Row className="mb-3">
      <Col md={12} className="mb-2">
        <div className="input-group">
          <input
            type="text"
            className="form-control"
            placeholder="Card number"
            value={cardNumber}
            onChange={handleCardNumberChange}
            maxLength={cardType?.maxLength}
          />

          {cardType?.name !== "Unknown" && (
            <div className="input-group-append">
              <img
                src={cardImages[cardType?.name]}
                alt={cardType?.name}
                style={{
                  width: "40px",
                  height: "auto",
                  marginLeft: "10px",
                  paddingTop: "5px",
                }}
              />
            </div>
          )}
        </div>
      </Col>

      <Col md={6} className="mb-2">
        <input
          type="text"
          className="form-control"
          placeholder="Name on Card"
          onChange={(e) =>
            setFormData((prev) => ({
              ...prev,
              card_name: e?.target?.value,
            }))
          }
        />
      </Col>

      <Col md={3} className="mb-2">
        <input
          type="text"
          className="form-control"
          placeholder="MM / YY"
          value={expiryDate}
          onChange={handleExpiryChange}
        />
      </Col>

      <Col md={3} className="mb-2">
        <input
          type="text"
          className="form-control"
          placeholder="CVC"
          maxLength={cardType?.name == "Amex" ? 4 : 3}
          onChange={(e) =>
            setFormData((prev) => ({
              ...prev,
              cvv: e?.target?.value,
            }))
          }
        />
      </Col>

      <Col md={12}>
        <div className="d-flex justify-content-end gap-2">
          <img src={visa} alt="Visa" style={{ width: "30px" }} />
          <img src={mastercard} alt="Mastercard" style={{ width: "30px" }} />
          <img src={discover} alt="Discover" style={{ width: "30px" }} />
          <img src={amex} alt="Amex" style={{ width: "30px" }} />
        </div>
      </Col>
    </Row>
  );
}
