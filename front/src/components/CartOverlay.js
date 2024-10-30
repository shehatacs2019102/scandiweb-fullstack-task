import React, { Component } from 'react';
import './CartOverlayStyle.css';

class CartOverlay extends Component {
  constructor(props) {
    super(props);
    this.state = {
      cartItems: JSON.parse(localStorage.getItem('products-stored')) || [],
      
    };
  }
  order = {
    
  }
  HandleOrderInformation=()=>{
   
   const keys = Object.keys(this.state.cartItems)
   let products = []
   let total = 0
for (let i = 0; i < keys.length ; i++) {
    
   products.push(' product:',(this.state.cartItems[i]['tag']))
   products.push(' price:',(this.state.cartItems[i]['price']))
   products.push(' quantity:',(this.state.cartItems[i]['quantity']))
   total = total + (this.state.cartItems[i]['quantity']*this.state.cartItems[i]['price'])
   const productKeys = Object.keys(this.state.cartItems[i])
   for(let j =0 ; j <productKeys.length -1 ; j++){
    if(this.state.cartItems[i]['attributeNames'][j]){

    
      products.push(' ',(this.state.cartItems[i]['attributeNames'][j]),':')
      products.push(this.state.cartItems[i]['selected-choices'][this.state.cartItems[i]['attributeNames'][j]])

    }  
    
    
   }
   

}
fetch('http://shehatacs322.serv00.net:54767/graphql', {
  method: 'POST',
  headers: {'Content-Type': 'application/json'},
  body: JSON.stringify({
    query: `mutation {createOrder(items: "${products.join('')}" , total_price: ${total}){items total_price} }`
  })
}) 
  .then(res => res.json()).then(()=>{
    localStorage.setItem('products-stored', JSON.stringify([]))

    this.setState({
        cartItems : [],


    })
    console.log('thisi s product array',products);
    // 
  })
   
  }
 
  handelIncrement = (tag) =>{

    
   const update = this.state.cartItems? this.state.cartItems.map((item)=>{

      if(item['tag'] === tag && item['quantity']<20){
        item['quantity']++
        

      }

      return item
}

) : ''

localStorage.setItem('products-stored', JSON.stringify(update))

console.log(update);
this.setState({
   cartItems: update
 })
    }

  handelDecrement = (tag) =>{
    const update = this.state.cartItems? this.state.cartItems.map((item)=>{

      if(item['tag'] === tag && item['quantity']>1){
        item['quantity']--
        

      }

      return item
}

) : ''

localStorage.setItem('products-stored', JSON.stringify(update))

console.log(update);
this.setState({
   cartItems: update
 })

    
  }

  render() {
    const { cartItems } = this.state;
    

    return (
      <div className="cart-wrapper" data-testid="cart-overlay">
        <div className="cart-holder">
          <div className="cart-items-count">
            <b>My Bag</b>: {cartItems && cartItems.length>0 ? `${cartItems.reduce((total, item)=>{return item['quantity'] +total },0)} ${cartItems.length === 1 ? 'Item' : 'Items'}` : ''}
          </div>

          {cartItems && cartItems.length > 0 ? cartItems.map((item, index) => (
            <div key={index} className='cart-item'>
             
              
              <div className='cart-item-info'>
                <div className='cart-item-name'>
                  {item['tag']}
                </div>
                
                <div className='cart-item-amount' data-testid='cart-item-amount'>
                  {item['symbol']}{item['price']}
                </div>
             

        
              {item['attributeNames'] && item['attributeNames'].length > 0 ? (
                <>
                  {item['attributeNames'].map((attrtag, attrtagIndex) => (
                    <div data-testid={`cart-item-attribute-${attrtag.toLowerCase()}`} className={`product-${attrtag==='Color'?'color':'standard' }`} key={attrtagIndex}>
                      <div className={`product-${attrtag==='Color'?'color':'standard'}-tag`}>
                        {attrtag
                        }
                      </div>

                      <div className={`product-${attrtag==='Color'?'color':'standard'}-choices`}>
                        {item['attributes'] ? (
                          item['attributes']
                            .filter(attribute => attribute.name === attrtag)
                            .map((attribute, attributeIndex) => (
                              <div
                                key={attribute.value}
                                className={`product-${attribute['name']==='Color'? 'color':'standard'}${item['selected-choices'] && item['selected-choices'][attrtag] === attribute.value
                                  ? attrtag === 'Color'
                                    ? '-active-color'
                                    : '-active'
                                  : ''
                                }-choice`}
                                style={{
                                  backgroundColor: attrtag === 'Color' ? attribute.value : '',
                                }}
                                data-test-id={`cart-item-attribute-${attrtag.toLowerCase()}-${attribute.value}${item['selected-choices']&&item['selected-choices'][attrtag]===attribute.value?'-selected':''}`}
                              >
                           
                                {attrtag === 'Color' ? ' ' : attribute.value}
                              </div>
                            ))
                        ) : ''}
                      </div>
                    </div>
                  ))}
                </>
              ) : ''}

                  <div className='quantity'>
                      <button className='quantity-button' id = {item['tag']} onClick={(e)=>{this.handelIncrement(e.target.id)}} data-testid='cart-item-amount-increase'>+</button>
                      <h3 data-testid='cart-item-amount' >{item.quantity}</h3>
                      <button className='quantity-button' id = {item['tag']} onClick={(e)=>{this.handelDecrement(e.target.id)}} data-testid='cart-item-amount-decrease' >-</button>
                  </div>

              </div>

              
<img className='cart-item-image' alt='cart-image' src={item.gallery.split(' ').map(url => url.trim())[0].replace(/[[\]"]/g, '').slice(0, -1)}/>


            </div>
          )) : ''}
          <div className='total' data-testid='cart-total' >
            {
             cartItems.length > 0 && cartItems? cartItems.reduce((total, item)=>{
                return (item['quantity'] * item['price']) + total 
              },0 ).toFixed(2): ''
            }
           
          </div>
          <button onClick={this.HandleOrderInformation} className="place-order">Place Order</button>
        </div>
      </div>

    );
  }
}

export default CartOverlay;
