/* eslint-disable no-constant-binary-expression */
/* eslint-disable react/prop-types */
import { useEffect, useState } from "react";
import CustomModal from "../../components/CustomModal";
import CustomInput from "../../components/CustomInput";
import CustomButton from "../../components/CustomButton";
import { SelectBox } from "../../components/CustomSelect";
import {
  getCountry,
  getStates,
  updateShippingAddress,
} from "../../api/Services/addressServices";
import { toast } from "react-toastify";

import { faSpinner } from "@fortawesome/free-solid-svg-icons";

export default function EditShippingAdress({
  addShippingAddress,
  setAddShippingAddress,
  setSelectedAddress,
  shippingdata,
  fetchpaymentAddress,
}) {

  const [address, setAddress] = useState({
    states: [],
    countries: [],
  });
  const [isLoading, setisLoading] = useState(false);
  const [formData, setFormData] = useState({
    first_name: "",
    last_name: "",
    company: "",
    phone: "",
    email: "",
    address: "",
    city: "",
    state_id: null,
    postal_code: "",
    country_id: null,
  });
  const fetchState = async (countryId) => {
    try {
      const response = await getStates(countryId);
      setAddress((prev) => ({
        ...prev,
        states: response,
      }));
    } catch (error) {
      console.log(error);
    }
  };
  const fetchCountry = async () => {
    try {
      const response = await getCountry();
      setAddress((prev) => ({
        ...prev,
        countries: response,
      }));
    } catch (error) {
      console.log(error);
    }
  };
  const handleChange = (event) => {
    const { name, value, type, checked } = event.target;
    if (type === "checkbox") {
      setFormData((prevData) => ({
        ...prevData,
        [name]: checked,
      }));
    } else {
      setFormData((prevData) => ({
        ...prevData,
        [name]: value,
      }));
    }
  };
  const handleCountryChange = (e) => {
    const { value, name } = e.target;
    fetchState(value);
    setFormData((prevData) => ({
      ...prevData,
      [name]: value,
    }));
  };
  useEffect(() => {
    const isPrimarydata = shippingdata?.find((e) => e?.is_primary == 1);

    if (isPrimarydata) {
      setFormData({
        first_name: isPrimarydata.first_name || "",
        last_name: isPrimarydata.last_name || "",
        company: isPrimarydata.company || "",
        phone: isPrimarydata.phone || "",
        id: isPrimarydata.id || "",
        email: isPrimarydata.email || "",
        address: isPrimarydata.address || "",
        city: isPrimarydata.city || "",
        is_primary: 1 || "",
        state_id: isPrimarydata.state_id || null,
        postal_code: isPrimarydata.postal_code || "",
        country_id: isPrimarydata.country_id || null,
      });
    }
  }, [shippingdata]);
  useEffect(() => {
    fetchCountry();
    if (formData.country_id) {
      fetchState(formData.country_id);
    }
  }, [formData.country_id]);

  const handleSubmitAddress = async () => {
    setisLoading(true);
    try {
      const response = await updateShippingAddress(formData);
      setSelectedAddress(response?.data)
      console.log(formData)
      // toast.success(response?.message);
      fetchpaymentAddress();
      setisLoading(false);
      setAddShippingAddress(false);
    } catch (error) {
      toast.error(error?.message);
    }
  };
  return (
    <CustomModal
      show={addShippingAddress}
      close={() => {
        setAddShippingAddress(false);
      }}
      size="lg"
      heading="Edit SHIPPING ADDRESS"
    >
      <div className="apricot-modal-content mt-5">
        <div className="container-fluid">
          <div className="row">
            <div className="col-lg-6 mb-3">
              <CustomInput
                label="First Name"
                required
                id="addShippFirstName"
                type="text"
                labelClass="mainLabel"
                inputClass="mainInput"
                name="first_name"
                value={formData.first_name}
                onChange={handleChange}
              />
            </div>
            <div className="col-lg-6 mb-3">
              <CustomInput
                label="Last Name"
                required
                id="addShippLastName"
                type="text"
                labelClass="mainLabel"
                inputClass="mainInput"
                name="last_name"
                value={formData.last_name}
                onChange={handleChange}
              />
            </div>
            <div className="col-lg-12 mb-3">
              <SelectBox
                selectClass="mainInput"
                name="country_id"
                required
                label="Select Country"
                value={formData.country_id}
                option={address.countries}
                onChange={handleCountryChange}
              />
            </div>
            <div className="col-lg-12 mb-3">
              <SelectBox
                selectClass="mainInput"
                name="state_id"
                required
                label="State/Province"
                value={formData.state_id}
                option={address.states}
                onChange={handleChange}
              />
            </div>
            <div className="col-lg-12 mb-3">
              <CustomInput
                label="Address"
                required
                id="addShippAddress"
                type="text"
                labelClass="mainLabel"
                inputClass="mainInput"
                name="address"
                value={formData.address}
                onChange={handleChange}
              />
            </div>
            <div className="col-lg-12 mb-3">
              <CustomInput
                label="City"
                required
                id="addShippCity"
                type="text"
                labelClass="mainLabel"
                inputClass="mainInput"
                name="city"
                value={formData.city}
                onChange={handleChange}
              />
            </div>

            <div className="col-lg-12 mb-3">
              <CustomInput
                label="ZIP/Postal Code"
                required
                id="addShippZIPPostalCode"
                type="number"
                labelClass="mainLabel"
                inputClass="mainInput"
                name="postal_code"
                value={formData.postal_code}
                onChange={handleChange}
              />
            </div>
            <div className="col-lg-12 mb-3">
              <CustomInput
                label="Phone"
                required
                id="addShippPhone"
                type="text"
                labelClass="mainLabel"
                inputClass="mainInput"
                name="phone"
                value={formData.phone}
                onChange={handleChange}
              />
            </div>
            <div className="col-lg-12 mb-3">
              <CustomInput
                label="Company"
                required
                id="addShippCompany"
                type="text"
                labelClass="mainLabel"
                inputClass="mainInput"
                name="company"
                value={formData.company}
                onChange={handleChange}
              />
            </div>
            <div className="col-lg-12 mb-3">
              <CustomInput
                label="Email"
                required
                id="addShippEmail"
                type="text"
                labelClass="mainLabel"
                inputClass="mainInput"
                name="email"
                value={formData.email}
                onChange={handleChange}
              />
            </div>
            <div className="col-md-12">
              <div className="account_details-buttons d-flex flex-wrap justify-content-center gap-2">
                <CustomButton
                  text="Cancel"
                  variant=""
                  onClick={() => setAddShippingAddress(false)}
                />
                <CustomButton
                  onClick={() => {
                    handleSubmitAddress();
                  }}
                  icon={isLoading ? faSpinner : null}
                  text="Save Changes"
                  variant="primaryButton"
                />
              </div>
            </div>
          </div>
        </div>
      </div>
    </CustomModal>
  );
}
