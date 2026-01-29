/* eslint-disable react/prop-types */
import { Image, Spinner } from "react-bootstrap";
import OffcanvasNav from "../../components/OffcanvasNav";
import { useNavigate } from "react-router-dom";

export default function SideBar({
  showOfCanvas,
  handleShowOfCanvas,
  handleCloseOfCanvas,
  products,
  handleCart,
  loadingbtn,
}) {
  const navigate = useNavigate();
  return (
    <OffcanvasNav
      show={showOfCanvas}
      handleShow={handleShowOfCanvas}
      handleClose={handleCloseOfCanvas}
      title="UpSell Product"
    >
      <div
        className="showOfCanvas-content"
        style={{ display: "flex", flexDirection: "column", gap: "10px" }}
      >
        {products?.map((item, index) => (
          <div className="col-xl-12 col-lg-12 col-md-12 " key={index}>
            <div className="fuel-product-item">
              <div className="fuel-product-item-top">
                <div className="fuel-product-img">
                  <Image
                    style={{
                      width: "200px",
                      height: "250px",
                      // transform: "rotate(-15deg)",
                    }}
                    src={item.image}
                    alt="Product Image"
                  />
                </div>
              </div>
              <div className="fuel-product-item-content cursor">
                <h3
                  onClick={() => {
                    const slug = `${item?.product_url.replace(/\s+/g, "-")}`;
                    navigate(`/item/${slug}`, {
                      state: { productID: item?.id },
                    });
                  }}
                  className="text-theme h-89"
                >
                  {item.product_name}
                </h3>

                <div className="hot-buys-item-content-price">
                  <span className="price">Price:</span>{" "}
                  {item?.map_price === item?.sell_price ? (
                    <span className="discount-price secondary-color">
                      ${item?.sell_price}
                    </span>
                  ) : (
                    <>
                      <span className="actual-price">${item?.map_price}</span>{" "}
                      <span className="discount-price secondary-color">
                        ${item?.sell_price}
                      </span>
                    </>
                  )}
                </div>
                <div className="hot-buys-item-description">
                  <p>{item?.you_save}</p>
                  <p
                    title={`You Must Have at least ${item?.min_quantity} items in your cart to receive this discounted base price `}
                  >
                    minimum purchase required (?)
                  </p>
                </div>
                <div className="fuel-product-rating" style={{ height: "30px" }}>
                  <div
                    className="feefo-product-stars-widget "
                    data-product-sku={item?.item_code}
                  />
                </div>
                <button
                  className="button-with-icon"
                  onClick={() => {
                    handleCart(item?.id, "addtocard", 1,item);
                  }}
                >
                  Add To Cart {item?.id === loadingbtn && <Spinner size="sm" />}
                </button>
              </div>
            </div>
          </div>
        ))}
      </div>
    </OffcanvasNav>
  );
}
