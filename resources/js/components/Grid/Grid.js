import React, { Component } from 'react';

import Columns from 'react-bulma-components/lib/components/columns';

class Grid extends Component{

    constructor(props){
        super(props);
        this.state = {
            size:document.querySelector('.grid-wrap').offsetWidth / this.props.config.columns,
        }
    }
    updateDimensions() {
        let updatedSize = document.querySelector('.grid-cell').offsetWidth;
        this.setState({
            size: updatedSize,
        });
    }

    /**
     * Add event listener
     */
    componentDidMount() {
        this.updateDimensions();
        window.addEventListener("resize", this.updateDimensions.bind(this));
    }

    /**
     * Remove event listener
     */
    componentWillUnmount() {
        window.removeEventListener("resize", this.updateDimensions.bind(this));
    }
    render(){
        let gridItems = false;
        if(this.props.map){
            
            gridItems = this.props.map.map((row,index) => {
                const colItems = [];
                Object.entries(row).forEach((cell,index) => {
                    cell = cell[1];
                    colItems.push(
                        <div className="grid-cell" key={index} style={{
                            background: 'rgb(' + cell.grey + ',' + cell.grey + ',' + cell.grey + ')',
                            height:`${this.state.size}px`,
                            display:'flex',
                            justifyContent:'center'
                        }}></div>
                    );
                });
                // FOR LATER, DIMMER LEVELS DIV: <div className="dimmer-level" style={{color:'rgb(' + textColor + ',' + textColor + ',' + textColor + ')'}}>{cell.dimmer}</div>
                // console.log('items',colItems);
                
                return(
                    <Columns.Column key={index} className="grid-column">
                        {colItems}
                    </Columns.Column>
                )
            });
        }
        
        return (
            <div className="grid">
                <br></br>
                <Columns>
                    {gridItems}
                </Columns>
            </div>  
        );
    }
}
export default Grid