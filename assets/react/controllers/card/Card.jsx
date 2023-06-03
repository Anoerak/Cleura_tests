import React from 'react';

const Card = (props) => {
	const { title, description, image, imageAlt, path } = props;

	return (
		<a href={path} id='card'>
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
	);
};

export default Card;
