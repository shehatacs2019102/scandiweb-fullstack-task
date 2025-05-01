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
  order = {};
  HandleOrderInformation = () => {
    const keys = Object.keys(this.state.cartItems);
    let products = [];
    let total = 0;
    for (let i = 0; i < keys.length; i++) {
      products.push(" product:", this.state.cartItems[i]["tag"]);
      products.push(" price:", this.state.cartItems[i]["price"]);
      products.push(" quantity:", this.state.cartItems[i]["quantity"]);
      total =
        total +
        this.state.cartItems[i]["quantity"] * this.state.cartItems[i]["price"];
      const productKeys = Object.keys(this.state.cartItems[i]);
      for (let j = 0; j < productKeys.length - 1; j++) {
        if (this.state.cartItems[i]["attributeNames"][j]) {
          products.push(" ", this.state.cartItems[i]["attributeNames"][j], ":");
          products.push(
            this.state.cartItems[i]["selected-choices"][
              this.state.cartItems[i]["attributeNames"][j]
            ]
          );
        }
      }
    }
    fetch("http://localhost:8000/graphql", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        query: `mutation {createOrder(items: "${products.join(
          ""
        )}" , total_price: ${total}){items total_price} }`,
      }),
    })
      .then((res) => res.json())
      .then(() => {
        localStorage.setItem("products-stored", JSON.stringify([]));

        this.setState({
          cartItems: [],
        });
        console.log("thisi s product array", products);
        //
      });
  };

  handleUpdate = (update) => {
    this.setState({
      cartItems: update,
    });
  };

  render() {
    const { cartItems } = this.state;

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
                />
              ))
            : ""}

          <div className="total" data-testid="cart-total">
            <div> Total:  </div>
            <div>
            {cartItems.length > 0 && cartItems
              ? cartItems
                  .reduce((total, item) => {
                    return  item["quantity"] * item["price"] + total ;
                  }, 0)
                  .toFixed(2)
              : ""} $
              
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
