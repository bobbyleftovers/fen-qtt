import React, { Component } from 'react';

import Columns from 'react-bulma-components/lib/components/columns';
import Box from 'react-bulma-components/lib/components/box';
import Button from 'react-bulma-components/lib/components/button';
import Loading from '../UI/Loading';

import Grid from '../Grid/Grid';

class SubmissionMain extends Component{

    constructor(props){
        super(props);
        this.state = {
            config:null,
            activeConfig:null,
            filename:'',
            image:null,
            imagetest:null,
            updating:false
        };
        this.updateFromActiveConfig = this.updateFromActiveConfig.bind(this);
    }

    componentWillMount(){
        console.log('image id',this.props.match.params.id);
        if(this.props.match.params.id){
            this.getImageData();
        }
        this.getActiveConfig()
    }

    updateFromActiveConfig(){
        console.log('props',this.props);
        this.setState({updating:true})
        axios.post('/update/' + this.props.match.params.id,{id:this.props.match.params.id})
            .then(res => {
                console.log('ok',res.data);
                
                this.setState({
                    updating:false,
                    image:res.data
                })
                // this.getImageData();
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
            console.log('active',res.data);
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
                filename:res.data.cropped_image,
                image:JSON.parse(res.data.image_json)
            });
            // console.log(this.state);
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
        if(this.state.image && this.state.config){
            grid = <Grid map={this.state.image} config={this.state.config}/>
        }
        let updater = null;
        return (
            <div>
                <pre>
                    <code>
                        {JSON.stringify(this.state.config, null, 2)}
                    </code><br/>
                    <code>
                        {JSON.stringify(this.state.updating, null, 2)}
                    </code>
                </pre>
                <Columns>
                    <Columns.Column>
                        <img src={'/' + this.state.filename} className="column" style={{width:'100%'}}/>
                    </Columns.Column>
                    <Columns.Column>
                        <div className="grid-wrap">
                            {grid}
                        </div>
                    </Columns.Column>
                </Columns>
                <Columns>
                    <Columns.Column>
                        <Box>
                            <Button onClick={this.updateFromActiveConfig}>Update To Active Config</Button>
                            <Button onClick={this.recordAdjustments}>Record Image Adjustments</Button>
                        </Box>
                    </Columns.Column>
                </Columns>
                <Columns>
                    <Columns.Column>
                        <pre>
                            <code>
                                {JSON.stringify(this.state.image, null, 2)}
                            </code>
                        </pre>
                    </Columns.Column>
                    
                    <Columns.Column>
                        <pre>
                            <code>
                                {JSON.stringify(this.state.imagetest, null, 2)}
                            </code>
                        </pre>
                    </Columns.Column>
                </Columns>
            </div>  
        );
    }
}

export default SubmissionMain;