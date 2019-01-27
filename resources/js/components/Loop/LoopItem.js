import React, {Component} from 'react';
import { NavLink,withRouter } from 'react-router-dom';

class ProjectMenuItem extends Component {

	render(){
		return(
			<NavLink to={'/submissions/' + this.props.entry.id}>
				<div className="collection-item">
					<div className="collection-link">
						<img
							className="collection-image"
							src={'/storage/'+this.props.entry.original_path}/>
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