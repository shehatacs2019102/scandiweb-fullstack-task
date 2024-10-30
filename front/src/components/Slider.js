import React, { Component } from "react";
import './SliderStyle.css'






class Slider extends Component {
  constructor(props) {
    super(props);
    this.state = {
      currentIndex: 0, 
      gallery:[], 
    };
  }

  componentDidMount() {

    const {id} = this.props;

    if(id){
    fetch('http://shehatacs322.serv00.net:54767/graphql', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        query: `{getProductById(id:"${id}"){ gallery }}`,
      }),
    })
      .then((res) => res.json())
      .then((data) =>{
        const galleryString=data.data.getProductById.gallery;
        const galleryArray=JSON.parse(galleryString)
        this.setState({
          gallery: galleryArray
        })
        
    })
      .catch((error) => {
        console.error('Error fetching categories:', error);
      });
  }
  }
  
componentDidUpdate(prevProps){

  const {id} = this.props;

  if(prevProps.id!==id){

    if(id){

      fetch('http://shehatacs322.serv00.net:54767/graphql', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          query: `{getProductById(id:"${id}"){ gallery }}`,
        }),
      })
        .then((res) => res.json())
        .then((data) =>{
          const galleryString=data.data.getProductById.gallery;
          const galleryArray=JSON.parse(galleryString)
          this.setState({
            gallery: galleryArray
          })
          console.log(this.state.gallery)
      })
        .catch((error) => {
          console.error('Error fetching categories:', error);
        });

    }
  }
}
  showSlide = (index) => {
    
    if (index >= this.state.gallery.length) {
      this.setState({ currentIndex: 0 }); 
    } else if (index < 0) {
      this.setState({ currentIndex: this.state.gallery.length - 1 }); 
    } else {
      this.setState({ currentIndex: index });
    }
  };

  
  moveSlide = (step) => {
    const { currentIndex } = this.state;
    this.showSlide(currentIndex + step);
  };

 
  setCurrentSlide = (index) => {
    this.showSlide(index);
  };

  render() {

    const { currentIndex,gallery } = this.state;

    return (

      <div  className="gallery-container" data-testid='product-gallery'> 

        <div className="thumbnails">

          {gallery.map((image, index) => (
            <img
              key={index}
              src={image}
              alt={`Thumbnail ${index + 1}`}
              className={`thumbnail ${index === currentIndex ? "active-thumbnail" : ""}`}
              onClick={() => this.setCurrentSlide(index)}
            />
          ))
          }

        </div>
        
        <div className="slider">

            <button className="prev" onClick={() => this.moveSlide(-1)}>&#10094;</button>

            <div className="slider-container">
              {gallery.map((image, index) => (
                <img
                  key={index}
                  src={image}
                  alt={`Slide ${index + 1}`}
                  className={`slide ${index === currentIndex ? "active-slide" : ""}`}
                />
              ))}
            </div>

           <button className="next" onClick={() =>{ this.moveSlide(1);console.log(gallery)}}>&#10095;</button>
        
        </div>

      </div>

    );
  }
  
 }

export default Slider;
