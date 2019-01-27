import React, { Component } from 'react';

import Columns from 'react-bulma-components/lib/components/columns';
import Box from 'react-bulma-components/lib/components/box';
import Button from 'react-bulma-components/lib/components/button';
// import Loading from '../UI/Loading';

import Grid from '../Grid/Grid';

class SubmissionMain extends Component{

    constructor(props){
        super(props);
        this.state = {
            config:null,
            activeConfig:null,
            filename:'',
            image:null,
            updating:false
        };
        this.updateFromActiveConfig = this.updateFromActiveConfig.bind(this);
    }

    componentWillMount(){
        if(this.props.match.params.id){
            this.getImageData();
        }
        this.getActiveConfig()
    }

    updateFromActiveConfig(){
        this.setState({updating:true})
        axios.post('/update/' + this.props.match.params.id,{id:this.props.match.params.id})
            .then(res => {
                this.setState({
                    updating:false,
                    image:res.data
                })
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
                console.log('ERROR CONFIG:',error.config,message);
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

    recordAdjustments(){
        console.log('click');
    }

    getActiveConfig(){
        axios.get('/active-config')
        .then(res => {
            this.setState({activeConfig:res.data})
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

    getImageData(){
        axios.post('/get-image',{id:this.props.match.params.id})
        .then(res => {
            this.setState({
                config:res.data.config,
                original_path:res.data.original_path,
                filename:res.data.filename,
                image:JSON.parse(res.data.image_json)
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
            console.log('ERROR CONFIG:',error.config,message);
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
    
    render(){
        let grid = null;
        let image = null;
        let configInfo = null;
        if(this.state.image && this.state.config){
            image = <img src={'/storage/' + this.state.original_path} style={{width:'100%'}}/>
            configInfo = (
                <pre>
                    <code>ID: {this.state.config.id}</code><br />
                    <code>Name: {this.state.config.name}</code><br />
                    <code>Rows: {this.state.config.rows}</code><br />
                    <code>Columns: {this.state.config.columns}</code><br />
                    <code>Dimmer : {this.state.config.dimmer_levels} levels</code><br />
                </pre>
            );
            
            // determine some dimensions (we don't need all of these atm but we may later on)
            const aspectRatio = this.state.config.rows/this.state.config.columns;
            const imgWidth = document.querySelector('.grid-wrap').offsetWidth;
            const cellWidth = imgWidth/this.state.config.columns;
            const imgHeight = cellWidth * aspectRatio * this.state.config.columns;
            const cellHeight = imgHeight/this.state.config.rows
            
            grid = <Grid map={this.state.image} config={this.state.config}/>
        }
        // let updater = null;
        return (
            <div>
                <Columns>
                    <Columns.Column>
                        <h1 className="title">Results for {this.state.filename}</h1>
                    </Columns.Column>
                </Columns>
                <Columns>
                    <Columns.Column>
                        <Box>
                            <h2 className="subtitle">Config</h2>
                            {configInfo}
                            <hr/>
                            <Button onClick={this.updateFromActiveConfig}>Update To Active Config</Button>
                            <Button onClick={this.recordAdjustments}>Record Image Adjustments</Button>
                        </Box>
                    </Columns.Column>
                    <Columns.Column>
                        {image}
                    </Columns.Column>
                </Columns>
                <Columns>
                    <Columns.Column className="grid-wrap">
                        {grid}
                    </Columns.Column>
                </Columns>
            </div>  
        );
    }
}

export default SubmissionMain;