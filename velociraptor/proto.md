# La spécification technique du protocole Raptor v1.0

**Equipe B2.1** : SoftArmor
**Version**: 1.0  
**Langue**: Français  
**Public cible**: Développeurs backend

---

## Résumé technique

**Raptor** est un protocole **client‑serveur** conçu pour **transmettre les informations d’un colis**. Il utilise un **format de message personnalisé** avec une **taille maximale de 100 caractères par instruction**, des **délimiteurs distincts** selon le sens du flux. Aucun chiffrement n’est défini dans la version 1.0. Ce document contient le résumé technique, la spécification complète et des annexes d’exemples.

---

## Architecture

**Topologie**: Client‑Server

**Acteurs**:
- **Client** : envoie les instructions.
- **Serveur** : valide, traite et répond aux instructions.

**Diagramme**:  
![Diagramme d'architecture Raptor](URL_DE_L_IMAGE)

---

## Cycle d’échange de données

Les échanges suivent quatre étapes principales : **handshake**, **authentification**, **échange de données**, **terminaison**.

### Handshake

- Le client initie la connexion sur le socket du serveur
- Objectif : établir le canal et vérifier la disponibilité du serveur.

### Authentification

- **Mécanisme** : `1.id.MD5`
- Le client envoie son **id** et un **hash MD5** calculé selon la méthode convenue.
- Le serveur vérifie l’id et le MD5 ; en cas d’échec la connexion est **fermée immédiatement**.

### Échange de données

- **Format** : personnalisé
- **Taille maximale** : 100 caractères par instruction (UTF‑8)
- **Délimiteurs** :
  - `.` pour client → serveur
  - `=` pour serveur → client


### Terminaison

- Une instruction `-1` peut être envoyée par le client.
- Le server ferme la connexion.

---

## Glosaire

- **Instruction**
  - -1 : Fin de connection
  - 1 : Authentification
  - 2 : Nouveau colis
  - 3 : Info colis
  - 4 : Get image

- **Erreur**
  - -2 : Interne
  - -1 : Time out
  - 0 : instruction inconu
  - 1 : acces (pas identifier)
  - 2 : pas de nouveau colis
  - 3 : colis existe pas
  - 4 : photo existe pas

- **Etape du colis**
  - 1 : Création d’un bordereau de livraison
  - 2 : Prise en charge du colis chez le vendeur
  - 3 : arrivée chez le transporteur.
  - 4 : départ vers la plateforme régionale.
  - 5 : arrivée sur la plateforme régionale.
  - 6 : départ vers le centre local.
  - 7 : arrivée au centre local.
  - 8 : départ pour la livraison finale.
  - 9 : Livré ou refusé

- **Mode de remise**
  - 0 : Main propre
  - 1 : Absent (photo disponible)
  - 2 : refusé (donc cause)

- **Cause**
  - 0 : Colis endomagé
  - 1 : ne correspond pas à la commande 
  - 2 : en retard
  - 3 : plus besoin du colis

---

## Format des messages

- **Encodage** : UTF‑8
- **Taille instruction maximale** : 100 caractères
- **Taille reponse maximale** : 400 caractères (la taille varie leson les instruction)

### Exemples

#### Authentification

```text
# Client vers Serveur
1.alizon.5d41402abc4b2a76b9719d911017c592.

# Serveur vers Client si accepter
CONNECT=1

# Serveur vers Client si refuser 
CONNECT=0
```

#### Nouveau colis

```text
# Client demande la prise en charge un nouveau colis
2

# Serveur accepte ne nouveau colis
COLIS=7A74KHYV33SM

# Server refuse le nouveau colis
ERROR=2
```

#### Info colis

```text
# Client demande la prise en charge un nouveau colis
3.7A74KHYV33SM

# Serveur si le colis existe
ETAPE=1
REMISE=N/A
RAISON=N/A
DATE=854894

# Server existe pas colis
ERROR=3
```

#### Photo colis

```text
# Client demande la prise en charge un nouveau colis
2

# Serveur envois la photo en binaire(# pour signifer la fin de l'image)
PHOTO=01010110110101010100...
...1011110# 

# Server photo existe pas
ERROR=4
```
