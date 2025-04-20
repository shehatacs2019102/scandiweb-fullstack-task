import React, { Component } from "react";
import "./CartOverlayStyle.css";
export default class CartItem extends Component {
  constructor(props) {
    super(props);
    this.state = {};
  }
  handelIncrement = (tag, choices) => {
    const { CartItems, handleUpdate } = this.props;
    const update = CartItems
      ? CartItems.map((item) => {
          const currentvalues = JSON.stringify(Object.values(choices));
          const othervalues = JSON.stringify(
            Object.values(item["selected-choices"])
          );
          if (
            item["tag"] === tag &&
            item["quantity"] < 20 &&
            currentvalues === othervalues
          ) {
            item["quantity"]++;
          }

          return item;
        })
      : "";

    localStorage.setItem("products-stored", JSON.stringify(update));

    handleUpdate(update);
  };

  handelDecrement = (tag, choices) => {
    const { CartItems, handleUpdate } = this.props;
    const update = CartItems
      ? CartItems.map((item) => {
          const currentvalues = JSON.stringify(Object.values(choices));
          const othervalues = JSON.stringify(
            Object.values(item["selected-choices"])
          );
          if (
            item["tag"] === tag &&
            item["quantity"] > 1 &&
            currentvalues === othervalues
          ) {
            item["quantity"]--;
          }

          return item;
        })
      : "";

    localStorage.setItem("products-stored", JSON.stringify(update));

    handleUpdate(update);
  };

  render() {
    const { Item, Index } = this.props;

    return (
      <div key={Index} className="cart-item">
        <div className="cart-item-info">
          <div className="cart-item-name">{Item["tag"]}</div>

          <div className="cart-item-amount" data-testid="cart-item-amount">
            {Item["symbol"]}
            {Item["price"]}
          </div>

          {Item["attributeNames"] && Item["attributeNames"].length > 0 ? (
            <>
              {Item["attributeNames"].map((attrtag, attrtagIndex) => (
                <div
                  data-testid={`cart-item-attribute-${attrtag.toLowerCase()}`}
                  className={`product-${
                    attrtag === "Color" ? "color" : "standard"
                  }`}
                  key={attrtagIndex}
                >
                  <div
                    className={`product-${
                      attrtag === "Color" ? "color" : "standard"
                    }-tag`}
                  >
                    {attrtag}
                  </div>

                  <div
                    className={`product-${
                      attrtag === "Color" ? "color" : "standard"
                    }-choices`}
                  >
                    {Item["attributes"]
                      ? Item["attributes"]
                          .filter((attribute) => attribute.name === attrtag)
                          .map((attribute, attributeIndex) => (
                            <div
                              key={attribute.value}
                              className={`product-${
                                attribute["name"] === "Color"
                                  ? "color"
                                  : "standard"
                              }${
                                Item["selected-choices"] &&
                                Item["selected-choices"][attrtag] ===
                                  attribute.value
                                  ? attrtag === "Color"
                                    ? "-active-color"
                                    : "-active"
                                  : ""
                              }-choice`}
                              style={{
                                backgroundColor:
                                  attrtag === "Color" ? attribute.value : "",
                              }}
                              data-test-id={`cart-item-attribute-${attrtag.toLowerCase()}-${
                                attribute.value
                              }${
                                Item["selected-choices"] &&
                                Item["selected-choices"][attrtag] ===
                                  attribute.value
                                  ? "-selected"
                                  : ""
                              }`}
                            >
                              {attrtag === "Color" ? " " : attribute.value}
                            </div>
                          ))
                      : ""}
                  </div>
                </div>
              ))}
            </>
          ) : (
            ""
          )}

          <div className="quantity">
            <button
              className="quantity-button"
              id={Item["tag"]}
              onClick={(e) => {
                this.handelIncrement(e.target.id, Item["selected-choices"]);
              }}
              data-testid="cart-item-amount-increase"
            >
              +
            </button>
            <h3 data-testid="cart-item-amount">{Item.quantity}</h3>
            <button
              className="quantity-button"
              id={Item["tag"]}
              onClick={(e) => {
                this.handelDecrement(e.target.id, Item["selected-choices"]);
              }}
              data-testid="cart-item-amount-decrease"
            >
              -
            </button>
          </div>
        </div>

        <img
          className="cart-item-image"
          alt="cart-image"
          src={Item.gallery
            .split(" ")
            .map((url) => url.trim())[0]
            .replace(/[[\]"]/g, "")
            .slice(0, -1)}
        />
      </div>
    );
  }
}
