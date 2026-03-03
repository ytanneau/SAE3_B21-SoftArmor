# La spécification technique du protocole Raptor v1.0

**Equipe B2.1**: SoftArmor

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

---

### Format des messages
- **Encodage** : UTF‑8
- **Taille instruction maximale** : 100 caractères
- **Taille reponse maximale** : 100-400 caractères (la taille varie selon les instructions)

---

## Cycle d’échange de données

Les échanges suivent quatre étapes principales : **handshake**, **authentification**, **échange de données**, **fermeture**.

### Handshake

- Le client initie la connexion sur le socket du serveur
- Objectif : établir le canal et vérifier la disponibilité du serveur.

- **Délimiteurs** :
  - `.` pour client → serveur
  - `=` pour serveur → client


### Authentification

- **Mécanisme** : `1.id.MD5`
- Le client envoie son **id** et un **hash MD5** calculé selon la méthode convenue.
- Le serveur vérifie l’id et le MD5 ; en cas d’échec, la connexion est **fermée immédiatement**.

### Échange de données

- **Client** : Donne les instruction.
- **Server** : Repond au client.


### Fermeture

- Une instruction `-1` peut être envoyée par le client.
- Le serveur ferme la connexion.

---

## Glossaire

- **Instructions**
  - -1 : Fin de connection
  - 1 : Authentification
  - 2 : Nouveau colis
  - 3 : Informations du colis
  - 4 : Récupérer l'image

- **Erreurs**
  - -2 : Interne
  - -1 : Time out
  - 0 : Instruction inconnue
  - 1 : Accès (non identifié)
  - 2 : Pas de nouveau colis
  - 3 : Le colis n'existe pas
  - 4 : La photo n'existe pas

- **Etapes du colis**
  - 1 : Création d’un bordereau de livraison
  - 2 : Prise en charge du colis chez le vendeur
  - 3 : Arrivée chez le transporteur.
  - 4 : Départ vers la plateforme régionale
  - 5 : Arrivée sur la plateforme régionale
  - 6 : Départ vers le centre local
  - 7 : Arrivée au centre local
  - 8 : Départ pour la livraison finale
  - 9 : Livré ou refusé

- **Mode de remise**
  - 0 : Main propre
  - 1 : Absent (photo disponible)
  - 2 : Refusé (avec cause)

- **Causes**
  - 0 : Colis endommagé
  - 1 : Ne correspond pas à la commande 
  - 2 : En retard
  - 3 : Plus besoin du colis


