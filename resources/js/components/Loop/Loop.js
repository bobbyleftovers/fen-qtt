import React, { Component } from 'react';

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
        axios.get('/submissions')
        .then(res => {
            this.setState({
                entries:res.data
            });
        })
        .catch(error => {
            // log out the error
            let message = `ERROR: `;

            // loader
            this.tableLoading = false;

            // Error
            if (error.response) {
                // The request was made and the server responded with a status code
                // that falls out of the range of 2xx
                message += error.response.status + `; ` + error.response.data.message;
                console.log(error.response.data);
                console.log(error.response.status);
                console.log(error.response.headers);

            } else if (error.request) {
                // The request was made but no response was received
                // `error.request` is an instance of XMLHttpRequest in the browser and an instance of
                // http.ClientRequest in node.js
                message += error.request;
                console.log(error.request);
            } else {
                // Something happened in setting up the request that triggered an Error
                message += error.message;
                console.log('ERROR:', error.message);
            }
            console.log('ERROR CONFIG:',error.config);
            message += ` (see console)`;
            // Show toast with error message
            this.$toast.open({
                duration: 5000,
                message: message,
                position: 'is-bottom',
                type: 'is-danger'
            });
        })
    }

    componentWillMount(){
        this.getItems();
    }
    
    render(){
        const entries = this.state.entries.map((entry,index) => (
            <Columns.Column key={index} className="entry is-6">                
                <LoopItem entry={entry} />
            </Columns.Column>
        ));
        return (
            <Columns>{entries}</Columns>
            
        );
    }
}

export default Loop;