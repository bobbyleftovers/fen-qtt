import React from 'react';

import Columns from 'react-bulma-components/lib/components/columns';

const Grid = function(props){
    console.log('grid',props.map,props.config);
    let gridItems = false;
    if(props.map){
        gridItems = props.map.map((row,index) => {
            const colItems = [];
            Object.entries(row).forEach((cell,index) => {
                // console.log(cell[1],index);
                cell = cell[1];
                const textColor = 255 - cell.grey;
                // const cellH = props.map.image.height / props.config.rows;
                colItems.push(
                    <div className="grid-cell" key={index} style={{
                        background: 'rgb(' + cell.grey + ',' + cell.grey + ',' + cell.grey + ')',
                        height:'1px',
                        width:'1px',
                        display:'flex',
                        justifyContent:'center'
                    }}><div className="dimmer-level" style={{color:'rgb(' + textColor + ',' + textColor + ',' + textColor + ')'}}>{cell.dimmer}</div></div>
                );
            })
            // console.log('items',colItems);
            // const cellW = props.map.image.width / props.config.columns
            return(
                <Columns.Column key={index} className="grid-column" style={{width:'1px',height:'1px'}}>
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