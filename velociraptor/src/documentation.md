# Documentation du raptor

---

## Prérequie

### La librairie mariadb
```text
sudo apt install libmariadb3 libmariadb-dev 
```

### Un fichier .env avec les donnée pour la bdd

Exemple :
```text
BDD_HOST=10.253.5.107
BDD_USER=user
BDD_PASSWORD=mdp
BDD_NAME=nom
BDD_PORT=3306
```

### Un fichier avec les login et mode passe


Exemple (mdp en MD5)
```text
root=63a9f0ea7bb98050f96b649e85481845
dede=098f6bcd4621d373aade4e832627b4f6
alizon=098f6bcd4621du73cade4e832627b4f6
```

---

## Compilation, Execution et verification du démarage


### Compilation

```text
gcc raptor.c fonction.c -o prog `mariadb_config --cflags --libs`
```

### Execution

Avec les paramètre par defaut :
```
./prog
```

Avec les paramètre personaliser :
```
./prog -a login.txt -n 50 -p 9000
```

- a : fichier des compte, defaut = login.txt
- n : capaciter du nombre de colis, defaut = -1 donc infini
- p : port du socket, defaut = 9000