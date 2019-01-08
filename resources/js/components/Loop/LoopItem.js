import React, {Component} from 'react';
import { NavLink,withRouter } from 'react-router-dom';

class ProjectMenuItem extends Component {

	render(){
		console.log('entry',this.props.entry);
		return(
			<NavLink to={'/submissions/' + this.props.entry.id}>
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
			</NavLink>
		);
	}
}

export default withRouter(ProjectMenuItem);