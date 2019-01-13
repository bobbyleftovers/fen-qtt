import React, { Component } from 'react';

import Button from 'react-bulma-components/lib/components/button';

export default class Uploader extends Component
{
    constructor(props) {
        super(props);
        this.state = {
            isError: false,
            errorMsg: null,
            image: '',
            imageInfo: null,
            loading:false,
        }
    
        this.onFormSubmit = this.onFormSubmit.bind(this)
        this.onChange = this.onChange.bind(this)
        this.fileUpload = this.fileUpload.bind(this)
        this.fileSave = this.fileSave.bind(this)
    }

    onFormSubmit(e){
        e.preventDefault()
        console.log('submit',[this.state.image]);
        this.fileSave(this.state.image);
    }

    onChange(e) {
        console.log('input change',e.target.files);
        const files = e.target.files || e.dataTransfer.files;
        const file = files[0];
        const accepted = ['image/jpg','image/jpeg','image/png'];
        this.setState({
            isError: false,
            errorMsg: null
        });

        if(file.size > 2000000 ){
            this.setState({
                isError: true,
                errorMsg: 'File must be 2MB or less'
            });
            return;
        }

        if(accepted.indexOf(file.type) < 0){
            this.setState({
                isError: true,
                errorMsg: 'File type not accepted'
            });
            return;
        }

        if (!files.length){
            this.setState({
                isError: true,
                errorMsg: 'Add a file'
            });
            return;
        }

        const imageInfo = {
            name: file.name.substring(0,file.name.indexOf('.')),
            full_name: file.name,
            type: file.type,
            size: file.size
        };

        this.setState({
            imageInfo: imageInfo
        });
        this.createImage(file);
    }
    
    createImage(file) {
        console.log('create image',file);
        let reader = new FileReader();
        reader.onload = (e) => {
            this.setState({
                image: e.target.result
            })
        };
        reader.readAsDataURL(file);
    }

    fileSave(){
        console.log('fileSave',[this.state.image]);
        const formData = {
            file: this.state.image,
            info: this.state.imageInfo
        }
        return axios.post('/store', formData)
            .then(response => {
                console.log('saved',response,formData);
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
        console.log('uploading',data);
        const formData = {
            path: data.path,
            name: data.name,
            url: 'http://ienjoybobby.com/api/litebrite/receive'
        }
        return axios.post('/upload', formData)
            .then(response => {
                console.log('uploaded',response);
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
        let error = '';
        let imageDetails = '';
        let fileLabel = 'Choose a file…';
        if (this.state.isError){
            error = <span className="error">{this.state.errorMsg}</span>
        }
        if(this.state.imageInfo){
            imageDetails = (
                <ul>
                    <li><span>Name: </span><span>{this.state.imageInfo.name}</span></li>
                    <li><span>Type: </span><span>{this.state.imageInfo.type}</span></li>
                    <li><span>Size: </span><span>{this.state.imageInfo.size} bytes</span></li>
                </ul>
            );
            fileLabel = this.state.imageInfo.name;
        }
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
                                {fileLabel}
                            </span>
                        </span>
                    </label>
                    {error}
                    {imageDetails}
                </div>

                <Button disabled={!this.state.imageInfo} type="submit">
                    Submit
                </Button>
            </form>
        )
    }
}