const competence = document.querySelector('.container.recent');

document.querySelector('.fleche.gauche.recent').onclick = () => {
    competence.scrollBy({ left: -700, behavior: 'smooth' });
};
document.querySelector('.fleche.droit.recent').onclick = () => {
    competence.scrollBy({ left: 700, behavior: 'smooth' });
};