const homePage = document.querySelector('#home');
const connexionButton = document.querySelector('#connexion-button');

if (homePage) {
	connexionButton.addEventListener('click', () => {
		document.querySelector('#connexion-link').click();
	});
}
