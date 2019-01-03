import React, { Component } from 'react';
import ReactDOM from 'react-dom';

import Columns from 'react-bulma-components/lib/components/columns';
import LoopItem from './LoopItem';

class Loop extends Component{

    constructor(props){
        super(props);
        this.state = {
            entries: []
        };
    }

    getItems(){
        axios.get('/entries')
        .then(res => {
            console.log(res);
            this.setState({
                entries:res.data
            });
        })
    }

    componentWillMount(){
        this.getItems();
    }
    
    render(){
        const entries = this.state.entries.map((entry,index) => (
            <Columns.Column key={index} className="entry">                
                <LoopItem entry={entry} />
            </Columns.Column>
        ));
        return (
            <div>{entries}</div>
            
        );
    }
}

export default Loop;