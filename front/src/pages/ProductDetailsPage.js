import React, { Component } from 'react';
import './ProductDetailsPageStyle.css';
import Slider from '../components/Slider.js'




class ProductDetailsPage extends Component {
  constructor(props) {
    super(props);
    this.state = {
        product:[],
        attributes:[],
        attributeNames:[],
        selectedChoices:{},
        selectedProducts:[],
    }; 
  }

  componentDidMount() {

    const {currentProductId} = this.props;

    if(currentProductId){

    fetch('http://localhost:8000/graphql', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        query: `{getProductById(id:"${currentProductId}"){ id name  description brand amount gallery currency_symbol attributes { id name value }}}`,
      }),
    })
      .then((res) => res.json())
      .then((data) =>
        this.setState({
          product: data.data.getProductById,
          attributes:data.data.getProductById.attributes,
          attributeNames:data.data.getProductById.attributes.map((value)=>{return value.name}).filter((value,index,array)=>{return array.indexOf(value) === index})
          
        })
      )
      .catch((error) => {
        console.error('Error fetching categories:', error);
      });

    }

  }

removeHTMLTags(htmlString) {
    
    const parser = new DOMParser();
    
    const doc = parser.parseFromString(htmlString, 'text/html');
    
    const textContent = doc.body.textContent || "";
    
    return textContent.trim();
}

handleChoiceClick = (attributeName, value) => {

  this.setState((prevState) => ({

    selectedChoices: {
      ...prevState.selectedChoices,
      [attributeName]: value, 
    },

  }));

};

handleAddToCart = () => {
  const { product, attributes, selectedChoices } = this.state;
  const storedProducts = JSON.parse(localStorage.getItem("products-stored")) || [];

  const newProduct = {
    tag: product.name,
    price: product.amount,
    symbol: product.currency_symbol,
    gallery: product.gallery,
    quantity: 1,
    attributes: attributes || [],
    attributeNames: attributes.map((value)=>{return value.name}).filter((value,index,array)=>{return array.indexOf(value) === index}) || []
    ,
    "selected-choices": selectedChoices || {},
  };

  const productIndex = storedProducts.findIndex((item) =>
    item.tag === newProduct.tag &&
    JSON.stringify(item["selected-choices"]) === JSON.stringify(newProduct["selected-choices"]) &&
    JSON.stringify(item.attributes) === JSON.stringify(newProduct.attributes)
  );
if((newProduct.attributes.length>0 && Object.keys(newProduct['selected-choices']).length>0)||(newProduct.attributes.length===0 && Object.keys(newProduct['selected-choices']).length===0)){


  if (productIndex !== -1) {
    
    storedProducts[productIndex].quantity += 1;
  } else {
    
    storedProducts.push(newProduct);
  }

}

  localStorage.setItem("products-stored", JSON.stringify(storedProducts));
  
};  


render() {

    const {isCartOpen,onCartOpen}=this.props;

    return (

                 <div className={`details-container ${isCartOpen ? 'disabled' : ''}`}
                      style={{
                          opacity: isCartOpen ? 0.5 : 1,
                          pointerEvents: isCartOpen ? 'none' : 'auto',
                         
               }}>

                            <Slider id={this.state.product.id} />

                            
                            <div className='product-details'>
                              

                              <div className='product-name'>

                                {this.state.product.name}

                              </div>

                              <div className="attributes" >

                              {this.state.attributeNames.map((header, headerIndex) => (

                                    <div className="attribute-container" data-testid={`product-attribute-${header.toLowerCase().replaceAll(' ','-')}${this.state.selectedChoices[header]?`-${this.state.selectedChoices[header].toLowerCase().replaceAll(' ','-')}`:""}`}>

                                    <div key={headerIndex} className="attribute-tag">

                                    {header}
                                    </div>

                                    <div className="attributes-variations">

                                                      {this.state.attributes
                                                        .filter((attribute) => attribute.name === header)
                                                        .map((attribute, index) => (
                                                            
                                                                <div className={`preference ${
                                                                  this.state.selectedChoices[header] === attribute.value
                                                                   ? header === 'Color'
                                                                     ? 'active-color'
                                                                      : 'active'
                                                                    : ''
                                                               }`}
                                                                  style={{
                                                                  backgroundColor: header === 'Color' ? attribute.value : "",
                                                                }} key={attribute.value}
                                                                   onClick={() => this.handleChoiceClick(header, attribute.value)}
                                                                   data-testid={`product-attribute-${header.toLowerCase().replaceAll(" ","-")}-${attribute.value}`}
                                                                  >
                                                                        <div>{header==='Color'?" ":attribute.value}</div>

                                                               </div>

                                                         ))}

                                                  </div>

                                             </div>

                                           ))}

                                          </div>  


                                           <div className='price'>

                                             <div className='price-tag'>PRICE:</div>

                                             <div className='price-amount'>{this.state.product.currency_symbol}{this.state.product.amount}</div>

                                           </div>

                                           <button className='add-to-cart' disabled={this.state.selectedChoices?Object.keys(this.state.selectedChoices).length===this.state.attributeNames.length?false:true:true} data-testid='add-to-cart' onClick={()=>{this.handleAddToCart();onCartOpen()}}>

                                           Add to cart
        
                                           </button>

                                           <div className='description' data-testid='product-description'>

                                                {this.removeHTMLTags(this.state.product.description)}

                                          </div>

                              </div>

                    </div>

      )

    }

  }

export default ProductDetailsPage;
