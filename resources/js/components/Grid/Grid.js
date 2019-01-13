import React, { Component } from 'react';

import Columns from 'react-bulma-components/lib/components/columns';
// import Box from 'react-bulma-components/lib/components/box';
// import Button from 'react-bulma-components/lib/components/button';

const Grid = function(props){
    // console.log('grid',props.map,props.config);
    let gridItems = false;
    if(props.map.values){
        gridItems = props.map.values.map((row,index) => {
            const colItems = [];
            Object.entries(row).forEach((cell,index) => {
                // console.log(cell[1],index);
                cell = cell[1];
                const textColor = 255 - cell.grey;
                const cellH = props.map.image.height / props.config.rows;
                colItems.push(
                    <div className="grid-cell" key={index} style={{
                        background: 'rgb(' + cell.grey + ',' + cell.grey + ',' + cell.grey + ')',
                        height:cellH,
                        display:'flex',
                        justifyContent:'center'
                    }}><div className="cell-level" style={{color:'rgb(' + textColor + ',' + textColor + ',' + textColor + ')'}}>{cell.dimmer}</div></div>
                );
            })
            // console.log('items',colItems);
            const cellW = props.map.image.width / props.config.columns
            return(
                <Columns.Column key={index} className="grid-column" style={{width:cellW}}>              
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

export default Grid;