import React, { Component } from 'react';
import ReactDOM from 'react-dom';

import {
    Field,
    Control,
    Label,
    Input,
    Textarea,
    Select,
    Checkbox,
    Radio,
    Help,
  } from 'react-bulma-components/lib/components/form';
import Icon from 'react-bulma-components/lib/components/icon';
import Columns from 'react-bulma-components/lib/components/columns';
import Box from 'react-bulma-components/lib/components/box';
import Button from 'react-bulma-components/lib/components/button';

class Main extends Component {
    
    state = {
        response: null,
        file: null,
        loading:false,
        testVar: 0
	}

    componentDidMount(){}
    
    submit(evt){
        evt.preventDefault();
        axios.post('/store')
        .then(res => {
            console.log('response:',res)
        })
        .error(err => {
            console.log('error:',err);
        })
    }

	testConnect(){
        this.setState({
            loading:true
        });
		axios.get('http://ienjoybobby.com/api/test', {
            headers: {
                "Access-Control-Allow-Origin": "*",
                crossorigin:true,
                'Access-Control-Allow-Methods' : 'GET,PUT,POST,DELETE,PATCH,OPTIONS'
            },
            responseType: 'json',
            })
			.then((response) => {
                console.log(response);
                let num = this.state.testVar;
                num++;
				this.setState({
                    response:response.data.test1,
                    testVar: num,
					loading:false
				});
			})
			.catch((error) => {
				console.log(error);
				this.setState({
					error:true,
					loading:false
				});
			});
    }
    
    render() {
        let responseText = '';
        if(this.state.response != null){
            responseText = <p>{this.state.response}</p>
        }
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
                                <div className="file has-name is-fullwidth">
                                    <label className="file-label">
                                        <input className="file-input" type="file" name="resume"/>
                                        <span className="file-cta">
                                            <span className="file-icon">
                                                <i className="fas fa-upload"></i>
                                            </span>
                                            <span className="file-label">
                                                Choose a file…
                                            </span>
                                            <span className="file-name">
                                            </span>
                                        </span>
                                    </label>
                                </div>

                                <Button type="submit" onClick={() => this.testConnect()}>
                                    Submit
                                </Button>
                        </Box>
                        {responseText}
                    </Columns.Column>
                </Columns>
            </div>
        );
    }
}

export default Main;
