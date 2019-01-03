import React, { Component } from 'react';
import ReactDOM from 'react-dom';

import Uploader from './Uploader';
import Loop from './Loop/Loop';
import Columns from 'react-bulma-components/lib/components/columns';
import Box from 'react-bulma-components/lib/components/box';

class Main extends Component {
    constructor(props) {
        super(props);
        this.state = {
            response: null,
            file: '',
            loading:false,
            testVar: 0
        }
    }
    
    render() {
        let responseText = '';
        if(this.state.response != null){
            responseText = <p>{this.state.response}</p>
        }
        console.log(this.state.file)
        return (
            <div className="container">
                <Columns>
                    <Columns.Column>
                        <Box>
                            <div className="card-header">
                                <h1 className="title is-1">LiteBrite</h1>
                            </div>

                            <div className="card-body">
                                <h2 className="subtitle is-4">Upload a file and we'll put it up on the LiteBrite {this.state.testVar}</h2>
                            </div>
                            <Uploader />
                        </Box>
                        {responseText}
                    </Columns.Column>
                </Columns>
                <hr />
                <Columns>
                    <h2 className="subtitle is-2">Uploads</h2>
                    <Loop />
                </Columns>
            </div>
        );
    }
}

export default Main;
