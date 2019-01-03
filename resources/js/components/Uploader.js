import React, { Component } from 'react';

import Button from 'react-bulma-components/lib/components/button';

export default class Uploader extends Component
{
    constructor(props) {
        super(props);
        this.state = {
            response: null,
            image: '',
            loading:false,
        }
    
        this.handleChange = this.handleChange.bind(this);
        this.handleSubmit = this.handleSubmit.bind(this);

        // ----
        this.onFormSubmit = this.onFormSubmit.bind(this)
        this.onChange = this.onChange.bind(this)
        this.fileUpload = this.fileUpload.bind(this)
        this.fileSave = this.fileSave.bind(this)
        // ----
    }

    // ----

    onFormSubmit(e){
        e.preventDefault() 
        this.fileSave(this.state.image);
    }

    onChange(e) {
        let files = e.target.files || e.dataTransfer.files;
        if (!files.length)
            return;
        this.createImage(files[0]);
    }
    
    createImage(file) {
        let reader = new FileReader();
        reader.onload = (e) => {
            this.setState({
                image: e.target.result
            })
        };
        reader.readAsDataURL(file);
    }

    fileSave(){

        const formData = {
            file: this.state.image,
        }
        return axios.post('/store', formData)
            .then(response => {
                console.log(response);
                this.fileUpload(response.data)
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
                this.$toast.open({
                    duration: 5000,
                    message: message,
                    position: 'is-bottom',
                    type: 'is-danger'
                });
            })
    }
    
    fileUpload(data){
        console.log(data);
        const formData = {
            path: data.path,
            name: data.name,
            url: 'http://ienjoybobby.com/api/litebrite/receive'
        }
        return axios.post('/upload', formData)
            .then(response => {
                console.log(response);
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
                this.$toast.open({
                    duration: 5000,
                    message: message,
                    position: 'is-bottom',
                    type: 'is-danger'
                });
            })
    }

    // ----

    handleChange(event) {
        console.log(event.target.value);
        this.setState({file: event.target.value});
    }

    handleSubmit(event) {
        console.log(event);
        event.preventDefault();
        axios.post('/store',{file:this.state.file,also:'testing'})
        .then(res => {
            console.log('response:',res);
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
            this.$toast.open({
                duration: 5000,
                message: message,
                position: 'is-bottom',
                type: 'is-danger'
            });
        })
    }

    render(){
        return(
            <form onSubmit={this.onFormSubmit}>
                <div className="file has-name is-fullwidth">
                    
                    <label className="file-label">
                        <input className="file-input" type="file" name="resume" value={this.state.file} onChange={this.onChange}/>
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

                <Button type="submit">
                    Submit
                </Button>
            </form>
        )
    }
}