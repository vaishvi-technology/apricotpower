/* eslint-disable react/prop-types */

export default function PdfView({ data }) {
  return (
    <div className="nutritionContainer">
      <div className="nutritionLabel">
        <div className="border">
          {/* Title */}
          <span className="nl_biggest bold center">Nutrition Facts</span>
          <hr />

          {/* Serving Per Container */}
          <span>{data?.serving_per_container} servings per container</span>
          <br />

          {/* Serving Size */}
          <span className="bold right">{data?.serving_size}</span>
          <span className="bold">Serving Size</span>

          <hr className="thickest" />

          {/* Amount Per Serving */}
          <span className="nl_small bold">Amount per serving</span>
          <br />

          {/* Calories */}
          <span className="nl_bigger bold right">
            {data?.calories_per_serving || "Not Listed"}
          </span>
          <span className="nl_bigger bold">Calories</span>
          <hr />
          <span className="right">
            {data?.calories_from_fat || "Not Listed"}
          </span>
          <span>Calories From Fat</span>

          <hr className="thick" />

          <span className="nl_small bold right">% Daily Value*</span>
          <hr />

          {data?.line_item_options?.map((item, index) => (
            <>
              <span className="bold right">
                {item?.not_established === 1 ? "†" : `${item?.value}%`}
              </span>

              <span className="bold">{item?.name}</span>

              <em>{item?.amount}</em>

              <hr />
            </>
          ))}
        </div>

        <span className="nl_small">
          † Daily Value not established.
          <br />
          *Percent Daily Values are based on a 2,000 calorie diet.
        </span>
      </div>
      {data?.ingredients != null && (
        <div className="ingredients">
          <span className="bold">Other ingredients</span>
          <p className="nl_small" style={{ marginTop: "0px" }}>
            {data?.ingredients}
          </p>
        </div>                        
      )}
    </div>
  );
}
