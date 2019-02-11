import React, { Component } from 'react';
import { Route } from 'react-router-dom';

import Home from './Home/Home';
import Config from './Config/Config';
import SubmissionMain from './Submissions/SubmissionMain';
import Container from 'react-bulma-components/lib/components/container';

class Main extends Component {
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
            <Container>
                <Route path="/" exact component={Home} />
                <Route path="/config" exact component={Config} />
                <Route path="/config/:id" component={Config} />
                <Route path="/submissions/:id" component={SubmissionMain} />
            </Container>
        );
    }
}

export default Main;
