# Documentation du raptor

---

## Prérequis

### La librairie mariadb

```
sudo apt install libmariadb3 libmariadb-dev 
```

### Un fichier .env avec les données pour la bdd

Exemple :
```text
BDD_HOST=10.253.5.107
BDD_USER=user
BDD_PASSWORD=mdp
BDD_NAME=nom
BDD_PORT=3306
```

### Un fichier avec les login et mode passe

Exemple (mdp en MD5)
```text
root=63a9f0ea7bb98050f96b649e85481845
dede=098f6bcd4621d373aade4e832627b4f6
alizon=098f6bcd4621du73cade4e832627b4f6
```

### Un fichier image

Un fichier nommé ```image.png``` qui sera envoier si la photo du colis est demander.

---

## Compilation, Execution et Verification du démarage


### Compilation

```text
gcc raptor.c fonction.c -o prog `mariadb_config --cflags --libs`
```

### Execution

Avec les paramètre par defaut :
```
./prog &
```

Avec les paramètre personaliser :
```
./prog -a compte.txt -n 50 -p 9500 &
```

- a : fichier des compte, defaut = login.txt
- n : capaciter du nombre de colis, defaut = -1 donc infini
- p : port du socket, defaut = 9000


### Verification du démarage

Pour ouvire les log de l'inisalisation :
```
less init.log
```

Si la dernière ligne est ``` [RAPTOR] READY ``` alors votre serveur est près, sinon voici un exemple de liste des élément attendue dans le init.log.
```
[PARAMETRE] ... // si il y a des paramètre
[RAPTOR] START // tout les paramère son bien passsé
[RAPTOR] SUCCESS INIT COMPTE // le fichier et un au moins un compte a été touver
[RAPTOR] BDD : 
BDD_HOST 10.253.5.107
BDD_USER sae
BDD_PASSWORD dbs*******
BDD_NAME saedb
BDD_PORT 3306
[RAPTOR] SUCCESS CONNECT MYSQL // la connection a la base de donnée est bon
[RAPTOR] SUCCESS COLIS SET INFINIT // le nombre de colis est correcte
[RAPTOR] SUCCESS PORT SET 9000 // le numero du port a pu etre définie
[RAPTOR] SUCCESS INIT SOCKET // le socket est en place
[RAPTOR] READY // le serveur est prêt
```
