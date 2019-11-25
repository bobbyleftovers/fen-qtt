import React, { Component } from 'react';
import Uploader from '../Uploader/Uploader';
import Loop from '../Loop/Loop';

import Columns from 'react-bulma-components/lib/components/columns';
import Box from 'react-bulma-components/lib/components/box';

class Home extends Component {
    constructor(props) {
        super(props);
        this.state = {
            response: null,
        };
    }

    render() {
        let responseText = '';
        if (this.state.response != null) {
            responseText = <p>{this.state.response}</p>;
        }
        return (
            <div>
                <Columns>
                    <Columns.Column>
                        <Box>
                            <Uploader />
                            {responseText}
                        </Box>
                    </Columns.Column>
                </Columns>
                <hr />
                <div>
                    <Columns>
                        <Columns.Column>
                            <h2 className="subtitle is-2">Previous Uploads</h2>
                        </Columns.Column>
                    </Columns>
                    <Loop className="columns" />
                </div>
            </div>
        );
    }
}

export default Home;
