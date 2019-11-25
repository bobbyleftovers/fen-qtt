import React from 'react';
import Columns from 'react-bulma-components/lib/components/columns';
import Nav from './Nav';

const Header = function(props) {
    return (
        <Columns>
            <Columns.Column>
                <Nav />
                <div className="card-header">
                    <h1 className="title is-1">LiteBrite</h1>
                </div>

                <div className="card-body">
                    <h2 className="subtitle is-4">
                        Upload a file and we'll put it up on the LiteBrite
                    </h2>
                </div>
            </Columns.Column>
        </Columns>
    );
};

export default Header;
