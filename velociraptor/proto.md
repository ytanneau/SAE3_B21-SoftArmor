# Génération du fichier Markdown complet pour la spécification technique du protocole Raptor v1.0
import os

markdown_content = """# Raptor Protocol — Spécification technique

**Version**: 1.0  
**Langue**: Français  
**Public cible**: Développeurs backend

---

## Résumé technique

**Raptor** est un protocole **client‑serveur** conçu pour **transmettre les informations d’un colis**. Il utilise un **format de message personnalisé** avec une **taille maximale de 100 caractères par instruction**, des **délimiteurs distincts** selon le sens du flux et une **authentification simple id + MD5**. Aucun chiffrement n’est défini dans la version 1.0. Ce document contient le résumé technique, la spécification complète et des annexes d’exemples.

---

## Architecture

**Topologie**: Client‑Server

**Acteurs**:
- **Client** : envoie les instructions et les données du colis.
- **Serveur** : valide, traite et répond aux instructions.

**Diagramme**:  
![Diagramme d'architecture Raptor](URL_DE_L_IMAGE)

---

## Cycle d’échange de données

Les échanges suivent quatre étapes principales : **handshake**, **authentification**, **échange de données**, **terminaison**.

### Handshake

- Le client initie la connexion sur le transport choisi (TCP recommandé).
- Objectif : établir le canal et vérifier la disponibilité du serveur.

### Authentification

- **Mécanisme** : `id + MD5`
- Le client envoie son **id** et un **hash MD5** calculé selon la méthode convenue.
- Le serveur vérifie l’id et le MD5 ; en cas d’échec la connexion est **fermée immédiatement**.
- **Recommandation** : utiliser un **nonce** côté serveur pour éviter les replays ; calculer MD5 sur `nonce + secret_partagé` ou `secret_partagé + nonce`.

### Échange de données

- **Format** : personnalisé
- **Taille maximale** : 100 caractères par instruction (UTF‑8)
- **Délimiteurs** :
  - `.` pour client → serveur
  - `=` pour serveur → client
- Chaque instruction est une unité logique terminée par le délimiteur approprié.

### Terminaison

- Une instruction `TERM` peut être envoyée par l’une ou l’autre partie.
- La partie destinataire confirme la terminaison puis la connexion est fermée.

---

## Format des messages et framing

- **Encodage** : UTF‑8
- **Framing** : chaque instruction est transmise comme une séquence d’octets se terminant par le délimiteur. Le transport doit préserver l’ordre et l’intégrité jusqu’au délimiteur.
- **Taille maximale** : 100 caractères par instruction

### Exemples

```text
# Client vers Serveur
AUTH id:client123 md5:5d41402abc4b2a76b9719d911017c592.

# Serveur vers Client
AUTH_OK id:client123 status:accepted=

# Client envoi info colis
PKG id:PKG0001 loc:FR-35000 wt:2.5kg.

# Serveur accusé réception
PKG_ACK id:PKG0001 status:received=
