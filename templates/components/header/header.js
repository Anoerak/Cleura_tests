const homePage = document.querySelector('#home');
const connexionButton = document.querySelector('#connexion-button');

if (homePage) {
	connexionButton.addEventListener('click', () => {
		document.querySelector('#turbo-login-link').click();
	});
	document.querySelector('#turbo-login-link').addEventListener('click', () => {
		console.log('click');
	});
}
