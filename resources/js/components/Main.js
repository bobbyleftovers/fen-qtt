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
		response: null
	}

    componentDidMount(){}

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
			.then( response => {
                console.log(response);
				this.setState({
					response:response.data.test1,
					loading:false
				});
			})
			.catch(error => {
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
                            <i className="fas fa-ad"></i>
                             Example Component</div>

                            <div className="card-body">
                                I'm an example component!
                            </div>
                        
                            <Field>
                                <Label>Name</Label>
                                <Control>
                                <Input placeholder="Text input" />
                                </Control>
                            </Field>

                            <Field kind="group">
                                <Control>
                                <Button type="primary">Submit</Button>
                                </Control>
                                <Control>
                                <Button color="link">Cancel</Button>
                                </Control>
                            </Field>

                            <Button onClick={() => this.testConnect()}>
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
