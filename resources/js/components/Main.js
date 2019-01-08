import React, { Component } from 'react';
import ReactDOM from 'react-dom';
import { Route,withRouter } from 'react-router-dom';

import Uploader from './Uploader';
import Loop from './Loop/Loop';
import Columns from 'react-bulma-components/lib/components/columns';
import Box from 'react-bulma-components/lib/components/box';

class Main extends Component {
    constructor(props) {
        super(props);
        this.state = {
            response: null
        }
    }
    
    render() {
        let responseText = '';
        if(this.state.response != null){
            responseText = <p>{this.state.response}</p>
        }
        return (
            <div className="container">
                <Route path="/" exact render={ () => {
                    return(
                        <div>
                            <Columns>
                                <Columns.Column>
                                    <Box>
                                        <div className="card-header">
                                            <h1 className="title is-1">LiteBrite</h1>
                                        </div>

                                        <div className="card-body">
                                            <h2 className="subtitle is-4">Upload a file and we'll put it up on the LiteBrite</h2>
                                        </div>
                                        <Uploader />
                                    </Box>
                                    {responseText}
                                </Columns.Column>
                            </Columns>
                            <hr />
                            <div>
                                <h2 className="subtitle is-2">Uploads</h2>
                                <Loop className="columns"/>
                            </div>
                        </div>
                    )
                }}/>
                <Route path="/submissions/:id" render={(id) => {
                    return(
                        <p>ID is: {id}</p>
                    )
                }} />
            </div>
        );
    }
}

export default Main;
