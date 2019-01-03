import React, {Component} from 'react';
import { withRouter } from 'react-router-dom';

class ProjectMenuItem extends Component {

	render(){
		return(
			<div className="collection-item">
				<div className="collection-link">
					<img
						className="collection-image"
						src={'/images/' + this.props.entry.filename }/>
				</div>
				<div className="collection-title">
					<h3 className="subtitle is-4">{this.props.entry.filename}</h3>
				</div>
			</div>
		);
	}
}

export default withRouter(ProjectMenuItem);