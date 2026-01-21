# Exemple de cas d'utilisation

## Authentification

```text
# Client vers Serveur
1.alizon.5d41402abc4b2a76b9719d911017c592.

# Serveur vers Client si accepter
CONNECT=1

# Serveur vers Client si refuser 
CONNECT=0
```

---

## Nouveau colis

```text
# Client demande la prise en charge un nouveau colis
2

# Serveur accepte ne nouveau colis
COLIS=7A74KHYV33SM

# Server refuse le nouveau colis
ERROR=2
```

---

## Info colis

```text
# Client demande la prise en charge un nouveau colis
3.7A74KHYV33SM

# Serveur si le colis existe
ETAPE=1
REMISE=N/A
RAISON=N/A
DATE=2026-01-18 21:43:39

# Server existe pas colis
ERROR=3
```

---

## Photo colis

```text
# Client demande la prise en charge un nouveau colis
2

# Serveur envois la photo en binaire(# pour signifer la fin de l'image)
PHOTO=01010110110101010100...
...1011110# 

# Server photo existe pas
ERROR=4
```

---

## Fin de communication

```
# Client demande de fermé la communication
-1

# Le server la ferme
```