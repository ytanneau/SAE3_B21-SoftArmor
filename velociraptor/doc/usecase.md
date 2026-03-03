# Exemples de cas d'utilisation

## Authentification

```text
# Client vers Serveur
1.alizon.5d41402abc4b2a76b9719d911017c592.

# Serveur vers Client si accepté
CONNECT=1

# Serveur vers Client si refusé 
CONNECT=0
```

---

## Nouveau colis

```text
# Client demande la prise en charge d'un nouveau colis
2

# Serveur accepte le nouveau colis
COLIS=7A74KHYV33SM

# Serveur refuse le nouveau colis
ERROR=2
```

---

## Info colis

```text
# Client demande la prise en charge d'un nouveau colis
3.7A74KHYV33SM

# Serveur si le colis existe
ETAPE=1
REMISE=N/A
RAISON=N/A
DATE=2026-01-18 21:43:39

# Serveur si le colis n'existe pas
ERROR=3
```

---

## Photo colis

```text
# Client demande la prise en charge d'un nouveau colis
2

# Serveur envoie la photo en binaire (# pour signifer la fin de l'image)
PHOTO=01010110110101010100...
...1011110# 

# Serveur si la photo n'existe pas
ERROR=4
```

---

## Fin de communication

```
# Client demande de fermer la communication
-1

# Serveur ferme la connexion
```