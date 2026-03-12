
let listeAides = document.getElementsByClassName('aide');

for (i = 0; i < listeAides.length; i++) {
    let element = listeAides[i];

    element.addEventListener('mouseover', (e) => {
        let baliseAide = document.createElement('p');

        let lignes = e.target.getAttribute('data-tooltip').split('\\n');

        for (ligne of lignes) {
            let baliseLigne = document.createElement('span');
            baliseLigne.textContent = ligne;
            baliseAide.appendChild(baliseLigne);

            baliseAide.appendChild(document.createElement('br'));
        }

        baliseAide.className = 'info-bulle';
        
        let x = Math.max(e.target.getBoundingClientRect().x - 250, 0);
        let y = e.target.getBoundingClientRect().y - 40 - 20 * lignes.length;
        if (y < 0) {
            y = e.target.getBoundingClientRect().y + 20;
        }
        
        baliseAide.setAttribute('style', 'top : ' + y + 'px; left : ' + x + 'px');

        e.target.appendChild(baliseAide);
    });

    element.addEventListener('mouseout', e => {
        e.target.removeChild(document.getElementsByClassName('info-bulle')[0]);
    })
}