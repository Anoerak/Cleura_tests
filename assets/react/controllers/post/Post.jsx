import React from 'react';

function Post(props) {
	const { title, message } = props;

	return (
		<div className='post'>
			<div className='post-title'>
				<h3>{title}</h3>
			</div>
			<div className='post-message'>
				<p>{message}</p>
			</div>
		</div>
	);
}

export default Post;
