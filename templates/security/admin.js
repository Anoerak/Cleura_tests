const usersLink = document.querySelector('#users-link');
const forumsLink = document.querySelector('#forums-link');
const postsLink = document.querySelector('#posts-link');

const usersDiv = document.querySelector('#users');
const forumsDiv = document.querySelector('#forums');
const postsDiv = document.querySelector('#posts');

usersLink.addEventListener('click', () => {
	usersLink.classList.add('active');
	forumsLink.classList.remove('active');
	postsLink.classList.remove('active');
	usersDiv.style.display = 'block';
	forumsDiv.style.display = 'none';
	postsDiv.style.display = 'none';
	return false;
});

forumsLink.addEventListener('click', () => {
	usersLink.classList.remove('active');
	forumsLink.classList.add('active');
	postsLink.classList.remove('active');
	usersDiv.style.display = 'none';
	forumsDiv.style.display = 'block';
	postsDiv.style.display = 'none';
	return false;
});

postsLink.addEventListener('click', () => {
	usersLink.classList.remove('active');
	forumsLink.classList.remove('active');
	postsLink.classList.add('active');
	usersDiv.style.display = 'none';
	forumsDiv.style.display = 'none';
	postsDiv.style.display = 'block';
	return false;
});
