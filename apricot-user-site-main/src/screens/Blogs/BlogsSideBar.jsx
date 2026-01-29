/* eslint-disable react/prop-types */
import CustomButton from "../../components/CustomButton";
import { SelectBox } from "../../components/CustomSelect";
import OffcanvasNav from "../../components/OffcanvasNav";

export default function BlogsSideBar({
  showOfCanvas,
  handleShowOfCanvas,
  handleCloseOfCanvas,
  category,
  formData,
  setFormData,
  applyFilters,
}) {
  const handleCheckboxChange = (group, value) => {
    setFormData((prev) => {
      const current = prev[group];
      const updated = current.includes(value)
        ? current.filter((item) => item !== value)
        : [...current, value];
      return { ...prev, [group]: updated };
    });
  };

  const handleSortChange = (e) => {
    setFormData((prev) => ({ ...prev, sortOrder: e.target.value }));
  };

  const sortbyoption = [
    { id: "new", name: "Newest" },
    { id: "old", name: "Oldest" },
  ];

  return (
    <OffcanvasNav
      show={showOfCanvas}
      handleShow={handleShowOfCanvas}
      handleClose={handleCloseOfCanvas}
      title="FILTERS"
    >
      <div className="wholesale-application-form-group">
        <label>Filter by Blog Tags</label>

        <div className="form_check_boxes mb-3">
          {category?.map((type, idx) => (
            <div className="form-group" key={idx}>
              <input
                type="checkbox"
                checked={formData?.tags?.includes(type?.category_slug)}
                id={`store_type_${idx}`}
                onChange={() =>
                  handleCheckboxChange("tags", type?.category_slug)
                }
              />
              <label htmlFor={`store_type_${idx}`} style={{ fontSize: "20px" }}>
                {type?.category_name}
              </label>
            </div>
          ))}
        </div>

        <SelectBox
          selectClass="mainInput w-100"
          name="sort"
          label="Sort By"
          option={sortbyoption}
          value={formData.sortOrder || ""}
          onChange={handleSortChange}
        />

        <CustomButton
          onClick={() => applyFilters()}
          variant="primaryButton"
          text={"Apply Filters"}
          className="w-100"
        />
      </div>
    </OffcanvasNav>
  );
}
