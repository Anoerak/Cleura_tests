import React from 'react';

const Card = (props) => {
	const { title, description, image, imageAlt, path } = props;

	return (
		<div id='cleura-htmx'>
			<a
				hx-target='#cleura-htmx'
				hx-swap='innerHTML'
				hx-post={path}
				// href={path}
				id='card'
			>
				<div className='card'>
					<div className='card__title'>
						<h3>{title}</h3>
					</div>
					<div className='card__image'>
						<img src={image} alt={imageAlt} />
					</div>
					<div className='card__description'>
						<p>{description}</p>
					</div>
				</div>
			</a>
		</div>
	);
};

export default Card;
