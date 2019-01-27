import React, { Component } from 'react';

import Columns from 'react-bulma-components/lib/components/columns';
import Box from 'react-bulma-components/lib/components/box';
import Button from 'react-bulma-components/lib/components/button';
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
import Loading from '../UI/Loading';

class Config extends Component{

    constructor(props){
        super(props);
        this.state = {
            id: null,
            rows: 0,
            columns: 0,
            dimmer_levels: 0,
            config_name: '',
            bulb_type:'',
            loading: true,
            is_active: false
        };

        this.handleChange = this.handleChange.bind(this);
        this.handleSubmit = this.handleSubmit.bind(this);
    }

    componentWillMount(){
        if(this.props.match.params.id){
            this.getConfig();
        } else {
            this.setState({loading:false});
        }
    }

    getConfig(){
        axios.post('/get-config',{id:this.props.match.params.id})
        .then(res => {
            this.setState({
                id:res.data.id,
                rows:res.data.rows,
                columns:res.data.columns,
                dimmer_levels:res.data.dimmer_levels,
                config_name: res.data.name,
                bulb_type:res.data.bulb_type,
                is_active:res.data.is_active,
                loading: false
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
            // this.$toast.open({
            //     duration: 5000,
            //     message: message,
            //     position: 'is-bottom',
            //     type: 'is-danger'
            // });
        })
    }

    handleSubmit(evt){
        evt.preventDefault();
        const route = (this.props.match.params.id) ? '/config/store/' + this.props.match.params.id : '/config/store';
        axios.post(route,this.state)
        .then(res => {
            console.log('saved',res.data);
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
            // this.$toast.open({
            //     duration: 5000,
            //     message: message,
            //     position: 'is-bottom',
            //     type: 'is-danger'
            // });
        })
        return;
    }

    handleChange(e){
        const value = e.target.type === 'checkbox' ? e.target.checked : e.target.value;
        this.setState({
          [e.target.name]: value,
        });
    }
    
    render(){
        let buttons = <Button>Save</Button>;
        if(this.state.id){
            buttons = <Button>Update</Button>;
        }
        let loading = null;
        if(this.props.match.params.id){
            loading = <Loading loading={this.state.loading}/>
        }
        const { dimmer_levels, rows, columns, bulb_type, config_name, is_active } = this.state;
        return (
            <div>
                <Columns>
                    <Columns.Column>
                        <h1 className="title is-1">Configuration</h1>
                    </Columns.Column>
                </Columns>
                <Columns>
                    <Columns.Column>
                        <Box>
                            {loading}
                            <form onSubmit={this.handleSubmit}>
                                <h2 className="subtitle is-2">Grid Options</h2>
                                <div className="field">
                                    <label className="label">Name</label>
                                    <div className="control">
                                        <input className="input" name="config_name" onChange={this.handleChange} type="text" value={config_name} placeholder="Configuration name"/>
                                    </div>
                                </div>
                                <div className="field">
                                    <label className="label">Bulb Type</label>
                                    <div className="control">
                                        <input className="input" name="bulb_type" onChange={this.handleChange} type="text" value={bulb_type} placeholder="Bulb make/model"/>
                                    </div>
                                </div>
                                <Columns>
                                    <Columns.Column>
                                        <div className="field">
                                            <label className="label">Grid Rows</label>
                                            <div className="control">
                                                <input className="input" name="rows" onChange={this.handleChange} value={rows} type="number"/>
                                            </div>
                                        </div>
                                    </Columns.Column>
                                    <Columns.Column>
                                        <div className="field">
                                            <label className="label">Grid Columns</label>
                                            <div className="control">
                                                <input className="input" name="columns" onChange={this.handleChange} value={columns} type="number"/>
                                            </div>
                                        </div>
                                    </Columns.Column>
                                    <Columns.Column>
                                        <div className="field">
                                            <label className="label">Number of Bulb Dimness values</label>
                                            <div className="control">
                                                <input className="input" name="dimmer_levels" onChange={this.handleChange} value={dimmer_levels} type="number"/>
                                            </div>
                                        </div>
                                    </Columns.Column>
                                    <Columns.Column>
                                    <div className="field">
                                        <label className="label">Set as active grid?</label>
                                            <div className="control">
                                                <Checkbox name="is_active" onChange={this.handleChange} checked={is_active}/>
                                            </div>
                                      </div>
                                    </Columns.Column>
                                </Columns>
                                {buttons}
                            </form>
                        </Box>
                    </Columns.Column>
                </Columns>
            </div>
            
        );
    }
}

export default Config;