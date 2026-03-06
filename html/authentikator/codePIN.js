

    let blocCodePIN = document.getElementById("codePIN");

    for (const input in blocCodePIN.children) {
        if (!Object.hasOwn(blocCodePIN.children, input)) continue;
        
        const element = blocCodePIN.children[input];
        
        element.addEventListener('keypress', (e) => {
            
            if (!('0' <= e.key && e.key <= '9')) {
                e.preventDefault();
                
            } else {
                if (e.target.value != "") {
                    e.target.value = "";
                }

                if (e.target.nextSibling.nodeType == 1) { // passe le focus sur le suivant

                    e.target.nextSibling.focus();

                } else { // dernier champ, donc valide

                    // écrit la touche parce que ça envoie sur le bouton avant de prendre en compte la touche de l'utilsateur
                    e.preventDefault();
                    e.target.value = e.key;
                    document.getElementById("valider").click();

                }
            }
        });
        
        element.addEventListener('keydown', (e) => {
            let elementPrec = e.target.previousSibling;
            let elementSuiv = e.target.nextSibling;

            if (e.keyCode == 8 && elementPrec.nodeType == 1) { // backspace
                if (e.target.value == "") {
                    elementPrec.focus();
                    elementPrec.value = "";

                } else {
                    e.target.value = "";
                }
            }

            if (e.keyCode == 37 && elementPrec.nodeType == 1) { // flèche gauche
                elementPrec.focus();
            } else if (e.keyCode == 39 && elementSuiv.nodeType == 1) { // flèche droite
                elementSuiv.focus();
            }
        });
    }

    document.getElementById("valider").addEventListener("click", (e) => {
        let code = "";

        for (const input in blocCodePIN.children) {
            if (!Object.hasOwn(blocCodePIN.children, input)) continue;
            
            const element = blocCodePIN.children[input];

            code += element.value;
        }

        if (code.length == 6) {
            document.getElementById("inputPIN").setAttribute('value', code);

        } else {
            document.getElementById("inputPIN").setAttribute('value', '');
            e.preventDefault();
            document.getElementsByClassName("error")[0].textContent = "Veuillez remplir toutes les cases";
            document.getElementById("codePIN").firstElementChild.focus();

        }
    });