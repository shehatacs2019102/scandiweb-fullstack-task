import React, { Component } from "react";
import "./CartOverlayStyle.css";
import CartItem from "./CartItem";

class CartOverlay extends Component {
  constructor(props) {
    super(props);
    this.state = {
      cartItems: JSON.parse(localStorage.getItem("products-stored")) || [],
    };
  }

  HandleOrderInformation = () => {
    const { onCartOpen, handleCountChange } = this.props;
    const { cartItems } = this.state;
    let products = [];
    let total = 0;
    cartItems.forEach((item) => {
      total += item.quantity * item.price;

      const itemDescription = [
        `product: ${item.tag}`,
        `price: ${item.price}`,
        `quantity: ${item.quantity}`,
        ...item.attributeNames.map(
          (header) => `${header}: ${item["selected-choices"][header]}`
        ),
      ].join(", ");

      products.push(itemDescription);
    });
    if (products.length > 0) {
      fetch("http://localhost:8000/graphql", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          query: `mutation {
          createOrder(items: "${products.join("; ")}", total_price: ${total}) {
            items
            total_price
          }
        }`,
        }),
      })
        .then(() => {
          localStorage.setItem("products-stored", JSON.stringify([]));
          localStorage.setItem("cart-count", 0);
          handleCountChange();
          onCartOpen();
          this.setState({ cartItems: [] });
          {
            console.log("button pressed");
          }
        })
        .catch((error) => console.error("Error placing order:", error));
    }
  };

  handleUpdate = (update) => {
    this.setState({
      cartItems: update,
    });
  };

  render() {
    const { cartItems } = this.state;
    const { handleCountChange } = this.props;
    return (
      <div className="cart-wrapper" data-testid="cart-overlay">
        <div className="cart-holder">
          <div className="cart-items-count">
            <b>My Bag</b>:{" "}
            {cartItems && cartItems.length > 0
              ? `${cartItems.reduce((total, item) => {
                  return item["quantity"] + total;
                }, 0)} ${cartItems.length === 1 ? "Item" : "Items"}`
              : ""}
          </div>

          {cartItems && cartItems.length > 0
            ? cartItems.map((item, index) => (
                <CartItem
                  Item={item}
                  Index={index}
                  CartItems={cartItems}
                  handleUpdate={this.handleUpdate}
                  handleCountChange={handleCountChange}
                />
              ))
            : ""}

          <div className="total" data-testid="cart-total">
            <div> Total: </div>
            <div>
              {cartItems.length > 0 && cartItems
                ? cartItems
                    .reduce((total, item) => {
                      return item["quantity"] * item["price"] + total;
                    }, 0)
                    .toFixed(2)
                : ""}{" "}
              $
            </div>
          </div>
          <button onClick={this.HandleOrderInformation} className="place-order">
            Place Order
          </button>
        </div>
      </div>
    );
  }
}

export default CartOverlay;
