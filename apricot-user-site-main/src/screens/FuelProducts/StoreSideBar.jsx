/* eslint-disable react/prop-types */
import CustomButton from "../../components/CustomButton";
import OffcanvasNav from "../../components/OffcanvasNav";

export default function StoreSideBar({
  showOfCanvas,
  handleShowOfCanvas,
  handleCloseOfCanvas,
  products,
}) {
  return (
    <OffcanvasNav
      show={showOfCanvas}
      handleShow={handleShowOfCanvas}
      handleClose={handleCloseOfCanvas}
      title="FILTERS"
    >
      <div
        className="showOfCanvas-content"
        style={{ display: "flex", flexDirection: "column", gap: "10px" }}
      >
        {products?.map((cat, i) => (
          <CustomButton
            text={cat?.category_title}
            variant="secondaryButton"
            key={i}
            onClick={() => {
              setTimeout(() => {
                const target = document.getElementById(`category-${i}`);
                if (target) {
                  target.scrollIntoView({ behavior: "smooth" });
                }
              }, 500);
              handleCloseOfCanvas();
            }}
          />
        ))}
      </div>
    </OffcanvasNav>
  );
}
