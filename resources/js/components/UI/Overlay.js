import React from 'react';
import ReactCSSTransitionGroup from 'react-addons-css-transition-group';


const Overlay = function(props){

    const loader = document.querySelector('.loading');
    
    if(!props.loading && loader){
        loader.classList.add('remove');
        loader.addEventListener('transitionend',function(e){
            if(e.propertyName !== 'opacity') return;
            loader.classList.add('removed');
        });
    }

    return null; // for now

    // return (
    //     <ReactCSSTransitionGroup
    //       transitionName="example"
    //       transitionEnterTimeout={500}
    //       transitionLeaveTimeout={300}>
    //         <div className="loading">
    //             <Loader />
    //         </div>
    //     </ReactCSSTransitionGroup>
    // );
}

export default Overlay;